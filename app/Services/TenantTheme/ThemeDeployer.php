<?php

namespace App\Services\TenantTheme;

use App\Models\Company;
use App\Models\ThemeDeployment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Publishes a tenant's developer theme.
 *
 * Every deploy unpacks into a fresh folder, public/<public_dir>/<company id>/<name>/,
 * and only then points the company at it, so a failed deploy never touches
 * what is live. The last few successful folders are kept for rollback.
 */
class ThemeDeployer
{
    // GitHub's own formats: owner is letters, digits and single dashes; the
    // repository name may not be "." or "..", so it can never step out of
    // /repos/{owner}/{name}/ in an API URL.
    public const REPO_PATTERN = '/^[A-Za-z0-9](?:[A-Za-z0-9]|-(?=[A-Za-z0-9])){0,38}\/(?!\.\.?$)[A-Za-z0-9_.\-]{1,100}$/';
    // Git's own rules, narrowed: no "..", no leading/trailing or doubled
    // slash, no leading dot or dash, no ".lock" ending.
    public const BRANCH_PATTERN = '/^(?!.*\.\.)(?!.*\/\/)(?![\/.\-])(?!.*[\/.]$)(?!.*\.lock$)[A-Za-z0-9._\/\-]{1,100}$/';

    public static function publicRoot(): string
    {
        return public_path(config('tenant_themes.public_dir'));
    }

    /**
     * Deploy a commit from the company's GitHub repository. With no SHA, the
     * current head of the tracked branch is used.
     */
    public function deployFromGitHub(Company $company, ?string $sha, string $triggeredBy): ThemeDeployment
    {
        $meta = ['source' => 'github', 'ref' => $company->theme_branch, 'commit_sha' => $sha, 'triggered_by' => $triggeredBy];

        $zipPath = null;
        try {
            if (!$company->theme_repo || !preg_match(self::REPO_PATTERN, $company->theme_repo)) {
                throw new ThemePackageException('No GitHub repository is set for this company.');
            }

            $sha = $sha ?: $this->headCommit($company);
            if (!preg_match('/^[0-9a-f]{40}$/', $sha)) {
                throw new ThemePackageException('GitHub returned an invalid commit id.');
            }
            $meta['commit_sha'] = $sha;

            $zipPath = $this->download($company->theme_repo, $sha);

            return $this->deployFromZip($company, $zipPath, $meta);
        } catch (ThemePackageException $e) {
            return $this->recordFailure($company, $meta, $e->getMessage());
        } catch (Throwable $e) {
            Log::error('Tenant theme GitHub deploy failed', ['company_id' => $company->id, 'error' => $e->getMessage()]);
            return $this->recordFailure($company, $meta, 'Could not reach GitHub: ' . Str::limit($e->getMessage(), 200));
        } finally {
            if ($zipPath) {
                @unlink($zipPath);
            }
        }
    }

    /**
     * Unpack, check and publish a theme zip. Returns the deployment record,
     * which is 'failed' (with the reason) if the theme was refused.
     */
    public function deployFromZip(Company $company, string $zipPath, array $meta): ThemeDeployment
    {
        $lock = Cache::lock('tenant-theme-deploy:' . $company->id, 180);
        if (!$lock->get()) {
            return $this->recordFailure($company, $meta, 'Another deploy for this company is already running. Try again in a minute.');
        }

        $staging = storage_path('app/' . config('tenant_themes.staging_dir') . '/' . $company->id . '-' . Str::random(10));

        try {
            $package = ThemePackage::fromZip($zipPath, $staging);

            $this->ensureGuard();
            $short = isset($meta['commit_sha']) ? substr((string) $meta['commit_sha'], 0, 7) : $meta['source'];
            $directory = $company->id . '/' . now()->format('Ymd-His') . '-' . $short . '-' . Str::lower(Str::random(6));
            $target = self::publicRoot() . '/' . $directory;

            File::ensureDirectoryExists(dirname($target), 0755);
            if (!File::moveDirectory($staging, $target)) {
                throw new ThemePackageException('The theme could not be published on the server. Nothing was changed.');
            }

            $message = count($package->files) . ' files published.';
            if ($package->skipped) {
                $message .= ' Not published (repository files): ' . Str::limit(implode(', ', $package->skipped), 300) . '.';
            }

            $deployment = ThemeDeployment::create([
                'company_id'   => $company->id,
                'source'       => $meta['source'],
                'ref'          => $meta['ref'] ?? null,
                'commit_sha'   => $meta['commit_sha'] ?? null,
                'status'       => 'success',
                'message'      => $message,
                'manifest'     => $package->manifest,
                'files'        => $package->files,
                'file_count'   => count($package->files),
                'total_bytes'  => $package->totalBytes,
                'directory'    => $directory,
                'triggered_by' => $meta['triggered_by'] ?? null,
            ]);

            $company->forceFill(['theme_deployment_id' => $deployment->id])->save();
            $this->prune($company);

            return $deployment;
        } catch (ThemePackageException $e) {
            return $this->recordFailure($company, $meta, $e->getMessage());
        } finally {
            File::deleteDirectory($staging);
            $lock->release();
        }
    }

    /**
     * Make an earlier successful deployment live again.
     */
    public function rollback(Company $company, ThemeDeployment $target, string $triggeredBy): ThemeDeployment
    {
        if ($target->company_id !== $company->id || !$target->isAvailable()) {
            throw new ThemePackageException('That version is no longer available to roll back to.');
        }

        $company->forceFill(['theme_deployment_id' => $target->id])->save();

        return ThemeDeployment::create([
            'company_id'   => $company->id,
            'source'       => 'rollback',
            'ref'          => $target->ref,
            'commit_sha'   => $target->commit_sha,
            'status'       => 'success',
            'message'      => 'Rolled back to deployment #' . $target->id . ($target->shortSha() ? ' (' . $target->shortSha() . ')' : '') . '.',
            'triggered_by' => $triggeredBy,
        ]);
    }

    /**
     * Remove every published file for the company. The theme stops showing
     * at once; the history stays.
     */
    public function wipe(Company $company, string $triggeredBy): ThemeDeployment
    {
        File::deleteDirectory(self::publicRoot() . '/' . $company->id);
        ThemeDeployment::where('company_id', $company->id)->whereNotNull('directory')->update(['directory' => null]);
        $company->forceFill(['theme_deployment_id' => null])->save();

        return ThemeDeployment::create([
            'company_id'   => $company->id,
            'source'       => 'wipe',
            'status'       => 'success',
            'message'      => 'All published theme files were removed.',
            'triggered_by' => $triggeredBy,
        ]);
    }

    /**
     * Keep the live deployment plus the newest others, up to keep_deployments.
     */
    public function prune(Company $company): void
    {
        $keep = max(1, (int) config('tenant_themes.keep_deployments', 5));

        $stored = ThemeDeployment::where('company_id', $company->id)
            ->where('status', 'success')
            ->whereNotNull('directory')
            ->orderByDesc('id')
            ->get();

        $kept = $stored->filter(fn ($d) => $d->id === $company->theme_deployment_id)->values();
        foreach ($stored as $d) {
            if ($kept->count() >= $keep) {
                break;
            }
            if (!$kept->contains('id', $d->id)) {
                $kept->push($d);
            }
        }

        foreach ($stored as $d) {
            if (!$kept->contains('id', $d->id)) {
                File::deleteDirectory(self::publicRoot() . '/' . $d->directory);
                $d->forceFill(['directory' => null])->save();
            }
        }
    }

    /**
     * Belt and braces: even though no executable file can get through the
     * package checks, tell Apache/LiteSpeed never to run scripts or list
     * folders under the themes directory.
     */
    public function ensureGuard(): void
    {
        $root = self::publicRoot();
        File::ensureDirectoryExists($root, 0755);
        $guard = $root . '/.htaccess';
        $contents = <<<'HTACCESS'
# Tenant developer themes: static files only. Managed by ThemeDeployer.
Options -Indexes -ExecCGI
<IfModule mod_php.c>
    php_flag engine off
</IfModule>
<IfModule mod_php7.c>
    php_flag engine off
</IfModule>
<IfModule mod_php8.c>
    php_flag engine off
</IfModule>
RemoveHandler .php .phtml .phar .cgi .pl .py .sh
RemoveType .php .phtml .phar
<FilesMatch "\.(php|phtml|phar|pht|cgi|pl|py|sh)$">
    Require all denied
</FilesMatch>
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
</IfModule>
HTACCESS;
        if (!is_file($guard) || file_get_contents($guard) !== $contents) {
            file_put_contents($guard, $contents);
        }
    }

    private function headCommit(Company $company): string
    {
        $branch = $company->theme_branch ?: 'main';
        $response = $this->github()
            ->withHeaders(['Accept' => 'application/vnd.github.sha'])
            ->get('/repos/' . $company->theme_repo . '/commits/' . implode('/', array_map('rawurlencode', explode('/', $branch))));

        if ($response->status() === 404) {
            throw new ThemePackageException("GitHub could not find branch \"{$branch}\" in {$company->theme_repo}. "
                . 'Check the repository name, the branch, and that the platform token can read the repository.');
        }
        if (!$response->successful()) {
            throw new ThemePackageException('GitHub refused the request (HTTP ' . $response->status() . '). Check the platform token.');
        }

        return trim($response->body());
    }

    private function download(string $repo, string $sha): string
    {
        $response = $this->github()->get('/repos/' . $repo . '/zipball/' . $sha);

        if (!$response->successful()) {
            throw new ThemePackageException('GitHub refused to send commit ' . substr($sha, 0, 7) . ' (HTTP ' . $response->status() . ').');
        }
        if (strlen($response->body()) > config('tenant_themes.limits.max_zip_bytes')) {
            throw new ThemePackageException('The repository archive is too large.');
        }

        $path = tempnam(sys_get_temp_dir(), 'gt-theme-') . '.zip';
        file_put_contents($path, $response->body());
        return $path;
    }

    private function github()
    {
        $request = Http::baseUrl(rtrim(config('tenant_themes.github.api'), '/'))
            ->timeout((int) config('tenant_themes.github.timeout', 30))
            ->withHeaders([
                'Accept'               => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
                'User-Agent'           => 'GoTrips-Tenant-Themes',
            ]);

        if ($token = config('tenant_themes.github.token')) {
            $request = $request->withToken($token);
        }

        return $request;
    }

    private function recordFailure(Company $company, array $meta, string $message): ThemeDeployment
    {
        return ThemeDeployment::create([
            'company_id'   => $company->id,
            'source'       => $meta['source'] ?? 'github',
            'ref'          => $meta['ref'] ?? null,
            'commit_sha'   => $meta['commit_sha'] ?? null,
            'status'       => 'failed',
            'message'      => $message,
            'triggered_by' => $meta['triggered_by'] ?? null,
        ]);
    }
}

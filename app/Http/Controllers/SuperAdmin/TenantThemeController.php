<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SuperAdminAuditLog;
use App\Models\ThemeDeployment;
use App\Services\TenantTheme\ThemeDeployer;
use App\Services\TenantTheme\ThemePackageException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Super-admin control of a tenant's developer theme: switch it on or off,
 * connect the GitHub repository, deploy, upload, roll back and wipe.
 * Every action is written to the super-admin audit log.
 */
class TenantThemeController extends Controller
{
    public function __construct(private ThemeDeployer $deployer)
    {
    }

    public function show(Company $company): View
    {
        $deployments = $company->themeDeployments()->paginate(20);
        $live = $company->activeThemeDeployment;

        return view('superadmin.companies.theme', [
            'company'     => $company,
            'deployments' => $deployments,
            'live'        => ($live && $live->isAvailable()) ? $live : null,
            'webhookUrl'  => route('tenant-themes.webhook', $company->id),
            'hasToken'    => (bool) config('tenant_themes.github.token'),
        ]);
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'theme_enabled' => 'nullable|boolean',
            'theme_repo'    => ['nullable', 'string', 'max:200', 'regex:' . ThemeDeployer::REPO_PATTERN],
            'theme_branch'  => ['required', 'string', 'max:100', 'regex:' . ThemeDeployer::BRANCH_PATTERN],
        ], [
            'theme_repo.regex'   => 'Enter the repository as owner/name, for example gotrips-themes/savanna.',
            'theme_branch.regex' => 'The branch name may only use letters, numbers, dots, dashes, underscores and slashes.',
        ]);

        $before = $company->only(['theme_enabled', 'theme_repo', 'theme_branch']);
        $company->forceFill([
            'theme_enabled' => $request->boolean('theme_enabled'),
            // A blank field clears the repository; a missing one keeps it.
            'theme_repo'    => array_key_exists('theme_repo', $validated) ? ($validated['theme_repo'] ?: null) : $company->theme_repo,
            'theme_branch'  => $validated['theme_branch'],
        ])->save();

        SuperAdminAuditLog::log('company.theme.settings', $company, [
            'before' => $before,
            'after'  => $company->only(['theme_enabled', 'theme_repo', 'theme_branch']),
        ]);

        return back()->with('success', $company->theme_enabled
            ? 'Developer theme settings saved. The theme is ON.'
            : 'Developer theme settings saved. The theme is OFF: nothing from it shows on the tenant site.');
    }

    public function generateSecret(Company $company): RedirectResponse
    {
        $company->forceFill(['theme_webhook_secret' => Str::random(48)])->save();
        SuperAdminAuditLog::log('company.theme.secret', $company, ['rotated' => true]);

        return back()->with('success', 'New webhook secret generated. Update it in the GitHub webhook settings; the old one no longer works.');
    }

    public function deploy(Company $company): RedirectResponse
    {
        $deployment = $this->deployer->deployFromGitHub($company, null, $this->actor());
        SuperAdminAuditLog::log('company.theme.deploy', $company, $this->summary($deployment));

        return $this->result($deployment);
    }

    public function upload(Request $request, Company $company): RedirectResponse
    {
        $request->validate([
            'theme_zip' => 'required|file|mimes:zip|max:' . (int) (config('tenant_themes.limits.max_zip_bytes') / 1024),
        ], [
            'theme_zip.mimes' => 'Upload the theme as a .zip file.',
        ]);

        $deployment = $this->deployer->deployFromZip($company, $request->file('theme_zip')->getRealPath(), [
            'source'       => 'upload',
            'triggered_by' => $this->actor(),
        ]);
        SuperAdminAuditLog::log('company.theme.upload', $company, $this->summary($deployment));

        return $this->result($deployment);
    }

    public function rollback(Company $company, ThemeDeployment $deployment): RedirectResponse
    {
        try {
            $record = $this->deployer->rollback($company, $deployment, $this->actor());
        } catch (ThemePackageException $e) {
            return back()->with('error', $e->getMessage());
        }
        SuperAdminAuditLog::log('company.theme.rollback', $company, ['to_deployment' => $deployment->id]);

        return back()->with('success', $record->message);
    }

    public function wipe(Company $company): RedirectResponse
    {
        $this->deployer->wipe($company, $this->actor());
        SuperAdminAuditLog::log('company.theme.wipe', $company);

        return back()->with('success', 'All published theme files were removed. The tenant site is back to the standard look.');
    }

    private function result(ThemeDeployment $deployment): RedirectResponse
    {
        return $deployment->isSuccess()
            ? back()->with('success', 'Deployed. ' . $deployment->message)
            : back()->with('error', 'Not deployed: ' . $deployment->message);
    }

    private function summary(ThemeDeployment $deployment): array
    {
        return ['deployment' => $deployment->id, 'status' => $deployment->status, 'commit' => $deployment->shortSha()];
    }

    private function actor(): string
    {
        return 'admin:' . (auth()->user()->email ?? 'unknown');
    }
}

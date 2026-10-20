<?php

namespace Tests\Feature\TenantTheme;

use App\Models\ThemeDeployment;
use App\Services\TenantTheme\TenantTheme;
use App\Services\TenantTheme\ThemeDeployer;
use App\Services\TenantTheme\ThemePackageException;
use Illuminate\Support\Facades\Http;

class ThemeDeployerTest extends ThemeTestCase
{
    private const SHA = 'a1b2c3d4e5f60718293a4b5c6d7e8f9012345678';

    private function deployer(): ThemeDeployer
    {
        return app(ThemeDeployer::class);
    }

    private function fakeGitHub(string $repo, ?string $zip = null, int $headStatus = 200): void
    {
        Http::fake([
            "api.github.test/repos/{$repo}/commits/main" => Http::response(self::SHA, $headStatus),
            "api.github.test/repos/{$repo}/zipball/*"    => Http::response(file_get_contents($zip ?? ThemeZip::valid('acme-theme-' . substr(self::SHA, 0, 7) . '/')), 200),
        ]);
    }

    public function test_github_deploy_publishes_the_head_commit_and_makes_it_live(): void
    {
        $company = $this->company();
        $company->save();
        $this->fakeGitHub($company->theme_repo);

        $d = $this->deployer()->deployFromGitHub($company, null, 'test');

        $this->assertSame('success', $d->status, (string) $d->message);
        $this->assertSame(self::SHA, $d->commit_sha);
        $this->assertSame($d->id, $company->fresh()->theme_deployment_id);
        $this->assertFileExists($this->themePath($d->directory . '/theme.css'));
        $this->assertFileExists($this->themePath('.htaccess'), 'The no-scripts guard must be written.');
        $this->assertStringContainsString('php_flag engine off', file_get_contents($this->themePath('.htaccess')));
        $this->assertStringContainsString('README.md', $d->message, 'Skipped repository files are reported.');

        Http::assertSent(fn ($r) => $r->hasHeader('Authorization', 'Bearer test-token'));
    }

    public function test_refused_theme_is_recorded_and_live_theme_is_untouched(): void
    {
        $company = $this->company();
        $company->save();
        $this->fakeGitHub($company->theme_repo);
        $good = $this->deployer()->deployFromGitHub($company, null, 'test');

        $bad = ThemeZip::valid('', ['assets/shell.php' => '<?php']);
        $failed = $this->deployer()->deployFromZip($company->fresh(), $bad, ['source' => 'upload', 'triggered_by' => 'test']);

        $this->assertSame('failed', $failed->status);
        $this->assertStringContainsString('server-side', $failed->message);
        $this->assertSame($good->id, $company->fresh()->theme_deployment_id);
        $this->assertFileExists($this->themePath($good->directory . '/theme.css'));
        $this->assertCount(0, glob(storage_path('app/theme-staging/' . $company->id . '-*')), 'Staging is cleaned up.');
    }

    public function test_missing_branch_gives_a_clear_failure(): void
    {
        $company = $this->company();
        $company->save();
        $this->fakeGitHub($company->theme_repo, null, 404);

        $d = $this->deployer()->deployFromGitHub($company, null, 'test');

        $this->assertSame('failed', $d->status);
        $this->assertStringContainsString('could not find branch "main"', $d->message);
        $this->assertNull($company->fresh()->theme_deployment_id);
    }

    public function test_no_repository_configured_fails_cleanly(): void
    {
        $company = $this->company();
        $company->forceFill(['theme_repo' => null])->save();

        $d = $this->deployer()->deployFromGitHub($company, null, 'test');

        $this->assertSame('failed', $d->status);
        $this->assertStringContainsString('No GitHub repository', $d->message);
    }

    public function test_rollback_switches_back_to_an_earlier_version(): void
    {
        $company = $this->company();
        $company->save();
        $first = $this->deployer()->deployFromZip($company, ThemeZip::make(['theme.css' => 'a{color:red}']), ['source' => 'upload']);
        $second = $this->deployer()->deployFromZip($company->fresh(), ThemeZip::make(['theme.css' => 'a{color:blue}']), ['source' => 'upload']);
        $this->assertSame($second->id, $company->fresh()->theme_deployment_id);

        $record = $this->deployer()->rollback($company->fresh(), $first, 'test');

        $this->assertSame('rollback', $record->source);
        $this->assertSame($first->id, $company->fresh()->theme_deployment_id);
        TenantTheme::flush();
        $this->assertStringContainsString($first->directory, TenantTheme::for($company->fresh())->url('theme.css'));
    }

    public function test_rollback_refuses_another_companys_deployment(): void
    {
        $a = $this->company(); $a->save();
        $b = $this->company(); $b->save();
        $bDeploy = $this->deployer()->deployFromZip($b, ThemeZip::make(['theme.css' => 'x']), ['source' => 'upload']);

        $this->expectException(ThemePackageException::class);
        $this->deployer()->rollback($a, $bDeploy, 'test');
    }

    public function test_only_the_newest_versions_are_kept_on_disk(): void
    {
        config(['tenant_themes.keep_deployments' => 2]);
        $company = $this->company();
        $company->save();

        $ids = [];
        foreach (['a', 'b', 'c', 'd'] as $c) {
            $ids[] = $this->deployer()->deployFromZip($company->fresh(), ThemeZip::make(['theme.css' => $c]), ['source' => 'upload'])->id;
        }

        $onDisk = ThemeDeployment::where('company_id', $company->id)->whereNotNull('directory')->pluck('id')->all();
        sort($onDisk);
        $this->assertSame([$ids[2], $ids[3]], $onDisk);
        $this->assertCount(2, glob($this->themePath($company->id . '/*'), GLOB_ONLYDIR));
    }

    public function test_pruning_never_removes_the_live_version_after_a_rollback(): void
    {
        config(['tenant_themes.keep_deployments' => 2]);
        $company = $this->company();
        $company->save();
        $this->deployer()->deployFromZip($company, ThemeZip::make(['theme.css' => 'a']), ['source' => 'upload']);
        $older = $this->deployer()->deployFromZip($company->fresh(), ThemeZip::make(['theme.css' => 'b']), ['source' => 'upload']);
        $newest = $this->deployer()->deployFromZip($company->fresh(), ThemeZip::make(['theme.css' => 'c']), ['source' => 'upload']);

        // Live is now an older version than the newest one on disk.
        $this->deployer()->rollback($company->fresh(), $older, 'test');
        config(['tenant_themes.keep_deployments' => 1]);
        $this->deployer()->prune($company->fresh());

        $this->assertTrue($older->fresh()->isAvailable(), 'The live version must survive pruning.');
        $this->assertFalse($newest->fresh()->isAvailable(), 'Newer but not live, so it goes when only one is kept.');
    }

    public function test_wipe_removes_every_file_and_turns_the_theme_off(): void
    {
        $company = $this->company();
        $company->save();
        $this->deployer()->deployFromZip($company, ThemeZip::valid(), ['source' => 'upload']);

        $this->deployer()->wipe($company->fresh(), 'test');

        $this->assertDirectoryDoesNotExist($this->themePath((string) $company->id));
        $this->assertNull($company->fresh()->theme_deployment_id);
        $this->assertSame(0, ThemeDeployment::where('company_id', $company->id)->whereNotNull('directory')->count());
        TenantTheme::flush();
        $this->assertNull(TenantTheme::for($company->fresh()));
    }

    public function test_tenant_theme_view_helper(): void
    {
        $company = $this->company();
        $company->save();
        $this->deployer()->deployFromZip($company, ThemeZip::valid(), ['source' => 'upload']);
        $theme = TenantTheme::for($company->fresh());

        $this->assertNotNull($theme);
        $this->assertTrue($theme->wants('storefront', 'css'));
        $this->assertTrue($theme->wants('storefront', 'js'));
        $this->assertTrue($theme->wants('dashboard', 'css'));
        $this->assertFalse($theme->wants('dashboard', 'js'), 'No dashboard.js in the package.');
        $this->assertStringContainsString('/assets/logo.png', $theme->logoUrl());
        $this->assertSame(':root{--tenant-primary: #C8102E;--tenant-background: #101010;--tenant-font-heading: "Poppins", system-ui, sans-serif;}', $theme->cssVariables());
        $this->assertSame('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap', $theme->googleFontsUrl());
        // Heading font is set in the test theme, body font is not.
        $this->assertStringContainsString(':is(h1,h2,h3,h4,h5,h6,.alt-font){font-family:var(--tenant-font-heading)!important}', $theme->fontRules());
        $this->assertStringNotContainsString('--tenant-font-body', $theme->fontRules());
    }

    public function test_theme_is_hidden_when_switched_off(): void
    {
        $company = $this->company();
        $company->save();
        $this->deployer()->deployFromZip($company, ThemeZip::valid(), ['source' => 'upload']);

        $company = $company->fresh();
        $company->forceFill(['theme_enabled' => false])->save();
        TenantTheme::flush();

        $this->assertNull(TenantTheme::for($company->fresh()));
    }

    public function test_theme_json_can_switch_a_file_off(): void
    {
        $company = $this->company();
        $company->save();
        $this->deployer()->deployFromZip($company, ThemeZip::make([
            'theme.json' => '{"storefront": {"js": false}}',
            'theme.js'   => 'x',
            'theme.css'  => 'y',
        ]), ['source' => 'upload']);

        $theme = TenantTheme::for($company->fresh());
        $this->assertFalse($theme->wants('storefront', 'js'));
        $this->assertTrue($theme->wants('storefront', 'css'));
    }
}

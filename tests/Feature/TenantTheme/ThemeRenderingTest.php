<?php

namespace Tests\Feature\TenantTheme;

use App\Models\Company;
use App\Models\User;
use App\Services\TenantTheme\TenantTheme;
use App\Services\TenantTheme\ThemeDeployer;
use Illuminate\Support\Str;

/**
 * The theme must appear on its own tenant's storefront and dashboard, and
 * nowhere else: not on another tenant, not on the main site, not when it is
 * switched off, and its JavaScript never for a platform super admin.
 */
class ThemeRenderingTest extends ThemeTestCase
{
    private function deployed(?array $files = null): Company
    {
        $company = $this->company();
        $company->save();
        app(ThemeDeployer::class)->deployFromZip($company, $files ? ThemeZip::make($files) : ThemeZip::valid(), ['source' => 'upload']);
        TenantTheme::flush();
        return $company->fresh();
    }

    private function host(Company $company, string $path = '/'): string
    {
        return 'http://' . $company->subdomain . '.gotrips.test' . $path;
    }

    private function user(Company $company, array $attrs): User
    {
        $user = User::create(['name' => 'U', 'email' => 'u-' . Str::random(8) . '@t.test', 'password' => bcrypt('x')]);
        $user->forceFill(array_merge(['company_id' => $company->id], $attrs))->save();
        return $user;
    }

    public function test_storefront_loads_the_theme_on_its_own_subdomain(): void
    {
        $company = $this->deployed();
        $dir = $company->activeThemeDeployment->directory;

        $html = $this->get($this->host($company))->assertOk()->getContent();

        $this->assertStringContainsString('id="gt-tenant-theme-css"', $html);
        $this->assertStringContainsString($this->publicDir . '/' . $dir . '/theme.css', $html);
        $this->assertStringContainsString('id="gt-tenant-theme-js"', $html);
        $this->assertStringContainsString('--tenant-primary: #C8102E;', $html);
        $this->assertStringContainsString('fonts.googleapis.com/css2?family=Poppins:wght@400;700', $html);
        $this->assertStringContainsString($dir . '/assets/logo.png', $html, 'theme.json logo replaces the site logo.');

        // Theme CSS comes after the platform's last stylesheet so it wins.
        $this->assertGreaterThan(strpos($html, 'css/gt-theme.css'), strpos($html, 'id="gt-tenant-theme-css"'));
        $this->assertLessThan(strpos($html, '</head>'), strpos($html, 'id="gt-tenant-theme-css"'));
    }

    public function test_theme_never_leaks_to_another_tenant_or_the_main_site(): void
    {
        $themed = $this->deployed();
        $other = $this->company();
        $other->forceFill(['theme_enabled' => false])->save();

        $this->assertStringNotContainsString('gt-tenant-theme', $this->get($this->host($other))->getContent());
        $this->assertStringNotContainsString('gt-tenant-theme', $this->get('http://127.0.0.1/')->getContent());
        $this->assertStringNotContainsString($this->publicDir . '/' . $themed->id . '/', $this->get($this->host($other))->getContent());
    }

    public function test_switching_off_removes_the_theme_on_the_next_request(): void
    {
        $company = $this->deployed();
        $this->assertStringContainsString('gt-tenant-theme-css', $this->get($this->host($company))->getContent());

        $company->forceFill(['theme_enabled' => false])->save();
        TenantTheme::flush();

        $html = $this->get($this->host($company))->getContent();
        $this->assertStringNotContainsString('gt-tenant-theme', $html);
        $this->assertStringNotContainsString('/assets/logo.png', $html);
    }

    public function test_dashboard_loads_dashboard_files_for_the_tenant_manager(): void
    {
        $company = $this->deployed([
            'dashboard.css' => '.x{}',
            'dashboard.js'  => 'window.d=1',
            'theme.css'     => '.y{}',
        ]);
        $owner = $this->user($company, ['role' => 'company_owner']);

        $html = $this->actingAs($owner)->get($this->host($company, '/manager'))->assertOk()->getContent();

        $this->assertStringContainsString('/dashboard.css', $html);
        $this->assertStringContainsString('id="gt-tenant-theme-js"', $html);
        $this->assertStringContainsString('/dashboard.js', $html);
        $this->assertStringNotContainsString('/theme.css"', $html, 'Storefront CSS is not loaded in the dashboard.');
    }

    public function test_theme_javascript_never_runs_for_a_super_admin(): void
    {
        $company = $this->deployed(['dashboard.css' => '.x{}', 'dashboard.js' => 'steal()', 'theme.js' => 'steal()']);
        $admin = $this->user($company, ['is_super_admin' => true, 'company_id' => null]);

        $dash = $this->actingAs($admin)->get($this->host($company, '/manager'))->getContent();
        $this->assertStringContainsString('/dashboard.css', $dash, 'CSS still shows so the admin sees the real look.');
        $this->assertStringNotContainsString('gt-tenant-theme-js', $dash);

        $store = $this->actingAs($admin)->get($this->host($company))->getContent();
        $this->assertStringNotContainsString('gt-tenant-theme-js', $store);
    }

    public function test_theme_javascript_is_skipped_while_impersonating(): void
    {
        $company = $this->deployed(['theme.js' => 'x()', 'theme.css' => '.a{}']);

        $html = $this->withSession(['impersonating_from' => 1])->get($this->host($company))->getContent();

        $this->assertStringContainsString('gt-tenant-theme-css', $html);
        $this->assertStringNotContainsString('gt-tenant-theme-js', $html);
    }

    public function test_login_pages_never_load_theme_files(): void
    {
        $company = $this->deployed(['theme.js' => 'x()', 'dashboard.js' => 'y()']);

        foreach (['/login', '/manager/login', '/agent/login', '/password/reset'] as $path) {
            $html = $this->get($this->host($company, $path))->getContent();
            $this->assertStringNotContainsString('gt-tenant-theme-js', $html, "Theme JS must not run on {$path}, where passwords are typed.");
        }
    }

    public function test_page_hooks_are_exposed_for_tenant_css_and_js(): void
    {
        $company = $this->deployed(['theme.css' => '.a{}']);

        $html = $this->get($this->host($company))->getContent();

        $this->assertStringContainsString("d.setAttribute('data-gt-theme', \"{$company->slug}\")", $html);
        $this->assertStringContainsString("d.setAttribute('data-gt-surface', \"storefront\")", $html);
        $this->assertStringContainsString('window.GoTripsTheme', $html);
    }

    public function test_theme_whose_files_vanished_is_ignored_safely(): void
    {
        $company = $this->deployed();
        \Illuminate\Support\Facades\File::deleteDirectory($this->themePath((string) $company->id));
        TenantTheme::flush();

        $html = $this->get($this->host($company))->assertOk()->getContent();
        $this->assertStringNotContainsString('gt-tenant-theme', $html);
    }
}

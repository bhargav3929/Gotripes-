<?php

namespace Tests\Feature\TenantTheme;

use App\Models\Company;
use App\Services\TenantTheme\TenantTheme;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Shared setup: a throwaway public themes folder (removed afterwards) and a
 * helper for making tenant companies.
 */
abstract class ThemeTestCase extends TestCase
{
    use DatabaseTransactions;

    protected string $publicDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->publicDir = 'tenant-themes-test-' . Str::lower(Str::random(8));
        config([
            'tenant_themes.public_dir'   => $this->publicDir,
            'tenant_themes.github.api'   => 'https://api.github.test',
            'tenant_themes.github.token' => 'test-token',
        ]);
        TenantTheme::flush();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(public_path($this->publicDir));
        TenantTheme::flush();
        parent::tearDown();
    }

    protected function company(array $attrs = []): Company
    {
        $slug = 'thm' . Str::lower(Str::random(8));
        $company = Company::create(array_merge([
            'name'      => 'Theme Test ' . $slug,
            'slug'      => $slug,
            'subdomain' => $slug,
            'is_active' => true,
            'plan'      => 'pro',
            'subscription_ends_at' => now()->addYear(),
        ], $attrs));

        return $company->forceFill([
            'theme_enabled' => true,
            'theme_repo'    => 'acme/' . $slug . '-theme',
            'theme_branch'  => 'main',
        ]);
    }

    protected function themePath(string $relative = ''): string
    {
        return public_path($this->publicDir . ($relative !== '' ? '/' . $relative : ''));
    }
}

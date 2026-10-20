<?php

namespace App\Services\TenantTheme;

use App\Models\Company;
use App\Models\ThemeDeployment;

/**
 * The live developer theme of one company, as the page templates see it.
 *
 * TenantTheme::for($company) is null unless the super admin has the theme
 * switched on AND a deployment is live with its files on disk, so turning
 * the switch off removes every trace of the theme from the next page load.
 */
class TenantTheme
{
    /** @var array<int, self|null> per-request memo */
    private static array $memo = [];

    private function __construct(
        public readonly Company $company,
        public readonly ThemeDeployment $deployment,
    ) {
    }

    public static function for(?Company $company): ?self
    {
        if (!$company || !$company->theme_enabled || !$company->theme_deployment_id) {
            return null;
        }
        if (array_key_exists($company->id, self::$memo)) {
            return self::$memo[$company->id];
        }

        $deployment = $company->activeThemeDeployment;
        $theme = ($deployment && $deployment->company_id === $company->id && $deployment->isAvailable())
            ? new self($company, $deployment)
            : null;

        return self::$memo[$company->id] = $theme;
    }

    /** Forget memoised themes (tests, and after a deploy within one request). */
    public static function flush(): void
    {
        self::$memo = [];
    }

    public function manifest(): array
    {
        return $this->deployment->manifest ?? [];
    }

    public function has(string $path): bool
    {
        return $this->deployment->hasFile($path);
    }

    public function url(string $path): string
    {
        $segments = array_map('rawurlencode', explode('/', $path));
        return asset(config('tenant_themes.public_dir') . '/' . $this->deployment->directory . '/' . implode('/', $segments));
    }

    /**
     * Whether a surface ('storefront' or 'dashboard') should load its CSS or JS.
     * Present files load unless theme.json switches them off.
     */
    public function wants(string $surface, string $kind): bool
    {
        $file = ($surface === 'dashboard' ? 'dashboard' : 'theme') . '.' . $kind;
        return $this->has($file) && (($this->manifest()[$surface][$kind] ?? true) !== false);
    }

    public function fileFor(string $surface, string $kind): string
    {
        return ($surface === 'dashboard' ? 'dashboard' : 'theme') . '.' . $kind;
    }

    public function logoUrl(): ?string
    {
        $logo = $this->manifest()['logo'] ?? null;
        return $logo && $this->has($logo) ? $this->url($logo) : null;
    }

    public function faviconUrl(): ?string
    {
        $icon = $this->manifest()['favicon'] ?? null;
        return $icon && $this->has($icon) ? $this->url($icon) : null;
    }

    /**
     * :root custom properties from theme.json. Values were validated to hex
     * colours and plain family names on deploy; they are checked again here
     * because this string is printed raw inside <style>.
     */
    public function cssVariables(): string
    {
        $m = $this->manifest();
        $vars = [];
        foreach (($m['colors'] ?? []) as $name => $hex) {
            if (in_array($name, ThemeManifest::COLORS, true) && preg_match('/^#[0-9a-fA-F]{3,8}$/', (string) $hex)) {
                $vars[] = "--tenant-{$name}: {$hex};";
            }
        }
        foreach (['heading', 'body'] as $role) {
            $family = $m['fonts'][$role] ?? null;
            if (is_string($family) && preg_match('/^[A-Za-z0-9 \-]{1,60}$/', $family)) {
                $vars[] = "--tenant-font-{$role}: \"{$family}\", system-ui, sans-serif;";
            }
        }
        return $vars ? ':root{' . implode('', $vars) . '}' : '';
    }

    /**
     * theme.json "fonts.body" / "fonts.heading" are applied site-wide. The
     * platform pins its own fonts with !important, so these rules need
     * !important plus the html[data-gt-theme] prefix to take over.
     */
    public function fontRules(): string
    {
        $fonts = $this->manifest()['fonts'] ?? [];
        $rules = '';
        if (is_string($fonts['body'] ?? null) && preg_match('/^[A-Za-z0-9 \-]{1,60}$/', $fonts['body'])) {
            $rules .= 'html[data-gt-theme] body{font-family:var(--tenant-font-body)!important}';
        }
        if (is_string($fonts['heading'] ?? null) && preg_match('/^[A-Za-z0-9 \-]{1,60}$/', $fonts['heading'])) {
            $rules .= 'html[data-gt-theme] :is(h1,h2,h3,h4,h5,h6,.alt-font){font-family:var(--tenant-font-heading)!important}';
        }
        return $rules;
    }

    public function googleFontsUrl(): ?string
    {
        $families = $this->manifest()['fonts']['google'] ?? [];
        $families = array_values(array_filter($families, fn ($f) => is_string($f)
            && preg_match('/^[A-Za-z0-9 ]+(?::[A-Za-z0-9@;,.]+)?$/', $f)));
        if (!$families) {
            return null;
        }
        $query = implode('&', array_map(fn ($f) => 'family=' . str_replace(' ', '+', $f), $families));
        return 'https://fonts.googleapis.com/css2?' . $query . '&display=swap';
    }

    /**
     * Theme JavaScript is skipped while a platform super admin is looking at
     * the tenant (directly or by impersonating), so a tenant's script never
     * runs with platform-level privileges.
     */
    public static function scriptsAllowedForViewer(): bool
    {
        if (session()->has('impersonating_from')) {
            return false;
        }
        // Same test as SuperAdminMiddleware.
        $user = auth()->user();
        return !($user && (($user->is_super_admin ?? false) || $user->role === 'super_admin'));
    }
}

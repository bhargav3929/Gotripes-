<?php

namespace Tests\Feature\TenantTheme;

use App\Services\TenantTheme\ThemeManifest;
use App\Services\TenantTheme\ThemePackage;
use App\Services\TenantTheme\ThemePackageException;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * The package checks are what keep a tenant's repository from putting
 * anything executable, or anything outside its own folder, on the server.
 */
class ThemePackageTest extends TestCase
{
    private string $staging;

    protected function setUp(): void
    {
        parent::setUp();
        $this->staging = sys_get_temp_dir() . '/theme-test-' . uniqid();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->staging);
        parent::tearDown();
    }

    private function refused(string $zip, string $expected): void
    {
        try {
            ThemePackage::fromZip($zip, $this->staging);
            $this->fail('The theme should have been refused.');
        } catch (ThemePackageException $e) {
            $this->assertStringContainsString($expected, $e->getMessage());
            $this->assertDirectoryDoesNotExist($this->staging, 'A refused theme must leave nothing behind.');
        }
    }

    public function test_valid_github_zipball_is_unpacked_without_its_wrapper_folder(): void
    {
        $package = ThemePackage::fromZip(ThemeZip::valid('acme-theme-abc123/'), $this->staging);

        $this->assertSame(['assets/logo.png', 'dashboard.css', 'theme.css', 'theme.js', 'theme.json'], $package->files);
        $this->assertFileExists($this->staging . '/theme.css');
        $this->assertFileExists($this->staging . '/assets/logo.png');
        $this->assertSame('#C8102E', $package->manifest['colors']['primary']);
        $this->assertGreaterThan(0, $package->totalBytes);
    }

    public function test_repository_clutter_is_skipped_not_published(): void
    {
        $package = ThemePackage::fromZip(ThemeZip::valid('', [
            '.htaccess' => 'php_flag engine on',
            '.env'      => 'SECRET=1',
            'src/app.php' => '<?php echo 1;',
            'docs/notes.txt' => 'x',
        ]), $this->staging);

        $this->assertContains('README.md', $package->skipped);
        $this->assertContains('.htaccess', $package->skipped);
        $this->assertContains('.env', $package->skipped);
        $this->assertContains('src/app.php', $package->skipped);
        $this->assertFileDoesNotExist($this->staging . '/.htaccess');
        $this->assertFileDoesNotExist($this->staging . '/src/app.php');
    }

    public function test_server_side_files_are_refused(): void
    {
        $this->refused(ThemeZip::valid('', ['assets/shell.php' => '<?php system($_GET[1]);']), 'server-side');
        $this->refused(ThemeZip::valid('', ['assets/logo.php.png' => 'x']), 'server-side');
        $this->refused(ThemeZip::valid('', ['assets/x.phtml' => 'x']), 'server-side');
        $this->refused(ThemeZip::valid('', ['assets/run.sh' => 'x']), 'server-side');
    }

    public function test_path_traversal_is_refused(): void
    {
        $this->refused(ThemeZip::make(['theme.css' => 'a', '../evil.css' => 'x']), 'invalid path');
        $this->refused(ThemeZip::make(['theme.css' => 'a', 'assets/../../evil.css' => 'x']), 'invalid path');
        $this->refused(ThemeZip::make(['theme.css' => 'a', '/etc/evil.css' => 'x']), 'invalid path');
    }

    public function test_symlinks_are_refused(): void
    {
        $this->refused(ThemeZip::make(['theme.css' => 'a', 'assets/link.css' => '/etc/passwd'], '', ['assets/link.css']), 'symbolic link');
    }

    public function test_unknown_top_level_files_and_folders_are_refused(): void
    {
        $this->refused(ThemeZip::make(['theme.css' => 'a', 'index.html' => '<h1>x</h1>']), 'not allowed at the top');
        $this->refused(ThemeZip::make(['theme.css' => 'a', 'views/home.css' => 'x']), 'only the assets/ folder');
        $this->refused(ThemeZip::make(['theme.css' => 'a', 'assets/page.html' => 'x']), 'assets may be');
    }

    public function test_odd_file_names_are_refused(): void
    {
        $this->refused(ThemeZip::make(['theme.css' => 'a', 'assets/my logo.png' => 'x']), 'letters, numbers');
    }

    public function test_size_limits_are_enforced(): void
    {
        config(['tenant_themes.limits.max_file_bytes' => 1000]);
        $this->refused(ThemeZip::make(['theme.css' => str_repeat('a', 2000)]), 'larger than');

        config(['tenant_themes.limits.max_file_bytes' => 10000, 'tenant_themes.limits.max_total_bytes' => 3000]);
        $this->refused(ThemeZip::make(['theme.css' => str_repeat('a', 2000), 'theme.js' => str_repeat('b', 2000)]), 'in total');

        config(['tenant_themes.limits.max_files' => 2]);
        $this->refused(ThemeZip::make(['theme.css' => 'a', 'theme.js' => 'b', 'dashboard.css' => 'c']), 'the limit is 2');
    }

    public function test_empty_theme_is_refused(): void
    {
        $this->refused(ThemeZip::make(['README.md' => 'hi']), 'no files to publish');
    }

    public function test_not_a_zip_is_refused(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'nz');
        file_put_contents($path, 'not a zip');
        $this->refused($path, 'not a valid zip');
    }

    public function test_bad_theme_json_is_refused_with_a_useful_message(): void
    {
        $this->refused(ThemeZip::make(['theme.json' => '{bad']), 'not valid JSON');
        $this->refused(ThemeZip::make(['theme.json' => '{"colours": {}}']), 'unknown key(s): colours');
        $this->refused(ThemeZip::make(['theme.json' => '{"colors": {"primary": "red; background:url(x)"}}']), 'hex colour');
        $this->refused(ThemeZip::make(['theme.json' => '{"logo": "assets/missing.png"}']), 'not in the theme');
        $this->refused(ThemeZip::make(['theme.json' => '{"logo": "https://evil.test/x.png"}']), 'inside assets/');
        $this->refused(ThemeZip::make(['theme.json' => '{"fonts": {"google": ["Poppins&family=x\"><script>"]}}']), 'Google Fonts family');
        $this->refused(ThemeZip::make(['theme.json' => '{"fonts": {"body": "Arial;}body{x"}}']), 'font family name');
        $this->refused(ThemeZip::make(['theme.json' => '{"storefront": {"js": "yes"}}']), 'true or false');
    }

    public function test_manifest_accepts_every_documented_key(): void
    {
        $manifest = ThemeManifest::parse(json_encode([
            'name' => 'Full', 'version' => '1.2.0',
            'logo' => 'assets/l.svg', 'favicon' => 'assets/f.ico',
            'colors' => ['primary' => '#fff', 'secondary' => '#000000', 'accent' => '#12345678',
                'text' => '#abc', 'background' => '#abcd', 'surface' => '#111111', 'border' => '#222'],
            'fonts' => ['google' => ['Inter', 'Playfair Display:ital,wght@0,400;1,700'], 'heading' => 'Playfair Display', 'body' => 'Inter'],
            'storefront' => ['css' => true, 'js' => false],
            'dashboard' => ['css' => false, 'js' => true],
        ]), ['assets/l.svg', 'assets/f.ico']);

        $this->assertSame('Full', $manifest['name']);
        $this->assertFalse($manifest['storefront']['js']);
        $this->assertSame('Inter', $manifest['fonts']['body']);
    }

    public function test_the_starter_repository_given_to_tenants_is_a_valid_theme(): void
    {
        $root = base_path('docs/tenant-theme-starter');
        $files = [];
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)) as $file) {
            $files[substr($file->getPathname(), strlen($root) + 1)] = file_get_contents($file->getPathname());
        }

        $package = ThemePackage::fromZip(ThemeZip::make($files, 'gotrips-themes-starter-0a1b2c3/'), $this->staging);

        $this->assertSame(['assets/README.txt', 'dashboard.css', 'dashboard.js', 'theme.css', 'theme.js', 'theme.json'], $package->files);
        $this->assertContains('README.md', $package->skipped);
        $this->assertSame('Bricolage Grotesque', $package->manifest['fonts']['heading']);
    }
}

<?php

namespace App\Services\TenantTheme;

use Illuminate\Support\Facades\File;
use ZipArchive;

/**
 * Unpacks a theme zip (a GitHub zipball or an uploaded zip) into a staging
 * folder, publishing only files that are safe to serve as static content.
 *
 * Every entry is checked before anything is written:
 *   - paths stay inside the theme (no "..", absolute paths, backslashes)
 *   - no symlinks
 *   - only the known top-level files and an assets/ folder of allowed types
 *   - nothing that a web server could execute (.php, .phtml, .htaccess ...)
 *   - file count and size limits, enforced on the bytes actually written
 * Repository clutter (README, .github/, dotfiles) is skipped, not published.
 */
class ThemePackage
{
    /** Name fragments a web server might execute or obey. Refused anywhere in a path. */
    private const DANGEROUS = [
        '.php', '.phtml', '.phar', '.pht', '.phps', '.cgi', '.pl', '.py', '.sh', '.asp', '.aspx',
        '.jsp', '.shtml', '.htaccess', '.htpasswd', '.user.ini', 'web.config',
    ];

    /** @var string[] published files, relative paths */
    public array $files = [];

    /** @var string[] repository files that were deliberately not published */
    public array $skipped = [];

    public array $manifest = [];

    public int $totalBytes = 0;

    private function __construct(public readonly string $directory)
    {
    }

    /**
     * @throws ThemePackageException
     */
    public static function fromZip(string $zipPath, string $stagingDir): self
    {
        $limits = config('tenant_themes.limits');

        if (!is_file($zipPath)) {
            throw new ThemePackageException('The theme archive could not be read.');
        }
        if (filesize($zipPath) > $limits['max_zip_bytes']) {
            throw new ThemePackageException('The theme archive is larger than ' . self::mb($limits['max_zip_bytes']) . '.');
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new ThemePackageException('The theme archive is not a valid zip file.');
        }

        try {
            $entries = self::readEntries($zip);
            $prefix = self::commonPrefix(array_column($entries, 'name'));

            $package = new self($stagingDir);
            File::deleteDirectory($stagingDir);
            File::ensureDirectoryExists($stagingDir, 0755);

            $plan = [];
            foreach ($entries as $entry) {
                $path = substr($entry['name'], strlen($prefix));
                if ($path === '' || str_ends_with($entry['name'], '/')) {
                    continue; // directory entry
                }
                if ($entry['symlink']) {
                    throw new ThemePackageException("{$path} is a symbolic link. Themes may only contain regular files.");
                }

                $verdict = self::classify($path);
                if ($verdict === 'skip') {
                    $package->skipped[] = $path;
                    continue;
                }
                $plan[] = ['index' => $entry['index'], 'path' => $path, 'size' => $entry['size']];
            }

            if (!$plan) {
                throw new ThemePackageException('The theme has no files to publish. Add at least one of: '
                    . implode(', ', config('tenant_themes.root_files')) . '.');
            }
            if (count($plan) > $limits['max_files']) {
                throw new ThemePackageException('The theme has ' . count($plan) . ' files; the limit is ' . $limits['max_files'] . '.');
            }

            foreach ($plan as $item) {
                if ($item['size'] > $limits['max_file_bytes']) {
                    throw new ThemePackageException("{$item['path']} is larger than " . self::mb($limits['max_file_bytes']) . '.');
                }
                $written = self::extract($zip, $item['index'], $stagingDir . '/' . $item['path'], $limits['max_file_bytes']);
                $package->totalBytes += $written;
                if ($package->totalBytes > $limits['max_total_bytes']) {
                    throw new ThemePackageException('The theme is larger than ' . self::mb($limits['max_total_bytes']) . ' in total.');
                }
                $package->files[] = $item['path'];
            }
            sort($package->files);

            if (in_array('theme.json', $package->files, true)) {
                $package->manifest = ThemeManifest::parse(
                    (string) file_get_contents($stagingDir . '/theme.json'),
                    $package->files
                );
            }

            return $package;
        } catch (ThemePackageException $e) {
            File::deleteDirectory($stagingDir);
            throw $e;
        } finally {
            $zip->close();
        }
    }

    /**
     * Decide what happens to one path: 'publish', 'skip', or refuse (throws).
     */
    public static function classify(string $path): string
    {
        if (str_contains($path, "\0") || str_contains($path, '\\') || str_starts_with($path, '/')
            || preg_match('/^[A-Za-z]:/', $path)) {
            throw new ThemePackageException("{$path} has an invalid path.");
        }

        $segments = explode('/', $path);
        foreach ($segments as $segment) {
            if ($segment === '' || $segment === '.' || $segment === '..') {
                throw new ThemePackageException("{$path} has an invalid path.");
            }
        }

        // Clutter that is fine in a repository but never published.
        $first = $segments[0];
        $base = end($segments);
        if (in_array($first, config('tenant_themes.ignored_dirs'), true)) {
            return 'skip';
        }
        foreach ($segments as $segment) {
            if (str_starts_with($segment, '.')) {
                return 'skip'; // dotfiles and dot-folders (.github, .htaccess, .env ...)
            }
        }
        if (count($segments) === 1 && in_array($base, config('tenant_themes.ignored'), true)) {
            return 'skip';
        }
        if (strtolower(pathinfo($base, PATHINFO_EXTENSION)) === 'md') {
            return 'skip';
        }

        $lower = strtolower($path);
        foreach (self::DANGEROUS as $needle) {
            if (str_contains($lower, $needle)) {
                throw new ThemePackageException("{$path} is not allowed: themes cannot contain server-side files.");
            }
        }

        foreach ($segments as $segment) {
            if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9._\-]*$/', $segment)) {
                throw new ThemePackageException("{$path}: file and folder names may only use letters, numbers, dots, dashes and underscores.");
            }
        }

        if (count($segments) === 1) {
            if (in_array($base, config('tenant_themes.root_files'), true)) {
                return 'publish';
            }
            throw new ThemePackageException("{$path} is not allowed at the top of the theme. Allowed there: "
                . implode(', ', config('tenant_themes.root_files')) . '. Put images, fonts and other files in assets/.');
        }

        if ($first !== 'assets') {
            throw new ThemePackageException("{$path} is not allowed: only the assets/ folder may contain files below the top level.");
        }

        $ext = strtolower(pathinfo($base, PATHINFO_EXTENSION));
        if (!in_array($ext, config('tenant_themes.asset_extensions'), true)) {
            throw new ThemePackageException("{$path} is not allowed: assets may be "
                . implode(', ', config('tenant_themes.asset_extensions')) . '.');
        }

        return 'publish';
    }

    private static function readEntries(ZipArchive $zip): array
    {
        $entries = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if ($stat === false) {
                throw new ThemePackageException('The theme archive is damaged.');
            }
            $symlink = false;
            if ($zip->getExternalAttributesIndex($i, $opsys, $attr) && $opsys === ZipArchive::OPSYS_UNIX) {
                $symlink = ((($attr >> 16) & 0170000) === 0120000);
            }
            $entries[] = ['index' => $i, 'name' => $stat['name'], 'size' => (int) $stat['size'], 'symlink' => $symlink];
        }
        return $entries;
    }

    /**
     * GitHub zipballs wrap everything in "owner-repo-sha/"; zips made by hand
     * often wrap it in the folder name. Strip a single folder shared by all.
     */
    private static function commonPrefix(array $names): string
    {
        if (!$names) {
            return '';
        }
        $first = explode('/', $names[0], 2)[0] . '/';
        foreach ($names as $name) {
            if (!str_starts_with($name, $first)) {
                return '';
            }
        }
        // A lone root file such as "theme.css" shares no folder.
        return in_array(rtrim($first, '/'), config('tenant_themes.root_files'), true) ? '' : $first;
    }

    /**
     * Copy one entry to disk, stopping if it turns out bigger than its header
     * claimed (a crafted zip can lie about sizes).
     */
    private static function extract(ZipArchive $zip, int $index, string $target, int $maxBytes): int
    {
        File::ensureDirectoryExists(dirname($target), 0755);
        $in = $zip->getStream($zip->getNameIndex($index));
        if ($in === false) {
            throw new ThemePackageException('A file in the theme archive could not be read.');
        }
        $out = fopen($target, 'wb');
        $written = 0;
        try {
            while (!feof($in)) {
                $chunk = fread($in, 65536);
                if ($chunk === false) {
                    throw new ThemePackageException('A file in the theme archive could not be read.');
                }
                $written += strlen($chunk);
                if ($written > $maxBytes) {
                    throw new ThemePackageException(basename($target) . ' is larger than ' . self::mb($maxBytes) . '.');
                }
                fwrite($out, $chunk);
            }
        } finally {
            fclose($in);
            fclose($out);
        }
        return $written;
    }

    private static function mb(int $bytes): string
    {
        return round($bytes / 1048576) . ' MB';
    }
}

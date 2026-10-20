<?php

namespace Tests\Feature\TenantTheme;

use ZipArchive;

/**
 * Builds theme zip files for tests.
 */
class ThemeZip
{
    /**
     * @param  array<string,string>  $files    path => contents
     * @param  string                $prefix   folder every entry is wrapped in (GitHub style)
     * @param  string[]              $symlinks paths to mark as symbolic links
     */
    public static function make(array $files, string $prefix = '', array $symlinks = []): string
    {
        $path = tempnam(sys_get_temp_dir(), 'theme') . '.zip';
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        foreach ($files as $name => $contents) {
            $zip->addFromString($prefix . $name, $contents);
            if (in_array($name, $symlinks, true)) {
                $zip->setExternalAttributesName($prefix . $name, ZipArchive::OPSYS_UNIX, (0120777 << 16));
            }
        }
        $zip->close();
        return $path;
    }

    /** A small, valid theme with every kind of file. */
    public static function valid(string $prefix = '', array $extra = []): string
    {
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==');
        return self::make(array_merge([
            'theme.json'      => json_encode([
                'name'   => 'Savanna Test',
                'logo'   => 'assets/logo.png',
                'colors' => ['primary' => '#C8102E', 'background' => '#101010'],
                'fonts'  => ['google' => ['Poppins:wght@400;700'], 'heading' => 'Poppins'],
            ]),
            'theme.css'       => 'body{--x:1}',
            'theme.js'        => 'window.__themeLoaded = true;',
            'dashboard.css'   => '.sidebar{--y:1}',
            'assets/logo.png' => $png,
            'README.md'       => '# readme',
            '.github/workflows/ci.yml' => 'on: push',
        ], $extra), $prefix);
    }
}

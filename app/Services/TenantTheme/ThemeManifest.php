<?php

namespace App\Services\TenantTheme;

/**
 * Checks a theme's theme.json. Every key is optional, but unknown keys and
 * wrong types are refused rather than ignored, so a typo shows up in the
 * deploy history instead of silently doing nothing on the live site.
 *
 * Values that reach the page (colours, font names, file paths) are limited
 * to safe character sets here, and escaped again when rendered.
 */
class ThemeManifest
{
    public const KEYS = ['name', 'version', 'logo', 'favicon', 'colors', 'fonts', 'storefront', 'dashboard'];
    public const COLORS = ['primary', 'secondary', 'accent', 'text', 'background', 'surface', 'border'];
    public const IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'gif', 'webp', 'avif', 'svg', 'ico'];
    public const MAX_BYTES = 65536;

    /**
     * @param  string  $json   raw theme.json contents
     * @param  array   $files  every file the theme publishes (relative paths)
     * @return array           the manifest, normalised
     *
     * @throws ThemePackageException
     */
    public static function parse(string $json, array $files): array
    {
        if (strlen($json) > self::MAX_BYTES) {
            throw new ThemePackageException('theme.json is larger than 64 KB.');
        }

        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ThemePackageException('theme.json is not valid JSON: ' . json_last_error_msg() . '.');
        }
        if (!is_array($data) || ($data !== [] && array_is_list($data))) {
            throw new ThemePackageException('theme.json must be a JSON object, like {"name": "My theme"}.');
        }

        $unknown = array_diff(array_keys($data), self::KEYS);
        if ($unknown) {
            throw new ThemePackageException('theme.json has unknown key(s): ' . implode(', ', $unknown)
                . '. Allowed keys: ' . implode(', ', self::KEYS) . '.');
        }

        $out = [];

        foreach (['name' => 80, 'version' => 20] as $key => $max) {
            if (array_key_exists($key, $data)) {
                if (!is_string($data[$key]) || mb_strlen($data[$key]) > $max) {
                    throw new ThemePackageException("theme.json \"{$key}\" must be text of at most {$max} characters.");
                }
                $out[$key] = $data[$key];
            }
        }

        foreach (['logo', 'favicon'] as $key) {
            if (array_key_exists($key, $data)) {
                $out[$key] = self::imagePath($key, $data[$key], $files);
            }
        }

        if (array_key_exists('colors', $data)) {
            $out['colors'] = self::colors($data['colors']);
        }

        if (array_key_exists('fonts', $data)) {
            $out['fonts'] = self::fonts($data['fonts']);
        }

        foreach (['storefront', 'dashboard'] as $surface) {
            if (array_key_exists($surface, $data)) {
                $out[$surface] = self::surface($surface, $data[$surface]);
            }
        }

        return $out;
    }

    private static function imagePath(string $key, $value, array $files): string
    {
        if (!is_string($value) || !str_starts_with($value, 'assets/')) {
            throw new ThemePackageException("theme.json \"{$key}\" must be a path inside assets/, like \"assets/{$key}.png\".");
        }
        $ext = strtolower(pathinfo($value, PATHINFO_EXTENSION));
        if (!in_array($ext, self::IMAGE_EXTENSIONS, true)) {
            throw new ThemePackageException("theme.json \"{$key}\" must be an image (" . implode(', ', self::IMAGE_EXTENSIONS) . ').');
        }
        if (!in_array($value, $files, true)) {
            throw new ThemePackageException("theme.json \"{$key}\" points to {$value}, but that file is not in the theme.");
        }
        return $value;
    }

    private static function colors($value): array
    {
        if (!is_array($value) || ($value !== [] && array_is_list($value))) {
            throw new ThemePackageException('theme.json "colors" must be an object, like {"primary": "#C8102E"}.');
        }
        $unknown = array_diff(array_keys($value), self::COLORS);
        if ($unknown) {
            throw new ThemePackageException('theme.json "colors" has unknown name(s): ' . implode(', ', $unknown)
                . '. Allowed: ' . implode(', ', self::COLORS) . '.');
        }
        foreach ($value as $name => $hex) {
            if (!is_string($hex) || !preg_match('/^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $hex)) {
                throw new ThemePackageException("theme.json colour \"{$name}\" must be a hex colour like #C8102E.");
            }
        }
        return $value;
    }

    private static function fonts($value): array
    {
        if (!is_array($value) || ($value !== [] && array_is_list($value))) {
            throw new ThemePackageException('theme.json "fonts" must be an object, like {"google": ["Poppins:wght@400;700"], "body": "Poppins"}.');
        }
        $unknown = array_diff(array_keys($value), ['google', 'heading', 'body']);
        if ($unknown) {
            throw new ThemePackageException('theme.json "fonts" has unknown key(s): ' . implode(', ', $unknown) . '. Allowed: google, heading, body.');
        }

        $out = [];
        if (array_key_exists('google', $value)) {
            $list = $value['google'];
            if (!is_array($list) || !array_is_list($list) || count($list) > 4) {
                throw new ThemePackageException('theme.json "fonts.google" must be a list of up to 4 Google Fonts, like ["Poppins:wght@400;700"].');
            }
            foreach ($list as $family) {
                if (!is_string($family) || strlen($family) > 100
                    || !preg_match('/^[A-Za-z0-9 ]+(?::[A-Za-z0-9@;,.]+)?$/', $family)) {
                    throw new ThemePackageException('theme.json "fonts.google" entry "' . (is_string($family) ? $family : gettype($family))
                        . '" is not a Google Fonts family, like "Poppins" or "Poppins:wght@400;700".');
                }
            }
            $out['google'] = $list;
        }
        foreach (['heading', 'body'] as $role) {
            if (array_key_exists($role, $value)) {
                $family = $value[$role];
                if (!is_string($family) || !preg_match('/^[A-Za-z0-9 \-]{1,60}$/', $family)) {
                    throw new ThemePackageException("theme.json \"fonts.{$role}\" must be a font family name, like \"Poppins\".");
                }
                $out[$role] = $family;
            }
        }
        return $out;
    }

    private static function surface(string $surface, $value): array
    {
        if (!is_array($value) || ($value !== [] && array_is_list($value))) {
            throw new ThemePackageException("theme.json \"{$surface}\" must be an object, like {\"css\": true, \"js\": true}.");
        }
        $unknown = array_diff(array_keys($value), ['css', 'js']);
        if ($unknown) {
            throw new ThemePackageException("theme.json \"{$surface}\" has unknown key(s): " . implode(', ', $unknown) . '. Allowed: css, js.');
        }
        foreach ($value as $k => $v) {
            if (!is_bool($v)) {
                throw new ThemePackageException("theme.json \"{$surface}.{$k}\" must be true or false.");
            }
        }
        return $value;
    }
}

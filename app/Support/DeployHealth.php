<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Is the live code exactly what is on GitHub's main branch?
 *
 * Reads storage/app/deploy-status.json (written by deploy.sh on every deploy
 * and check) and, every few minutes, asks git whether any tracked file has
 * been edited on the server since. The Super Admin dashboard shows a red
 * warning when either says something is wrong, so a failed deploy or a file
 * uploaded straight to the server is seen within minutes, not months.
 */
class DeployHealth
{
    public const STATUS_FILE = 'app/deploy-status.json';

    /**
     * @return array{state:string,message:string,files:array,commit:?string,at:?string}|null
     *         null when everything is fine or nothing has been recorded yet
     */
    public static function problem(): ?array
    {
        $status = self::status();

        $edited = Cache::remember('deploy-health:edited-files', 300, fn () => self::editedFiles());
        if ($edited) {
            return [
                'state'   => 'drift',
                'message' => count($edited) . ' file(s) on the live server were changed outside git. The next deploy will refuse to run until they are committed.',
                'files'   => array_slice($edited, 0, 20),
                'commit'  => $status['commit'] ?? null,
                'at'      => $status['at'] ?? null,
            ];
        }

        if ($status && in_array($status['state'] ?? '', ['failed', 'drift', 'behind'], true)) {
            return [
                'state'   => $status['state'],
                'message' => (string) ($status['message'] ?? 'The last deploy did not finish.'),
                'files'   => array_slice((array) ($status['files'] ?? []), 0, 20),
                'commit'  => $status['commit'] ?? null,
                'at'      => $status['at'] ?? null,
            ];
        }

        return null;
    }

    public static function status(): ?array
    {
        $file = storage_path(self::STATUS_FILE);
        if (!is_file($file)) {
            return null;
        }
        $data = json_decode((string) file_get_contents($file), true);
        return is_array($data) ? $data : null;
    }

    /**
     * Tracked files that differ from the deployed commit. Only meaningful on a
     * server deployed from git; returns [] when git is unavailable.
     */
    public static function editedFiles(): array
    {
        // Only where deploy.sh runs (it writes the status file). Production
        // currently has APP_ENV=local, so the environment name can't be used.
        if (!is_file(storage_path(self::STATUS_FILE)) || !function_exists('shell_exec') || !is_dir(base_path('.git'))) {
            return [];
        }
        $out = @shell_exec('cd ' . escapeshellarg(base_path()) . ' && git status --porcelain --untracked-files=no 2>/dev/null');
        if (!is_string($out) || trim($out) === '') {
            return [];
        }
        return array_values(array_filter(array_map(fn ($l) => trim(substr($l, 3)), explode("\n", trim($out)))));
    }
}

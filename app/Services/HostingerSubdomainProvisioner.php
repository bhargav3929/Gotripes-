<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Provisions a subdomain on Hostinger:
 *   1. Calls Hostinger Hosting API to create the addon website
 *   2. Replaces the new public_html with a symlink to the main Laravel public_html
 *
 * Required env vars:
 *   HOSTINGER_API_TOKEN — token with hosting:write scope
 *   HOSTINGER_ORDER_ID  — the order ID under which to create the website (e.g. 1008484143)
 *   APP_DOMAIN          — root domain (default: gotrips.ai)
 *
 * Optional:
 *   HOSTINGER_DOMAINS_PATH — absolute path to ~/domains on the server
 *                            (default: /home/u705168859/domains)
 */
class HostingerSubdomainProvisioner
{
    public function provision(Company $company): array
    {
        $subdomain = $company->subdomain;
        if (!$subdomain) {
            return ['ok' => false, 'step' => 'precheck', 'message' => 'Company has no subdomain set.'];
        }

        $rootDomain = config('app.domain', env('APP_DOMAIN', 'gotrips.ai'));
        $fullDomain = $subdomain . '.' . $rootDomain;
        $token      = env('HOSTINGER_API_TOKEN');
        $orderId    = (int) env('HOSTINGER_ORDER_ID', 0);

        if (!$token) {
            return ['ok' => false, 'step' => 'config', 'message' => 'HOSTINGER_API_TOKEN missing in .env'];
        }
        if (!$orderId) {
            return ['ok' => false, 'step' => 'config', 'message' => 'HOSTINGER_ORDER_ID missing in .env'];
        }

        // 1. Check if already exists
        $list = Http::withToken($token)
            ->acceptJson()
            ->timeout(20)
            ->get('https://developers.hostinger.com/api/hosting/v1/websites', ['domain' => $fullDomain]);

        $alreadyExists = false;
        if ($list->ok()) {
            $items = $list->json('data', []);
            foreach ($items as $item) {
                if (($item['domain'] ?? '') === $fullDomain) {
                    $alreadyExists = true;
                    break;
                }
            }
        }

        if (!$alreadyExists) {
            $resp = Http::withToken($token)
                ->acceptJson()
                ->timeout(45)
                ->post('https://developers.hostinger.com/api/hosting/v1/websites', [
                    'domain'   => $fullDomain,
                    'order_id' => $orderId,
                ]);

            if (!$resp->successful()) {
                Log::warning('Hostinger subdomain create failed', [
                    'subdomain' => $fullDomain,
                    'status'    => $resp->status(),
                    'body'      => $resp->body(),
                ]);
                return [
                    'ok'      => false,
                    'step'    => 'hostinger_api',
                    'message' => 'Hostinger API rejected: ' . $resp->status() . ' — ' . substr($resp->body(), 0, 240),
                ];
            }
        }

        // 2. Symlink the new public_html → main Laravel public_html.
        // Hostinger usually creates the folder within ~30s. Poll briefly.
        $domainsPath  = env('HOSTINGER_DOMAINS_PATH', '/home/u705168859/domains');
        $newSitePath  = $domainsPath . '/' . $fullDomain;
        $newPublic    = $newSitePath . '/public_html';
        $mainPublic   = $domainsPath . '/' . $rootDomain . '/public_html';

        $deadline = time() + 90;
        while (!is_dir($newSitePath) && time() < $deadline) {
            usleep(2_000_000); // 2s
        }

        if (!is_dir($newSitePath)) {
            return [
                'ok'      => true, // Hostinger accepted the request — site folder still creating
                'step'    => 'symlink_pending',
                'message' => "Hostinger accepted the request. Site folder for {$fullDomain} not yet present after 90s — symlink can be applied later.",
            ];
        }

        if (!is_link($newPublic)) {
            // Remove existing public_html (Hostinger creates an empty one with parking page).
            if (is_dir($newPublic)) {
                $this->removeDir($newPublic);
            }
            @symlink($mainPublic, $newPublic);
        }

        if (!is_link($newPublic)) {
            return [
                'ok'      => false,
                'step'    => 'symlink',
                'message' => "Could not create symlink {$newPublic} → {$mainPublic}. Run manually via SSH.",
            ];
        }

        return [
            'ok'      => true,
            'step'    => 'done',
            'message' => "Subdomain {$fullDomain} provisioned and linked to main Laravel app.",
        ];
    }

    private function removeDir(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }
        $items = scandir($path) ?: [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $full = $path . '/' . $item;
            if (is_dir($full) && !is_link($full)) {
                $this->removeDir($full);
            } else {
                @unlink($full);
            }
        }
        @rmdir($path);
    }
}

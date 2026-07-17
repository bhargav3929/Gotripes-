<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Services\MontyEsimService;
use App\Services\FluxirService;

class TestApis extends Command
{
    protected $signature = 'app:test-apis {--clear-tokens : Forget cached OAuth tokens before testing}';

    protected $description = 'Verify environment variables and test live authentication/APIs for MontyeSIM and Fluxir';

    public function handle(): int
    {
        $this->info("=========================================");
        $this->info("      GoTrips API Credentials Test");
        $this->info("=========================================");

        // Clear tokens if requested
        if ($this->option('clear-tokens')) {
            $this->warn("Clearing cached OAuth tokens...");
            Cache::forget('montyesim_access_token');
            Cache::forget('montyesim_refresh_token');
            Cache::forget('fluxir_access_token');
            $this->info("Cache cleared.");
        }

        $this->line("");
        $this->info("--- [1] MontyeSIM Environment Verification ---");
        $montyBaseUrl = config('montyesim.base_url');
        $montyUser = config('montyesim.username');
        $montyPass = config('montyesim.password');

        $this->line("MONTYESIM_BASE_URL: " . ($montyBaseUrl ?: '[NOT SET]'));
        $this->line("MONTYESIM_USERNAME: " . $this->maskString($montyUser));
        $this->line("MONTYESIM_PASSWORD: " . $this->maskString($montyPass));

        $this->line("");
        $this->info("--- [2] Fluxir Environment Verification ---");
        $fluxirAuthUrl = config('fluxir.auth_url');
        $fluxirApiUrl = config('fluxir.api_url');
        $fluxirTenant = config('fluxir.tenant_id');
        $fluxirClient = config('fluxir.client_id');
        $fluxirSecret = config('fluxir.client_secret');
        $fluxirScope = config('fluxir.scope');
        $fluxirGrant = config('fluxir.grant_type');

        $this->line("FLUXIR_AUTH_URL:      " . ($fluxirAuthUrl ?: '[NOT SET]'));
        $this->line("FLUXIR_API_URL:       " . ($fluxirApiUrl ?: '[NOT SET]'));
        $this->line("FLUXIR_TENANT_ID:     " . $this->maskString($fluxirTenant));
        $this->line("FLUXIR_CLIENT_ID:     " . $this->maskString($fluxirClient));
        $this->line("FLUXIR_CLIENT_SECRET: " . $this->maskString($fluxirSecret));
        $this->line("FLUXIR_SCOPE:         " . ($fluxirScope ?: '[NOT SET]'));
        $this->line("FLUXIR_GRANT_TYPE:    " . ($fluxirGrant ?: '[NOT SET]'));

        $this->line("");
        $this->info("--- [3] Testing MontyeSIM API Auth & Operations ---");
        if (!$montyUser || !$montyPass) {
            $this->error("MontyeSIM credentials are not configured.");
        } else {
            try {
                $service = new MontyEsimService();
                $this->line("Attempting token acquisition...");
                $token = $service->getToken();
                $this->info("✔ Authentication SUCCESS!");
                $this->info("Token (first 15 chars): " . substr($token, 0, 15) . "...");

                $this->line("Testing complete workflow: Fetching eSIM bundles for UAE (ARE)...");
                $bundles = $service->getBundles('ARE');
                if (isset($bundles['error'])) {
                    $this->error("✘ Workflow Failed: " . $bundles['error']);
                } else {
                    $this->info("✔ Workflow SUCCESS!");
                    $this->info("Found " . count($bundles) . " bundles in UAE.");
                    if (count($bundles) > 0) {
                        $first = reset($bundles);
                        $this->line("  Sample Bundle: " . ($first['name'] ?? 'N/A') . " - " . ($first['dataAmount'] ?? 'N/A') . " GB / " . ($first['duration'] ?? 'N/A') . " days");
                    }
                }
            } catch (\Exception $e) {
                $this->error("✘ Authentication / Request Failed!");
                $this->error("Message: " . $e->getMessage());
            }
        }

        $this->line("");
        $this->info("--- [4] Testing Fluxir API Auth & Operations ---");
        if (!$fluxirTenant || !$fluxirClient || !$fluxirSecret) {
            $this->error("Fluxir credentials are not configured.");
        } else {
            try {
                $service = new FluxirService();
                $this->line("Attempting token acquisition...");
                $token = $service->getToken();
                $this->info("✔ Authentication SUCCESS!");
                $this->info("Token (first 15 chars): " . substr($token, 0, 15) . "...");

                $this->line("Testing complete workflow: Fetching online visa catalog...");
                $catalog = $service->getOnlineVisaCatalog(0); // pass 0 ttl to bypass cache
                if (isset($catalog['error'])) {
                    $this->error("✘ Workflow Failed: " . $catalog['error']);
                } else {
                    $this->info("✔ Workflow SUCCESS!");
                    $this->info("Found " . count($catalog) . " items in Fluxir visa catalog.");
                    if (count($catalog) > 0) {
                        $first = reset($catalog);
                        $this->line("  Sample Entry: " . ($first['name'] ?? 'N/A') . " (ID: " . ($first['id'] ?? 'N/A') . ")");
                    }
                }
            } catch (\Exception $e) {
                $this->error("✘ Authentication / Request Failed!");
                $this->error("Message: " . $e->getMessage());
            }
        }

        $this->info("=========================================");
        return Command::SUCCESS;
    }

    private function maskString(?string $str): string
    {
        if (empty($str)) {
            return '[NOT SET]';
        }
        $len = strlen($str);
        if ($len <= 4) {
            return str_repeat('*', $len) . " (Length: $len)";
        }
        return substr($str, 0, 2) . str_repeat('*', $len - 4) . substr($str, -2) . " (Length: $len)";
    }
}

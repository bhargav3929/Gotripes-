<?php

namespace App\Console\Commands;

use App\Services\FluxirService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * Refresh the cached Fluxir visa catalog and country list.
 *
 * The /e-visa storefront reads both from a 24h cache. When that cache expires
 * the next real visitor pays for two upstream calls inline (~12s), and if the
 * provider happens to be slow or down at that moment the page renders as
 * "temporarily unavailable" for everyone until it recovers. Refreshing on a
 * schedule means the expiry is always absorbed by this command instead.
 */
class EvisaWarmCatalog extends Command
{
    protected $signature = 'evisa:warm-catalog';

    protected $description = 'Refresh the cached Fluxir e-visa catalog and country list';

    public function handle(FluxirService $fluxir): int
    {
        if (!$fluxir->isConfigured()) {
            $this->warn('Fluxir is not configured — nothing to warm.');
            return self::SUCCESS;
        }

        // Rebuild rather than read: forget first so a stale-but-present cache
        // is actually replaced.
        Cache::forget('fluxir.online_catalog.v1');
        Cache::forget('fluxir.country_options.v1');

        $catalog   = $fluxir->getOnlineVisaCatalog();
        $countries = $fluxir->getCountryOptions();

        if (empty($catalog)) {
            // Leave the cache empty rather than storing a bad result: the
            // storefront's "temporarily unavailable" state is the honest
            // response, and the next run will try again.
            $this->error('Fluxir returned no visa catalog — cache left empty, storefront will show unavailable.');
            return self::FAILURE;
        }

        $types = array_sum(array_map(fn ($c) => count($c['types'] ?? []), $catalog));
        $this->info(sprintf(
            'Warmed catalog: %d destination(s), %d visa type(s), %d nationality option(s).',
            count($catalog),
            $types,
            count($countries)
        ));

        return self::SUCCESS;
    }
}

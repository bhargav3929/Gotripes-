<?php

namespace App\Services\TourPackages;

use App\Http\Controllers\FluxirEvisaController;
use App\Models\DestinationVisaRule;
use App\Models\EvisaSetting;
use App\Services\FluxirService;
use App\Support\CountryCodes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Step 1 of the tour wizard: can this passport travel to the package's
 * country, and what does it need? Answered by the same Fluxir eVisa API
 * that runs /e-visa, so the wizard and the visa shop never disagree.
 *
 * Order of trust:
 *   1. a rule staff wrote for this exact nationality (Booking setup, visa rules)
 *   2. Fluxir: visa free / e-Visa we can apply for / visa needed
 *   3. a staff rule for every nationality ('*')
 *   4. "we will confirm it for you"
 *
 * The payload keeps the shape TourPackageQuote::visaPayload() always had,
 * plus an 'evisa' block when we can apply for the visa online.
 */
class VisaEligibility
{
    private const CACHE_HOURS = 6;

    public static function check(string $destination, string $nationality): array
    {
        $destination = trim($destination);
        $nationality = trim($nationality);
        if ($destination === '' || $nationality === '') {
            return TourPackageQuote::visaPayload(null);
        }

        $own = DestinationVisaRule::where('destination', $destination)->where('nationality', $nationality)->first();
        if ($own) {
            return TourPackageQuote::visaPayload($own);
        }

        $fluxir = self::fromFluxir($destination, $nationality);
        if ($fluxir) {
            return $fluxir;
        }

        return TourPackageQuote::visaPayload(DestinationVisaRule::lookup($destination, $nationality));
    }

    /** Fluxir's answer, or null when it has none (not configured, unreachable, or no data). */
    private static function fromFluxir(string $destination, string $nationality): ?array
    {
        $dest = self::alpha3($destination);
        $nat = self::alpha3($nationality);
        if (!$dest || !$nat) {
            return null;
        }
        $fluxir = app(FluxirService::class);
        if (!$fluxir->isConfigured()) {
            return null;
        }

        $key = 'tour.visa.v1.' . $dest . '.' . $nat;
        $cached = Cache::get($key);
        if (is_array($cached)) {
            return $cached['payload'];
        }

        try {
            $res = $fluxir->withTimeout(12)->getServiceIntents([
                'originationCode' => $nat,
                'destinationCode' => $dest,
                'from' => now()->addDays(30)->format('Y-m-d'),
                'to' => now()->addDays(37)->format('Y-m-d'),
            ], ['Visa']);
        } catch (\Throwable $e) {
            Log::warning('Tour visa check: Fluxir unreachable', ['dest' => $dest, 'nat' => $nat, 'error' => $e->getMessage()]);
            return null;
        }
        if (!($res['success'] ?? false)) {
            return null; // not cached: try again on the next visitor
        }

        $payload = self::interpret($res['data']['items'] ?? [], $dest, $fluxir);
        Cache::put($key, ['payload' => $payload], now()->addHours(self::CACHE_HOURS));
        return $payload;
    }

    private static function interpret(array $items, string $dest, FluxirService $fluxir): ?array
    {
        $visas = array_values(array_filter($items, fn ($i) => ($i['serviceType'] ?? null) === 'Visa' && empty($i['isDisabled'])));
        if (!$visas) {
            return null;
        }

        $free = array_values(array_filter($visas, fn ($i) => ($i['extraProperties']['visaFree'] ?? '') === 'true'));
        if ($free) {
            $stay = max(array_map(fn ($i) => self::stayDays($i['extraProperties'] ?? []), $free));
            return [
                'known' => true, 'source' => 'evisa', 'tone' => 'ok', 'requirement' => 'visa_free',
                'label' => 'Eligible: no visa needed for your passport',
                'max_stay_days' => $stay ?: null, 'processing_days' => null, 'fee' => null,
                'documents' => 'A passport valid for your whole trip',
                'notes' => null,
            ];
        }

        $apply = array_values(array_filter($visas, fn ($i) => !empty($i['isSelectable'])));
        if ($apply) {
            $catalog = [];
            try {
                foreach (($fluxir->getOnlineVisaCatalog()[$dest]['types'] ?? []) as $t) {
                    $catalog[$t['id']] = $t;
                }
            } catch (\Throwable $e) {
            }
            $options = [];
            foreach ($apply as $i) {
                $typeId = preg_match('/-(\d+)$/', (string) ($i['serviceIntentKey'] ?? ''), $m) ? (int) $m[1] : null;
                $meta = $catalog[$typeId] ?? [];
                $net = (float) ($i['fee'] ?? 0) > 0 ? (float) $i['fee'] : (float) ($meta['net_fee'] ?? 0);
                $hours = is_numeric($meta['processing'] ?? null) ? (float) $meta['processing'] : null;
                $options[] = [
                    'name' => $meta['name'] ?? ($i['title'] ?? 'e-Visa'),
                    'price' => $net > 0 ? EvisaSetting::customerPrice($net) : null,
                    'stay' => self::stayDays($i['extraProperties'] ?? []) ?: (int) ($meta['stay'] ?? 0),
                    'processing_days' => $hours ? max(1, (int) round($hours / 24)) : null,
                ];
            }
            usort($options, fn ($a, $b) => ($a['price'] ?? PHP_INT_MAX) <=> ($b['price'] ?? PHP_INT_MAX));
            $best = $options[0];
            // Fluxir's requirements are a list under headings ("For application:"); keep the first item.
            $requirements = collect(preg_split('/\R/', (string) ($apply[0]['extraProperties']['requirements'] ?? '')))
                ->map(fn ($l) => preg_replace('/^\d+[.)]\s*/', '', trim($l, " \t-•*")))
                ->first(fn ($l) => $l !== '' && !str_ends_with($l, ':')) ?? '';
            return [
                'known' => true, 'source' => 'evisa', 'tone' => 'ok', 'requirement' => 'evisa',
                'label' => 'Eligible: e-Visa needed, and we apply for it for you',
                'max_stay_days' => $best['stay'] ?: null,
                'processing_days' => $best['processing_days'],
                'fee' => null,
                'documents' => $requirements !== '' ? $requirements : null,
                'notes' => null,
                'evisa' => [
                    'name' => $best['name'],
                    'price' => $best['price'],
                    'currency' => 'USD',
                    'url' => route('visa.evisa.form'),
                ],
            ];
        }

        return [
            'known' => true, 'source' => 'evisa', 'tone' => 'warn', 'requirement' => 'visa_required',
            'label' => 'A visa is needed, and it cannot be applied for online',
            'max_stay_days' => null, 'processing_days' => null, 'fee' => null,
            'documents' => null,
            'notes' => 'Our team will arrange it with you before you pay. You can still choose your package.',
        ];
    }

    private static function stayDays(array $props): int
    {
        $n = (int) ($props['stayDuration'] ?? 0);
        return match (strtolower((string) ($props['stayDurationUnit'] ?? 'days'))) {
            'years', 'year' => $n * 365,
            'months', 'month' => $n * 30,
            default => $n,
        };
    }

    /** ISO alpha-3 for a country name as the site stores it ("Georgia" -> "GEO"). */
    public static function alpha3(string $name): ?string
    {
        $want = self::norm($name);
        foreach (CountryCodes::all() as $c) {
            if (self::norm($c['name']) === $want) {
                $code = FluxirEvisaController::alpha3($c['iso']);
                if ($code) {
                    return $code;
                }
            }
        }
        // Fluxir's own list uses ISO names, e.g. "United Arab Emirates (the)".
        try {
            foreach (app(FluxirService::class)->getCountryOptions() as $c) {
                if (self::norm($c['name']) === $want) {
                    return $c['code'];
                }
            }
        } catch (\Throwable $e) {
        }
        return null;
    }

    private static function norm(string $s): string
    {
        $s = strtolower(str_replace(['&', ' (the)'], ['and', ''], $s));
        return preg_replace('/[^a-z]/', '', $s);
    }
}

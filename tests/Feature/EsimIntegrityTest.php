<?php

namespace Tests\Feature;

use App\Models\EsimOrder;
use App\Services\MontyEsimService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Regression cover for the eSIM audit of Aug 2026.
 *
 * The headline problem was that placeholder plans were shown whenever the
 * provider returned nothing — and they were fully purchasable, so the customer
 * paid real money for a bundle code that does not exist upstream. These tests
 * pin that shut, along with the wallet guard and the model cleanup.
 */
class EsimIntegrityTest extends TestCase
{
    use DatabaseTransactions;

    // ---- fake plans must never be sellable again ----

    public function test_storefront_has_no_fake_bundle_fallback(): void
    {
        $html = file_get_contents(resource_path('views/esim.blade.php'));

        foreach (['loadDemoBundles', 'isMockData', 'esim_1GB_7D', 'esim_5GB_30D', 'esim_UNL_30D'] as $needle) {
            $this->assertStringNotContainsString(
                $needle,
                $html,
                "Placeholder plans are purchasable but cannot be fulfilled: '{$needle}' must not return."
            );
        }
    }

    public function test_purchase_endpoint_has_no_hardcoded_demo_pricing(): void
    {
        $php = file_get_contents(app_path('Http/Controllers/EsimController.php'));

        $this->assertStringNotContainsString('$isDemo', $php);
        $this->assertStringNotContainsString('esim_1GB_7D', $php);
        $this->assertStringNotContainsString(
            'getStaticCountries',
            $php,
            'A static country list turns a provider outage into "every plan sold out".'
        );
    }

    public function test_unknown_bundle_is_rejected_rather_than_priced_locally(): void
    {
        $response = $this->postJson(route('esim.purchase'), [
            'name'         => 'Test Bhargav',
            'email'        => 'testbybhargav@example.com',
            'phone'        => '+971500000000',
            'bundle_code'  => 'esim_5GB_30D',   // the old placeholder code
            'country_code' => 'ARE',
            'country_name' => 'United Arab Emirates',
        ]);

        // Must NOT create an order or a checkout for a bundle that does not exist.
        $response->assertStatus(404);
        $this->assertDatabaseMissing('esim_orders', ['bundle_code' => 'esim_5GB_30D', 'payment_status' => 'paid']);
    }

    // ---- wallet guard ----

    public function test_wallet_guard_blocks_a_sale_it_cannot_cover(): void
    {
        $monty = new MontyEsimService();

        Cache::put('montyesim_wallet_balance', 2.00, 60);
        $this->assertFalse($monty->canAfford(10.00), 'A sale costing more than the wallet holds must be refused.');
        $this->assertTrue($monty->canAfford(1.50), 'A sale the wallet covers must go through.');
    }

    public function test_wallet_guard_fails_open_when_the_balance_is_unknown(): void
    {
        // Refusing every sale because we cannot read a balance would be worse
        // than the occasional failed assign, which is alerted and retryable.
        Cache::forget('montyesim_wallet_balance');
        $monty = \Mockery::mock(MontyEsimService::class)->makePartial();
        $monty->shouldReceive('getWalletBalance')->andReturn(null);

        $this->assertTrue($monty->canAfford(999.00));
    }

    // ---- model matches the table ----

    public function test_model_only_declares_columns_that_exist(): void
    {
        $columns = Schema::getColumnListing('esim_orders');
        $fillable = (new EsimOrder())->getFillable();

        $missing = array_diff($fillable, $columns);

        $this->assertSame(
            [],
            array_values($missing),
            'Fillable lists columns the table does not have: ' . implode(', ', $missing)
        );
    }

    public function test_qr_sent_at_is_tracked(): void
    {
        $this->assertTrue(
            Schema::hasColumn('esim_orders', 'qr_sent_at'),
            'Without this, "did the customer actually get their eSIM?" is unanswerable.'
        );
    }

    // ---- the QR email ----

    public function test_qr_email_renders_with_installation_details(): void
    {
        $order = EsimOrder::create([
            'company_id'      => 1,
            'order_reference' => 'ORDESIM-TEST-' . uniqid(),
            'customer_name'   => 'Test Bhargav',
            'customer_email'  => 'testbybhargav@example.com',
            'country_code'    => 'ARE',
            'country_name'    => 'United Arab Emirates',
            'bundle_code'     => 'ARE_TEST',
            'bundle_name'     => 'UAE 1GB 7 Days',
            'data_amount'     => '1 GB',
            'validity_days'   => 7,
            'monty_cost_price' => 7.31,
            'selling_price'   => 8.77,
            'currency'        => 'AED',
            'monty_iccid'     => '892200650000117237',
            'payment_status'  => 'paid',
        ]);

        $rendered = (new \App\Mail\EsimQrMail(
            order: $order,
            activationCode: 'LPA:1$example.rsp.com$2D29D-314C0-9FDC8-5038B',
            smdpAddress: 'example.rsp.com',
            matchingId: '2D29D-314C0-9FDC8-5038B',
        ))->render();

        $this->assertStringContainsString($order->order_reference, $rendered);
        $this->assertStringContainsString("example.rsp.com", $rendered);
        $this->assertStringContainsString('2D29D-314C0-9FDC8-5038B', $rendered);
        // Manual install must always be possible, not just the QR image.
        $this->assertStringContainsString('892200650000117237', $rendered);
    }

    public function test_reconcile_command_is_registered(): void
    {
        $this->artisan('list')
            ->expectsOutputToContain('esim:reconcile')
            ->assertSuccessful();
    }
}

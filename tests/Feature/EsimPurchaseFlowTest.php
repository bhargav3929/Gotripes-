<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Mail\BookingNotificationMail;
use App\Models\EsimOrder;
use App\Models\NomodTransaction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * End-to-end cover for the eSIM purchase flow, with MontyeSIM and Nomod stubbed
 * so it can run without spending the reseller wallet or taking a real payment.
 *
 * The live half of this flow failed in production on 17 Jul 2026 with
 * "Insufficient Balance" — orders 37, 38 and 39 were paid but never provisioned.
 * That was a funding problem, not a code one, but it exposed two real defects
 * these tests now pin: the provider's reason was not recorded on the order, and
 * a Thailand lookup by iso2 silently returns nothing.
 */
class EsimPurchaseFlowTest extends TestCase
{
    use DatabaseTransactions;

    /** A real Thailand 1 GB / 7-day bundle as the provider returns it. */
    private const THAILAND_1GB = [
        'bundle_code'            => 'THA_0410202611062919191355361GB7days20260410110629',
        'bundle_name'            => 'Thailand',
        'bundle_marketing_name'  => 'Thailand',
        'gprs_limit'             => 1,
        'data_unit'              => 'GB',
        'validity'               => 7,
        'unlimited'              => false,
        'bundle_price_final'     => 1.20,   // USD, before markup and AED conversion
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush(); // never let a cached token or bundle list leak between tests
    }

    /**
     * Stub the provider. $assign controls what POST /Bundles returns, which is
     * the call that actually provisions the eSIM and charges the wallet.
     */
    private function fakeMonty(array $assign): void
    {
        Http::fake([
            '*/Agent/login' => Http::response([
                'access_token'  => 'stub-access-token',
                'refresh_token' => 'stub-refresh-token',
            ], 200),

            // GET Bundles (catalogue) and POST Bundles (assign) share a URL, so
            // the sequence matters: the catalogue is fetched first.
            '*/Bundles*' => Http::sequence()
                ->push(['bundles' => [self::THAILAND_1GB]], 200)
                ->push($assign['body'], $assign['status']),

            '*/v1/checkout' => Http::response([
                'id' => 'chk_esim_test', 'url' => 'https://pay.test/chk_esim_test', 'status' => 'created',
            ], 200),

            '*/v1/checkout/*' => Http::response([
                'id' => 'chk_esim_test', 'status' => 'paid', 'amount' => 529, 'currency' => 'AED',
            ], 200),
        ]);
    }

    private function purchase(string $email = 'esim.test@example.com'): EsimOrder
    {
        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson(route('esim.purchase'), [
                'name'         => 'Thailand Tester',
                'email'        => $email,
                'phone'        => '+971500000123',
                'bundle_code'  => self::THAILAND_1GB['bundle_code'],
                'country_code' => 'THA',
                'country_name' => 'Thailand',
            ]);

        $response->assertOk()->assertJson(['success' => true]);

        return EsimOrder::withoutCompanyScope()
            ->where('customer_email', $email)
            ->latest('id')
            ->firstOrFail();
    }

    // ------------------------------------------------------------- happy ----

    public function test_full_purchase_flow_provisions_the_esim_and_records_it(): void
    {
        Mail::fake();
        $this->fakeMonty([
            'status' => 200,
            'body'   => [
                'order_id'                 => 'MONTY-TEST-0001',
                'iccid'                    => '8944478000001234567',
                'remaining_wallet_balance' => 94.71,
            ],
        ]);

        // --- Step 1: customer buys through the GoTrips app ---
        $order = $this->purchase();

        $this->assertEquals('Thailand', $order->country_name);
        $this->assertEquals('1 GB', $order->data_amount);
        $this->assertEquals(7, $order->validity_days);
        $this->assertEquals('unpaid', $order->payment_status);
        $this->assertEquals('pending', $order->reservation_status);
        $this->assertEquals('ORDESIM' . $order->id, $order->order_reference);

        // Price is recomputed server-side: USD 1.20 × 3.6725 AED = 4.41, +20% = 5.29
        $this->assertEquals(4.41, (float) $order->monty_cost_price);
        $this->assertEquals(5.29, (float) $order->selling_price);
        $this->assertEquals('AED', $order->currency);

        // --- Step 2: the Nomod checkout was created and logged ---
        $txn = NomodTransaction::where('order_id', $order->order_reference)->first();
        $this->assertNotNull($txn, 'a Nomod transaction should be recorded');
        $this->assertEquals('esim', $txn->booking_type);
        $this->assertEquals(5.29, (float) $txn->amount);

        // --- Step 3: customer returns from a successful payment ---
        $this->get(route('nomod.success', ['reference_id' => $order->order_reference]))
            ->assertOk();

        // --- Step 4: order is paid, provisioned, and the eSIM identifiers stored ---
        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('completed', $order->reservation_status);
        $this->assertEquals('MONTY-TEST-0001', $order->monty_order_id);
        $this->assertEquals('8944478000001234567', $order->monty_iccid);

        // The provider's response is kept for reconciliation against their portal.
        $this->assertEquals('MONTY-TEST-0001', $order->monty_response['order_id'] ?? null);
        $this->assertEquals(94.71, $order->monty_response['remaining_wallet_balance'] ?? null);

        // --- Step 5: the team was told a new order came in ---
        Mail::assertSent(BookingNotificationMail::class, fn ($m) => $m->heading === 'New eSIM order');
    }

    public function test_provisioned_order_is_visible_and_actionable_in_the_manager_portal(): void
    {
        Mail::fake();
        $this->fakeMonty([
            'status' => 200,
            'body'   => ['order_id' => 'MONTY-TEST-0002', 'iccid' => '8944478000009999999'],
        ]);

        $order = $this->purchase('portal.check@example.com');
        $this->get(route('nomod.success', ['reference_id' => $order->order_reference]))->assertOk();

        $manager = \App\Models\User::factory()->create([
            'access_type' => 'manager', 'role' => 'company_admin', 'company_id' => $order->company_id ?: 1,
        ]);

        $this->actingAs($manager)->get(route('manager.orders.esim'))
            ->assertOk()
            ->assertSee('portal.check@example.com');

        $this->actingAs($manager)->get(route('manager.orders.esim.show', $order))
            ->assertOk()
            ->assertSee('MONTY-TEST-0002')
            ->assertSee('8944478000009999999');
    }

    // ----------------------------------------------------------- failure ----

    public function test_insufficient_balance_records_the_reason_and_alerts_the_team(): void
    {
        Mail::fake();

        // Exactly what the provider returned on 17 Jul 2026.
        $this->fakeMonty([
            'status' => 400,
            'body'   => ['detail' => 'Insufficient Balance', 'status' => 400, 'title' => 'Error'],
        ]);

        $order = $this->purchase('broke.wallet@example.com');
        $this->get(route('nomod.success', ['reference_id' => $order->order_reference]))->assertOk();

        $order->refresh();

        // The customer's money was taken, so the payment stands...
        $this->assertEquals('paid', $order->payment_status);
        // ...but the eSIM was never issued.
        $this->assertEquals('assign_failed', $order->reservation_status);
        $this->assertNull($order->monty_order_id);

        // The reason must survive on the order itself. Previously only the log
        // held it and monty_response was saved as [], so an agent opening the
        // order in the portal had no idea why it failed.
        $this->assertNotEmpty($order->monty_response, 'the failure reason must be recorded on the order');
        $this->assertStringContainsString(
            'Insufficient Balance',
            json_encode($order->monty_response),
            'the provider reason must be readable from the order'
        );

        Mail::assertSent(BookingNotificationMail::class,
            fn ($m) => str_contains($m->heading, 'FAILED'));

        // And an agent must be able to read that reason in the portal without
        // being sent to the log files.
        $manager = \App\Models\User::factory()->create([
            'access_type' => 'manager', 'role' => 'company_admin', 'company_id' => $order->company_id ?: 1,
        ]);

        $this->actingAs($manager)
            ->get(route('manager.orders.esim.show', $order))
            ->assertOk()
            ->assertSee('Insufficient Balance')
            ->assertSee('Top up the reseller wallet', false);
    }

    public function test_a_paid_order_is_never_provisioned_twice(): void
    {
        Mail::fake();
        $this->fakeMonty([
            'status' => 200,
            'body'   => ['order_id' => 'MONTY-TEST-0003', 'iccid' => '8944478000001111111'],
        ]);

        $order = $this->purchase('double.charge@example.com');
        $this->get(route('nomod.success', ['reference_id' => $order->order_reference]))->assertOk();

        $order->refresh();
        $this->assertEquals('MONTY-TEST-0003', $order->monty_order_id);

        // Replaying the callback (customer refreshes the success page) must not
        // assign a second eSIM or charge the wallet again.
        $this->get(route('nomod.success', ['reference_id' => $order->order_reference]))->assertOk();

        $order->refresh();
        $this->assertEquals('MONTY-TEST-0003', $order->monty_order_id, 'the eSIM must not be reassigned');

        $result = (new \App\Services\EsimProvisioningService())->provision($order->fresh());
        $this->assertFalse($result['success']);
        $this->assertTrue($result['skipped'] ?? false);
    }

    public function test_unpaid_orders_are_never_provisioned(): void
    {
        $order = new EsimOrder(['payment_status' => 'unpaid', 'reservation_status' => 'pending']);

        $result = (new \App\Services\EsimProvisioningService())->provision($order);

        $this->assertFalse($result['success']);
        $this->assertTrue($result['skipped'] ?? false);
        $this->assertStringContainsString('not paid', $result['error']);
    }
}

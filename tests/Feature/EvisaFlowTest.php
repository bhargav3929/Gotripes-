<?php

namespace Tests\Feature;

use App\Mail\EvisaCustomerMail;
use App\Models\Company;
use App\Models\FluxirVisaApplication;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Regression cover for the e-visa fixes of Aug 2026.
 *
 * The audit found the storefront could take an application all the way to a
 * Stripe session and then strand it: the redirect used a retired URL format,
 * the customer was never emailed, an abandoned payment was never recovered,
 * and a tenant-subdomain order could not even be looked up on return. Each
 * test below pins one of those.
 */
class EvisaFlowTest extends TestCase
{
    use DatabaseTransactions;

    private function application(array $overrides = []): FluxirVisaApplication
    {
        return FluxirVisaApplication::create(array_merge([
            'company_id'       => 1,
            'order_id'         => 'ORDVISA-TEST-' . uniqid(),
            'status'           => 'submitted',
            'is_paid'          => true,
            'first_name'       => 'Test',
            'last_name'        => 'Bhargav',
            'email'            => 'testbybhargav@example.com',
            'nationality'      => 'IND',
            'destination_code' => 'ARE',
            'currency'         => 'USD',
            'amount'           => 111.00,
        ], $overrides));
    }

    // ---- C1: the payment redirect ----

    public function test_storefront_no_longer_uses_the_retired_stripe_pay_url(): void
    {
        $html = file_get_contents(resource_path('views/visa/evisa.blade.php'));

        $this->assertStringNotContainsString(
            'checkout.stripe.com/pay/',
            $html,
            'The retired checkout.stripe.com/pay/<id> URL renders Stripe\'s error page.'
        );
        $this->assertStringContainsString(
            'redirectToCheckout',
            $html,
            'A Checkout Session id must be opened through Stripe.js.'
        );
    }

    public function test_storefront_has_no_fake_country_fallback(): void
    {
        $html = file_get_contents(resource_path('views/visa/evisa.blade.php'));

        // A hardcoded country list made an API outage look like "every visa is
        // sold out" on 200+ countries.
        $this->assertStringNotContainsString('CountryCodes::all()', $html);
    }

    // ---- C3: the customer email ----

    public function test_customer_is_emailed_when_the_application_is_submitted(): void
    {
        Mail::fake();
        $record = $this->application(['customer_notified_at' => null]);

        $this->get(route('visa.fluxir.success', ['order_id' => $record->order_id]))
            ->assertStatus(302);

        Mail::assertSent(EvisaCustomerMail::class, fn ($mail) =>
            $mail->hasTo('testbybhargav@example.com') && $mail->stage === 'submitted');

        $this->assertNotNull($record->fresh()->customer_notified_at);
    }

    public function test_customer_email_is_not_sent_twice_on_page_refresh(): void
    {
        Mail::fake();
        $record = $this->application(['customer_notified_at' => null]);

        $this->get(route('visa.fluxir.success', ['order_id' => $record->order_id]));
        $this->get(route('visa.fluxir.success', ['order_id' => $record->order_id]));

        Mail::assertSent(EvisaCustomerMail::class, 1);
    }

    public function test_customer_mail_renders_for_every_stage(): void
    {
        $record = $this->application();

        foreach (['submitted', 'approved', 'rejected'] as $stage) {
            $rendered = (new EvisaCustomerMail($record, $stage))->render();

            $this->assertStringContainsString($record->order_id, $rendered);
            $this->assertStringContainsString('Test Bhargav', $rendered);
        }
    }

    // ---- H2: tenant-scoped lookups on payment return ----

    public function test_success_finds_an_application_belonging_to_another_tenant(): void
    {
        Mail::fake();

        // Simulate an order created on a tenant subdomain: the payment returns
        // on the apex host, where CompanyScope binds a different company.
        $otherCompanyId = Company::where('id', '!=', 1)->value('id');
        if (!$otherCompanyId) {
            $this->markTestSkipped('Needs a second company to exercise cross-tenant lookup.');
        }

        $record = $this->application([
            'company_id'           => $otherCompanyId,
            'customer_notified_at' => null,
        ]);

        $this->get(route('visa.fluxir.success', ['order_id' => $record->order_id]))
            ->assertStatus(302);

        // Found and processed despite belonging to a different tenant.
        $this->assertNotNull(
            $record->fresh()->customer_notified_at,
            'A tenant-subdomain order must still be found when payment returns on the apex host.'
        );
    }

    public function test_status_endpoint_finds_cross_tenant_orders(): void
    {
        $otherCompanyId = Company::where('id', '!=', 1)->value('id');
        if (!$otherCompanyId) {
            $this->markTestSkipped('Needs a second company to exercise cross-tenant lookup.');
        }

        $record = $this->application(['company_id' => $otherCompanyId]);

        $this->getJson(route('visa.fluxir.status', ['orderId' => $record->order_id]))
            ->assertOk()
            ->assertJsonPath('order_id', $record->order_id);
    }

    // ---- M3: the dead v1 pipeline ----

    public function test_legacy_apply_route_is_gone(): void
    {
        $this->assertFalse(
            \Route::has('visa.fluxir.apply'),
            'The v1 UAE-only apply route bypassed the newer validation and should be removed.'
        );
    }

    // ---- C2: the reconcile safety net ----

    public function test_reconcile_command_is_registered(): void
    {
        $this->artisan('list')
            ->expectsOutputToContain('evisa:reconcile')
            ->assertSuccessful();
    }

    /**
     * Payment detection drives the whole safety net, and Fluxir gives us no
     * boolean for it — only checkoutStatus plus the state machine. Getting this
     * wrong either strands a paying customer or "recovers" one who never paid.
     */
    public function test_payment_detection_reads_checkout_status_and_state(): void
    {
        $fluxir = app(\App\Services\FluxirService::class);

        // Unpaid: sitting at the payment gate with nothing settled.
        $this->assertFalse($fluxir->applicationIsPaid(['state' => 'ReadyForPayment', 'checkoutStatus' => 'Unknown']));
        $this->assertFalse($fluxir->applicationIsPaid(['state' => 'Draft', 'checkoutStatus' => 'Unknown']));

        // Paid: checkout settled, even if the state has not moved yet.
        $this->assertTrue($fluxir->applicationIsPaid(['state' => 'ReadyForPayment', 'checkoutStatus' => 'Paid']));

        // Paid: past the payment gate, whatever checkoutStatus reports.
        $this->assertTrue($fluxir->applicationIsPaid(['state' => 'ReadyForReview', 'checkoutStatus' => 'Unknown']));
        $this->assertTrue($fluxir->applicationIsPaid(['state' => 'ApplicationConfirmed', 'checkoutStatus' => 'Unknown']));

        // Cancelled work is not payment.
        $this->assertFalse($fluxir->applicationIsPaid(['state' => 'Cancelled', 'checkoutStatus' => 'Unknown']));
        $this->assertFalse($fluxir->applicationIsPaid(['state' => '', 'checkoutStatus' => '']));
    }

    /**
     * Fluxir answers 405 to GET /api/app/travel-services/{id} (verified live),
     * so state must be read from the trip. A regression here is invisible: the
     * poll just silently stops updating.
     */
    public function test_application_state_is_read_from_the_trip_not_the_service_endpoint(): void
    {
        $reflection = new \ReflectionMethod(\App\Services\FluxirService::class, 'getServiceApplication');

        $this->assertSame(
            2,
            $reflection->getNumberOfRequiredParameters(),
            'getServiceApplication must take a trip id — the direct service-application GET returns 405.'
        );
        $this->assertSame('tripId', $reflection->getParameters()[0]->getName());
    }
}

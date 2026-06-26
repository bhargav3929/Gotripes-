<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Mail\BookingNotificationMail;
use App\Models\Company;
use App\Models\EsimOrder;
use App\Models\FluxirVisaApplication;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Per-service booking notifications (eSIM, e-Visa, FIFA). Recipients come from
 * the tenant's `booking_notification_emails` setting. DatabaseTransactions keeps
 * the dev DB clean.
 */
class ServiceBookingNotificationTest extends TestCase
{
    use DatabaseTransactions;

    private function setServiceEmails(string $service, string $value): void
    {
        $company = Company::findOrFail(1);
        $map = $company->getSetting('booking_notification_emails', []);
        $map = is_array($map) ? $map : [];
        $map[$service] = $value;
        $company->setSetting('booking_notification_emails', $map);
    }

    /** Read messages captured by the in-memory "array" mail transport. */
    private function arrayMessages(): array
    {
        return collect(Mail::mailer('array')->getSymfonyTransport()->messages())
            ->map(fn ($sent) => $sent->getOriginalMessage())
            ->all();
    }

    private function recipientsOf($email): array
    {
        return collect($email->getTo())->map(fn ($a) => strtolower($a->getAddress()))->all();
    }

    // ---- FIFA (uses Mail::html → array transport) ----

    public function test_fifa_enquiry_emails_configured_recipients(): void
    {
        config(['mail.default' => 'array']);
        $this->setServiceEmails('fifa', 'Fifa1@biz.com, fifa2@biz.com');

        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('fifa.request'), [
                'name'     => 'FIFA Fan',
                'email'    => 'fan@example.com',
                'quantity' => 2,
            ])->assertStatus(302);

        $messages = $this->arrayMessages();
        $this->assertNotEmpty($messages, 'No FIFA enquiry email was sent');
        $to = $this->recipientsOf($messages[0]);

        $this->assertContains('fifa1@biz.com', $to);
        $this->assertContains('fifa2@biz.com', $to);
        $this->assertNotContains('main@gotrips.ai', $to);
    }

    public function test_fifa_falls_back_to_company_email_when_unset(): void
    {
        config(['mail.default' => 'array']);
        $this->setServiceEmails('fifa', '');

        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('fifa.request'), [
                'name'     => 'FIFA Fan',
                'email'    => 'fan2@example.com',
                'quantity' => 1,
            ])->assertStatus(302);

        $to = $this->recipientsOf($this->arrayMessages()[0]);
        $this->assertContains('main@gotrips.ai', $to);
    }

    // ---- e-Visa (credit flow → no external Fluxir call) ----

    public function test_visa_success_notifies_and_dedupes(): void
    {
        Mail::fake();
        $this->setServiceEmails('visa', 'visa@biz.com');

        $record = FluxirVisaApplication::create([
            'company_id'  => 1,
            'order_id'    => 'ORDVISA-TEST-1',
            'status'      => 'submitted', // credit flow: success() skips finalize
            'is_paid'     => false,
            'first_name'  => 'Test',
            'last_name'   => 'Traveller',
            'email'       => 'traveller@example.com',
            'nationality' => 'IN',
            'currency'    => 'AED',
            'amount'      => 350.00,
        ]);

        $this->get(route('visa.fluxir.success', ['order_id' => 'ORDVISA-TEST-1']))
            ->assertStatus(302);

        Mail::assertSent(BookingNotificationMail::class, fn ($mail) => $mail->hasTo('visa@biz.com'));
        $this->assertNotNull($record->fresh()->notified_at, 'notified_at should be set');

        // Refresh of the success page must NOT re-notify.
        $this->get(route('visa.fluxir.success', ['order_id' => 'ORDVISA-TEST-1']))
            ->assertStatus(302);
        Mail::assertSent(BookingNotificationMail::class, 1);
    }

    // ---- eSIM (resolution from the order's tenant, as the webhook does) ----

    public function test_esim_recipients_resolve_from_order_company(): void
    {
        $this->setServiceEmails('esim', 'esim@biz.com, esim2@biz.com');

        $order = EsimOrder::create([
            'company_id'       => 1,
            'order_reference'  => 'ESIM-TEST-1',
            'customer_name'    => 'Buyer',
            'customer_email'   => 'buyer@example.com',
            'country_code'     => 'AE',
            'country_name'     => 'United Arab Emirates',
            'bundle_code'      => 'uae-5gb',
            'bundle_name'      => 'UAE 5GB',
            'data_amount'      => '5GB',
            'validity_days'    => 30,
            'monty_cost_price' => 10.00,
            'selling_price'    => 12.00,
            'currency'         => 'USD',
        ]);

        $recipients = booking_recipients(service_notification_emails('esim', $order->company));

        $this->assertContains('esim@biz.com', $recipients);
        $this->assertContains('esim2@biz.com', $recipients);
        $this->assertNotContains('main@gotrips.ai', $recipients); // configured -> no fallback
    }
}

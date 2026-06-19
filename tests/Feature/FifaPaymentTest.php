<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Mail\BookingNotificationMail;
use App\Models\Company;
use App\Models\FifaMatch;
use App\Models\FifaTicket;
use App\Models\FifaTicketRequest;
use App\Models\NomodTransaction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * FIFA online payment via Nomod: checkout creates a paid booking + Nomod
 * checkout; the paid callback marks it paid and notifies the FIFA recipients.
 * Http::fake stubs the Nomod API so no real gateway call is made.
 */
class FifaPaymentTest extends TestCase
{
    use DatabaseTransactions;

    private function makeTicket(): FifaTicket
    {
        $match = FifaMatch::create([
            'match_code' => 'TST1', 'team_a' => 'Brazil', 'team_b' => 'Spain', 'is_active' => 1,
        ]);

        return FifaTicket::create([
            'match_id' => $match->id, 'category' => 'Category 1',
            'supplier_price' => 100, 'quantity' => 10, 'is_active' => 1,
        ]);
    }

    public function test_checkout_creates_booking_and_returns_checkout_url(): void
    {
        Http::fake([
            '*/v1/checkout' => Http::response(['id' => 'chk_test', 'url' => 'https://pay.test/chk_test', 'status' => 'created'], 200),
        ]);

        $ticket   = $this->makeTicket();
        $expected = round((float) $ticket->customer_price * 2, 2); // qty 2

        $resp = $this->withoutMiddleware(VerifyCsrfToken::class)->postJson(route('fifa.checkout'), [
            'name'      => 'Pay Customer',
            'email'     => 'payer@example.com',
            'phone'     => '+971500000000',
            'ticket_id' => $ticket->id,
            'quantity'  => 2,
        ]);

        $resp->assertOk()->assertJson(['success' => true]);
        $this->assertSame('https://pay.test/chk_test', $resp->json('checkout_url'));

        $booking = FifaTicketRequest::where('email', 'payer@example.com')->firstOrFail();
        $this->assertSame('awaiting_payment', $booking->payment_status);
        $this->assertSame('ORDFIFA' . $booking->id, $booking->order_id);
        $this->assertEquals($expected, (float) $booking->amount);
        $this->assertEquals(1, (int) $booking->company_id);

        $this->assertDatabaseHas('nomod_transactions', ['order_id' => $booking->order_id, 'booking_type' => 'fifa']);
    }

    public function test_paid_callback_marks_paid_and_notifies_recipients_once(): void
    {
        Mail::fake();
        Http::fake([
            '*/v1/checkout/*' => Http::response(['status' => 'paid'], 200),
        ]);

        // Configure FIFA recipients on the tenant.
        $company = Company::findOrFail(1);
        $map = $company->getSetting('booking_notification_emails', []);
        $map = is_array($map) ? $map : [];
        $map['fifa'] = 'fifaops@biz.com';
        $company->setSetting('booking_notification_emails', $map);

        $booking = FifaTicketRequest::create([
            'company_id'     => 1,
            'name'           => 'Paid Customer',
            'email'          => 'fan@example.com',
            'quantity'       => 2,
            'amount'         => 240,
            'currency'       => 'USD',
            'match_label'    => 'Brazil vs Spain',
            'category'       => 'Category 1',
            'status'         => 'new',
            'payment_status' => 'awaiting_payment',
        ]);
        $orderId = 'ORDFIFA' . $booking->id;
        $booking->update(['order_id' => $orderId]);

        NomodTransaction::create([
            'company_id' => 1,
            'checkout_id' => 'chk_paid', 'order_id' => $orderId, 'status' => 'created',
            'amount' => 240, 'currency' => 'USD', 'booking_type' => 'fifa',
        ]);

        $this->get(route('nomod.success') . '?reference_id=' . $orderId)->assertOk();

        Mail::assertSent(BookingNotificationMail::class, fn ($m) => $m->hasTo('fifaops@biz.com'));

        $fresh = $booking->fresh();
        $this->assertSame('paid', $fresh->payment_status);
        $this->assertNotNull($fresh->notified_at);

        // A duplicate callback must not re-notify the agents.
        $this->get(route('nomod.success') . '?reference_id=' . $orderId)->assertOk();
        Mail::assertSent(BookingNotificationMail::class, 1);
    }
}

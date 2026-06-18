<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Mail\ActivityBookingMail;
use App\Models\UAEActivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * End-to-end check that a booking emails the per-activity notification
 * recipients (+ the customer) and falls back to the company email when none
 * are configured. Uses DatabaseTransactions so the dev sqlite DB is untouched.
 */
class ActivityBookingNotificationTest extends TestCase
{
    use DatabaseTransactions;

    private function bookPayload(int $activityId, string $customerEmail): array
    {
        return [
            'name'           => 'Test Customer',
            'date'           => '2026-07-01',
            'email'          => $customerEmail,
            'phone'          => '0500000000',
            'adults'         => 2,
            'childrens'      => 0,
            'payment_option' => 'book_with_us',
            'transfer'       => 'none',
            'currency'       => 'AED',
            'activityId'     => $activityId,
        ];
    }

    private function recipientsOf($mail): array
    {
        return collect($mail->to)->pluck('address')->map(fn ($a) => strtolower($a))->all();
    }

    public function test_booking_emails_the_configured_notification_recipients(): void
    {
        Mail::fake();

        $activity = UAEActivity::withoutGlobalScopes()->where('company_id', 1)->firstOrFail();
        $activity->notification_emails = "Owner@Biz.com, ops@biz.com\nops@biz.com";
        $activity->save();

        $resp = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post('/activity/book', $this->bookPayload($activity->activityID, 'customer@example.com'));

        $resp->assertStatus(302); // book_with_us redirects back with success

        Mail::assertSent(ActivityBookingMail::class, function ($mail) {
            $to = $this->recipientsOf($mail);
            // Configured business recipients (deduped, lowercased) + customer copy,
            // and NOT the company fallback since per-activity emails were set.
            return in_array('owner@biz.com', $to, true)
                && in_array('ops@biz.com', $to, true)
                && in_array('customer@example.com', $to, true)
                && count(array_unique($to)) === 3
                && !in_array('main@gotrips.ai', $to, true);
        });
    }

    public function test_booking_falls_back_to_company_email_when_none_configured(): void
    {
        Mail::fake();

        $activity = UAEActivity::withoutGlobalScopes()->where('company_id', 1)->firstOrFail();
        $activity->notification_emails = null;
        $activity->save();

        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post('/activity/book', $this->bookPayload($activity->activityID, 'customer2@example.com'))
            ->assertStatus(302);

        Mail::assertSent(ActivityBookingMail::class, function ($mail) {
            $to = $this->recipientsOf($mail);
            // No per-activity emails -> company email (main@gotrips.ai) + customer.
            return in_array('main@gotrips.ai', $to, true)
                && in_array('customer2@example.com', $to, true);
        });
    }
}

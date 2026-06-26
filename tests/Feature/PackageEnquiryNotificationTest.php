<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTenantFeature;
use App\Http\Middleware\VerifyCsrfToken;
use App\Mail\BookingNotificationMail;
use App\Models\PackageEnquiry;
use App\Models\TravelPackage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Tour-package enquiry → emails the package's per-package recipients and stores
 * the enquiry. DatabaseTransactions keeps the dev DB clean.
 */
class PackageEnquiryNotificationTest extends TestCase
{
    use DatabaseTransactions;

    private function makePackage(?string $emails): TravelPackage
    {
        return TravelPackage::create([
            'company_id'          => 1,
            'title'               => 'Test Bali Escape',
            'country'             => 'Indonesia',
            'package_type'        => 'enquire',
            'notification_emails' => $emails,
            'price'               => 1999,
            'description'         => 'Test package',
            'duration'            => '5 days',
            'isActive'            => 1,
            'createdBy'           => 'test',
            'createdDate'         => now(),
        ]);
    }

    private function payload(): array
    {
        return [
            'name'        => 'Curious Customer',
            'email'       => 'lead@example.com',
            'phone'       => '+971500000000',
            'travel_date' => 'Aug 2026',
            'travellers'  => 3,
            'message'     => 'Is this available with a beach villa?',
        ];
    }

    public function test_enquiry_notifies_package_recipients_and_is_stored(): void
    {
        Mail::fake();
        $package = $this->makePackage('Sales@biz.com, ops@biz.com');

        $this->withoutMiddleware([VerifyCsrfToken::class, EnsureTenantFeature::class])
            ->post(route('tour-packages.enquire', $package->id), $this->payload())
            ->assertStatus(302);

        Mail::assertSent(BookingNotificationMail::class, function ($mail) {
            return $mail->hasTo('sales@biz.com')
                && $mail->hasTo('ops@biz.com')
                && !$mail->hasTo('main@gotrips.ai'); // configured -> no fallback
        });

        $this->assertDatabaseHas('package_enquiries', [
            'package_id' => $package->id,
            'email'      => 'lead@example.com',
            'travellers' => 3,
            'status'     => 'new',
        ]);
    }

    public function test_enquiry_falls_back_to_company_email(): void
    {
        Mail::fake();
        $package = $this->makePackage(null);

        $this->withoutMiddleware([VerifyCsrfToken::class, EnsureTenantFeature::class])
            ->post(route('tour-packages.enquire', $package->id), $this->payload())
            ->assertStatus(302);

        Mail::assertSent(BookingNotificationMail::class, fn ($mail) => $mail->hasTo('main@gotrips.ai'));
    }
}

<?php

namespace Tests\Feature;

use App\Http\Controllers\NomodController;
use App\Mail\SaudiVisaMail;
use App\Models\Company;
use App\Models\SaudiVisaApplication;
use App\Models\SaudiVisaType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ReflectionMethod;
use Tests\TestCase;

/**
 * Saudi Visa per-visa-type Company/Supplier email workflow. On payment success
 * the configured supplier and company inboxes each receive the full application
 * with the uploaded documents attached (mirrors the UAE visa supplier flow).
 * Empty addresses are skipped without error. DatabaseTransactions keeps the dev
 * DB clean; binding the tenant mirrors what IdentifyTenant does on the webhook.
 */
class SaudiVisaSupplierEmailTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // The Nomod callback runs through IdentifyTenant, which binds the tenant
        // from the host. Reproduce that so the company-scoped models resolve.
        app()->instance('current_company', Company::findOrFail(1));
    }

    private function makeVisaType(?string $company, ?string $supplier): SaudiVisaType
    {
        return SaudiVisaType::create([
            'company_id'     => 1,
            'name'           => '1-Year Multiple Entry',
            'price'          => 450,
            'company_email'  => $company,
            'supplier_email' => $supplier,
            'isActive'       => true,
        ]);
    }

    private function makeApplication(SaudiVisaType $type): SaudiVisaApplication
    {
        Storage::fake('public');
        Storage::disk('public')->put('visas/saudi/passports/pp.jpg', 'fake-passport-bytes');
        Storage::disk('public')->put('visas/saudi/photos/photo.jpg', 'fake-photo-bytes');

        return SaudiVisaApplication::create([
            'company_id'         => 1,
            'saudi_visa_type_id' => $type->id,
            'full_name'          => 'Aisha Khan',
            'first_name'         => 'Aisha',
            'last_name'          => 'Khan',
            'email'              => 'customer@example.com',
            'phone'              => '+971500000000',
            'nationality'        => 'India',
            'passport_path'      => 'visas/saudi/passports/pp.jpg',
            'photo_path'         => 'visas/saudi/photos/photo.jpg',
            'price'              => 450,
            'payment_status'     => 'paid',
            'status'             => 'pending',
            'order_id'           => 'ORDSV-TEST-' . Str::random(5),
        ]);
    }

    private function notify(SaudiVisaApplication $application): void
    {
        $method = new ReflectionMethod(NomodController::class, 'notifySaudiVisaApplication');
        $method->setAccessible(true);
        $method->invoke(app(NomodController::class), $application);
    }

    public function test_supplier_and_company_each_receive_the_application_with_attachments(): void
    {
        Mail::fake();
        $type = $this->makeVisaType('company@agency.test', 'supplier@agency.test');
        $application = $this->makeApplication($type);

        $this->notify($application);

        Mail::assertSent(SaudiVisaMail::class, fn ($mail) => $mail->hasTo('supplier@agency.test'));
        Mail::assertSent(SaudiVisaMail::class, fn ($mail) => $mail->hasTo('company@agency.test'));
        Mail::assertSent(SaudiVisaMail::class, 2);

        // The documents must actually ride along with the mail.
        Mail::assertSent(SaudiVisaMail::class, function ($mail) {
            $built = $mail->build();
            $names = collect($built->attachments ?? [])
                ->map(fn ($a) => $a['options']['as'] ?? null)
                ->filter()
                ->all();
            return in_array('passport.jpg', $names, true)
                && in_array('photo.jpg', $names, true);
        });
    }

    public function test_only_supplier_is_emailed_when_company_email_is_blank(): void
    {
        Mail::fake();
        $type = $this->makeVisaType(null, 'supplier@agency.test');
        $application = $this->makeApplication($type);

        $this->notify($application);

        Mail::assertSent(SaudiVisaMail::class, 1);
        Mail::assertSent(SaudiVisaMail::class, fn ($mail) => $mail->hasTo('supplier@agency.test'));
    }

    public function test_nothing_is_sent_to_the_visa_type_inboxes_when_both_are_blank(): void
    {
        Mail::fake();
        $type = $this->makeVisaType(null, null);
        $application = $this->makeApplication($type);

        // Must not throw, and must not send a SaudiVisaMail to anyone.
        $this->notify($application);

        Mail::assertNotSent(SaudiVisaMail::class);
    }

    public function test_the_two_email_columns_persist_on_the_visa_type(): void
    {
        $type = $this->makeVisaType('company@agency.test', 'supplier@agency.test');

        $fresh = SaudiVisaType::findOrFail($type->id);
        $this->assertSame('company@agency.test', $fresh->company_email);
        $this->assertSame('supplier@agency.test', $fresh->supplier_email);
    }
}

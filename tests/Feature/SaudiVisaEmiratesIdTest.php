<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\SaudiVisaApplication;
use App\Models\SaudiVisaType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Visa types that list an Emirates ID / GCC residence copy among their required
 * documents must actually collect it. The requirement used to be display-only
 * text with no upload field, so the document never reached the supplier.
 */
class SaudiVisaEmiratesIdTest extends TestCase
{
    use DatabaseTransactions;

    private function visaType(array $documents): SaudiVisaType
    {
        return SaudiVisaType::create([
            'company_id'         => 1,
            'name'               => '1-Year Multiple Entry Visa',
            'description'        => 'Multiple entry visa valid for 1 year.',
            'required_documents' => $documents,
            'processing_days'    => 5,
            'price'              => 850.00,
            'isActive'           => true,
        ]);
    }

    private function payload(SaudiVisaType $type): array
    {
        return [
            'full_name'          => 'Amer Rains',
            'email'              => 'amer.rains@example.com',
            'phone'              => '+971505574373',
            'saudi_visa_type_id' => $type->id,
            'passport_copy'      => UploadedFile::fake()->create('passport.pdf', 100, 'application/pdf'),
            'passport_photo'     => UploadedFile::fake()->create('photo.jpg', 100, 'image/jpeg'),
        ];
    }

    public function test_the_requirement_is_read_from_the_manager_editable_document_list(): void
    {
        $this->assertTrue($this->visaType(['Passport Copy', 'Emirates ID or GCC Residence'])->requiresEmiratesId());
        $this->assertTrue($this->visaType(['Passport Copy', 'GCC Residence'])->requiresEmiratesId());
        $this->assertTrue($this->visaType(['Passport Copy', 'emirates id'])->requiresEmiratesId());
        $this->assertFalse($this->visaType(['Passport Copy', 'Passport Photo'])->requiresEmiratesId());
        $this->assertFalse($this->visaType([])->requiresEmiratesId());
    }

    public function test_submission_is_rejected_when_the_visa_type_requires_an_emirates_id_and_none_is_uploaded(): void
    {
        Storage::fake('public');
        $type = $this->visaType(['Passport Copy', 'Passport Photo', 'Emirates ID or GCC Residence']);

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('saudi-visa.submit'), $this->payload($type));

        $response->assertSessionHasErrors('emirates_id');
        $this->assertNull(SaudiVisaApplication::where('email', 'amer.rains@example.com')->first());
    }

    public function test_the_uploaded_emirates_id_is_stored_on_the_application(): void
    {
        Storage::fake('public');
        Http::fake(['*/v1/checkout' => Http::response(['id' => 'chk', 'url' => 'https://pay.test/chk'], 200)]);

        $type = $this->visaType(['Passport Copy', 'Passport Photo', 'Emirates ID or GCC Residence']);

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)->post(
            route('saudi-visa.submit'),
            $this->payload($type) + ['emirates_id' => UploadedFile::fake()->create('eid.jpg', 100, 'image/jpeg')]
        );

        $response->assertStatus(200);

        $application = SaudiVisaApplication::where('email', 'amer.rains@example.com')->first();
        $this->assertNotNull($application);
        $this->assertNotNull($application->emirates_id_path);
        Storage::disk('public')->assertExists($application->emirates_id_path);
    }

    public function test_a_visa_type_that_does_not_ask_for_it_still_submits_without_one(): void
    {
        Storage::fake('public');
        Http::fake(['*/v1/checkout' => Http::response(['id' => 'chk', 'url' => 'https://pay.test/chk'], 200)]);

        $type = $this->visaType(['Passport Copy', 'Passport Photo']);

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('saudi-visa.submit'), $this->payload($type));

        $response->assertStatus(200);

        $application = SaudiVisaApplication::where('email', 'amer.rains@example.com')->first();
        $this->assertNotNull($application);
        $this->assertNull($application->emirates_id_path);
    }

    public function test_the_storefront_marks_up_which_visa_types_need_one(): void
    {
        $this->visaType(['Passport Copy', 'Passport Photo', 'Emirates ID or GCC Residence']);

        $response = $this->get(route('saudi-visa.index'));

        $response->assertStatus(200);
        $response->assertSee('data-requires-emirates-id="1"', false);
        $response->assertSee('Emirates ID / GCC Residence');
    }
}

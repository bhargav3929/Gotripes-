<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\NomodTransaction;
use App\Models\SaudiVisaApplication;
use App\Models\SaudiVisaType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SaudiVisaTest extends TestCase
{
    use DatabaseTransactions;

    public function test_saudi_visa_public_index_loads(): void
    {
        $response = $this->get(route('saudi-visa.index'));
        $response->assertStatus(200);
        $response->assertSee('Saudi Visa');
    }

    public function test_saudi_visa_submission_creates_records_and_initiates_payment(): void
    {
        Storage::fake('public');
        Http::fake([
            '*/v1/checkout' => Http::response([
                'id' => 'mock_chk_saudi',
                'url' => 'https://pay.test/mock_chk_saudi',
                'status' => 'created'
            ], 200),
        ]);

        $visaType = SaudiVisaType::create([
            'company_id' => 1,
            'name' => 'Premium Umrah Visa',
            'description' => 'A special premium visa',
            'required_documents' => ['Passport Copy', 'Photo'],
            'processing_days' => 2,
            'price' => 500.00,
            'isActive' => true,
        ]);

        $payload = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+971501234567',
            'nationality' => 'India',
            'passport_number' => 'P9876543',
            'passport_expiry' => '2030-05-15',
            'dob' => '1995-10-10',
            'gender' => 'Male',
            'saudi_visa_type_id' => $visaType->id,
            'passport_copy' => UploadedFile::fake()->create('passport.pdf', 100, 'application/pdf'),
            'passport_photo' => UploadedFile::fake()->create('photo.jpg', 100, 'image/jpeg'),
            'passport_valid' => 1,
            'not_stay_long' => 1,
        ];

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('saudi-visa.submit'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Assert database records
        $application = SaudiVisaApplication::where('email', 'john.doe@example.com')->first();
        $this->assertNotNull($application);
        $this->assertEquals('John Doe', $application->full_name);
        $this->assertEquals('John', $application->first_name);
        $this->assertEquals('Doe', $application->last_name);
        $this->assertEquals('P9876543', $application->passport_number);
        $this->assertEquals('Male', $application->gender);
        $this->assertEquals(500.00, (float) $application->price);
        $this->assertEquals('pending', $application->status);

        // Assert files are stored
        Storage::disk('public')->assertExists($application->passport_path);
        Storage::disk('public')->assertExists($application->photo_path);

        // Assert transaction log
        $txn = NomodTransaction::where('order_id', $application->order_id)->first();
        $this->assertNotNull($txn);
        $this->assertEquals(500.00, (float) $txn->amount);
        $this->assertEquals('saudi_visa', $txn->booking_type);
    }

    public function test_manager_portal_lists_and_updates_applications(): void
    {
        // Authenticate as a manager user
        $managerUser = \App\Models\User::factory()->create([
            'access_type' => 'manager',
            'role' => 'company_admin',
            'company_id' => 1,
        ]);

        $visaType = SaudiVisaType::create([
            'company_id' => 1,
            'name' => 'Standard Tourist Visa',
            'price' => 400.00,
            'isActive' => true,
        ]);

        $application = SaudiVisaApplication::create([
            'company_id' => 1,
            'saudi_visa_type_id' => $visaType->id,
            'full_name' => 'Jane Smith',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'phone' => '+971507654321',
            'nationality' => 'Canada',
            'passport_path' => 'visas/saudi/passports/mock.pdf',
            'price' => 400.00,
            'payment_status' => 'paid',
            'status' => 'pending',
            'order_id' => 'ORDSV-MOCK-JANE',
        ]);

        // 1. Check Manager applications list
        $response = $this->actingAs($managerUser)
            ->get(route('manager.orders.saudi-visa'));
        $response->assertStatus(200);
        $response->assertSee('Jane Smith');

        // 2. Check Manager detail view
        $response = $this->actingAs($managerUser)
            ->get(route('manager.orders.saudi-visa.show', $application));
        $response->assertStatus(200);
        $response->assertSee('Jane');
        $response->assertSee('Smith');

        // 3. Update Status and Add Notes
        $statusPayload = [
            'status' => 'approved',
            'internal_notes' => 'Everything is correct. Verified passport copy.',
        ];

        $response = $this->actingAs($managerUser)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.orders.saudi-visa.status', $application), $statusPayload);

        $response->assertRedirect(route('manager.orders.saudi-visa.show', $application));
        
        $application->refresh();
        $this->assertEquals('approved', $application->status);
        $this->assertEquals('Everything is correct. Verified passport copy.', $application->internal_notes);
    }
}

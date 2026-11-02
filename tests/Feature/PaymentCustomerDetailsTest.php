<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\UmrahDeparture;
use App\Models\UmrahPackage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * What reaches Nomod as the customer. Nomod keeps one customer per email and reuses that
 * customer's saved phone, and Tabby / Tamara check the client against their own account:
 * Pay Online payments taken with the agency's own email went to Tabby with the agency's
 * phone and were declined. Http::fake stubs the Nomod API.
 */
class PaymentCustomerDetailsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        config(['nomod.api_key' => 'test_key', 'nomod.own_email_domains' => ['aynalamirtourism.com', 'gotrips.ai'], 'nomod.own_phones' => ['971543651065']]);
        Http::fake(['*/v1/checkout' => Http::response(['id' => 'chk_test', 'url' => 'https://pay.test/chk_test', 'status' => 'created'], 200)]);
    }

    private function payOnline(array $overrides = [])
    {
        return $this->withoutMiddleware(VerifyCsrfToken::class)->postJson(route('agent.pay'), $overrides + [
            'client_name' => 'Sk Sahil',
            'client_email' => 'sahil@example.com',
            'client_phone' => '+971543110729',
            'service' => 'TICKETS SERVICES',
            'amount' => 2576.03,
        ]);
    }

    public function test_pay_online_refuses_the_agency_email_for_the_client(): void
    {
        $this->payOnline(['client_email' => 'INFO@AYNALAMIRTOURISM.COM'])
            ->assertStatus(422)->assertJsonValidationErrors('client_email');
        $this->payOnline(['client_email' => 'ops@gotrips.ai'])
            ->assertStatus(422)->assertJsonValidationErrors('client_email');
        Http::assertNothingSent();
        $this->assertDatabaseMissing('agent_bookings', ['client_email' => 'INFO@AYNALAMIRTOURISM.COM']);
    }

    public function test_pay_online_refuses_the_agency_phone_for_the_client(): void
    {
        $this->payOnline(['client_phone' => '+971 54 365 1065'])
            ->assertStatus(422)->assertJsonValidationErrors('client_phone');
        Http::assertNothingSent();
    }

    public function test_pay_online_requires_an_email(): void
    {
        $this->payOnline(['client_email' => ''])->assertStatus(422)->assertJsonValidationErrors('client_email');
        Http::assertNothingSent();
    }

    public function test_pay_online_sends_the_clients_own_details_to_nomod(): void
    {
        $this->payOnline()->assertOk()->assertJson(['success' => true, 'checkout_url' => 'https://pay.test/chk_test']);

        Http::assertSent(fn (HttpRequest $r) => str_ends_with($r->url(), '/v1/checkout')
            && $r['customer']['first_name'] === 'Sk' && $r['customer']['last_name'] === 'Sahil'
            && $r['customer']['email'] === 'sahil@example.com' && $r['customer']['phone_number'] === '+971543110729');
    }

    public function test_umrah_checkout_sends_the_customer_name_to_nomod(): void
    {
        $package = UmrahPackage::create([
            'company_id' => 1, 'title' => 'Test Umrah', 'category' => 'air', 'price' => 1000, 'currency' => 'AED',
            'description' => 'Test', 'duration' => '7 Nights', 'isActive' => 1, 'sortOrder' => 0,
            'createdBy' => 'test', 'createdDate' => now(),
        ]);
        $date = now()->addDays(20)->toDateString();
        UmrahDeparture::create(['umrah_package_id' => $package->id, 'departure_date' => $date, 'seats_available' => 10, 'seats_booked' => 0, 'status' => 'available']);

        $this->withoutMiddleware(VerifyCsrfToken::class)->postJson(route('going-saudi.checkout', $package->id), [
            'departure_date' => $date, 'adults' => 1, 'children' => 0, 'infants' => 0, 'payment_gateway' => 'Tabby',
            'customer_name' => 'Amina Rahman', 'customer_email' => 'amina@example.com', 'customer_phone' => '+971501112233',
            'passenger_details' => [['name' => 'Amina Rahman', 'passport_copy' => 'uploads/passports/test.jpg']],
        ])->assertOk()->assertJson(['success' => true]);

        Http::assertSent(fn (HttpRequest $r) => str_ends_with($r->url(), '/v1/checkout')
            && $r['customer']['first_name'] === 'Amina' && $r['customer']['last_name'] === 'Rahman');
    }
}

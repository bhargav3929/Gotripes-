<?php

namespace Tests\Feature\TourPackages;

use App\Models\NomodTransaction;
use App\Models\TenantCommission;
use App\Models\TourPackageBooking;
use App\Models\TravelPackage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TourPackageBookingFlowTest extends TestCase
{
    use DatabaseTransactions, TourPackageFixture;

    protected function setUp(): void
    {
        parent::setUp();
        config(['nomod.api_key' => 'test-key', 'nomod.base_url' => 'https://api.nomod.test']);
        Mail::fake();
    }

    private function host($company, string $path): string
    {
        return 'http://' . $company->subdomain . '.gotrips.test' . $path;
    }

    private function customer(array $over = []): array
    {
        return array_merge($this->input(), [
            'customer_name' => 'Priya Nair', 'customer_email' => 'priya@example.com',
            'customer_phone' => '+971 50 123 4567', 'nationality' => 'India', 'accept_terms' => 1,
        ], $over);
    }

    public function test_wizard_page_renders_with_selling_prices_only(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);

        $html = $this->get($this->host($company, "/tour-packages/{$p->id}/book"))->assertOk()->getContent();

        $this->assertStringContainsString('Georgia Highlights', $html);
        $this->assertStringContainsString('Dates &amp; visa', $html);
        $this->assertStringContainsString('"durations":[4,5,6]', $html);
        // Arabic guide is net 40 per day; the page may only show the 10%-margin price.
        $this->assertStringContainsString('"price":44', $html);
        $this->assertStringNotContainsString('"net_price"', $html);
        $this->assertStringNotContainsString('adult_net', $html);
    }

    public function test_detail_page_links_wizard_packages_to_the_wizard(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        $this->get($this->host($company, "/tour-packages/{$p->id}"))->assertOk()
            ->assertSee(route('tour-packages.book', $p->id, false));
    }

    public function test_non_wizard_and_inactive_packages_have_no_wizard(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company, ['package_type' => 'enquire']);
        $this->get($this->host($company, "/tour-packages/{$p->id}/book"))->assertNotFound();
        $q = $this->georgia($company, ['isActive' => false]);
        $this->get($this->host($company, "/tour-packages/{$q->id}/book"))->assertNotFound();
    }

    public function test_quote_endpoint_never_returns_net_figures(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);

        $res = $this->postJson($this->host($company, "/tour-packages/{$p->id}/quote"), $this->input())->assertOk();

        $this->assertSame(3696, (int) $res->json('quote.total'));
        $this->assertNull($res->json('quote.net_total'));
        $this->assertNull($res->json('quote.net_lines'));
        $this->assertStringNotContainsString('"net"', $res->getContent());
    }

    public function test_quote_endpoint_explains_bad_choices(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        $this->postJson($this->host($company, "/tour-packages/{$p->id}/quote"), $this->input(['hotel_stars' => 3, 'meal_plan' => 'full_board']))
            ->assertStatus(422)->assertJsonPath('errors.meal_plan', 'That meal plan is not offered by the 3-star hotel.');
    }

    public function test_visa_endpoint(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        $this->getJson($this->host($company, "/tour-packages/{$p->id}/visa?nationality=India"))
            ->assertOk()->assertJson(['requirement' => 'evisa', 'tone' => 'warn', 'processing_days' => 5]);
        $this->getJson($this->host($company, "/tour-packages/{$p->id}/visa?nationality=Japan"))
            ->assertOk()->assertJson(['requirement' => 'visa_free', 'tone' => 'ok']);
    }

    public function test_checkout_charges_the_server_price_whatever_the_browser_sends(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        Http::fake(['api.nomod.test/v1/checkout' => Http::response(['id' => 'chk_1', 'url' => 'https://pay.nomod.test/chk_1', 'status' => 'created'])]);

        $res = $this->postJson($this->host($company, "/tour-packages/{$p->id}/checkout"),
            $this->customer(['total' => 1, 'amount' => 1, 'price' => 1]))->assertOk();

        $booking = TourPackageBooking::withoutCompanyScope()->latest('id')->first();
        $this->assertSame('ORDTPK' . $booking->id, $res->json('order_id'));
        $this->assertSame('https://pay.nomod.test/chk_1', $res->json('checkout_url'));
        $this->assertSame(3696.0, $booking->total);
        $this->assertSame(3360.0, $booking->net_total);
        $this->assertSame($company->id, $booking->company_id);
        $this->assertSame('evisa', $booking->visa_requirement);
        $this->assertSame('pending', $booking->payment_status);
        Http::assertSent(fn ($r) => str_ends_with($r->url(), '/v1/checkout') && $r['amount'] === '3696.00');

        $tx = NomodTransaction::where('order_id', $booking->order_id)->first();
        $this->assertSame('tour_package', $tx->booking_type);
        $this->assertSame($company->id, $tx->company_id);
    }

    public function test_checkout_requires_customer_details_and_terms(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        $this->postJson($this->host($company, "/tour-packages/{$p->id}/checkout"), $this->customer([
            'customer_email' => 'not-an-email', 'accept_terms' => 0, 'nationality' => 'Atlantis', 'customer_phone' => 'call me',
        ]))->assertStatus(422)->assertJsonValidationErrors(['customer_email', 'accept_terms', 'nationality', 'customer_phone']);
        $this->assertSame(0, TourPackageBooking::withoutCompanyScope()->where('package_id', $p->id)->count());
    }

    public function test_paid_callback_marks_booking_paid_emails_and_records_commission(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        Http::fake([
            'api.nomod.test/v1/checkout' => Http::response(['id' => 'chk_2', 'url' => 'https://pay.nomod.test/chk_2', 'status' => 'created']),
            'api.nomod.test/v1/checkout/chk_2' => Http::response(['id' => 'chk_2', 'status' => 'paid']),
        ]);
        $order = $this->postJson($this->host($company, "/tour-packages/{$p->id}/checkout"), $this->customer())->json('order_id');

        // Nomod sends the customer back to the main domain, not the tenant's.
        $this->get('http://127.0.0.1/payment/nomod/success?reference_id=' . $order)->assertOk();

        $booking = TourPackageBooking::withoutCompanyScope()->where('order_id', $order)->first();
        $this->assertSame('paid', $booking->payment_status);

        $commission = TenantCommission::where('source_type', 'tour_package_booking')->where('source_id', $booking->id)->first();
        $this->assertNotNull($commission, 'A paid tour booking earns the tenant its commission like every other service.');
        $this->assertSame($company->id, (int) $commission->company_id);
        $this->assertEqualsWithDelta(369.6, (float) $commission->commission_amount, 0.01);

        Mail::assertSent(\App\Mail\CustomerPaymentConfirmationMail::class, fn ($m) => $m->hasTo('priya@example.com'));
    }

    public function test_late_failure_callback_does_not_undo_a_paid_booking(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        Http::fake([
            'api.nomod.test/v1/checkout' => Http::response(['id' => 'chk_3', 'url' => 'https://pay.nomod.test/chk_3']),
            'api.nomod.test/v1/checkout/chk_3' => Http::sequence()->push(['status' => 'paid'])->push(['status' => 'expired']),
        ]);
        $order = $this->postJson($this->host($company, "/tour-packages/{$p->id}/checkout"), $this->customer())->json('order_id');
        $this->get('http://127.0.0.1/payment/nomod/success?reference_id=' . $order);
        $this->get('http://127.0.0.1/payment/nomod/failure?reference_id=' . $order);

        $this->assertSame('paid', TourPackageBooking::withoutCompanyScope()->where('order_id', $order)->value('payment_status'));
    }

    public function test_one_tenant_cannot_book_another_tenants_package(): void
    {
        $a = $this->tenant();
        $b = $this->tenant();
        $pa = $this->georgia($a);

        $this->get($this->host($b, "/tour-packages/{$pa->id}/book"))->assertNotFound();
        $this->postJson($this->host($b, "/tour-packages/{$pa->id}/quote"), $this->input())->assertNotFound();
    }

    public function test_many_live_quotes_do_not_use_up_the_checkout_limit(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        Http::fake(['api.nomod.test/v1/checkout' => Http::response(['id' => 'chk_q', 'url' => 'https://pay.nomod.test/chk_q'])]);

        // A customer changing options triggers a quote each time.
        for ($i = 0; $i < 30; $i++) {
            $this->postJson($this->host($company, "/tour-packages/{$p->id}/quote"), $this->input())->assertOk();
        }
        $this->postJson($this->host($company, "/tour-packages/{$p->id}/checkout"), $this->customer())->assertOk();
    }
}

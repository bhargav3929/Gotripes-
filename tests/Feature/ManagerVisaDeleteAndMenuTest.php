<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\SaudiVisaApplication;
use App\Models\SaudiVisaType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Manager fixes from Amer's 26 Nov messages: deleting a Saudi visa type really
 * removes it, and Study Abroad's menu highlight can be switched off.
 */
class ManagerVisaDeleteAndMenuTest extends TestCase
{
    use DatabaseTransactions;

    private function company(): Company
    {
        $slug = 'mvd' . Str::lower(Str::random(8));
        return Company::create([
            'name' => 'Visa Test ' . $slug, 'slug' => $slug, 'subdomain' => $slug, 'email' => $slug . '@t.test',
            'is_active' => true, 'plan' => 'pro', 'subscription_ends_at' => now()->addYear(),
            'features' => ['visas'], 'commission_type' => 'percentage', 'commission_value' => 10,
        ]);
    }

    private function url(Company $c, string $path): string
    {
        return 'http://' . $c->subdomain . '.gotrips.test' . $path;
    }

    private function manager(Company $c): User
    {
        $u = User::create(['name' => 'Owner', 'email' => 'own-' . Str::random(8) . '@t.test', 'password' => bcrypt('x')]);
        $u->forceFill(['role' => 'company_owner', 'company_id' => $c->id])->save();
        return $u;
    }

    public function test_deleting_a_visa_type_removes_it_and_keeps_past_applications_readable(): void
    {
        $c = $this->company();
        $type = SaudiVisaType::withoutCompanyScope()->create(['company_id' => $c->id, 'name' => 'Old Tourist Visa ' . Str::random(4), 'price' => 450, 'isActive' => true]);
        $app = SaudiVisaApplication::withoutCompanyScope()->create([
            'company_id' => $c->id, 'saudi_visa_type_id' => $type->id, 'full_name' => 'A B', 'first_name' => 'A', 'last_name' => 'B',
            'email' => 'a@b.test', 'phone' => '+971500000000', 'nationality' => 'India', 'price' => 450, 'passport_path' => 'p.jpg', 'photo_path' => 'f.jpg',
            'payment_status' => 'paid', 'status' => 'pending', 'order_id' => 'ORDSV-T-' . Str::random(5),
        ]);

        $this->actingAs($this->manager($c))
            ->delete($this->url($c, '/manager/saudi-visas/' . $type->id))
            ->assertRedirect();

        $this->get($this->url($c, '/manager/saudi-visas'))->assertOk()->assertSee('deleted.')->assertSee('Visa Types (0)')->assertDontSee('visa-block-' . $type->id);
        $this->assertSoftDeleted('tbl_saudi_visa_types', ['id' => $type->id]);
        $this->assertSame($type->name, SaudiVisaApplication::withoutCompanyScope()->find($app->id)->visaType->name);
    }

    public function test_study_abroad_is_in_menu_highlights_and_on_until_switched_off(): void
    {
        $this->assertArrayHasKey('study_abroad', \App\Http\Controllers\Manager\SettingsController::MENU_FLASH_ITEMS);

        $c = $this->company();
        $this->actingAs($this->manager($c))->get($this->url($c, '/manager/settings/preferences'))
            ->assertOk()->assertSee('Study Abroad')->assertSee('name="flash_study_abroad" value="1" checked', false);

        $c->setSetting('menu_flash', ['study_abroad' => false]);
        $c->save();
        $this->get($this->url($c, '/manager/settings/preferences'))
            ->assertOk()->assertDontSee('name="flash_study_abroad" value="1" checked', false);
    }
}

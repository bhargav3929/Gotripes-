<?php

namespace Tests\Feature\TenantTheme;

use App\Models\SuperAdminAuditLog;
use App\Models\ThemeDeployment;
use App\Models\User;
use App\Services\TenantTheme\ThemeDeployer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SuperAdminThemeTest extends ThemeTestCase
{
    private function superAdmin(): User
    {
        $user = User::create([
            'name'     => 'Platform Admin',
            'email'    => 'sa-' . Str::random(8) . '@gotrips.test',
            'password' => bcrypt('x'),
        ]);
        $user->forceFill(['is_super_admin' => true])->save();
        return $user;
    }

    private function upload(string $zip): UploadedFile
    {
        return new UploadedFile($zip, 'theme.zip', 'application/zip', null, true);
    }

    public function test_page_requires_a_super_admin(): void
    {
        $company = $this->company(); $company->save();

        $this->get(route('superadmin.companies.theme.show', $company))->assertRedirect(route('superadmin.login'));

        $tenantOwner = User::create(['name' => 'Owner', 'email' => 'own-' . Str::random(6) . '@t.test', 'password' => bcrypt('x')]);
        $tenantOwner->forceFill(['role' => 'company_owner', 'company_id' => $company->id])->save();
        $this->actingAs($tenantOwner)->get(route('superadmin.companies.theme.show', $company))->assertRedirect(route('superadmin.login'));
        $this->actingAs($tenantOwner)->post(route('superadmin.companies.theme.wipe', $company))->assertRedirect(route('superadmin.login'));
    }

    public function test_page_renders_for_a_super_admin(): void
    {
        $company = $this->company(); $company->save();

        $this->actingAs($this->superAdmin())
            ->get(route('superadmin.companies.theme.show', $company))
            ->assertOk()
            ->assertSee('Developer Theme')
            ->assertSee(route('tenant-themes.webhook', $company->id))
            ->assertSee('No deployments yet');
    }

    public function test_company_page_links_to_the_theme_page(): void
    {
        $company = $this->company(); $company->save();
        $this->actingAs($this->superAdmin())
            ->get(route('superadmin.companies.show', $company))
            ->assertOk()
            ->assertSee(route('superadmin.companies.theme.show', $company));
    }

    public function test_settings_are_saved_validated_and_audited(): void
    {
        $company = $this->company(['name' => 'Settings Co']); $company->forceFill(['theme_enabled' => false, 'theme_repo' => null])->save();
        $admin = $this->superAdmin();

        $this->actingAs($admin)->put(route('superadmin.companies.theme.update', $company), [
            'theme_enabled' => '1', 'theme_repo' => 'gotrips-themes/settings-co', 'theme_branch' => 'live',
        ])->assertSessionHas('success');

        $company->refresh();
        $this->assertTrue($company->theme_enabled);
        $this->assertSame('gotrips-themes/settings-co', $company->theme_repo);
        $this->assertSame('live', $company->theme_branch);
        $this->assertTrue(SuperAdminAuditLog::where('action', 'company.theme.settings')->where('target_id', $company->id)->exists());

        $this->actingAs($admin)->put(route('superadmin.companies.theme.update', $company), [
            'theme_repo' => 'not a repo; rm -rf', 'theme_branch' => 'main',
        ])->assertSessionHasErrors('theme_repo');
        $this->actingAs($admin)->put(route('superadmin.companies.theme.update', $company), [
            'theme_repo' => 'a/b', 'theme_branch' => '../../etc',
        ])->assertSessionHasErrors('theme_branch');
        $this->actingAs($admin)->put(route('superadmin.companies.theme.update', $company), [
            'theme_repo' => '../..', 'theme_branch' => 'main',
        ])->assertSessionHasErrors('theme_repo');
        $this->assertSame('gotrips-themes/settings-co', $company->fresh()->theme_repo, 'Refused input changes nothing.');

        // Unticked switch turns it off.
        $this->actingAs($admin)->put(route('superadmin.companies.theme.update', $company), ['theme_branch' => 'main'])
            ->assertSessionHas('success');
        $this->assertFalse($company->fresh()->theme_enabled);
        $this->assertSame('gotrips-themes/settings-co', $company->fresh()->theme_repo, 'Missing field keeps the repository.');

        // A blank field clears it.
        $this->actingAs($admin)->put(route('superadmin.companies.theme.update', $company), ['theme_repo' => '', 'theme_branch' => 'main']);
        $this->assertNull($company->fresh()->theme_repo);
    }

    public function test_secret_can_be_generated_and_rotated(): void
    {
        $company = $this->company(); $company->save();
        $admin = $this->superAdmin();

        $this->actingAs($admin)->post(route('superadmin.companies.theme.secret', $company))->assertSessionHas('success');
        $first = $company->fresh()->theme_webhook_secret;
        $this->assertSame(48, strlen($first));

        $this->actingAs($admin)->post(route('superadmin.companies.theme.secret', $company));
        $this->assertNotSame($first, $company->fresh()->theme_webhook_secret);

        $raw = \DB::table('companies')->where('id', $company->id)->value('theme_webhook_secret');
        $this->assertStringNotContainsString($company->fresh()->theme_webhook_secret, $raw, 'Secret must be encrypted at rest.');
    }

    public function test_upload_publishes_and_bad_upload_is_explained(): void
    {
        $company = $this->company(); $company->save();
        $admin = $this->superAdmin();

        $this->actingAs($admin)->post(route('superadmin.companies.theme.upload', $company), [
            'theme_zip' => $this->upload(ThemeZip::valid()),
        ])->assertSessionHas('success');
        $live = $company->fresh()->activeThemeDeployment;
        $this->assertSame('upload', $live->source);
        $this->assertStringStartsWith('admin:', $live->triggered_by);

        $this->actingAs($admin)->post(route('superadmin.companies.theme.upload', $company), [
            'theme_zip' => $this->upload(ThemeZip::make(['theme.css' => 'x', 'assets/a.php' => '<?php'])),
        ])->assertSessionHas('error', fn ($m) => str_contains($m, 'server-side'));
        $this->assertSame($live->id, $company->fresh()->theme_deployment_id, 'A refused upload leaves the live theme alone.');
    }

    public function test_deploy_latest_from_github(): void
    {
        $company = $this->company(); $company->save();
        Http::fake([
            "api.github.test/repos/{$company->theme_repo}/commits/main" => Http::response(str_repeat('ab', 20), 200),
            "api.github.test/repos/{$company->theme_repo}/zipball/*"    => Http::response(file_get_contents(ThemeZip::valid('w/')), 200),
        ]);

        $this->actingAs($this->superAdmin())->post(route('superadmin.companies.theme.deploy', $company))
            ->assertSessionHas('success');

        $this->assertSame(str_repeat('ab', 20), $company->fresh()->activeThemeDeployment->commit_sha);
    }

    public function test_rollback_and_wipe(): void
    {
        $company = $this->company(); $company->save();
        $admin = $this->superAdmin();
        $deployer = app(ThemeDeployer::class);
        $first = $deployer->deployFromZip($company, ThemeZip::make(['theme.css' => 'a']), ['source' => 'upload']);
        $deployer->deployFromZip($company->fresh(), ThemeZip::make(['theme.css' => 'b']), ['source' => 'upload']);

        $this->actingAs($admin)->get(route('superadmin.companies.theme.show', $company))->assertSee('Make live');
        $this->actingAs($admin)->post(route('superadmin.companies.theme.rollback', [$company, $first]))->assertSessionHas('success');
        $this->assertSame($first->id, $company->fresh()->theme_deployment_id);

        $this->actingAs($admin)->post(route('superadmin.companies.theme.wipe', $company))->assertSessionHas('success');
        $this->assertNull($company->fresh()->theme_deployment_id);
        $this->assertDirectoryDoesNotExist($this->themePath((string) $company->id));
        $this->assertTrue(SuperAdminAuditLog::where('action', 'company.theme.wipe')->where('target_id', $company->id)->exists());
    }

    public function test_rollback_to_another_companys_version_is_refused(): void
    {
        $a = $this->company(); $a->save();
        $b = $this->company(); $b->save();
        $bDeploy = app(ThemeDeployer::class)->deployFromZip($b, ThemeZip::make(['theme.css' => 'b']), ['source' => 'upload']);

        $this->actingAs($this->superAdmin())->post(route('superadmin.companies.theme.rollback', [$a, $bDeploy]))
            ->assertSessionHas('error');
        $this->assertNull($a->fresh()->theme_deployment_id);
    }

    public function test_deleting_a_company_removes_its_theme(): void
    {
        $company = $this->company(); $company->save();
        app(ThemeDeployer::class)->deployFromZip($company, ThemeZip::valid(), ['source' => 'upload']);
        $this->assertDirectoryExists($this->themePath((string) $company->id));

        $this->actingAs($this->superAdmin())->delete(route('superadmin.companies.destroy', $company));

        $this->assertDirectoryDoesNotExist($this->themePath((string) $company->id));
        $trashed = \App\Models\Company::withTrashed()->find($company->id);
        $this->assertFalse($trashed->theme_enabled);
        $this->assertNull($trashed->theme_webhook_secret);
    }
}

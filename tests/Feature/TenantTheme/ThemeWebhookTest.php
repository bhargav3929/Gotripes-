<?php

namespace Tests\Feature\TenantTheme;

use App\Models\Company;
use App\Models\ThemeDeployment;
use Illuminate\Support\Facades\Http;

class ThemeWebhookTest extends ThemeTestCase
{
    private const SHA = 'f00dbabe0123456789abcdef0123456789abcdef';

    private function setUpCompany(array $theme = [], ?string $zip = null): Company
    {
        $company = $this->company();
        $company->forceFill(array_merge(['theme_webhook_secret' => 'secret-' . $company->slug], $theme))->save();
        Http::fake([
            "api.github.test/repos/{$company->theme_repo}/zipball/*" => Http::response(file_get_contents($zip ?? ThemeZip::valid('wrap/')), 200),
        ]);
        return $company;
    }

    private function push(Company $company, array $overrides = [], ?string $secret = null, string $event = 'push')
    {
        $payload = json_encode(array_replace_recursive([
            'ref'        => 'refs/heads/main',
            'after'      => self::SHA,
            'deleted'    => false,
            'repository' => ['full_name' => $company->theme_repo],
            'pusher'     => ['name' => 'tenant-dev'],
        ], $overrides));
        $sig = 'sha256=' . hash_hmac('sha256', $payload, $secret ?? $company->theme_webhook_secret);

        return $this->call('POST', "/api/tenant-themes/{$company->id}/webhook", [], [], [], [
            'CONTENT_TYPE'              => 'application/json',
            'HTTP_X_HUB_SIGNATURE_256'  => $sig,
            'HTTP_X_GITHUB_EVENT'       => $event,
        ], $payload);
    }

    public function test_signed_push_to_tracked_branch_deploys(): void
    {
        $company = $this->setUpCompany();

        $this->push($company)->assertOk()->assertJson(['ok' => true]);

        $live = $company->fresh()->activeThemeDeployment;
        $this->assertSame(self::SHA, $live->commit_sha);
        $this->assertSame('github:tenant-dev', $live->triggered_by);
        $this->assertFileExists($this->themePath($live->directory . '/theme.css'));
    }

    public function test_wrong_signature_is_rejected(): void
    {
        $company = $this->setUpCompany();
        $this->push($company, [], 'not-the-secret')->assertStatus(401);
        $this->assertNull($company->fresh()->theme_deployment_id);
    }

    public function test_another_companys_secret_cannot_deploy_here(): void
    {
        $victim = $this->setUpCompany();
        $attacker = $this->setUpCompany();

        $this->push($victim, ['repository' => ['full_name' => $victim->theme_repo]], $attacker->theme_webhook_secret)
            ->assertStatus(401);
        $this->assertNull($victim->fresh()->theme_deployment_id);
    }

    public function test_no_secret_configured_is_refused(): void
    {
        $company = $this->setUpCompany();
        $secret = $company->theme_webhook_secret;
        $company->forceFill(['theme_webhook_secret' => null])->save();
        $this->push($company->fresh(), [], $secret)->assertStatus(403);
    }

    public function test_ping_is_acknowledged(): void
    {
        $company = $this->setUpCompany();
        $this->push($company, [], null, 'ping')->assertOk()->assertJsonFragment(['ok' => true]);
        $this->assertSame(0, ThemeDeployment::where('company_id', $company->id)->count());
    }

    public function test_other_branches_and_events_are_ignored(): void
    {
        $company = $this->setUpCompany();
        $this->push($company, ['ref' => 'refs/heads/feature-x'])->assertStatus(202);
        $this->push($company, [], null, 'issues')->assertStatus(202);
        $this->push($company, ['deleted' => true])->assertStatus(202);
        $this->assertNull($company->fresh()->theme_deployment_id);
    }

    public function test_push_from_a_different_repository_is_refused(): void
    {
        $company = $this->setUpCompany();
        $this->push($company, ['repository' => ['full_name' => 'someone/else']])->assertStatus(422);
        $this->assertNull($company->fresh()->theme_deployment_id);
    }

    public function test_switched_off_theme_records_the_push_but_does_not_deploy(): void
    {
        $company = $this->setUpCompany(['theme_enabled' => false]);

        $this->push($company)->assertStatus(409);

        $this->assertNull($company->fresh()->theme_deployment_id);
        $this->assertSame('failed', ThemeDeployment::where('company_id', $company->id)->latest('id')->value('status'));
        Http::assertNothingSent();
    }

    public function test_deactivated_company_does_not_deploy(): void
    {
        $company = $this->setUpCompany();
        $company->forceFill(['is_active' => false])->save();
        $this->push($company->fresh())->assertStatus(409);
        $this->assertNull($company->fresh()->theme_deployment_id);
    }

    public function test_redelivery_of_the_live_commit_does_not_redeploy(): void
    {
        $company = $this->setUpCompany();
        $this->push($company)->assertOk();
        $count = ThemeDeployment::where('company_id', $company->id)->count();

        $this->push($company->fresh())->assertOk()->assertJsonFragment(['message' => 'Commit f00dbab is already live.']);
        $this->assertSame($count, ThemeDeployment::where('company_id', $company->id)->count());
    }

    public function test_refused_theme_returns_the_reason_to_github(): void
    {
        $company = $this->setUpCompany([], ThemeZip::make(['assets/x.php' => '<?php']));

        $this->push($company)->assertStatus(422)->assertJsonFragment(['ok' => false]);
        $this->assertStringContainsString('server-side', $this->push($company)->json('message'));
    }

    public function test_unknown_company_is_404(): void
    {
        $this->call('POST', '/api/tenant-themes/999999999/webhook', [], [], [], ['CONTENT_TYPE' => 'application/json'], '{}')
            ->assertStatus(404);
    }
}

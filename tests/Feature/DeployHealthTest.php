<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\DeployHealth;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * The Super Admin dashboard must say so, loudly, when the live code is not
 * what is on GitHub. Two months of failed deploys went unnoticed because
 * nothing did.
 */
class DeployHealthTest extends TestCase
{
    use DatabaseTransactions;

    private string $file;
    private ?string $saved = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->file = storage_path(DeployHealth::STATUS_FILE);
        $this->saved = is_file($this->file) ? file_get_contents($this->file) : null;
        @unlink($this->file);
        Cache::forget('deploy-health:edited-files');
    }

    protected function tearDown(): void
    {
        @unlink($this->file);
        if ($this->saved !== null) {
            file_put_contents($this->file, $this->saved);
        }
        Cache::forget('deploy-health:edited-files');
        parent::tearDown();
    }

    private function writeStatus(array $data): void
    {
        file_put_contents($this->file, json_encode(array_merge([
            'commit' => 'abc1234', 'at' => now()->toIso8601String(), 'files' => [],
        ], $data)));
    }

    private function dashboard()
    {
        $admin = User::create(['name' => 'SA', 'email' => 'sa-' . Str::random(8) . '@t.test', 'password' => bcrypt('x')]);
        $admin->forceFill(['is_super_admin' => true])->save();
        return $this->actingAs($admin)->get(route('superadmin.dashboard'))->assertOk();
    }

    public function test_no_warning_when_nothing_is_recorded(): void
    {
        $this->assertNull(DeployHealth::problem());
        $this->dashboard()->assertDontSee('deploy-health-warning', false);
    }

    public function test_no_warning_after_a_good_deploy(): void
    {
        $this->writeStatus(['state' => 'ok', 'message' => 'Deployed abc1234.']);
        Cache::put('deploy-health:edited-files', [], 300);

        $this->assertNull(DeployHealth::problem());
        $this->dashboard()->assertDontSee('deploy-health-warning', false);
    }

    public function test_failed_deploy_shows_a_warning_with_the_reason_and_files(): void
    {
        $this->writeStatus([
            'state'   => 'failed',
            'message' => '2 file(s) were edited on the server instead of in git',
            'files'   => ['resources/views/header.blade.php', 'routes/web.php'],
        ]);
        Cache::put('deploy-health:edited-files', [], 300);

        $this->dashboard()
            ->assertSee('deploy-health-warning', false)
            ->assertSee('The live site is not in sync with GitHub.')
            ->assertSee('2 file(s) were edited on the server instead of in git')
            ->assertSee('<code>resources/views/header.blade.php</code>', false)
            ->assertSee('abc1234');
    }

    public function test_server_behind_github_is_reported(): void
    {
        $this->writeStatus(['state' => 'behind', 'message' => 'GitHub main has commits that are not deployed yet. Run deploy.sh.']);
        Cache::put('deploy-health:edited-files', [], 300);

        $this->assertSame('behind', DeployHealth::problem()['state']);
    }

    public function test_files_edited_since_the_last_deploy_override_a_good_status(): void
    {
        $this->writeStatus(['state' => 'ok', 'message' => 'Deployed abc1234.']);
        Cache::put('deploy-health:edited-files', ['app/Http/Kernel.php'], 300);

        $problem = DeployHealth::problem();
        $this->assertSame('drift', $problem['state']);
        $this->assertSame(['app/Http/Kernel.php'], $problem['files']);
    }

    public function test_file_names_are_escaped(): void
    {
        $this->writeStatus(['state' => 'failed', 'message' => 'x', 'files' => ['<script>alert(1)</script>.php']]);
        Cache::put('deploy-health:edited-files', [], 300);

        $this->dashboard()->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;', false);
    }

    public function test_git_is_not_consulted_where_deploy_sh_has_never_run(): void
    {
        $this->assertSame([], DeployHealth::editedFiles(), 'No status file means this is not the deployed server.');
    }
}

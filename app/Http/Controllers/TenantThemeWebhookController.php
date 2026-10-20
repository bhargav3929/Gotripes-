<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ThemeDeployment;
use App\Services\TenantTheme\ThemeDeployer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * GitHub "push" webhook for a tenant's theme repository.
 *
 * Each company has its own secret, so a request signed for one company can
 * never deploy to another. The JSON reply is what the tenant's developer sees
 * under Settings → Webhooks → Recent deliveries in GitHub, so it says plainly
 * whether the push went live and, if not, why.
 */
class TenantThemeWebhookController extends Controller
{
    public function __invoke(Request $request, int $company, ThemeDeployer $deployer): JsonResponse
    {
        $company = Company::find($company);
        if (!$company) {
            return $this->reply(404, 'Unknown company.');
        }

        $secret = $company->theme_webhook_secret;
        if (!$secret) {
            return $this->reply(403, 'No webhook secret is set for this company. Ask the platform admin to generate one.');
        }

        $signature = (string) $request->header('X-Hub-Signature-256', '');
        $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);
        if (!hash_equals($expected, $signature)) {
            return $this->reply(401, 'Invalid signature. The webhook secret in GitHub does not match the one on the platform.');
        }

        $event = (string) $request->header('X-GitHub-Event', '');
        if ($event === 'ping') {
            return $this->reply(200, 'Connected. Pushes to "' . $company->theme_branch . '" will deploy.');
        }
        if ($event !== 'push') {
            return $this->reply(202, "Ignored \"{$event}\" event; only pushes deploy.");
        }

        $payload = json_decode($request->getContent(), true) ?: [];
        $ref = (string) ($payload['ref'] ?? '');
        if ($ref !== 'refs/heads/' . $company->theme_branch) {
            return $this->reply(202, "Ignored push to {$ref}; only \"{$company->theme_branch}\" deploys.");
        }

        $repo = (string) ($payload['repository']['full_name'] ?? '');
        if (strcasecmp($repo, (string) $company->theme_repo) !== 0) {
            return $this->reply(422, "This webhook belongs to {$company->theme_repo}, but the push came from {$repo}.");
        }

        $sha = (string) ($payload['after'] ?? '');
        if (($payload['deleted'] ?? false) || !preg_match('/^[0-9a-f]{40}$/', $sha) || trim($sha, '0') === '') {
            return $this->reply(202, 'Ignored: the branch was deleted or the push has no commit.');
        }

        $pusher = 'github:' . substr((string) ($payload['pusher']['name'] ?? $payload['sender']['login'] ?? 'unknown'), 0, 60);

        if (!$company->is_active || !$company->theme_enabled) {
            ThemeDeployment::create([
                'company_id'   => $company->id,
                'source'       => 'github',
                'ref'          => $company->theme_branch,
                'commit_sha'   => $sha,
                'status'       => 'failed',
                'message'      => 'Push received but not deployed: the developer theme is switched off for this company.',
                'triggered_by' => $pusher,
            ]);
            return $this->reply(409, 'Not deployed: the developer theme is switched off for this company. Contact the platform admin.');
        }

        $live = $company->activeThemeDeployment;
        if ($live && $live->commit_sha === $sha && $live->isAvailable()) {
            return $this->reply(200, 'Commit ' . substr($sha, 0, 7) . ' is already live.', ['deployment_id' => $live->id]);
        }

        // GitHub gives up waiting after 10 seconds; a large theme can take
        // longer. Finish the deploy even if GitHub has hung up.
        ignore_user_abort(true);
        @set_time_limit(180);

        $deployment = $deployer->deployFromGitHub($company, $sha, $pusher);

        return $deployment->isSuccess()
            ? $this->reply(200, 'Deployed ' . substr($sha, 0, 7) . '. ' . $deployment->message, ['deployment_id' => $deployment->id])
            : $this->reply(422, 'Not deployed: ' . $deployment->message, ['deployment_id' => $deployment->id]);
    }

    private function reply(int $status, string $message, array $extra = []): JsonResponse
    {
        return response()->json(array_merge(['ok' => $status < 300, 'message' => $message], $extra), $status);
    }
}

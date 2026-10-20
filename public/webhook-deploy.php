<?php
// GitHub push webhook: deploys main by running deploy.sh.
//
// The secret lives in .env as DEPLOY_WEBHOOK_SECRET (it used to be written
// here, in git). This script only checks the signature and starts deploy.sh;
// all deploy steps, and the record of whether they worked, are in deploy.sh
// and storage/app/deploy-status.json (shown on the Super Admin dashboard).
// Until 21 Oct 2026 this file ran its own copy of the steps, ignored every
// failure and answered "Deploy completed", which hid two months of failed deploys.

$projectDir = realpath(__DIR__ . '/..');

function deploy_env(string $file, string $key): ?string
{
    foreach (@file($file, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
        if (preg_match('/^\s*' . preg_quote($key, '/') . '\s*=\s*(.*)\s*$/', $line, $m)) {
            return trim($m[1], " \t\"'");
        }
    }
    return null;
}

function reply(int $status, array $body): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($body);
    exit;
}

$secret = deploy_env($projectDir . '/.env', 'DEPLOY_WEBHOOK_SECRET');
if (!$secret) {
    reply(503, ['error' => 'DEPLOY_WEBHOOK_SECRET is not set in .env']);
}

$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
if (!hash_equals('sha256=' . hash_hmac('sha256', $payload, $secret), $signature)) {
    reply(403, ['error' => 'Invalid signature']);
}

if (($_SERVER['HTTP_X_GITHUB_EVENT'] ?? '') === 'ping') {
    reply(200, ['message' => 'pong']);
}

$data = json_decode($payload, true) ?: [];
if (($data['ref'] ?? '') !== 'refs/heads/main') {
    reply(200, ['message' => 'Skipped: not main', 'ref' => $data['ref'] ?? '']);
}

// GitHub waits 10 seconds at most; a deploy can take longer. Start it in the
// background and answer now. The outcome is in deploy-status.json and the
// Super Admin dashboard, not in this response.
putenv('HOME=/home/u705168859');
shell_exec('cd ' . escapeshellarg($projectDir) . ' && nohup bash deploy.sh webhook > /dev/null 2>&1 &');

reply(202, [
    'message' => 'Deploy started. Result: Super Admin dashboard, or storage/logs/deploy.log',
    'commit'  => substr($data['after'] ?? '', 0, 7),
]);

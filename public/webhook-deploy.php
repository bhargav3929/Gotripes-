<?php
// GitHub Webhook Auto-Deploy Handler
// Called by GitHub on every push to main branch.
// Verifies webhook signature then runs deploy.sh.

$secret = "c67eadc859951399d8b31ff63856164835bc06a4";

// Read raw POST body
$payload = file_get_contents("php://input");
$signature = $_SERVER["HTTP_X_HUB_SIGNATURE_256"] ?? "";

// Verify GitHub signature
$expected = "sha256=" . hash_hmac("sha256", $payload, $secret);
if (!hash_equals($expected, $signature)) {
    http_response_code(403);
    echo json_encode(["error" => "Invalid signature"]);
    exit;
}

// Parse payload
$data = json_decode($payload, true);

// Handle ping event (GitHub sends this when webhook is created/updated)
if (isset($_SERVER["HTTP_X_GITHUB_EVENT"]) && $_SERVER["HTTP_X_GITHUB_EVENT"] === "ping") {
    http_response_code(200);
    echo json_encode(["message" => "pong"]);
    exit;
}

// Only deploy on push to main branch
$ref = $data["ref"] ?? "";
if ($ref !== "refs/heads/main") {
    http_response_code(200);
    echo json_encode(["message" => "Skipped: not main branch", "ref" => $ref]);
    exit;
}

// Paths
$projectDir = realpath(__DIR__ . "/..");
$logFile = $projectDir . "/storage/logs/deploy.log";

$timestamp = date("Y-m-d H:i:s");
$pusher = $data["pusher"]["name"] ?? "unknown";
$commitMsg = isset($data["head_commit"]["message"]) ? substr($data["head_commit"]["message"], 0, 80) : "unknown";

$logEntry = "\n===== DEPLOY: {$timestamp} =====\n";
$logEntry .= "Pusher: {$pusher}\n";
$logEntry .= "Commit: {$commitMsg}\n\n";

// Set HOME so git can find credentials
putenv("HOME=/home/u705168859");

// Run deploy commands directly (more reliable than calling deploy.sh from PHP)
$commands = [
    "cd {$projectDir} && /usr/bin/git pull origin main 2>&1",
    "cd {$projectDir} && /usr/local/bin/composer dump-autoload --no-interaction 2>&1",
    "cd {$projectDir} && /usr/bin/php artisan route:clear 2>&1",
    "cd {$projectDir} && /usr/bin/php artisan config:clear 2>&1",
    "cd {$projectDir} && /usr/bin/php artisan cache:clear 2>&1",
    "cd {$projectDir} && /usr/bin/php artisan view:clear 2>&1",
    "cd {$projectDir} && /usr/bin/php artisan migrate --force 2>&1",
];

foreach ($commands as $cmd) {
    $output = shell_exec($cmd);
    $logEntry .= "> " . $cmd . "\n" . $output . "\n";
}

$logEntry .= "Deploy completed at " . date("Y-m-d H:i:s") . "\n";

file_put_contents($logFile, $logEntry, FILE_APPEND);

http_response_code(200);
echo json_encode([
    "success" => true,
    "message" => "Deploy completed",
    "pusher" => $pusher,
    "commit" => $commitMsg
]);

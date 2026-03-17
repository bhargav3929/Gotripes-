<?php
$secret = "f646c4db9f13d2bb86958e0a3fa63b5118e9d76a";
$signature = $_SERVER["HTTP_X_HUB_SIGNATURE_256"] ?? "";
$payload = file_get_contents("php://input");

// Verify GitHub signature
$expected = "sha256=" . hash_hmac("sha256", $payload, $secret);
if (!hash_equals($expected, $signature)) {
    http_response_code(403);
    die("Invalid signature");
}

// Handle ping event
$event = $_SERVER["HTTP_X_GITHUB_EVENT"] ?? "";
if ($event === "ping") {
    echo "pong";
    exit;
}

// Only deploy on push to main
$data = json_decode($payload, true);
if (($data["ref"] ?? "") !== "refs/heads/main") {
    die("Not main branch, skipping");
}

// Run deploy script in background
$output = shell_exec("bash ~/domains/gotrips.ai/public_html/deploy.sh 2>&1");

// Log the deployment
file_put_contents("deploy.log", date("Y-m-d H:i:s") . "\n" . $output . "\n---\n", FILE_APPEND);

echo "Deployed successfully";

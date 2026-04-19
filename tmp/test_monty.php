<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

$baseUrl = Config::get('montyesim.base_url');
$username = Config::get('montyesim.username');
$password = Config::get('montyesim.password');

echo "Base URL: $baseUrl\n";
echo "Username: $username\n";
echo "Password Length: " . strlen($password) . "\n";

try {
    $response = Http::timeout(30)->post("$baseUrl/Agent/login", [
        'username' => $username,
        'password' => $password,
    ]);

    echo "Status: " . $response->status() . "\n";
    echo "Body: " . $response->body() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

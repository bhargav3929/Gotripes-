<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h2>Booking Diagnostic</h2><pre>";

// Test 1: Check activitybookings table
echo "=== TEST 1: activitybookings table ===\n";
try {
    $count = DB::table('activitybookings')->count();
    echo "activitybookings: WORKS - $count rows\n";
} catch (\Exception $e) {
    echo "activitybookings: FAILED - " . $e->getMessage() . "\n";
}

// Test 2: Check mail config
echo "\n=== TEST 2: Mail Config ===\n";
echo "MAIL_MAILER: " . config('mail.default') . "\n";
echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "MAIL_ENCRYPTION: " . (config('mail.mailers.smtp.encryption') ?: 'NULL/EMPTY') . "\n";
echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "MAIL_FROM: " . config('mail.from.address') . "\n";

// Test 3: Check NomodTransaction table
echo "\n=== TEST 3: nomod_transactions table ===\n";
try {
    $count = DB::table('nomod_transactions')->count();
    echo "nomod_transactions: WORKS - $count rows\n";
} catch (\Exception $e) {
    echo "nomod_transactions: FAILED - " . $e->getMessage() . "\n";
}

// Test 4: Try sending a test email
echo "\n=== TEST 4: Test Email ===\n";
try {
    Illuminate\Support\Facades\Mail::raw('Test email from GoTrips diagnostic', function($message) {
        $message->to('test@test.com')
                ->subject('GoTrips Test')
                ->from(config('mail.from.address'), config('mail.from.name'));
    });
    echo "Email: SENT SUCCESSFULLY\n";
} catch (\Exception $e) {
    echo "Email: FAILED - " . $e->getMessage() . "\n";
}

echo "\nDONE. Delete this file!\n</pre>";

<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h2>Find Booking Table</h2><pre>";

try {
    // List ALL tables in MySQL database
    echo "=== ALL TABLES IN DATABASE ===\n";
    $tables = DB::select('SHOW TABLES');
    foreach ($tables as $table) {
        $name = array_values((array) $table)[0];
        echo "  $name\n";
    }

    // Try different case variations
    echo "\n=== TESTING BOOKING TABLE NAMES ===\n";
    $variations = ['activitybookings', 'ActivityBookings', 'activity_bookings', 'activityBookings'];
    foreach ($variations as $v) {
        try {
            $count = DB::select("SELECT COUNT(*) as cnt FROM `$v`");
            echo "  $v: WORKS - " . $count[0]->cnt . " rows\n";
        } catch (\Exception $e) {
            echo "  $v: NOT FOUND\n";
        }
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
echo "\nDONE. Delete this file!\n</pre>";

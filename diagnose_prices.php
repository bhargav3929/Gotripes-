<?php
/**
 * Diagnose why /activity/prices/ returns 500
 * Upload to public_html/public/ and visit: https://gotrips.ai/diagnose_prices.php
 * DELETE AFTER USE
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h2>Price Endpoint Diagnostic</h2><pre>";

try {
    // Test 1: Table name case sensitivity
    echo "=== TEST 1: Table Name Case Sensitivity ===\n";

    try {
        $count1 = DB::table('tbl_UAEActivities')->count();
        echo "tbl_UAEActivities (mixed case): WORKS - $count1 rows\n";
    } catch (\Exception $e) {
        echo "tbl_UAEActivities (mixed case): FAILED - " . $e->getMessage() . "\n";
    }

    try {
        $count2 = DB::table('tbl_uaeactivities')->count();
        echo "tbl_uaeactivities (lowercase): WORKS - $count2 rows\n";
    } catch (\Exception $e) {
        echo "tbl_uaeactivities (lowercase): FAILED - " . $e->getMessage() . "\n";
    }

    // Test 2: Check columns exist
    echo "\n=== TEST 2: Column Check for Activity ID 1 ===\n";
    $activity = DB::table('tbl_UAEActivities')->where('activityID', 1)->first();
    if ($activity) {
        $cols = ['activityPrice', 'activityChildPrice', 'activityTransactionCharges',
                 'dubaiPrice', 'abuDhabiPrice', 'fromAbuDhabiToDubai', 'emirates'];
        foreach ($cols as $col) {
            $exists = property_exists($activity, $col);
            $val = $exists ? $activity->$col : 'N/A';
            echo "  $col: " . ($exists ? "EXISTS (value: $val)" : "MISSING") . "\n";
        }
    } else {
        echo "  Activity ID 1 not found!\n";
    }

    // Test 3: Simulate exact controller logic
    echo "\n=== TEST 3: Simulate Controller Response ===\n";
    $activity = DB::table('tbl_UAEActivities')->where('activityID', 1)->first();
    if ($activity) {
        echo json_encode([
            'adultPrice' => (float) ($activity->activityPrice ?? 0),
            'childPrice' => ($activity->activityChildPrice !== null && $activity->activityChildPrice > 0)
                ? (float) $activity->activityChildPrice
                : (float) ($activity->activityPrice ?? 0),
            'txnCharges' => (float) ($activity->activityTransactionCharges ?? 0),
            'dubaiPrice' => (float) ($activity->dubaiPrice ?? 0),
            'abuDhabiPrice' => (float) ($activity->abuDhabiPrice ?? 0),
            'fromAbuDhabiToDubai' => (float) ($activity->fromAbuDhabiToDubai ?? 0),
            'emirates' => (float) ($activity->emirates ?? 0),
            'taxPercent' => 5.0,
        ], JSON_PRETTY_PRINT);
    }

    echo "\n\nDONE. Delete this file now!\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
echo "</pre>";

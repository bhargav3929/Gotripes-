<?php
/**
 * Fix prices on live MySQL database
 * Upload to public_html/public/ and visit: https://gotrips.ai/fix_prices_live.php
 * DELETE THIS FILE IMMEDIATELY AFTER RUNNING
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h2>GoTripes - Price Fix Script</h2>";
echo "<pre>";

try {
    $connection = config('database.default');
    echo "Database connection: $connection\n";
    echo "Database: " . config("database.connections.$connection.database") . "\n\n";

    $count = DB::table('tbl_UAEActivities')->count();
    echo "Total activities in DB: $count\n\n";

    // Actual prices from local working database
    $prices = [
        1 => 110, 2 => 115, 3 => 138, 4 => 62, 5 => 120,
        6 => 87, 7 => 20, 8 => 48, 9 => 116, 10 => 0,
        11 => 85, 12 => 455, 13 => 365, 15 => 95, 16 => 290,
        17 => 347, 18 => 305, 19 => 305, 20 => 347, 21 => 305,
        22 => 98, 23 => 140, 24 => 194, 25 => 171, 26 => 220,
        27 => 75, 28 => 191, 29 => 216, 30 => 285, 31 => 232,
        32 => 249, 33 => 293, 34 => 323, 35 => 215, 36 => 365,
        37 => 385, 38 => 470, 39 => 475, 40 => 550, 41 => 650,
        42 => 145, 43 => 250, 44 => 275, 45 => 355, 46 => 88,
        47 => 106, 48 => 48, 49 => 63, 50 => 237, 51 => 290,
        52 => 190, 53 => 230, 54 => 230, 55 => 290, 56 => 85,
        57 => 80, 58 => 230, 59 => 290, 60 => 63, 61 => 240,
        62 => 305, 63 => 190, 64 => 185, 65 => 63, 66 => 305,
        67 => 340, 68 => 365, 69 => 440, 70 => 510, 71 => 330,
        72 => 73, 73 => 180, 74 => 180, 75 => 85, 76 => 125,
        77 => 130, 78 => 190, 79 => 65, 80 => 53, 81 => 73,
        82 => 205, 83 => 216, 84 => 26, 85 => 65, 86 => 210,
        87 => 265, 88 => 360, 89 => 50, 90 => 2155, 91 => 2650,
        92 => 85, 93 => 0, 94 => 0, 95 => 85, 96 => 665,
        97 => 670, 98 => 770, 99 => 350, 100 => 1100, 101 => 1350,
        102 => 705, 103 => 875, 104 => 1065, 105 => 0,
    ];

    echo "=== BEFORE UPDATE (first 5) ===\n";
    $before = DB::table('tbl_UAEActivities')->select('activityID', 'activityName', 'activityPrice')->limit(5)->get();
    foreach ($before as $a) {
        echo "  ID {$a->activityID}: {$a->activityName} | Price: {$a->activityPrice}\n";
    }

    echo "\n=== UPDATING PRICES ===\n";
    $updated = 0;
    foreach ($prices as $id => $adultPrice) {
        $affected = DB::table('tbl_UAEActivities')
            ->where('activityID', $id)
            ->update(['activityPrice' => $adultPrice]);
        if ($affected) {
            $updated++;
        }
    }
    echo "Updated $updated activities.\n";

    echo "\n=== AFTER UPDATE (first 5) ===\n";
    $after = DB::table('tbl_UAEActivities')->select('activityID', 'activityName', 'activityPrice')->limit(5)->get();
    foreach ($after as $a) {
        echo "  ID {$a->activityID}: {$a->activityName} | Price: {$a->activityPrice}\n";
    }

    echo "\nSUCCESS! Prices have been updated.\n";
    echo "DELETE THIS FILE NOW from File Manager!\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "</pre>";

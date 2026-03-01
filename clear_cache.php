<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

echo "<h2>Clear Laravel Cache</h2><pre>";

Artisan::call('config:clear');
echo "Config cache cleared.\n";

Artisan::call('cache:clear');
echo "Application cache cleared.\n";

Artisan::call('route:clear');
echo "Route cache cleared.\n";

Artisan::call('view:clear');
echo "View cache cleared.\n";

echo "\nAll caches cleared! Delete this file now.\n";
echo "</pre>";

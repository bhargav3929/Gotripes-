<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\MontyEsimService;

$service = new MontyEsimService();
try {
    $bundles = $service->getBundles('AIA');
    echo "Found " . count($bundles) . " bundles for AIA\n";
    foreach ($bundles as $b) {
        echo "- " . ($b['bundle_name'] ?? 'No Name') . ": " . $b['selling_price'] . " AED\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

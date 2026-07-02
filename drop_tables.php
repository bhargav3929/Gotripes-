<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

Illuminate\Support\Facades\DB::statement('DROP TABLE IF EXISTS uae_visa_prices, uae_visa_packages');

echo "Tables dropped successfully!\n";

<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\EsimController;
use Illuminate\Http\Request;

$c = new EsimController();

echo "Testing ARE (ISO3):\n";
$r1 = new Request(['country_code' => 'ARE']);
echo $c->getBundles($r1)->getContent() . "\n\n";

echo "Testing AE (ISO2):\n";
$r2 = new Request(['country_code' => 'AE']);
echo $c->getBundles($r2)->getContent() . "\n";

<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$admin = App\Models\User::where('email', 'admin@gotrips.ai')->first();
if ($admin) {
    $admin->password = Illuminate\Support\Facades\Hash::make('Test@1234');
    $admin->save();
    echo "Password for admin@gotrips.ai reset to: Test@1234\n";
}

$super = App\Models\User::where('email', 'superadmin@gotrips.ai')->first();
if ($super) {
    $super->password = Illuminate\Support\Facades\Hash::make('Test@1234');
    $super->save();
    echo "Password for superadmin@gotrips.ai reset to: Test@1234\n";
}

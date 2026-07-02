<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::all(['name', 'email', 'role', 'company_id']);
if ($users->isEmpty()) {
    echo "No users found in database.\n";
} else {
    foreach ($users as $user) {
        echo "Name: {$user->name} | Email: {$user->email} | Role: {$user->role} | Company ID: {$user->company_id}\n";
    }
}

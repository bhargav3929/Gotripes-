<?php
require __DIR__.'/bootstrap/app.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Announcement;
$items = Announcement::all();
echo "Total Announcements: " . $items->count() . "\n";
foreach($items as $item) {
    echo "ID: {$item->id}, Description: {$item->description}, Active: " . ($item->isActive ? 'Yes' : 'No') . ", TagType: {$item->tagType}\n";
}

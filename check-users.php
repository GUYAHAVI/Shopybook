<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== ALL USERS ===\n\n";

$users = User::select('id', 'name', 'email')->get();
foreach ($users as $user) {
    echo "User ID: {$user->id}\n";
    echo "  Name: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    
    $businessCount = $user->businesses()->count();
    echo "  Businesses: {$businessCount}\n";
    
    if ($businessCount > 0) {
        $businesses = $user->businesses()->select('name', 'slug')->get();
        foreach ($businesses as $biz) {
            echo "    - {$biz->name} ({$biz->slug})\n";
        }
    }
    echo "\n";
}

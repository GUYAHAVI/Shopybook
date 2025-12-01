<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Business;

echo "Current Business Status:\n";
echo str_repeat('-', 80) . "\n";

$businesses = Business::select('id', 'name', 'plan', 'on_trial', 'trial_ends_at')->get();

foreach ($businesses as $business) {
    echo "Business: {$business->name}\n";
    echo "  Plan: {$business->plan}\n";
    echo "  On Trial: " . ($business->on_trial ? 'Yes' : 'No') . "\n";
    
    if ($business->trial_ends_at) {
        echo "  Trial Ends: {$business->trial_ends_at->format('Y-m-d H:i:s')}\n";
        echo "  Days Remaining: " . $business->trialDaysRemaining() . "\n";
    }
    
    echo "\n";
}

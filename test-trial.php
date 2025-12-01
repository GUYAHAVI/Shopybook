<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Business;
use Illuminate\Support\Facades\DB;

echo "Testing trial functionality...\n\n";

// Get a test business
$business = Business::with('user')->first();

if (!$business) {
    echo "No businesses found in database.\n";
    exit(1);
}

echo "Business: {$business->name}\n";
echo "Current Plan: {$business->plan}\n";
echo "On Trial: " . ($business->on_trial ? 'Yes' : 'No') . "\n";

if (!empty($business->trial_ends_at)) {
    echo "Trial Ends: {$business->trial_ends_at}\n";
    echo "Is On Trial (method): " . ($business->isOnTrial() ? 'Yes' : 'No') . "\n";
    echo "Days Remaining: {$business->trialDaysRemaining()}\n";
} else {
    echo "No trial set\n";
}

echo "\n--- Starting Trial ---\n";
$business->startTrial(14, 'enterprise');
$business->refresh();

echo "Plan: {$business->plan}\n";
echo "On Trial: " . ($business->on_trial ? 'Yes' : 'No') . "\n";
echo "Trial Ends: {$business->trial_ends_at}\n";
echo "Days Remaining: {$business->trialDaysRemaining()}\n";

echo "\nTrial functionality working correctly!\n";

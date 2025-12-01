<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Website;
use App\Models\Business;

echo "=== ALL BUSINESSES ===\n\n";

$businesses = Business::select('id', 'name', 'slug', 'user_id')->get();
foreach ($businesses as $business) {
    echo "Business ID: {$business->id}\n";
    echo "  Name: {$business->name}\n";
    echo "  Slug: {$business->slug}\n";
    echo "  User ID: {$business->user_id}\n";
    
    // Check if it has a website
    $website = Website::where('business_id', $business->id)->first();
    if ($website) {
        echo "  ✓ Has Website:\n";
        echo "    - Subdomain: {$website->subdomain}\n";
        echo "    - Published: " . ($website->is_published ? 'Yes' : 'No') . "\n";
        echo "    - Theme ID: {$website->theme_id}\n";
        echo "    - Pages: " . $website->pages()->count() . "\n";
        echo "    - URL: https://{$website->subdomain}.shopybook.com\n";
        echo "    - Direct URL: https://shopybook.com/site/{$website->subdomain}\n";
    } else {
        echo "  ✗ No website created\n";
    }
    echo "\n";
}

echo "\n=== WEBSITES WITHOUT MATCHING BUSINESS ===\n\n";
$websites = Website::with('business')->get();
foreach ($websites as $website) {
    if (!$website->business) {
        echo "Orphaned Website ID: {$website->id}\n";
        echo "  Subdomain: {$website->subdomain}\n";
        echo "  Business ID: {$website->business_id} (NOT FOUND)\n\n";
    }
}

echo "\n=== SEARCH FOR 'JIHAMI' ===\n\n";
$jihamiBusinesses = Business::where('name', 'LIKE', '%jihami%')
    ->orWhere('slug', 'LIKE', '%jihami%')
    ->get();

if ($jihamiBusinesses->count() > 0) {
    echo "Found businesses matching 'jihami':\n";
    foreach ($jihamiBusinesses as $biz) {
        echo "- ID: {$biz->id}, Name: {$biz->name}, Slug: {$biz->slug}\n";
    }
} else {
    echo "No businesses found matching 'jihami'\n";
}

$jihamiWebsites = Website::where('subdomain', 'LIKE', '%jihami%')->get();
if ($jihamiWebsites->count() > 0) {
    echo "\nFound websites matching 'jihami':\n";
    foreach ($jihamiWebsites as $site) {
        echo "- ID: {$site->id}, Subdomain: {$site->subdomain}, Business ID: {$site->business_id}\n";
    }
} else {
    echo "\nNo websites found with 'jihami' subdomain\n";
}

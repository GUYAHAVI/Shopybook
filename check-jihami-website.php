<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Website;
use App\Models\Business;

echo "=== Checking jihami Website Configuration ===\n\n";

// Check if business exists
$business = Business::where('slug', 'jihami')->first();
if ($business) {
    echo "✓ Business found:\n";
    echo "  - ID: {$business->id}\n";
    echo "  - Name: {$business->name}\n";
    echo "  - Slug: {$business->slug}\n\n";
} else {
    echo "✗ Business 'jihami' not found!\n\n";
}

// Check if website exists
$website = Website::where('subdomain', 'jihami')->first();
if ($website) {
    echo "✓ Website found:\n";
    echo "  - ID: {$website->id}\n";
    echo "  - Business ID: {$website->business_id}\n";
    echo "  - Subdomain: {$website->subdomain}\n";
    echo "  - Is Published: " . ($website->is_published ? 'Yes' : 'No') . "\n";
    echo "  - Theme ID: {$website->theme_id}\n";
    echo "  - Custom Domain: " . ($website->custom_domain ?: 'None') . "\n\n";
    
    // Check pages
    $pages = $website->pages()->count();
    echo "  - Pages count: {$pages}\n";
    
    if ($pages > 0) {
        $homepage = $website->homepage()->first();
        if ($homepage) {
            echo "  - Homepage: {$homepage->title} (slug: {$homepage->slug})\n";
            $sections = $homepage->sections()->count();
            echo "  - Homepage sections: {$sections}\n";
        }
    }
} else {
    echo "✗ Website for subdomain 'jihami' not found!\n";
    echo "  This is the problem - the website hasn't been created yet.\n";
    echo "  You need to complete the website builder setup first.\n\n";
}

// List all websites
echo "\n=== All Websites in Database ===\n";
$allWebsites = Website::with('business')->get();
if ($allWebsites->count() > 0) {
    foreach ($allWebsites as $site) {
        echo "- {$site->subdomain}.shopybook.com (Business: {$site->business->name}, Published: " . ($site->is_published ? 'Yes' : 'No') . ")\n";
    }
} else {
    echo "No websites found in database.\n";
}

echo "\n=== APP Configuration ===\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "APP_ENV: " . config('app.env') . "\n";

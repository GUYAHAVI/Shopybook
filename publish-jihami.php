<?php
/**
 * UPLOAD THIS TO CPANEL
 * Access via: https://shopybook.com/publish-jihami.php
 * DELETE AFTER USE FOR SECURITY
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Website;
use App\Models\Business;

header('Content-Type: text/plain; charset=utf-8');

echo "=== Jihami Website Publisher ===\n\n";

// Find business
$business = Business::find('3a92b177-8638-4afb-98e0-19def2415e8c');

if (!$business) {
    echo "ERROR: Business not found!\n";
    exit;
}

echo "Business found: {$business->name} (ID: {$business->id})\n\n";

// Find website
$website = Website::where('business_id', $business->id)->first();

if (!$website) {
    echo "ERROR: No website created for this business!\n";
    echo "You need to create the website first:\n";
    echo "1. Go to https://shopybook.com/website-configurator/step1\n";
    echo "2. Complete the website setup\n";
    exit;
}

echo "Website found:\n";
echo "  - ID: {$website->id}\n";
echo "  - Subdomain: {$website->subdomain}\n";
echo "  - Published: " . ($website->is_published ? 'YES' : 'NO') . "\n";
echo "  - Theme ID: {$website->theme_id}\n";
echo "  - Pages: " . $website->pages()->count() . "\n\n";

if (!$website->is_published) {
    echo "Publishing website...\n";
    $website->is_published = true;
    $website->save();
    echo "✓ Website is now PUBLISHED!\n\n";
} else {
    echo "Website is already published.\n\n";
}

echo "Website should now be accessible at:\n";
echo "https://{$website->subdomain}.shopybook.com\n\n";

echo "=== IMPORTANT ===\n";
echo "DELETE THIS FILE NOW FOR SECURITY!\n";

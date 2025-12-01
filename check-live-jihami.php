<?php
/**
 * Upload this file to your cPanel public_html directory
 * Then access it via: https://shopybook.com/check-live-jihami.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Website;
use App\Models\Business;
use App\Models\WebsitePage;

header('Content-Type: text/plain; charset=utf-8');

echo "=== LIVE SERVER - Jihami Website Diagnostic ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

// Check business
echo "1. Checking for 'jihami' business...\n";
$business = Business::where('slug', 'jihami')
    ->orWhere('name', 'like', '%jihami%')
    ->first();

if ($business) {
    echo "   ✓ Business found:\n";
    echo "     - ID: {$business->id}\n";
    echo "     - Name: {$business->name}\n";
    echo "     - Slug: {$business->slug}\n";
    echo "     - User ID: {$business->user_id}\n\n";
} else {
    echo "   ✗ No business found with 'jihami'\n\n";
    
    // Search for similar names
    echo "   Searching for similar business names...\n";
    $similar = Business::where('name', 'like', '%jih%')
        ->orWhere('slug', 'like', '%jih%')
        ->get();
    
    if ($similar->count() > 0) {
        foreach ($similar as $biz) {
            echo "   - Found: {$biz->name} (slug: {$biz->slug})\n";
        }
    } else {
        echo "   No similar businesses found\n";
    }
    echo "\n";
}

// Check website
echo "2. Checking for 'jihami' website...\n";
$website = Website::where('subdomain', 'jihami')
    ->orWhere('subdomain', 'like', '%jihami%')
    ->first();

if ($website) {
    echo "   ✓ Website found:\n";
    echo "     - ID: {$website->id}\n";
    echo "     - Business ID: {$website->business_id}\n";
    echo "     - Subdomain: {$website->subdomain}\n";
    echo "     - Is Published: " . ($website->is_published ? 'YES' : 'NO') . "\n";
    echo "     - Theme ID: {$website->theme_id}\n";
    echo "     - Created: {$website->created_at}\n\n";
    
    // Check pages
    $pages = WebsitePage::where('website_id', $website->id)->get();
    echo "   Pages ({$pages->count()}):\n";
    foreach ($pages as $page) {
        $sectionsCount = $page->sections()->count();
        echo "     - {$page->title} (slug: {$page->slug}, sections: {$sectionsCount})\n";
    }
    echo "\n";
} else {
    echo "   ✗ No website found with subdomain 'jihami'\n\n";
}

// List all websites
echo "3. All websites on live server:\n";
$allWebsites = Website::with('business')->orderBy('created_at', 'desc')->get();
echo "   Total: {$allWebsites->count()}\n\n";

foreach ($allWebsites as $site) {
    $bizName = $site->business ? $site->business->name : 'No Business';
    $published = $site->is_published ? 'Published' : 'Unpublished';
    echo "   - {$site->subdomain}.shopybook.com\n";
    echo "     Business: {$bizName}\n";
    echo "     Status: {$published}\n";
    echo "     Created: {$site->created_at}\n\n";
}

// Check route configuration
echo "4. Configuration:\n";
echo "   APP_URL: " . config('app.url') . "\n";
echo "   APP_ENV: " . config('app.env') . "\n";
echo "   APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "\n\n";

// Check if routes are cached
echo "5. Routes status:\n";
$routesCached = file_exists(base_path('bootstrap/cache/routes-v7.php'));
echo "   Routes cached: " . ($routesCached ? 'YES' : 'NO') . "\n";
if ($routesCached) {
    echo "   Note: Run 'php artisan route:clear' if you changed routes\n";
}

echo "\n=== End of diagnostic ===\n";

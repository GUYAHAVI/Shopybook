<?php
/**
 * One-time script to backfill Pollinations.AI images into existing website sections
 * that have a null/missing image.
 *
 * Run: php repair-section-images.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\WebsiteSection;
use Illuminate\Support\Str;

// Different seed offsets so different section types get visually distinct photos
$typeContext = [
    'hero'         => 'storefront showcase professional photography',
    'about'        => 'team interior office workspace',
    'services'     => 'professional services offering',
    'features'     => 'product features quality detail',
    'stats'        => 'business achievement success',
    'testimonials' => 'happy satisfied customers',
    'cta'          => 'call to action background',
    'products'     => 'product display showcase retail',
    'contact'      => 'office location contact building',
    'team'         => 'team professionals office',
    'gallery'      => 'product portfolio showcase',
    'pricing'      => 'business plans pricing',
    'stats'        => 'business numbers achievements',
];

$sections = WebsiteSection::with(['page.website.business'])->get();
$updated = 0;
$skipped = 0;

foreach ($sections as $section) {
    $content = $section->content ?? [];

    // Skip only sections that already have a local/user-uploaded image (not a placeholder)
    $existing = $content['image'] ?? null;
    if ($existing && $existing !== 'null' && is_string($existing)
        && !str_contains($existing, 'pollinations.ai')
        && !str_contains($existing, 'picsum.photos')
        && str_starts_with($existing, '/')) {
        $skipped++;
        continue;
    }

    $business = $section->page->website->business ?? null;
    $businessName = $business ? $business->name : 'business';
    $businessType = $business ? ($business->business_type ?? 'general') : 'general';
    $businessSlug = Str::slug($businessName);

    $type = $section->type ?? 'general';

    // Hero and About use the template's built-in gradient visuals — clear any external image
    if (in_array($type, ['hero', 'about'])) {
        unset($content['image']);
        $section->content = $content;
        $section->save();
        $updated++;
        echo "Cleared image from section #{$section->id} ({$type}) for '{$businessName}' (using gradient visual)\n";
        continue;
    }

    $seed = abs(crc32($businessSlug . $type)) % 9999;

    // Use Claude's stored image_query (most specific), otherwise build from business context
    $typeKeywordFallback = [
        'services'     => $businessType . ',services,professional',
        'features'     => $businessType . ',quality,detail',
        'stats'        => $businessType . ',business',
        'testimonials' => 'happy,customers,satisfied',
        'cta'          => $businessType . ',lifestyle',
        'products'     => $businessType . ',products,retail',
        'contact'      => 'office,building,interior',
    ];
    $rawQuery = $content['image_query']
        ?? ($typeKeywordFallback[$type] ?? $businessType . ',professional');

    // Convert to max 5 comma-separated loremflickr keywords
    // Encode each word individually — commas must stay literal in the URL path
    $words = array_slice(preg_split('/[\s,]+/', strtolower(trim($rawQuery))), 0, 5);
    $words = array_map('rawurlencode', array_filter($words));
    $keywords = implode(',', $words);

    $content['image'] = "https://loremflickr.com/800/500/{$keywords}?lock={$seed}";

    // Persist
    $section->content = $content;
    $section->save();
    $updated++;
    echo "Updated section #{$section->id} ({$type}) for '{$businessName}'\n";
}

echo "\nDone. Updated: {$updated}, Skipped (already had image): {$skipped}\n";

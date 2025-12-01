<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Website;

echo "=== RECENT WEBSITES (Last 10) ===\n\n";

$recentWebsites = Website::with('business')
    ->latest()
    ->limit(10)
    ->get();

foreach ($recentWebsites as $website) {
    echo "Website ID: {$website->id}\n";
    echo "  Subdomain: {$website->subdomain}\n";
    echo "  Business: " . ($website->business ? $website->business->name : 'NULL') . "\n";
    echo "  Business Slug: " . ($website->business ? $website->business->slug : 'NULL') . "\n";
    echo "  Published: " . ($website->is_published ? 'Yes' : 'No') . "\n";
    echo "  Created: {$website->created_at}\n";
    echo "  Updated: {$website->updated_at}\n";
    echo "  Full URL: https://{$website->subdomain}.shopybook.com\n";
    echo "\n";
}

echo "\n=== CHECKING LARAVEL LOGS FOR JIHAMI ===\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    if (stripos($logs, 'jihami') !== false) {
        echo "Found 'jihami' mentions in logs\n";
        // Get last 20 lines mentioning jihami
        preg_match_all('/.*jihami.*/i', $logs, $matches);
        $jihamiLogs = array_slice($matches[0], -20);
        foreach ($jihamiLogs as $log) {
            echo "  " . trim($log) . "\n";
        }
    } else {
        echo "No 'jihami' mentions in logs\n";
    }
} else {
    echo "Log file not found\n";
}

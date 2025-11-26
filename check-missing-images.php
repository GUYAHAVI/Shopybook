<?php
/**
 * Check Missing Images - Detailed Report
 * 
 * This generates a detailed CSV report of all missing images
 * that you can share with affected users.
 * 
 * Usage: 
 * 1. Upload to your cPanel/server
 * 2. Access via browser: https://yoursite.com/check-missing-images.php
 * 3. Download the generated CSV
 * 4. DELETE this file after use
 */

// Load Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Business;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Missing Images Report</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #020258; border-bottom: 3px solid #13e8e9; padding-bottom: 10px; }
        h2 { color: #020258; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #020258; color: white; padding: 12px; text-align: left; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f9f9f9; }
        .stats { display: flex; gap: 20px; margin: 20px 0; }
        .stat-box { flex: 1; background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #13e8e9; }
        .stat-number { font-size: 2em; font-weight: bold; color: #020258; }
        .stat-label { color: #666; margin-top: 5px; }
        .missing { color: #dc3545; font-weight: bold; }
        .ok { color: #28a745; font-weight: bold; }
        .download-btn { background: #13e8e9; color: #020258; padding: 12px 24px; border: none; border-radius: 5px; font-weight: bold; text-decoration: none; display: inline-block; margin: 20px 0; cursor: pointer; }
        .download-btn:hover { background: #020258; color: #13e8e9; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
<div class='container'>
    <h1>🔍 Missing Images Report</h1>
    <p>Generated: " . date('Y-m-d H:i:s') . "</p>";

// Check Business Logos
echo "<h2>Business Logos</h2>";

$businesses = Business::whereNotNull('logo_path')
                     ->where('logo_path', '!=', '')
                     ->get();

$totalBusinesses = $businesses->count();
$missingBusinessLogos = 0;
$businessReport = [];

foreach ($businesses as $business) {
    $filePath = 'public/' . $business->logo_path;
    $exists = Storage::exists($filePath) || file_exists(public_path($business->logo_path));
    
    if (!$exists) {
        $missingBusinessLogos++;
        $businessReport[] = [
            'id' => $business->id,
            'name' => $business->name,
            'email' => $business->email,
            'phone' => $business->phone,
            'path' => $business->logo_path,
            'status' => 'MISSING',
        ];
    }
}

// Statistics
echo "<div class='stats'>
    <div class='stat-box'>
        <div class='stat-number'>$totalBusinesses</div>
        <div class='stat-label'>Total Businesses with Logos</div>
    </div>
    <div class='stat-box'>
        <div class='stat-number' style='color: #dc3545;'>$missingBusinessLogos</div>
        <div class='stat-label'>Missing Logo Files</div>
    </div>
    <div class='stat-box'>
        <div class='stat-number' style='color: #28a745;'>" . ($totalBusinesses - $missingBusinessLogos) . "</div>
        <div class='stat-label'>OK</div>
    </div>
</div>";

if (count($businessReport) > 0) {
    echo "<div class='warning'>
        <strong>⚠️ Warning:</strong> Found $missingBusinessLogos missing business logos. 
        These businesses will see a default placeholder image until they re-upload their logos.
    </div>";
    
    echo "<table>
        <tr>
            <th>Business Name</th>
            <th>Contact</th>
            <th>Missing File Path</th>
            <th>Status</th>
        </tr>";
    
    foreach ($businessReport as $item) {
        echo "<tr>
            <td><strong>{$item['name']}</strong></td>
            <td>{$item['email']}<br>{$item['phone']}</td>
            <td><code>{$item['path']}</code></td>
            <td class='missing'>{$item['status']}</td>
        </tr>";
    }
    
    echo "</table>";
} else {
    echo "<p class='ok'>✅ All business logos are present!</p>";
}

// Check Product Images
echo "<h2>Product Images</h2>";

$products = DB::table('products')
             ->whereNotNull('images')
             ->where('images', '!=', '[]')
             ->where('images', '!=', '')
             ->get();

$totalProductImages = 0;
$missingProductImages = 0;
$productReport = [];

foreach ($products as $product) {
    $images = json_decode($product->images, true);
    if (!is_array($images)) continue;
    
    foreach ($images as $imagePath) {
        $totalProductImages++;
        $filePath = 'public/' . $imagePath;
        $exists = Storage::exists($filePath) || file_exists(public_path($imagePath));
        
        if (!$exists) {
            $missingProductImages++;
            $productReport[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'path' => $imagePath,
                'status' => 'MISSING',
            ];
        }
    }
}

echo "<div class='stats'>
    <div class='stat-box'>
        <div class='stat-number'>$totalProductImages</div>
        <div class='stat-label'>Total Product Images</div>
    </div>
    <div class='stat-box'>
        <div class='stat-number' style='color: #dc3545;'>$missingProductImages</div>
        <div class='stat-label'>Missing Image Files</div>
    </div>
    <div class='stat-box'>
        <div class='stat-number' style='color: #28a745;'>" . ($totalProductImages - $missingProductImages) . "</div>
        <div class='stat-label'>OK</div>
    </div>
</div>";

if (count($productReport) > 0) {
    echo "<div class='warning'>
        <strong>⚠️ Warning:</strong> Found $missingProductImages missing product images.
    </div>";
    
    echo "<table>
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Missing File Path</th>
            <th>Status</th>
        </tr>";
    
    foreach (array_slice($productReport, 0, 50) as $item) { // Show first 50
        echo "<tr>
            <td>{$item['product_id']}</td>
            <td><strong>{$item['product_name']}</strong></td>
            <td><code>{$item['path']}</code></td>
            <td class='missing'>{$item['status']}</td>
        </tr>";
    }
    
    if (count($productReport) > 50) {
        echo "<tr><td colspan='4' style='text-align: center; font-style: italic;'>
            ... and " . (count($productReport) - 50) . " more missing images
        </td></tr>";
    }
    
    echo "</table>";
} else {
    echo "<p class='ok'>✅ All product images are present!</p>";
}

// Generate CSV download
if (count($businessReport) > 0 || count($productReport) > 0) {
    echo "<h2>📥 Download Report</h2>";
    echo "<p>Click below to download a CSV file with all missing images:</p>";
    echo "<a href='?download=csv' class='download-btn'>Download CSV Report</a>";
}

// Actions
echo "<h2>🔧 Recommended Actions</h2>
<ol>
    <li><strong>Recreate Storage Structure:</strong> Run <code>php recreate-storage-structure.php</code></li>
    <li><strong>Clean Database:</strong> Run <code>php artisan images:cleanup-missing --reset</code></li>
    <li><strong>Notify Users:</strong> Contact affected businesses to re-upload their images</li>
    <li><strong>Storage Link:</strong> Run <code>php artisan storage:link</code></li>
    <li><strong>Security:</strong> Delete this PHP file after use</li>
</ol>";

echo "<div class='warning' style='background: #f8d7da; border-color: #dc3545;'>
    <strong>⚠️ IMPORTANT:</strong> The physical image files are permanently deleted and cannot be recovered.
    Users MUST re-upload their images. Consider:
    <ul>
        <li>Sending email notifications to affected users</li>
        <li>Adding a banner on the dashboard for users with missing images</li>
        <li>Providing easy re-upload options in the admin panel</li>
    </ul>
</div>";

echo "</div></body></html>";

// Handle CSV download
if (isset($_GET['download']) && $_GET['download'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="missing-images-report-' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Business Logos CSV
    fputcsv($output, ['Type', 'ID', 'Name', 'Contact', 'Missing File Path']);
    
    foreach ($businessReport as $item) {
        fputcsv($output, [
            'Business Logo',
            $item['id'],
            $item['name'],
            $item['email'] . ' / ' . $item['phone'],
            $item['path'],
        ]);
    }
    
    // Product Images CSV
    foreach ($productReport as $item) {
        fputcsv($output, [
            'Product Image',
            $item['product_id'],
            $item['product_name'],
            '',
            $item['path'],
        ]);
    }
    
    fclose($output);
    exit;
}
?>


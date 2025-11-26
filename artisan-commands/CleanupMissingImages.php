<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Business;

class CleanupMissingImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:cleanup-missing 
                            {--dry-run : Show what would be changed without actually changing it}
                            {--reset : Reset missing images to NULL in database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for missing business logos and product images, and optionally reset them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $reset = $this->option('reset');

        $this->info('🔍 Checking for missing business logos and product images...');
        $this->newLine();

        // Check Business Logos
        $this->info('=== BUSINESS LOGOS ===');
        $missingBusinessLogos = $this->checkBusinessLogos($dryRun, $reset);
        
        $this->newLine();
        
        // Check Product Images
        $this->info('=== PRODUCT IMAGES ===');
        $missingProductImages = $this->checkProductImages($dryRun, $reset);

        $this->newLine();
        $this->info('=== SUMMARY ===');
        $this->table(
            ['Type', 'Total', 'Missing Files', 'Status'],
            [
                ['Business Logos', $missingBusinessLogos['total'], $missingBusinessLogos['missing'], $dryRun ? 'Dry Run' : ($reset ? 'Reset' : 'Reported')],
                ['Product Images', $missingProductImages['total'], $missingProductImages['missing'], $dryRun ? 'Dry Run' : ($reset ? 'Reset' : 'Reported')],
            ]
        );

        if ($dryRun) {
            $this->warn('This was a dry run. No changes were made.');
            $this->info('Run without --dry-run to see actual changes, or with --reset to reset missing images to NULL.');
        } elseif ($reset) {
            $this->info('✅ Missing images have been reset in the database.');
            $this->info('Users will now see default placeholder images instead of broken links.');
        } else {
            $this->warn('No changes were made. Use --reset to set missing images to NULL in database.');
        }

        $this->newLine();
        $this->info('💡 Next steps:');
        $this->line('  1. Notify affected users to re-upload their images');
        $this->line('  2. Check storage/app/public directory structure is correct');
        $this->line('  3. Ensure storage:link has been run');
        
        return Command::SUCCESS;
    }

    private function checkBusinessLogos($dryRun, $reset)
    {
        $businesses = Business::whereNotNull('logo_path')
                             ->where('logo_path', '!=', '')
                             ->get();

        $total = $businesses->count();
        $missing = 0;
        $missingBusinesses = [];

        foreach ($businesses as $business) {
            $filePath = 'public/' . $business->logo_path;
            
            // Check if file exists in storage
            if (!Storage::exists($filePath) && !file_exists(public_path($business->logo_path))) {
                $missing++;
                $missingBusinesses[] = [
                    'id' => $business->id,
                    'name' => $business->name,
                    'path' => $business->logo_path,
                ];

                $this->warn("❌ Missing: {$business->name} -> {$business->logo_path}");

                // Reset to NULL if requested and not dry run
                if ($reset && !$dryRun) {
                    $business->logo_path = null;
                    $business->save();
                    $this->line("   ✓ Reset to NULL");
                }
            }
        }

        if ($missing === 0) {
            $this->info('✅ All business logos are present!');
        } else {
            $this->warn("Found {$missing} missing business logos out of {$total} total.");
        }

        return [
            'total' => $total,
            'missing' => $missing,
            'details' => $missingBusinesses,
        ];
    }

    private function checkProductImages($dryRun, $reset)
    {
        // Note: Adjust this based on your Product model structure
        // Assuming products have an 'images' JSON field
        
        $products = DB::table('products')
                     ->whereNotNull('images')
                     ->where('images', '!=', '[]')
                     ->where('images', '!=', '')
                     ->get();

        $total = 0;
        $missing = 0;
        $missingProducts = [];

        foreach ($products as $product) {
            $images = json_decode($product->images, true);
            
            if (!is_array($images)) continue;

            foreach ($images as $imagePath) {
                $total++;
                $filePath = 'public/' . $imagePath;
                
                if (!Storage::exists($filePath) && !file_exists(public_path($imagePath))) {
                    $missing++;
                    
                    if (!isset($missingProducts[$product->id])) {
                        $missingProducts[$product->id] = [
                            'id' => $product->id,
                            'name' => $product->name,
                            'missing_images' => [],
                        ];
                    }
                    
                    $missingProducts[$product->id]['missing_images'][] = $imagePath;
                    $this->warn("❌ Missing: Product '{$product->name}' -> {$imagePath}");

                    // Reset images if requested and not dry run
                    if ($reset && !$dryRun) {
                        // Remove missing image from array
                        $images = array_filter($images, function($img) use ($imagePath) {
                            return $img !== $imagePath;
                        });
                        
                        DB::table('products')
                            ->where('id', $product->id)
                            ->update(['images' => json_encode(array_values($images))]);
                        
                        $this->line("   ✓ Removed from product images array");
                    }
                }
            }
        }

        if ($missing === 0) {
            $this->info('✅ All product images are present!');
        } else {
            $this->warn("Found {$missing} missing product images out of {$total} total image references.");
        }

        return [
            'total' => $total,
            'missing' => $missing,
            'details' => array_values($missingProducts),
        ];
    }
}


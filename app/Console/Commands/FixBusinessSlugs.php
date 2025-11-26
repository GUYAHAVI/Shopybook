<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;
use Illuminate\Support\Str;

class FixBusinessSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'business:fix-slugs {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and fix duplicate business slugs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('Running in DRY RUN mode - no changes will be made');
        }

        // Get all businesses
        $businesses = Business::orderBy('created_at', 'asc')->get();
        $slugMap = [];
        $duplicates = [];
        $fixed = 0;

        // Find duplicates
        foreach ($businesses as $business) {
            if (isset($slugMap[$business->slug])) {
                $duplicates[] = $business;
                $this->warn("Duplicate slug found: '{$business->slug}' for business '{$business->name}' (ID: {$business->id})");
            } else {
                $slugMap[$business->slug] = $business->id;
            }
        }

        if (empty($duplicates)) {
            $this->info('No duplicate slugs found. All business slugs are unique.');
            return;
        }

        $this->info('Found ' . count($duplicates) . ' businesses with duplicate slugs.');

        // Fix duplicates
        foreach ($duplicates as $business) {
            $baseSlug = Str::slug($business->name);
            $newSlug = $baseSlug;
            $counter = 1;

            // Find a unique slug
            while (isset($slugMap[$newSlug])) {
                $newSlug = $baseSlug . '-' . $counter;
                $counter++;
            }

            $this->info("Business '{$business->name}' (ID: {$business->id}):");
            $this->info("  Old slug: {$business->slug}");
            $this->info("  New slug: {$newSlug}");

            if (!$dryRun) {
                $business->update(['slug' => $newSlug]);
                $slugMap[$newSlug] = $business->id;
                $fixed++;
                $this->info("  ✓ Fixed");
            } else {
                $this->info("  (Would be fixed in actual run)");
            }
            
            $this->line('');
        }

        if ($dryRun) {
            $this->info('DRY RUN completed. Run without --dry-run to apply changes.');
        } else {
            $this->info("Fixed {$fixed} business slugs successfully.");
        }

        // Show current business list
        $this->line('');
        $this->info('Current businesses:');
        $currentBusinesses = Business::select('id', 'name', 'slug')->orderBy('name')->get();
        
        foreach ($currentBusinesses as $business) {
            $this->line("- {$business->name} → /business/{$business->slug}");
        }
    }
}
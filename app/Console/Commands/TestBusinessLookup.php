<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;

class TestBusinessLookup extends Command
{
    protected $signature = 'test:business-lookup {slug}';
    protected $description = 'Test business lookup by slug';

    public function handle()
    {
        $slug = $this->argument('slug');
        
        $this->info("Looking for business with slug: $slug");
        
        // Show all businesses
        $allBusinesses = Business::select('id', 'slug', 'name')->get();
        $this->info("All businesses in database:");
        foreach ($allBusinesses as $biz) {
            $this->line("  - {$biz->slug} => {$biz->name} (ID: {$biz->id})");
        }
        
        $this->line("");
        
        // Test the exact lookup
        $business = Business::where('slug', $slug)->first();
        
        if ($business) {
            $this->info("Found business: {$business->name} (ID: {$business->id})");
        } else {
            $this->error("No business found with slug: $slug");
        }
        
        return 0;
    }
}

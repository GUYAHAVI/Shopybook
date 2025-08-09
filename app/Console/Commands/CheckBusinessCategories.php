<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;

class CheckBusinessCategories extends Command
{
    protected $signature = 'business:check-categories';
    protected $description = 'Check business categories in database';

    public function handle()
    {
        $this->info('Checking business categories in database...');
        
        $businesses = Business::select('name', 'business_type', 'business_category')->get();
        
        foreach ($businesses as $business) {
            $this->line("Business: {$business->name}");
            $this->line("  Type: " . ($business->business_type ?? 'NULL'));
            $this->line("  Raw Category: " . ($business->business_category ?? 'NULL'));
            $this->line("  Accessor Category: " . $business->getBusinessCategoryAttribute());
            $this->line("  Is Product: " . ($business->isProductBusiness() ? 'YES' : 'NO'));
            $this->line("  Is Service: " . ($business->isServiceBusiness() ? 'YES' : 'NO'));
            $this->line("  Is Hybrid: " . ($business->isHybridBusiness() ? 'YES' : 'NO'));
            $this->line('---');
        }
        
        $this->info('Check complete!');
    }
} 
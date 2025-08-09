<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;

class CheckBusinessTypes extends Command
{
    protected $signature = 'business:check-types';
    protected $description = 'Check business types in database';

    public function handle()
    {
        $this->info('Checking business types in database...');
        
        $businesses = Business::select('name', 'business_type')->get();
        
        foreach ($businesses as $business) {
            $this->line("Business: {$business->name}");
            $this->line("Type: " . ($business->business_type ?? 'NULL'));
            $this->line('---');
        }
        
        $this->info('Check complete!');
    }
} 
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;

class BusinessCategorySeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Starting Business Category Population...');
        
        // Define the mapping of business types to categories
        $typeToCategory = [
            // Product types
            'retail' => 'product',
            'online' => 'product',
            'fashion' => 'product',
            'electronics' => 'product',
            'grocery' => 'product',
            'beauty' => 'product',
            'wholesale' => 'product',
            'other_product' => 'product',
            
            // Service types
            'consulting' => 'service',
            'beauty_service' => 'service',
            'repair' => 'service',
            'cleaning' => 'service',
            'education' => 'service',
            'healthcare' => 'service',
            'professional' => 'service',
            'other_service' => 'service',
            
            // Hybrid types
            'restaurant' => 'hybrid',
            'salon' => 'hybrid',
            'auto_service' => 'hybrid',
            'retail_service' => 'hybrid',
            'tech_service' => 'hybrid',
            'other_hybrid' => 'hybrid',
        ];
        
        $businesses = Business::all();
        $this->command->info("Found {$businesses->count()} businesses");
        
        $updatedCount = 0;
        
        foreach ($businesses as $business) {
            $this->command->line("Processing: {$business->name}");
            $this->command->line("  Current business_type: {$business->business_type}");
            
            if ($business->business_type && isset($typeToCategory[$business->business_type])) {
                $category = $typeToCategory[$business->business_type];
                $business->update(['business_category' => $category]);
                $this->command->line("  ✓ Updated category to: {$category}");
                $updatedCount++;
            } else {
                // Default to service for unknown types
                $business->update(['business_category' => 'service']);
                $this->command->line("  ✓ Defaulted to service for: {$business->business_type}");
                $updatedCount++;
            }
        }
        
        $this->command->info("Update complete!");
        $this->command->info("Updated: {$updatedCount} businesses");
    }
} 
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Business;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class CheckLowStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:check-low-stock 
                            {--business= : Specific business ID to check}
                            {--force : Force send notifications even if already sent recently}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for low stock products and send notifications';

    protected $notificationService;

    /**
     * Create a new command instance.
     */
    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for low stock products...');
        
        $businessId = $this->option('business');
        $force = $this->option('force');
        
        // Get businesses to check
        $businesses = $businessId 
            ? Business::where('id', $businessId)->get()
            : Business::all();
        
        if ($businesses->isEmpty()) {
            $this->warn('No businesses found to check.');
            return 0;
        }
        
        $totalChecked = 0;
        $totalNotified = 0;
        
        foreach ($businesses as $business) {
            $this->info("Checking business: {$business->name}");
            
            // Get products that are low stock or out of stock
            $lowStockProducts = Product::where('business_id', $business->id)
                ->where('is_active', true)
                ->get()
                ->filter(function($product) {
                    return $product->stock_status === 'low_stock' || $product->stock_status === 'out_of_stock';
                });
            
            $totalChecked += $lowStockProducts->count();
            
            foreach ($lowStockProducts as $product) {
                try {
                    if ($force) {
                        // Force notification by clearing cache
                        $cacheKey = "low_stock_notified_{$business->id}_{$product->id}";
                        \Cache::forget($cacheKey);
                    }
                    
                    $notification = $this->notificationService->checkAndNotifyLowStock($product);
                    
                    if ($notification) {
                        $this->line("  ✓ Notified: {$product->name} (Stock: {$product->stock_quantity})");
                        $totalNotified++;
                    } else {
                        $this->line("  - Skipped: {$product->name} (Already notified recently)");
                    }
                    
                } catch (\Exception $e) {
                    $this->error("  ✗ Error notifying for {$product->name}: {$e->getMessage()}");
                    Log::error('Low stock check command error', [
                        'product_id' => $product->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        $this->newLine();
        $this->info("Summary:");
        $this->info("- Total low stock products checked: {$totalChecked}");
        $this->info("- Notifications sent: {$totalNotified}");
        
        return 0;
    }
}








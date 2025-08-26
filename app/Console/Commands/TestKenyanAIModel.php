<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ShopybookAIBusinessAnalyst;
use App\Models\Business;
use Illuminate\Support\Facades\Log;

class TestKenyanAIModel extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ai:test-kenyan-model {business_id?} {--type=comprehensive}';

    /**
     * The console command description.
     */
    protected $description = 'Test the KENADA (Kenya National Data) AI model integration with a specific business';

    protected $aiAnalyst;

    public function __construct(ShopybookAIBusinessAnalyst $aiAnalyst)
    {
        parent::__construct();
        $this->aiAnalyst = $aiAnalyst;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🤖 Testing KENADA AI Model Integration');
        $this->info('====================================');

        // Get business ID
        $businessId = $this->argument('business_id');
        
        if (!$businessId) {
            // Show available businesses
            $businesses = Business::select('id', 'name')->limit(10)->get();
            
            if ($businesses->isEmpty()) {
                $this->error('❌ No businesses found in the database.');
                return;
            }

            $this->info('Available businesses:');
            foreach ($businesses as $business) {
                $this->line("  {$business->id}: {$business->name}");
            }

            $businessId = $this->ask('Enter business ID to test');
        }

        // Validate business exists
        $business = Business::find($businessId);
        if (!$business) {
            $this->error("❌ Business with ID {$businessId} not found.");
            return;
        }

        $this->info("🏢 Testing with business: {$business->name}");
        $this->info("📊 Analysis type: {$this->option('type')}");

        // Test the AI model
        try {
            $this->info('🔄 Starting AI analysis...');
            
            $startTime = microtime(true);
            $analysis = $this->aiAnalyst->generateBusinessAnalysis($businessId, $this->option('type'));
            $endTime = microtime(true);
            
            $duration = round($endTime - $startTime, 2);
            
            $this->info("✅ Analysis completed in {$duration} seconds");
            $this->info('');

            // Display results
            $this->displayAnalysisResults($analysis, $business);

        } catch (\Exception $e) {
            $this->error("❌ AI analysis failed: " . $e->getMessage());
            Log::error("Canadian AI model test failed: " . $e->getMessage());
            
            // Show additional debugging info
            if ($this->option('verbose')) {
                $this->error("Stack trace:");
                $this->error($e->getTraceAsString());
            }
        }
    }

    /**
     * Display analysis results in a formatted way
     */
    protected function displayAnalysisResults(array $analysis, Business $business)
    {
        $this->info('📊 ANALYSIS RESULTS');
        $this->info('==================');

        // Current Performance
        if (isset($analysis['current_performance'])) {
            $this->info('💰 Current Performance:');
            $performance = $analysis['current_performance'];
            
            $this->line("  Monthly Revenue: KSh " . number_format($performance['monthly_revenue'] ?? 0, 2));
            $this->line("  Monthly Expenses: KSh " . number_format($performance['monthly_expenses'] ?? 0, 2));
            $this->line("  Net Income: KSh " . number_format($performance['net_income'] ?? 0, 2));
            $this->line("  Profit Margin: " . number_format($performance['profit_margin'] ?? 0, 2) . "%");
            $this->line("  Employees: " . ($performance['employees'] ?? 0));
            $this->info('');
        }

        // Predictions
        if (isset($analysis['predictions']) && !empty($analysis['predictions']['predicted_net_income'])) {
            $this->info('🔮 AI Predictions:');
            $predictions = $analysis['predictions'];
            
            $this->line("  Predicted Net Income: KSh " . number_format($predictions['predicted_net_income'] ?? 0, 2));
            $this->line("  Improvement Potential: KSh " . number_format($predictions['improvement_potential'] ?? 0, 2));
            $this->line("  Confidence Level: " . ($predictions['confidence_level'] ?? 'Unknown'));
            $this->info('');
        }

        // Insights
        if (isset($analysis['insights'])) {
            $this->info('💡 Business Insights:');
            foreach ($analysis['insights'] as $category => $insights) {
                if (!empty($insights)) {
                    $this->line("  " . ucwords(str_replace('_', ' ', $category)) . ":");
                    foreach ($insights as $insight) {
                        $this->line("    • " . $insight);
                    }
                }
            }
            $this->info('');
        }

        // Recommendations
        if (isset($analysis['recommendations'])) {
            $this->info('📋 Recommendations:');
            foreach ($analysis['recommendations'] as $recommendation) {
                $priority = strtoupper($recommendation['priority'] ?? 'medium');
                $category = $recommendation['category'] ?? 'General';
                
                $this->line("  [{$priority}] {$category}:");
                if (isset($recommendation['actions'])) {
                    foreach ($recommendation['actions'] as $action) {
                        $this->line("    • " . $action);
                    }
                }
            }
            $this->info('');
        }

        // Benchmarks
        if (isset($analysis['benchmarks'])) {
            $this->info('📊 Business Benchmarks:');
            $benchmarks = $analysis['benchmarks'];
            
            $this->line("  Revenue per Employee: KSh " . number_format($benchmarks['revenue_per_employee'] ?? 0, 2));
            $this->line("  Expense Ratio: " . number_format($benchmarks['expense_ratio'] ?? 0, 2) . "%");
            $this->info('');
        }

        // Next Steps
        if (isset($analysis['next_steps'])) {
            $this->info('🎯 Recommended Next Steps:');
            $nextSteps = $analysis['next_steps'];
            
            if (isset($nextSteps['immediate'])) {
                $this->line("  Immediate (1-2 weeks):");
                foreach ($nextSteps['immediate'] as $step) {
                    $this->line("    • " . $step);
                }
            }
            
            if (isset($nextSteps['short_term'])) {
                $this->line("  Short-term (1-3 months):");
                foreach ($nextSteps['short_term'] as $step) {
                    $this->line("    • " . $step);
                }
            }
            
            if (isset($nextSteps['long_term'])) {
                $this->line("  Long-term (3-12 months):");
                foreach ($nextSteps['long_term'] as $step) {
                    $this->line("    • " . $step);
                }
            }
        }

        $this->info('');
        $this->info('✅ Test completed successfully!');
        $this->info('💾 Analysis results have been saved to the database.');
        $this->info('🌐 You can view detailed results in the Shopybook dashboard under AI Analysis.');
    }
}

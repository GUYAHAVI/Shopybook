<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Business;
use App\Models\Cost;
use Carbon\Carbon;

class GenerateMonthlySalaryCosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'costs:generate-salary-costs {--month= : The month to generate costs for (YYYY-MM format)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly salary costs for all businesses';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $month = $this->option('month') ? Carbon::createFromFormat('Y-m', $this->option('month')) : now();
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        $this->info("Generating salary costs for {$month->format('F Y')}...");

        $businesses = Business::with('staff')->get();
        $totalCostsCreated = 0;

        foreach ($businesses as $business) {
            $totalSalary = $business->staff->sum('salary');
            
            if ($totalSalary > 0) {
                // Check if salary cost already exists for this month
                $existingCost = Cost::where('business_id', $business->id)
                    ->where('type', 'salary')
                    ->where('description', 'like', "%{$month->format('F Y')}%")
                    ->first();

                if (!$existingCost) {
                    // Create salary cost entry
                    \Illuminate\Support\Facades\DB::transaction(fn () => app(\App\Services\Accounting\Ledger::class)->postCost(Cost::create([
                        'business_id' => $business->id,
                        'type' => 'salary',
                        'amount' => $totalSalary,
                        'description' => "Monthly staff salaries for {$month->format('F Y')}",
                        'date' => $startOfMonth->format('Y-m-d'),
                    ])));

                    $totalCostsCreated++;
                    $this->line("✓ Created salary cost for {$business->name}: KSh " . number_format($totalSalary, 2));
                } else {
                    $this->line("- Skipped {$business->name}: Salary cost already exists for {$month->format('F Y')}");
                }
            } else {
                $this->line("- Skipped {$business->name}: No staff with salaries");
            }
        }

        $this->info("\nCompleted! Created {$totalCostsCreated} salary cost entries for {$month->format('F Y')}.");
        
        return 0;
    }
}

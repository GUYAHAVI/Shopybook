<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use App\Models\Business;
use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Cost;
use Exception;

class ShopybookAIBusinessAnalyst
{
    protected $pythonPath;
    protected $modelPath;
    protected $dataPath;

    public function __construct()
    {
        // Configure paths for your Kenyan AI model (KENADA)
        $this->pythonPath = config('ai.models.kenyan_msme.python_path', 'python');
        $this->modelPath = config('ai.models.kenyan_msme.model_path', base_path('shopybookaimodels'));
        $this->dataPath = config('ai.models.kenyan_msme.data_path', storage_path('app/ai_data'));
        
        // Ensure data directory exists
        if (!file_exists($this->dataPath)) {
            mkdir($this->dataPath, 0755, true);
        }
    }

    /**
     * Generate comprehensive business analysis using the Canadian-trained model
     */
    public function generateBusinessAnalysis($businessId, $analysisType = 'comprehensive')
    {
        try {
            Log::info("Starting Shopybook AI analysis for business: {$businessId}");

            // Step 1: Prepare business data in format compatible with Canadian model
            $businessData = $this->prepareBusinessDataForModel($businessId);
            
            // Step 2: Save data to temporary file for Python processing
            $dataFile = $this->saveBusinessDataToFile($businessId, $businessData);
            
            // Step 3: Run the Canadian AI model
            $analysisResults = $this->runCanadianAIModel($dataFile, $analysisType);
            
            // Step 4: Process and format results for Shopybook
            $formattedResults = $this->formatAnalysisResults($analysisResults, $businessData);
            
            // Step 5: Store results in database
            $this->storeAnalysisResults($businessId, $formattedResults);
            
            Log::info("Shopybook AI analysis completed for business: {$businessId}");
            
            return $formattedResults;

        } catch (Exception $e) {
            Log::error("Shopybook AI analysis failed: " . $e->getMessage());
            throw new Exception("Business analysis failed: " . $e->getMessage());
        }
    }

    /**
     * Prepare business data in format compatible with Canadian MSME model
     */
    protected function prepareBusinessDataForModel($businessId)
    {
        $business = Business::find($businessId);
        if (!$business) {
            throw new Exception("Business not found");
        }

        // Get comprehensive business data
        $salesData = $this->getSalesData($businessId);
        $financialData = $this->getFinancialData($businessId);
        $operationalData = $this->getOperationalData($businessId);
        $employeeData = $this->getEmployeeData($businessId);
        
        // Map Shopybook data to Canadian model format
        $mappedData = [
            // Business identification
            'business_id' => $business->id,
            'business_name' => $business->name,
            'business_type' => $this->mapBusinessType($business->business_type),
            'business_category' => $business->business_category ?? 'general',
            
            // Operational metrics (map to Canadian model features)
            'monthly_revenue' => $salesData['monthly_revenue'] ?? 0,
            'monthly_expenses' => $financialData['monthly_expenses'] ?? 0,
            'net_income' => $salesData['monthly_revenue'] - $financialData['monthly_expenses'],
            'number_of_employees' => $employeeData['total_employees'] ?? 0,
            'male_employees' => $employeeData['male_employees'] ?? 0,
            'female_employees' => $employeeData['female_employees'] ?? 0,
            'working_hours_per_day' => $operationalData['hours_per_day'] ?? 8,
            'working_days_per_week' => $operationalData['days_per_week'] ?? 6,
            'months_per_year' => 12, // Assume year-round operation
            
            // Financial metrics
            'startup_capital' => $financialData['startup_capital'] ?? 0,
            'monthly_salary_expenses' => $financialData['salary_expenses'] ?? 0,
            'equipment_costs' => $financialData['equipment_costs'] ?? 0,
            'operational_costs' => $financialData['operational_costs'] ?? 0,
            
            // Performance indicators
            'total_customers' => $this->getCustomerCount($businessId),
            'total_products' => $this->getProductCount($businessId),
            'avg_order_value' => $salesData['avg_order_value'] ?? 0,
            'monthly_orders' => $salesData['monthly_orders'] ?? 0,
            
            // Business characteristics
            'business_location_type' => $this->getLocationCharacteristics($business),
            'years_in_operation' => $this->calculateYearsInOperation($business->created_at),
            'business_registration_status' => 1, // Assume registered since using Shopybook
            
            // Additional Shopybook-specific metrics
            'inventory_value' => $this->getInventoryValue($businessId),
            'customer_retention_rate' => $this->getCustomerRetentionRate($businessId),
            'profit_margin' => $this->calculateProfitMargin($salesData, $financialData),
        ];

        return $mappedData;
    }

    /**
     * Run the Canadian AI model with business data
     */
    protected function runCanadianAIModel($dataFile, $analysisType)
    {
        try {
            // Create a wrapper script to run the Canadian model
            $wrapperScript = $this->createModelWrapper($dataFile, $analysisType);
            
            // Execute the Python model
            $command = "{$this->pythonPath} {$wrapperScript}";
            
            Log::info("Executing AI model: {$command}");
            
            $result = Process::run($command);
            
            if ($result->failed()) {
                throw new Exception("AI model execution failed: " . $result->errorOutput());
            }
            
            // Parse the output from the model
            $output = $result->output();
            return $this->parseModelOutput($output);

        } catch (Exception $e) {
            Log::error("Canadian AI model execution failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a wrapper script to interface with the Canadian model
     */
    protected function createModelWrapper($dataFile, $analysisType)
    {
        $wrapperContent = <<<PYTHON
#!/usr/bin/env python3
import sys
import os
import json
import pandas as pd
import numpy as np
import pickle
from datetime import datetime

# Add the model directory to path
sys.path.append('{$this->modelPath}')

from Shopybookbusinessanalyst_local import *

def analyze_shopybook_business(data_file, analysis_type):
    """Analyze Shopybook business using Canadian MSME model"""
    try:
        # Load business data
        with open(data_file, 'r') as f:
            business_data = json.load(f)
        
        # Convert to DataFrame format expected by the model
        df = pd.DataFrame([business_data])
        
        # Load the trained model if it exists
        model_file = os.path.join('{$this->modelPath}', 'trained_model.pkl')
        if os.path.exists(model_file):
            with open(model_file, 'rb') as f:
                model = pickle.load(f)
            
            # Prepare features for prediction
            X, y = prepare_data(df)
            if X is not None:
                X_processed = preprocess_data(X)
                
                # Make prediction
                prediction = model.predict(X_processed.iloc[[0]])
                
                # Generate insights
                analysis_results = {
                    'predicted_net_income': float(prediction[0]),
                    'current_net_income': business_data.get('net_income', 0),
                    'improvement_potential': float(prediction[0]) - business_data.get('net_income', 0),
                    'analysis_type': analysis_type,
                    'timestamp': datetime.now().isoformat(),
                    'business_insights': generate_business_insights_for_shopybook(business_data, prediction[0])
                }
                
                print(json.dumps(analysis_results, indent=2))
            else:
                print(json.dumps({'error': 'Failed to prepare data for model'}))
        else:
            # Run basic analysis without trained model
            basic_analysis = analyze_business_without_model(business_data)
            print(json.dumps(basic_analysis, indent=2))
            
    except Exception as e:
        print(json.dumps({'error': str(e)}))

def generate_business_insights_for_shopybook(business_data, predicted_income):
    """Generate Shopybook-specific insights"""
    insights = {
        'revenue_optimization': [],
        'cost_management': [],
        'operational_efficiency': [],
        'growth_opportunities': [],
        'risk_factors': []
    }
    
    current_income = business_data.get('net_income', 0)
    revenue = business_data.get('monthly_revenue', 0)
    expenses = business_data.get('monthly_expenses', 0)
    employees = business_data.get('number_of_employees', 0)
    
    # Revenue optimization insights
    if predicted_income > current_income:
        potential_increase = predicted_income - current_income
        insights['revenue_optimization'].append(f"Potential to increase monthly income by KSh {potential_increase:,.2f}")
        
        if business_data.get('avg_order_value', 0) < 1000:
            insights['revenue_optimization'].append("Consider upselling strategies to increase average order value")
        
        if business_data.get('customer_retention_rate', 0) < 0.6:
            insights['revenue_optimization'].append("Focus on customer retention programs")
    
    # Cost management insights
    if expenses > revenue * 0.7:
        insights['cost_management'].append("High expense ratio detected - review operational costs")
    
    if business_data.get('salary_expenses', 0) > revenue * 0.3:
        insights['cost_management'].append("Staff costs are high relative to revenue")
    
    # Operational efficiency
    hours_per_day = business_data.get('working_hours_per_day', 8)
    if hours_per_day > 10:
        insights['operational_efficiency'].append("Consider optimizing working hours for better work-life balance")
    
    if employees > 0 and revenue / employees < 10000:
        insights['operational_efficiency'].append("Revenue per employee is below optimal - consider productivity improvements")
    
    # Growth opportunities
    if business_data.get('total_products', 0) < 10:
        insights['growth_opportunities'].append("Expand product catalog to increase revenue streams")
    
    if business_data.get('years_in_operation', 0) > 2 and predicted_income > current_income * 1.5:
        insights['growth_opportunities'].append("Strong growth potential - consider expansion or scaling strategies")
    
    return insights

def analyze_business_without_model(business_data):
    """Provide basic analysis when trained model is not available"""
    revenue = business_data.get('monthly_revenue', 0)
    expenses = business_data.get('monthly_expenses', 0)
    net_income = revenue - expenses
    
    return {
        'current_performance': {
            'monthly_revenue': revenue,
            'monthly_expenses': expenses,
            'net_income': net_income,
            'profit_margin': (net_income / revenue * 100) if revenue > 0 else 0
        },
        'benchmarks': {
            'revenue_per_employee': revenue / max(business_data.get('number_of_employees', 1), 1),
            'expense_ratio': (expenses / revenue * 100) if revenue > 0 else 0
        },
        'recommendations': generate_basic_recommendations(business_data),
        'analysis_type': 'basic_analysis',
        'timestamp': datetime.now().isoformat()
    }

def generate_basic_recommendations(business_data):
    """Generate basic recommendations"""
    recommendations = []
    
    revenue = business_data.get('monthly_revenue', 0)
    expenses = business_data.get('monthly_expenses', 0)
    
    if expenses > revenue * 0.8:
        recommendations.append("Reduce operational expenses to improve profitability")
    
    if business_data.get('total_customers', 0) < 50:
        recommendations.append("Focus on customer acquisition and marketing")
    
    if business_data.get('avg_order_value', 0) < 500:
        recommendations.append("Implement strategies to increase average order value")
    
    return recommendations

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print(json.dumps({'error': 'Usage: script.py <data_file> <analysis_type>'}))
        sys.exit(1)
    
    data_file = sys.argv[1]
    analysis_type = sys.argv[2]
    
    analyze_shopybook_business(data_file, analysis_type)
PYTHON;

        $wrapperFile = $this->dataPath . '/shopybook_ai_wrapper_' . time() . '.py';
        file_put_contents($wrapperFile, $wrapperContent);
        
        return $wrapperFile;
    }

    /**
     * Get sales data for the business
     */
    protected function getSalesData($businessId)
    {
        $thirtyDaysAgo = now()->subDays(30);
        
        $orders = Order::where('business_id', $businessId)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->get();
        
        $totalRevenue = $orders->sum('total_amount');
        $orderCount = $orders->count();
        $avgOrderValue = $orderCount > 0 ? $totalRevenue / $orderCount : 0;
        
        return [
            'monthly_revenue' => $totalRevenue,
            'monthly_orders' => $orderCount,
            'avg_order_value' => $avgOrderValue,
            'total_sales_last_30_days' => $totalRevenue
        ];
    }

    /**
     * Get financial data for the business
     */
    protected function getFinancialData($businessId)
    {
        $thirtyDaysAgo = now()->subDays(30);
        
        // Get costs from the last 30 days
        $costs = Cost::where('business_id', $businessId)
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->get();
        
        $totalExpenses = $costs->sum('amount');
        $salaryExpenses = $costs->where('category', 'salary')->sum('amount');
        $operationalCosts = $costs->whereNotIn('category', ['salary', 'equipment'])->sum('amount');
        $equipmentCosts = $costs->where('category', 'equipment')->sum('amount');
        
        return [
            'monthly_expenses' => $totalExpenses,
            'salary_expenses' => $salaryExpenses,
            'operational_costs' => $operationalCosts,
            'equipment_costs' => $equipmentCosts,
            'startup_capital' => 0 // Would need additional tracking
        ];
    }

    /**
     * Get operational data
     */
    protected function getOperationalData($businessId)
    {
        return [
            'hours_per_day' => 8, // Default, could be configurable
            'days_per_week' => 6,  // Default, could be configurable
            'months_per_year' => 12
        ];
    }

    /**
     * Get employee data
     */
    protected function getEmployeeData($businessId)
    {
        $staff = Staff::where('business_id', $businessId)->get();
        
        $totalEmployees = $staff->count();
        $maleEmployees = $staff->where('gender', 'male')->count();
        $femaleEmployees = $staff->where('gender', 'female')->count();
        
        return [
            'total_employees' => $totalEmployees,
            'male_employees' => $maleEmployees,
            'female_employees' => $femaleEmployees
        ];
    }

    /**
     * Additional helper methods
     */
    protected function getCustomerCount($businessId)
    {
        return Customer::where('business_id', $businessId)->count();
    }

    protected function getProductCount($businessId)
    {
        return Product::where('business_id', $businessId)->count();
    }

    protected function getInventoryValue($businessId)
    {
        return Product::where('business_id', $businessId)
            ->sum(DB::raw('quantity * selling_price'));
    }

    protected function getCustomerRetentionRate($businessId)
    {
        // Simplified calculation - could be more sophisticated
        $totalCustomers = Customer::where('business_id', $businessId)->count();
        $repeatCustomers = Order::where('business_id', $businessId)
            ->select('customer_id')
            ->groupBy('customer_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();
        
        return $totalCustomers > 0 ? $repeatCustomers / $totalCustomers : 0;
    }

    protected function calculateProfitMargin($salesData, $financialData)
    {
        $revenue = $salesData['monthly_revenue'] ?? 0;
        $expenses = $financialData['monthly_expenses'] ?? 0;
        
        return $revenue > 0 ? (($revenue - $expenses) / $revenue) * 100 : 0;
    }

    protected function mapBusinessType($shopybookType)
    {
        $mapping = [
            'retail' => 'retail_trade',
            'service' => 'service_business',
            'manufacturing' => 'manufacturing',
            'agriculture' => 'agriculture',
            'technology' => 'service_business',
            'food' => 'retail_trade',
            'fashion' => 'retail_trade',
            'beauty' => 'service_business'
        ];
        
        return $mapping[$shopybookType] ?? 'service_business';
    }

    protected function getLocationCharacteristics($business)
    {
        // This could be enhanced with actual location data
        return 'urban'; // Default assumption
    }

    protected function calculateYearsInOperation($createdAt)
    {
        return now()->diffInYears($createdAt);
    }

    protected function saveBusinessDataToFile($businessId, $data)
    {
        $filename = $this->dataPath . "/business_{$businessId}_" . time() . ".json";
        file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
        return $filename;
    }

    protected function parseModelOutput($output)
    {
        try {
            return json_decode($output, true);
        } catch (Exception $e) {
            Log::error("Failed to parse model output: " . $e->getMessage());
            return ['error' => 'Invalid model output'];
        }
    }

    protected function formatAnalysisResults($analysisResults, $businessData)
    {
        if (isset($analysisResults['error'])) {
            throw new Exception($analysisResults['error']);
        }

        return [
            'analysis_type' => $analysisResults['analysis_type'] ?? 'comprehensive',
            'timestamp' => $analysisResults['timestamp'] ?? now()->toISOString(),
            'current_performance' => [
                'monthly_revenue' => $businessData['monthly_revenue'] ?? 0,
                'monthly_expenses' => $businessData['monthly_expenses'] ?? 0,
                'net_income' => $businessData['net_income'] ?? 0,
                'profit_margin' => $businessData['profit_margin'] ?? 0,
                'employees' => $businessData['number_of_employees'] ?? 0
            ],
            'predictions' => [
                'predicted_net_income' => $analysisResults['predicted_net_income'] ?? null,
                'improvement_potential' => $analysisResults['improvement_potential'] ?? null,
                'confidence_level' => 'high' // Based on Canadian MSME model training
            ],
            'insights' => $analysisResults['business_insights'] ?? [],
            'recommendations' => $this->generateActionableRecommendations($analysisResults),
            'benchmarks' => $this->generateBenchmarks($businessData),
            'next_steps' => $this->generateNextSteps($analysisResults)
        ];
    }

    protected function generateActionableRecommendations($analysisResults)
    {
        $recommendations = [];
        
        if (isset($analysisResults['business_insights'])) {
            $insights = $analysisResults['business_insights'];
            
            if (!empty($insights['revenue_optimization'])) {
                $recommendations[] = [
                    'category' => 'Revenue Growth',
                    'priority' => 'high',
                    'actions' => $insights['revenue_optimization']
                ];
            }
            
            if (!empty($insights['cost_management'])) {
                $recommendations[] = [
                    'category' => 'Cost Control',
                    'priority' => 'medium',
                    'actions' => $insights['cost_management']
                ];
            }
            
            if (!empty($insights['operational_efficiency'])) {
                $recommendations[] = [
                    'category' => 'Operations',
                    'priority' => 'medium',
                    'actions' => $insights['operational_efficiency']
                ];
            }
        }
        
        return $recommendations;
    }

    protected function generateBenchmarks($businessData)
    {
        return [
            'revenue_per_employee' => $businessData['monthly_revenue'] / max($businessData['number_of_employees'], 1),
            'expense_ratio' => ($businessData['monthly_expenses'] / max($businessData['monthly_revenue'], 1)) * 100,
            'profit_margin' => $businessData['profit_margin'] ?? 0
        ];
    }

    protected function generateNextSteps($analysisResults)
    {
        return [
            'immediate' => ['Review cost structure', 'Analyze customer acquisition'],
            'short_term' => ['Implement pricing optimization', 'Enhance customer retention'],
            'long_term' => ['Consider business expansion', 'Invest in technology upgrades']
        ];
    }

    protected function storeAnalysisResults($businessId, $results)
    {
        try {
            DB::table('ai_business_analysis')->updateOrInsert(
                ['business_id' => $businessId],
                [
                    'analysis_data' => json_encode($results),
                    'analysis_type' => $results['analysis_type'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        } catch (Exception $e) {
            Log::error("Failed to store analysis results: " . $e->getMessage());
        }
    }

    /**
     * Get latest analysis for a business
     */
    public function getLatestAnalysis($businessId)
    {
        try {
            $result = DB::table('ai_business_analysis')
                ->where('business_id', $businessId)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($result) {
                return json_decode($result->analysis_data, true);
            }
            
            return null;
        } catch (Exception $e) {
            Log::error("Failed to get latest analysis: " . $e->getMessage());
            return null;
        }
    }
}

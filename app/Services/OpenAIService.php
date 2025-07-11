<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openai.com/v1';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', 'sk-ijklmnopqrstuvwxijklmnopqrstuvwxijklmnop');
    }

    public function analyzeBusinessData($businessData)
    {
        try {
            $prompt = $this->buildAnalysisPrompt($businessData);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a business analyst expert. Provide clear, actionable insights based on the business data provided. Focus on practical recommendations for growth and improvement.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 1500,
                'temperature' => 0.7
            ]);

            if ($response->successful()) {
                return $response->json()['choices'][0]['message']['content'];
            } else {
                Log::error('OpenAI API Error: ' . $response->body());
                return 'Unable to analyze data at this time. Please try again later.';
            }
        } catch (\Exception $e) {
            Log::error('OpenAI Service Error: ' . $e->getMessage());
            return 'Analysis service temporarily unavailable.';
        }
    }

    protected function buildAnalysisPrompt($businessData)
    {
        $prompt = "Analyze the following business data and provide insights:\n\n";
        
        // Sales Analysis
        if (isset($businessData['sales'])) {
            $prompt .= "SALES DATA:\n";
            $prompt .= "- Total Sales: " . ($businessData['sales']['total'] ?? 'N/A') . "\n";
            $prompt .= "- Average Order Value: " . ($businessData['sales']['average_order'] ?? 'N/A') . "\n";
            $prompt .= "- Number of Orders: " . ($businessData['sales']['count'] ?? 'N/A') . "\n";
            $prompt .= "- Sales Trend: " . ($businessData['sales']['trend'] ?? 'N/A') . "\n\n";
        }

        // Product Analysis
        if (isset($businessData['products'])) {
            $prompt .= "PRODUCT DATA:\n";
            $prompt .= "- Total Products: " . ($businessData['products']['total'] ?? 'N/A') . "\n";
            $prompt .= "- Low Stock Items: " . ($businessData['products']['low_stock'] ?? 'N/A') . "\n";
            $prompt .= "- Top Categories: " . implode(', ', $businessData['products']['top_categories'] ?? []) . "\n\n";
        }

        // Customer Analysis
        if (isset($businessData['customers'])) {
            $prompt .= "CUSTOMER DATA:\n";
            $prompt .= "- Total Customers: " . ($businessData['customers']['total'] ?? 'N/A') . "\n";
            $prompt .= "- New Customers: " . ($businessData['customers']['new'] ?? 'N/A') . "\n";
            $prompt .= "- Repeat Customers: " . ($businessData['customers']['repeat'] ?? 'N/A') . "\n\n";
        }

        // Financial Analysis
        if (isset($businessData['financial'])) {
            $prompt .= "FINANCIAL DATA:\n";
            $prompt .= "- Revenue: " . ($businessData['financial']['revenue'] ?? 'N/A') . "\n";
            $prompt .= "- Costs: " . ($businessData['financial']['costs'] ?? 'N/A') . "\n";
            $prompt .= "- Profit Margin: " . ($businessData['financial']['profit_margin'] ?? 'N/A') . "\n\n";
        }

        $prompt .= "Please provide:\n";
        $prompt .= "1. Key insights about business performance\n";
        $prompt .= "2. Areas of strength and opportunities for improvement\n";
        $prompt .= "3. Specific actionable recommendations\n";
        $prompt .= "4. Growth strategies to consider\n";
        $prompt .= "5. Risk factors to monitor\n\n";
        $prompt .= "Format your response in a clear, structured manner with bullet points and sections.";

        return $prompt;
    }

    public function generateFinancialReport($financialData)
    {
        try {
            $prompt = "Generate a comprehensive financial analysis report based on this data:\n\n";
            $prompt .= json_encode($financialData, JSON_PRETTY_PRINT);
            $prompt .= "\n\nProvide a detailed financial analysis including cash flow, profitability, and recommendations.";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a financial analyst. Provide detailed financial insights and recommendations.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 2000,
                'temperature' => 0.5
            ]);

            if ($response->successful()) {
                return $response->json()['choices'][0]['message']['content'];
            } else {
                Log::error('OpenAI Financial Analysis Error: ' . $response->body());
                return 'Unable to generate financial report at this time.';
            }
        } catch (\Exception $e) {
            Log::error('OpenAI Financial Service Error: ' . $e->getMessage());
            return 'Financial analysis service temporarily unavailable.';
        }
    }
} 
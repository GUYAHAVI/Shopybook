<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClaudeAPIService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.anthropic.com/v1/messages';
    protected $model = 'claude-sonnet-4-20250514';

    public function __construct()
    {
        $this->apiKey = config('services.claude.api_key', env('CLAUDE_API_KEY'));
        
        if (empty($this->apiKey)) {
            Log::warning('Claude API key is not configured');
        }
    }

    /**
     * Clean markdown formatting from AI responses to make them less obvious
     */
    private function cleanMarkdownFormatting(string $text): string
    {
        // Remove asterisks used for bold/italic (**, *, ***)
        $text = preg_replace('/\*\*\*(.+?)\*\*\*/', '$1', $text); // Remove ***bold italic***
        $text = preg_replace('/\*\*(.+?)\*\*/', '$1', $text); // Remove **bold**
        $text = preg_replace('/\*(.+?)\*/', '$1', $text); // Remove *italic*
        
        // Remove underscores used for emphasis
        $text = preg_replace('/__(.+?)__/', '$1', $text); // Remove __bold__
        $text = preg_replace('/_(.+?)_/', '$1', $text); // Remove _italic_
        
        // Remove markdown headers that might slip through
        $text = preg_replace('/^#+\s+/m', '', $text); // Remove # headers
        
        // Clean up any remaining asterisks at start of lines (bullet points)
        $text = preg_replace('/^\s*\*\s+/m', '• ', $text); // Replace * bullets with •
        
        return trim($text);
    }

    /**
     * Analyze business data using Claude AI with comparison to similar online businesses
     */
    public function analyzeBusinessData($businessData)
    {
        try {
            $prompt = $this->buildComprehensiveAnalysisPrompt($businessData);
            
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 4096,
                'temperature' => 0.7,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['content'][0]['text'] ?? 'No analysis generated.';
            } else {
                Log::error('Claude API Error: ' . $response->body());
                return $this->generateFallbackAnalysis($businessData);
            }
        } catch (\Exception $e) {
            Log::error('Claude Service Error: ' . $e->getMessage());
            return $this->generateFallbackAnalysis($businessData);
        }
    }

    /**
     * Build comprehensive analysis prompt with industry comparisons
     */
    protected function buildComprehensiveAnalysisPrompt($businessData)
    {
        $businessName = $businessData['business_name'] ?? 'Your Business';
        $businessType = $businessData['business_type'] ?? 'retail';
        $analysisType = $businessData['analysis_type'] ?? 'general';
        
        $prompt = "You are an expert business analyst with deep knowledge of e-commerce, retail, and online business trends in Kenya and globally. ";
        $prompt .= "Analyze the following business data for {$businessName} ({$businessType} business) and provide actionable insights.\n\n";
        
        // Sales Data Analysis
        if (isset($businessData['sales_data'])) {
            $salesData = $businessData['sales_data'];
            $prompt .= "## SALES PERFORMANCE DATA:\n";
            $prompt .= "- Total Sales: KSh " . number_format($salesData['total_sales'] ?? 0, 2) . "\n";
            $prompt .= "- Total Orders: " . ($salesData['total_orders'] ?? 0) . "\n";
            $prompt .= "- Recent Sales (Last 30 Days): KSh " . number_format($salesData['recent_sales'] ?? 0, 2) . "\n";
            $prompt .= "- Average Order Value: KSh " . number_format($salesData['average_order_value'] ?? 0, 2) . "\n\n";
            
            if (!empty($salesData['top_selling_products'])) {
                $prompt .= "### Top Selling Products:\n";
                foreach ($salesData['top_selling_products'] as $index => $product) {
                    $prompt .= ($index + 1) . ". " . $product['name'] . " - Quantity Sold: " . ($product['quantity_sold'] ?? 0) . "\n";
                }
                $prompt .= "\n";
            }
        }
        
        // Product Data Analysis
        if (isset($businessData['products_data'])) {
            $productsData = $businessData['products_data'];
            $prompt .= "## PRODUCT INVENTORY DATA:\n";
            $prompt .= "- Total Products: " . ($productsData['total_products'] ?? 0) . "\n";
            $prompt .= "- Low Stock Items: " . ($productsData['low_stock_items'] ?? 0) . "\n";
            $prompt .= "- Out of Stock: " . ($productsData['out_of_stock'] ?? 0) . "\n";
            $prompt .= "- Average Product Price: KSh " . number_format($productsData['average_price'] ?? 0, 2) . "\n";
            $prompt .= "- Total Inventory Value: KSh " . number_format($productsData['inventory_value'] ?? 0, 2) . "\n";
            
            if (!empty($productsData['categories'])) {
                $prompt .= "- Product Categories: " . implode(', ', $productsData['categories']->toArray()) . "\n";
            }
            $prompt .= "\n";
        }
        
        // Services Data Analysis
        if (isset($businessData['services_data'])) {
            $servicesData = $businessData['services_data'];
            $prompt .= "## SERVICES PERFORMANCE DATA:\n";
            $prompt .= "- Total Services: " . ($servicesData['total_services'] ?? 0) . "\n";
            $prompt .= "- Active Services: " . ($servicesData['active_services'] ?? 0) . "\n";
            $prompt .= "- Total Service Revenue: KSh " . number_format($servicesData['total_revenue'] ?? 0, 2) . "\n";
            $prompt .= "- Total Bookings: " . ($servicesData['total_bookings'] ?? 0) . "\n";
            $prompt .= "- Average Service Value: KSh " . number_format($servicesData['average_service_value'] ?? 0, 2) . "\n\n";
        }
        
        // Customer Data Analysis
        if (isset($businessData['customers_data'])) {
            $customersData = $businessData['customers_data'];
            $prompt .= "## CUSTOMER ANALYTICS:\n";
            $prompt .= "- Total Customers: " . ($customersData['total_customers'] ?? 0) . "\n";
            $prompt .= "- New Customers (Last 30 Days): " . ($customersData['new_customers_30_days'] ?? 0) . "\n";
            $prompt .= "- Customers with Orders: " . ($customersData['customers_with_orders'] ?? 0) . "\n";
            $prompt .= "- Average Orders per Customer: " . number_format($customersData['average_orders_per_customer'] ?? 0, 2) . "\n\n";
            
            if (!empty($customersData['top_customers'])) {
                $prompt .= "### Top Customers:\n";
                foreach ($customersData['top_customers'] as $index => $customer) {
                    $prompt .= ($index + 1) . ". " . $customer['name'] . " - Total Spent: KSh " . number_format($customer['total_spent'] ?? 0, 2) . " (" . ($customer['order_count'] ?? 0) . " orders)\n";
                }
                $prompt .= "\n";
            }
        }
        
        // Financial Data Analysis
        if (isset($businessData['financial_data'])) {
            $financialData = $businessData['financial_data'];
            $summary = $financialData['summary'] ?? [];
            $prompt .= "## FINANCIAL OVERVIEW:\n";
            $prompt .= "- Total Revenue: KSh " . ($summary['revenue'] ?? '0.00') . "\n";
            $prompt .= "- Total Costs: KSh " . ($summary['costs'] ?? '0.00') . "\n";
            $prompt .= "- Net Profit: KSh " . ($summary['profit'] ?? '0.00') . "\n";
            $prompt .= "- Profit Margin: " . ($summary['profit_margin'] ?? '0.0%') . "\n\n";
        }
        
        // Analysis Instructions
        $prompt .= "\n## ANALYSIS REQUIRED:\n\n";
        $prompt .= "Based on the above data, provide a comprehensive business analysis that includes:\n\n";
        
        $prompt .= "1. **PERFORMANCE ASSESSMENT**:\n";
        $prompt .= "   - Evaluate current business performance metrics\n";
        $prompt .= "   - Identify key strengths and areas of concern\n";
        $prompt .= "   - Compare performance against typical industry benchmarks for similar {$businessType} businesses in Kenya\n\n";
        
        $prompt .= "2. **INDUSTRY COMPARISON**:\n";
        $prompt .= "   - Compare this business's metrics with similar online businesses in Kenya and East Africa\n";
        $prompt .= "   - Reference typical average order values, conversion rates, and customer retention for similar businesses\n";
        $prompt .= "   - Highlight areas where the business is outperforming or underperforming industry standards\n\n";
        
        $prompt .= "3. **GROWTH OPPORTUNITIES**:\n";
        $prompt .= "   - Identify specific, actionable strategies to increase sales and revenue\n";
        $prompt .= "   - Suggest product mix optimization based on top sellers\n";
        $prompt .= "   - Recommend customer acquisition and retention strategies\n";
        $prompt .= "   - Propose pricing optimization opportunities\n\n";
        
        $prompt .= "4. **OPERATIONAL IMPROVEMENTS**:\n";
        $prompt .= "   - Address inventory management concerns (low stock, out of stock items)\n";
        $prompt .= "   - Suggest ways to improve average order value\n";
        $prompt .= "   - Recommend customer experience enhancements\n\n";
        
        $prompt .= "5. **RISK FACTORS**:\n";
        $prompt .= "   - Identify potential risks based on the data\n";
        $prompt .= "   - Suggest mitigation strategies\n\n";
        
        $prompt .= "6. **SPECIFIC ACTIONABLE RECOMMENDATIONS**:\n";
        $prompt .= "   - Provide 5-7 concrete, immediately actionable steps the business owner can take\n";
        $prompt .= "   - Prioritize recommendations by potential impact\n";
        $prompt .= "   - Include both quick wins and long-term strategies\n\n";
        
        $prompt .= "Format your response in clear, well-structured sections with markdown formatting. ";
        $prompt .= "Use bullet points, headings, and emphasis where appropriate. ";
        $prompt .= "Be specific, data-driven, and provide context by referencing similar businesses in the Kenyan market. ";
        $prompt .= "Focus on practical, implementable advice that will help grow the business.";
        
        return $prompt;
    }

    /**
     * Generate quick insights for specific metrics
     */
    public function generateQuickInsight($metric, $value, $context = [])
    {
        try {
            $prompt = "As a business analyst, provide a brief insight (2-3 sentences) about this metric:\n\n";
            $prompt .= "Metric: {$metric}\n";
            $prompt .= "Value: {$value}\n";
            
            if (!empty($context)) {
                $prompt .= "Context: " . json_encode($context, JSON_PRETTY_PRINT) . "\n";
            }
            
            $prompt .= "\nProvide actionable insight about what this means for the business and what actions to consider.";
            
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 512,
                'temperature' => 0.7,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['content'][0]['text'] ?? 'No insight generated.';
            }
            
            return "This metric requires attention for business optimization.";
            
        } catch (\Exception $e) {
            Log::error('Claude Quick Insight Error: ' . $e->getMessage());
            return "This is an important metric for tracking business performance.";
        }
    }

    /**
     * Generate fallback analysis if API fails
     */
    protected function generateFallbackAnalysis($businessData)
    {
        $analysis = "# Business Analysis Report\n\n";
        $analysis .= "## Performance Overview\n\n";
        
        if (isset($businessData['sales_data'])) {
            $salesData = $businessData['sales_data'];
            $totalSales = $salesData['total_sales'] ?? 0;
            $totalOrders = $salesData['total_orders'] ?? 0;
            $avgOrderValue = $salesData['average_order_value'] ?? 0;
            
            $analysis .= "**Sales Performance:**\n";
            $analysis .= "- Your business has generated KSh " . number_format($totalSales, 2) . " in total sales\n";
            $analysis .= "- With {$totalOrders} completed orders\n";
            $analysis .= "- Average order value: KSh " . number_format($avgOrderValue, 2) . "\n\n";
            
            if ($avgOrderValue > 0) {
                if ($avgOrderValue < 500) {
                    $analysis .= "💡 **Recommendation:** Consider upselling or bundling products to increase average order value.\n\n";
                } elseif ($avgOrderValue > 2000) {
                    $analysis .= "✅ **Strong Performance:** Your average order value is healthy. Focus on customer retention.\n\n";
                }
            }
        }
        
        if (isset($businessData['products_data'])) {
            $productsData = $businessData['products_data'];
            $lowStock = $productsData['low_stock_items'] ?? 0;
            $outOfStock = $productsData['out_of_stock'] ?? 0;
            
            $analysis .= "**Inventory Status:**\n";
            if ($lowStock > 0 || $outOfStock > 0) {
                $analysis .= "⚠️ **Alert:** You have {$lowStock} items with low stock and {$outOfStock} items out of stock.\n";
                $analysis .= "**Action Required:** Restock popular items to avoid losing sales opportunities.\n\n";
            } else {
                $analysis .= "✅ Inventory levels are healthy.\n\n";
            }
        }
        
        if (isset($businessData['customers_data'])) {
            $customersData = $businessData['customers_data'];
            $totalCustomers = $customersData['total_customers'] ?? 0;
            $newCustomers = $customersData['new_customers_30_days'] ?? 0;
            
            $analysis .= "**Customer Growth:**\n";
            $analysis .= "- Total customers: {$totalCustomers}\n";
            $analysis .= "- New customers (last 30 days): {$newCustomers}\n\n";
            
            if ($totalCustomers > 0) {
                $retentionRate = (($totalCustomers - $newCustomers) / $totalCustomers) * 100;
                $analysis .= "**Customer Retention:** " . number_format($retentionRate, 1) . "%\n\n";
            }
        }
        
        $analysis .= "## Key Recommendations\n\n";
        $analysis .= "1. **Focus on Customer Retention:** Implement loyalty programs and follow-up campaigns\n";
        $analysis .= "2. **Optimize Product Mix:** Promote top-selling items and consider removing slow movers\n";
        $analysis .= "3. **Inventory Management:** Set up automated alerts for low stock items\n";
        $analysis .= "4. **Marketing Strategy:** Invest in targeted digital marketing to attract similar customer profiles\n";
        $analysis .= "5. **Data Tracking:** Continue monitoring these metrics weekly to identify trends\n\n";
        
        $analysis .= "_Note: This is a basic analysis. For comprehensive AI-powered insights comparing your business with similar online businesses, please ensure your Claude API connection is active._";
        
        return $analysis;
    }

    /**
     * Compare business with industry benchmarks
     */
    public function compareWithIndustry($businessData, $industry = 'retail')
    {
        try {
            $prompt = "Based on the following business data, provide a comparison with typical industry benchmarks for {$industry} businesses in Kenya:\n\n";
            $prompt .= json_encode($businessData, JSON_PRETTY_PRINT) . "\n\n";
            $prompt .= "Include comparisons for:\n";
            $prompt .= "- Average order value vs industry average\n";
            $prompt .= "- Customer retention rate vs industry standard\n";
            $prompt .= "- Revenue growth vs market trends\n";
            $prompt .= "- Inventory turnover vs best practices\n\n";
            $prompt .= "Provide specific insights on how this business compares and what it should focus on to compete better.";
            
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 2048,
                'temperature' => 0.7,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['content'][0]['text'] ?? 'Comparison data unavailable.';
            }
            
            return "Industry comparison requires active API connection.";
            
        } catch (\Exception $e) {
            Log::error('Claude Industry Comparison Error: ' . $e->getMessage());
            return "Unable to generate industry comparison at this time.";
        }
    }

    /**
     * Enhance business description to make it SEO-friendly and improve structure
     */
    public function enhanceBusinessDescription($description, $businessName, $businessType)
    {
        try {
            $prompt = "You are an expert SEO copywriter and business consultant. ";
            $prompt .= "Enhance the following business description to be more SEO-friendly, professional, and engaging.\n\n";
            $prompt .= "Business Name: {$businessName}\n";
            $prompt .= "Business Type: {$businessType}\n";
            $prompt .= "Original Description:\n{$description}\n\n";
            $prompt .= "Requirements:\n";
            $prompt .= "1. Make it SEO-friendly with relevant keywords naturally integrated\n";
            $prompt .= "2. Improve word structure and grammar\n";
            $prompt .= "3. Make it more professional and engaging\n";
            $prompt .= "4. Keep it concise (150-250 words)\n";
            $prompt .= "5. Highlight unique value propositions\n";
            $prompt .= "6. Include location references if relevant (Kenya/Nairobi)\n";
            $prompt .= "7. Use active voice and compelling language\n";
            $prompt .= "8. Format with proper paragraphs if needed\n\n";
            $prompt .= "IMPORTANT: Write in plain text format. Do NOT use markdown formatting like asterisks, underscores, or hashtags. Do NOT use bold or italic formatting. Write naturally as a human would.\n\n";
            $prompt .= "Enhanced Description (return only the enhanced description, no explanations):";
            
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 1024,
                'temperature' => 0.7,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $enhancedText = $data['content'][0]['text'] ?? $description;
                // Clean any markdown formatting that might still appear
                return $this->cleanMarkdownFormatting($enhancedText);
            }
            
            return $description;
            
        } catch (\Exception $e) {
            Log::error('Claude Description Enhancement Error: ' . $e->getMessage());
            return $description;
        }
    }

    /**
     * Generate a concise, website-optimized business description
     * Perfect for website hero sections and about pages (max 500 characters)
     * 
     * @param string $originalDescription Original business description (can be long)
     * @param string $businessName Name of the business
     * @param string $businessType Type of business (store, service, restaurant, etc.)
     * @param string $websiteType Type of website being created
     * @return string Concise, website-ready description (under 500 characters)
     */
    public function generateWebsiteDescription($originalDescription, $businessName, $businessType, $websiteType = 'business')
    {
        try {
            $prompt = "You are an expert website copywriter specializing in creating compelling, concise descriptions for business websites.\n\n";
            $prompt .= "Business Name: {$businessName}\n";
            $prompt .= "Business Type: {$businessType}\n";
            $prompt .= "Website Type: {$websiteType}\n";
            
            if (!empty($originalDescription)) {
                $prompt .= "Original Description:\n{$originalDescription}\n\n";
                $prompt .= "Task: Transform this description into a concise, engaging website-ready description.\n\n";
            } else {
                $prompt .= "Task: Create a professional, engaging description for this business's website.\n\n";
            }
            
            $prompt .= "Requirements:\n";
            $prompt .= "1. Maximum 400 characters (leave room for the 500 limit)\n";
            $prompt .= "2. Write in second or third person (avoid first person 'we/our')\n";
            $prompt .= "3. Focus on what makes the business unique and valuable to customers\n";
            $prompt .= "4. Use clear, compelling language that converts visitors\n";
            $prompt .= "5. Include a subtle call-to-action tone\n";
            $prompt .= "6. Make it suitable for a website hero section or about page\n";
            $prompt .= "7. Keep it professional yet approachable\n";
            $prompt .= "8. Emphasize benefits and outcomes, not just features\n\n";
            $prompt .= "CRITICAL: Write in plain text format. NO markdown, NO asterisks, NO formatting. Just natural, flowing text.\n";
            $prompt .= "DO NOT include any explanations or notes - return ONLY the description text.\n\n";
            $prompt .= "Website-Optimized Description:";
            
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 512,
                'temperature' => 0.8,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $websiteDescription = $data['content'][0]['text'] ?? '';
                
                // Clean any markdown formatting
                $websiteDescription = $this->cleanMarkdownFormatting($websiteDescription);
                
                // Trim to ensure it's under 500 characters
                if (strlen($websiteDescription) > 500) {
                    $websiteDescription = substr($websiteDescription, 0, 497) . '...';
                }
                
                return trim($websiteDescription);
            }
            
            // Fallback: create a basic description if API fails
            return $this->createFallbackWebsiteDescription($businessName, $businessType);
            
        } catch (\Exception $e) {
            Log::error('Website Description Generation Error: ' . $e->getMessage());
            return $this->createFallbackWebsiteDescription($businessName, $businessType);
        }
    }

    /**
     * Create a basic fallback description if AI generation fails
     */
    private function createFallbackWebsiteDescription($businessName, $businessType)
    {
        $typeDescriptions = [
            'store' => "Discover quality products and exceptional service at {$businessName}. Your trusted destination for all your shopping needs.",
            'service' => "Professional services tailored to your needs. {$businessName} delivers excellence and reliability you can count on.",
            'restaurant' => "Experience delicious cuisine and warm hospitality at {$businessName}. Where great food meets great service.",
            'portfolio' => "Showcasing creativity and expertise. Explore the work and services of {$businessName}.",
            'blog' => "Insights, stories, and valuable content from {$businessName}. Stay informed and inspired.",
        ];
        
        return $typeDescriptions[$businessType] ?? "Welcome to {$businessName}. Quality service and customer satisfaction are our priorities.";
    }

    /**
     * Enhance product description with AI
     */
    public function enhanceProductDescription(string $description, string $productName, ?string $category = null): string
    {
        try {
            $prompt = "You are an expert product copywriter specializing in e-commerce and SEO optimization.\n\n";
            $prompt .= "Product Name: {$productName}\n";
            if ($category) {
                $prompt .= "Category: {$category}\n";
            }
            $prompt .= "Original Description:\n{$description}\n\n";
            $prompt .= "Requirements:\n";
            $prompt .= "1. Create an SEO-optimized product description with relevant keywords\n";
            $prompt .= "2. Improve grammar, readability, and professional tone\n";
            $prompt .= "3. Highlight key features and benefits\n";
            $prompt .= "4. Keep it concise (100-200 words)\n";
            $prompt .= "5. Use persuasive language that drives conversions\n";
            $prompt .= "6. Include use cases or applications if relevant\n";
            $prompt .= "7. Make it scannable with short paragraphs\n";
            $prompt .= "8. Target Kenyan market context where applicable\n\n";
            $prompt .= "IMPORTANT: Write in plain text format. Do NOT use markdown formatting like asterisks, underscores, or hashtags. Do NOT use bold or italic formatting. Write naturally as a human would.\n\n";
            $prompt .= "Enhanced Product Description (return only the enhanced description, no explanations):";
            
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 800,
                'temperature' => 0.7,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $enhancedText = $data['content'][0]['text'] ?? $description;
                // Clean any markdown formatting that might still appear
                return $this->cleanMarkdownFormatting($enhancedText);
            }
            
            return $description;
            
        } catch (\Exception $e) {
            Log::error('Claude Product Description Enhancement Error: ' . $e->getMessage());
            return $description;
        }
    }

    /**
     * Enhance service description with AI
     */
    public function enhanceServiceDescription(string $description, string $serviceName, ?int $duration = null): string
    {
        try {
            $prompt = "You are an expert service copywriter specializing in service-based businesses and customer engagement.\n\n";
            $prompt .= "Service Name: {$serviceName}\n";
            if ($duration) {
                $prompt .= "Duration: {$duration} minutes\n";
            }
            $prompt .= "Original Description:\n{$description}\n\n";
            $prompt .= "Requirements:\n";
            $prompt .= "1. Create an engaging, professional service description\n";
            $prompt .= "2. Optimize for search visibility with natural keyword integration\n";
            $prompt .= "3. Highlight what's included, benefits, and value proposition\n";
            $prompt .= "4. Keep it clear and concise (100-200 words)\n";
            $prompt .= "5. Use customer-focused language that builds trust\n";
            $prompt .= "6. Address common customer concerns or questions\n";
            $prompt .= "7. Include any relevant certifications, experience, or expertise\n";
            $prompt .= "8. Make it compelling for booking/purchase decisions\n";
            $prompt .= "9. Consider Kenyan market context and customer expectations\n\n";
            $prompt .= "IMPORTANT: Write in plain text format. Do NOT use markdown formatting like asterisks, underscores, or hashtags. Do NOT use bold or italic formatting. Write naturally as a human would.\n\n";
            $prompt .= "Enhanced Service Description (return only the enhanced description, no explanations):";
            
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 800,
                'temperature' => 0.7,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $enhancedText = $data['content'][0]['text'] ?? $description;
                // Clean any markdown formatting that might still appear
                return $this->cleanMarkdownFormatting($enhancedText);
            }
            
            return $description;
            
        } catch (\Exception $e) {
            Log::error('Claude Service Description Enhancement Error: ' . $e->getMessage());
            return $description;
        }
    }

    /**
     * Chat with business context - comprehensive business intelligence assistant
     */
    public function chatWithBusinessContext(string $userMessage, array $businessData, $business = null): string
    {
        try {
            $prompt = $this->buildBusinessChatPrompt($userMessage, $businessData, $business);
            
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 2048,
                'temperature' => 0.7,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['content'][0]['text'] ?? 'I apologize, but I could not process your request at this time.';
            }
            
            return 'I apologize, but I encountered an issue connecting to the AI service. Please try again.';
            
        } catch (\Exception $e) {
            Log::error('Claude Chat Error: ' . $e->getMessage());
            return 'I apologize, but I encountered an error. Please try again later.';
        }
    }

    /**
     * Build comprehensive prompt for business chat
     */
    protected function buildBusinessChatPrompt(string $userMessage, array $businessData, $business): string
    {
        $prompt = "You are an expert business intelligence assistant for a Kenyan business management platform called Shopybook. ";
        $prompt .= "You provide actionable insights, recommendations, and answers based on the business's actual data.\n\n";
        
        $prompt .= "## YOUR CAPABILITIES:\n";
        $prompt .= "- Analyze sales performance, trends, and patterns\n";
        $prompt .= "- Identify top products/services and underperforming items\n";
        $prompt .= "- Provide pricing recommendations based on market research\n";
        $prompt .= "- Suggest inventory optimization strategies\n";
        $prompt .= "- Compare business performance against industry benchmarks in Kenya\n";
        $prompt .= "- Recommend suppliers and cost-saving opportunities\n";
        $prompt .= "- Provide customer insights and retention strategies\n";
        $prompt .= "- Suggest marketing and growth strategies\n";
        $prompt .= "- Answer specific questions about their business metrics\n\n";

        if ($business && !empty($businessData)) {
            $prompt .= "## BUSINESS PROFILE:\n";
            $prompt .= "**Business Name:** " . ($businessData['business_info']['name'] ?? 'N/A') . "\n";
            $prompt .= "**Business Type:** " . ($businessData['business_info']['type'] ?? 'N/A') . "\n";
            $prompt .= "**Location:** " . ($businessData['business_info']['location'] ?? 'Kenya') . "\n";
            if (!empty($businessData['business_info']['description'])) {
                $prompt .= "**Description:** " . $businessData['business_info']['description'] . "\n";
            }
            $prompt .= "\n";

            // Products data
            if (isset($businessData['products'])) {
                $products = $businessData['products'];
                $prompt .= "## PRODUCTS INVENTORY:\n";
                $prompt .= "- Total Products: {$products['total']}\n";
                $prompt .= "- Active Products: {$products['active']}\n";
                $prompt .= "- Low Stock Items: {$products['low_stock']}\n";
                $prompt .= "- Out of Stock: {$products['out_of_stock']}\n\n";

                if (!empty($products['top_products'])) {
                    $prompt .= "### Top Performing Products:\n";
                    foreach ($products['top_products'] as $index => $product) {
                        $profit = ($product['price'] ?? 0) - ($product['cost'] ?? 0);
                        $margin = $product['price'] > 0 ? round(($profit / $product['price']) * 100, 1) : 0;
                        $prompt .= ($index + 1) . ". **{$product['name']}**\n";
                        $prompt .= "   - Price: KSh " . number_format($product['price'], 2) . " | Cost: KSh " . number_format($product['cost'] ?? 0, 2) . "\n";
                        $prompt .= "   - Profit Margin: {$margin}% | Stock: {$product['stock']}\n";
                        $prompt .= "   - Total Sold: {$product['total_sold']} units | Revenue: KSh " . number_format($product['revenue'] ?? 0, 2) . "\n";
                        if (!empty($product['category'])) {
                            $prompt .= "   - Category: {$product['category']}\n";
                        }
                    }
                    $prompt .= "\n";
                }

                if (!empty($products['by_category'])) {
                    $prompt .= "### Products by Category:\n";
                    foreach ($products['by_category'] as $cat) {
                        $prompt .= "- {$cat['category']}: {$cat['count']} products (Avg Price: KSh " . number_format($cat['avg_price'], 2) . ")\n";
                    }
                    $prompt .= "\n";
                }
            }

            // Services data
            if (isset($businessData['services'])) {
                $services = $businessData['services'];
                $prompt .= "## SERVICES OFFERED:\n";
                $prompt .= "- Total Services: {$services['total']}\n";
                $prompt .= "- Average Price: KSh " . number_format($services['avg_price'], 2) . "\n";
                $prompt .= "- Average Duration: {$services['avg_duration']} minutes\n\n";

                if (!empty($services['top_services'])) {
                    $prompt .= "### Most Popular Services:\n";
                    foreach ($services['top_services'] as $index => $service) {
                        $prompt .= ($index + 1) . ". {$service['name']} - KSh " . number_format($service['price'], 2);
                        $prompt .= " ({$service['duration']} min, {$service['bookings']} bookings)\n";
                    }
                    $prompt .= "\n";
                }
            }

            // Sales data
            if (isset($businessData['sales'])) {
                $sales = $businessData['sales'];
                $prompt .= "## SALES PERFORMANCE:\n";
                $prompt .= "- Total Revenue: KSh " . number_format($sales['total_revenue'], 2) . "\n";
                $prompt .= "- Total Orders: {$sales['total_orders']}\n";
                $prompt .= "- Average Order Value: KSh " . number_format($sales['avg_order_value'], 2) . "\n\n";

                if (isset($sales['last_30_days'])) {
                    $prompt .= "### Last 30 Days:\n";
                    $prompt .= "- Revenue: KSh " . number_format($sales['last_30_days']['revenue'], 2) . "\n";
                    $prompt .= "- Orders: {$sales['last_30_days']['orders']}\n";
                    $prompt .= "- Average Order: KSh " . number_format($sales['last_30_days']['avg_order'] ?? 0, 2) . "\n\n";
                }

                if (isset($sales['last_7_days'])) {
                    $prompt .= "### Last 7 Days:\n";
                    $prompt .= "- Revenue: KSh " . number_format($sales['last_7_days']['revenue'], 2) . "\n";
                    $prompt .= "- Orders: {$sales['last_7_days']['orders']}\n\n";
                }

                if (!empty($sales['by_payment_method'])) {
                    $prompt .= "### Payment Methods:\n";
                    foreach ($sales['by_payment_method'] as $payment) {
                        $prompt .= "- {$payment['method']}: {$payment['count']} orders (KSh " . number_format($payment['total'], 2) . ")\n";
                    }
                    $prompt .= "\n";
                }
            }

            // Customer data
            if (isset($businessData['customers'])) {
                $customers = $businessData['customers'];
                $prompt .= "## CUSTOMER BASE:\n";
                $prompt .= "- Total Customers: {$customers['total']}\n";
                $prompt .= "- Customers with Orders: {$customers['with_orders']}\n";
                $prompt .= "- New This Month: {$customers['new_this_month']}\n\n";

                if (!empty($customers['top_customers'])) {
                    $prompt .= "### Top Customers:\n";
                    foreach ($customers['top_customers'] as $index => $customer) {
                        $prompt .= ($index + 1) . ". {$customer['name']} - {$customer['orders']} orders (KSh " . number_format($customer['total_spent'], 2) . ")\n";
                    }
                    $prompt .= "\n";
                }
            }

            // Workforce data (Staff & Employees)
            if (isset($businessData['workforce'])) {
                $workforce = $businessData['workforce'];
                $prompt .= "## WORKFORCE & LABOR COSTS:\n";
                $prompt .= "- Total Workforce: {$workforce['total_workforce']} people\n";
                $prompt .= "  - Staff Members: {$workforce['total_staff']}\n";
                $prompt .= "  - Employees: {$workforce['total_employees']}\n";
                $prompt .= "- Monthly Salaries: KSh " . number_format($workforce['total_monthly_salaries'], 2) . "\n";
                $prompt .= "- Commissions Paid: KSh " . number_format($workforce['total_commissions_paid'], 2) . "\n";
                $prompt .= "- Total Labor Cost: KSh " . number_format($workforce['total_labor_cost'], 2) . "\n\n";

                if (!empty($workforce['staff_breakdown'])) {
                    $prompt .= "### Staff Details:\n";
                    foreach ($workforce['staff_breakdown'] as $staff) {
                        $prompt .= "- {$staff['name']} ({$staff['role']})\n";
                        $prompt .= "  - Salary: KSh " . number_format($staff['salary'] ?? 0, 2) . "\n";
                        $prompt .= "  - Commission Earned: KSh " . number_format($staff['commission_earned'], 2) . "\n";
                        $prompt .= "  - Total Earnings: KSh " . number_format($staff['total_earnings'], 2) . "\n";
                    }
                    $prompt .= "\n";
                }

                if (!empty($workforce['staff_by_role'])) {
                    $prompt .= "### Staff by Role:\n";
                    foreach ($workforce['staff_by_role'] as $role) {
                        $prompt .= "- {$role['role']}: {$role['count']} staff (Total Salary: KSh " . number_format($role['total_salary'], 2) . ")\n";
                    }
                    $prompt .= "\n";
                }

                if (!empty($workforce['employees_by_department'])) {
                    $prompt .= "### Employees by Department:\n";
                    foreach ($workforce['employees_by_department'] as $dept) {
                        $prompt .= "- {$dept['department']}: {$dept['count']} employees (Total Salary: KSh " . number_format($dept['total_salary'], 2) . ")\n";
                    }
                    $prompt .= "\n";
                }

                if (!empty($workforce['employees_by_type'])) {
                    $prompt .= "### Employment Types:\n";
                    foreach ($workforce['employees_by_type'] as $type) {
                        $prompt .= "- {$type['type']}: {$type['count']} employees\n";
                    }
                    $prompt .= "\n";
                }
            }

            // Payments data
            if (isset($businessData['payments'])) {
                $payments = $businessData['payments'];
                $prompt .= "## PAYMENTS & TRANSACTIONS:\n";
                $prompt .= "- Total Received: KSh " . number_format($payments['total_received'], 2) . "\n";
                $prompt .= "- Pending Payments: KSh " . number_format($payments['pending_payments'], 2) . "\n";
                $prompt .= "- Failed Payments: {$payments['failed_payments']}\n\n";

                if (!empty($payments['by_method'])) {
                    $prompt .= "### Payment Methods Breakdown:\n";
                    foreach ($payments['by_method'] as $method) {
                        $prompt .= "- {$method['method']}: {$method['count']} transactions\n";
                        $prompt .= "  - Total: KSh " . number_format($method['total'], 2) . "\n";
                        $prompt .= "  - Completed: KSh " . number_format($method['completed'], 2) . "\n";
                    }
                    $prompt .= "\n";
                }
            }

            // Suppliers data
            if (isset($businessData['suppliers'])) {
                $suppliers = $businessData['suppliers'];
                $prompt .= "## SUPPLIERS:\n";
                $prompt .= "- Total Suppliers: {$suppliers['total']}\n";
                $prompt .= "- Active Suppliers: {$suppliers['active']}\n\n";

                if (!empty($suppliers['suppliers_list'])) {
                    $prompt .= "### Supplier List:\n";
                    foreach ($suppliers['suppliers_list'] as $supplier) {
                        $prompt .= "- {$supplier['name']} ({$supplier['status']})\n";
                        if (!empty($supplier['contact'])) {
                            $prompt .= "  - Contact: {$supplier['contact']}\n";
                        }
                        if (!empty($supplier['phone'])) {
                            $prompt .= "  - Phone: {$supplier['phone']}\n";
                        }
                    }
                    $prompt .= "\n";
                }
            }

            // Costs & Expenses
            if (isset($businessData['costs_expenses'])) {
                $costs = $businessData['costs_expenses'];
                $prompt .= "## COSTS & EXPENSES:\n";
                $prompt .= "- Inventory Value: KSh " . number_format($costs['inventory_value'], 2) . "\n";
                $prompt .= "- Monthly Salaries: KSh " . number_format($costs['monthly_salaries'], 2) . "\n";
                $prompt .= "- Commissions: KSh " . number_format($costs['commissions'], 2) . "\n";
                $prompt .= "- Returns & Refunds: KSh " . number_format($costs['returns_refunds'], 2) . "\n";
                $prompt .= "- **Total Monthly Expenses: KSh " . number_format($costs['total_monthly_expenses'], 2) . "**\n\n";
            }

            // Profitability Analysis
            if (isset($businessData['profitability'])) {
                $profit = $businessData['profitability'];
                $prompt .= "## PROFITABILITY ANALYSIS:\n";
                $prompt .= "- Total Revenue: KSh " . number_format($profit['total_revenue'], 2) . "\n";
                $prompt .= "- Cost of Goods Sold: KSh " . number_format($profit['cost_of_goods_sold'], 2) . "\n";
                $prompt .= "- **Gross Profit: KSh " . number_format($profit['gross_profit'], 2) . "** ({$profit['gross_profit_margin']}% margin)\n";
                $prompt .= "- Operating Expenses: KSh " . number_format($profit['operating_expenses'], 2) . "\n";
                $prompt .= "- **Net Profit: KSh " . number_format($profit['net_profit'], 2) . "** ({$profit['net_profit_margin']}% margin)\n\n";
            }
        } else {
            $prompt .= "**Note:** No specific business selected. Please select a business to get detailed insights.\n\n";
        }

        $prompt .= "## USER QUESTION:\n";
        $prompt .= $userMessage . "\n\n";

        $prompt .= "## RESPONSE GUIDELINES:\n";
        $prompt .= "1. Be conversational, helpful, and encouraging\n";
        $prompt .= "2. Use the actual business data provided to give specific, actionable advice\n";
        $prompt .= "3. Include numbers and metrics from their data when relevant\n";
        $prompt .= "4. For pricing questions, consider Kenyan market context and competitive positioning\n";
        $prompt .= "5. For supplier recommendations, suggest practical online/local options in Kenya\n";
        $prompt .= "6. For growth advice, be specific about which products/services to focus on\n";
        $prompt .= "7. If data is limited, acknowledge it and provide general best practices\n";
        $prompt .= "8. Format your response clearly with bullet points or sections when appropriate\n";
        $prompt .= "9. For cost/expense questions, analyze the labor costs, inventory, and profitability\n";
        $prompt .= "10. For employee questions, consider workforce structure and compensation fairness\n";
        $prompt .= "11. Always end with a practical next step or recommendation\n\n";

        $prompt .= "**Your Response:**";

        return $prompt;
    }

    /**
     * Recommend best theme for business based on type and industry
     */
    public function recommendTheme($businessData, $availableThemes)
    {
        try {
            $businessType = $businessData['type'] ?? 'general';
            $businessName = $businessData['name'] ?? 'Business';
            $description = $businessData['description'] ?? '';
            
            $themesInfo = collect($availableThemes)->map(function($theme) {
                return "- **{$theme->name}** ({$theme->slug}): {$theme->description} | Category: {$theme->category} | Style: {$theme->style}";
            })->join("\n");

            $prompt = "You are an expert web designer specializing in matching businesses with perfect website themes.\n\n";
            $prompt .= "**Business Information:**\n";
            $prompt .= "- Name: {$businessName}\n";
            $prompt .= "- Type: {$businessType}\n";
            if ($description) {
                $prompt .= "- Description: {$description}\n";
            }
            $prompt .= "\n**Available Themes:**\n{$themesInfo}\n\n";
            $prompt .= "**Task:** Recommend the TOP 3 most suitable themes for this business.\n\n";
            $prompt .= "**Response Format:** Return ONLY a JSON array with theme slugs and reasons:\n";
            $prompt .= "```json\n[\n  {\"slug\": \"theme-slug\", \"reason\": \"Why this theme suits the business\"},\n  ...\n]\n```";

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 1024,
                'temperature' => 0.5,
                'messages' => [['role' => 'user', 'content' => $prompt]]
            ]);

            if ($response->successful()) {
                $text = $response->json()['content'][0]['text'] ?? '';
                preg_match('/\[.*\]/s', $text, $matches);
                if (!empty($matches[0])) {
                    return json_decode($matches[0], true);
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Theme Recommendation Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate SEO-optimized content for website sections
     */
    public function generateSectionContent($sectionType, $businessData, $existingContent = null)
    {
        try {
            $businessName = $businessData['name'] ?? 'Business';
            $businessType = $businessData['type'] ?? 'general';
            $description = $businessData['description'] ?? '';
            $location = $businessData['location'] ?? 'Kenya';

            $prompt = "You are an expert content writer and SEO specialist.\n\n";
            $prompt .= "Business Information:\n";
            $prompt .= "- Name: {$businessName}\n";
            $prompt .= "- Type: {$businessType}\n";
            $prompt .= "- Location: {$location}\n";
            if ($description) {
                $prompt .= "- Description: {$description}\n";
            }

            if ($existingContent) {
                $prompt .= "\nExisting Content to Improve:\n" . json_encode($existingContent, JSON_PRETTY_PRINT) . "\n";
            }

            $prompt .= "\nSection Type: {$sectionType}\n\n";

            $contentSpecs = $this->getSectionContentSpecs($sectionType);
            $prompt .= "Requirements:\n{$contentSpecs}\n\n";
            $prompt .= "IMPORTANT: Write in plain text format. Do NOT use markdown formatting like asterisks, underscores, or hashtags. Do NOT use bold or italic formatting. Write naturally as a human would.\n\n";
            $prompt .= "Output: Return ONLY a JSON object with the content fields. No explanations.\n";

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(40)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 2048,
                'temperature' => 0.7,
                'messages' => [['role' => 'user', 'content' => $prompt]]
            ]);

            if ($response->successful()) {
                $text = $response->json()['content'][0]['text'] ?? '';
                preg_match('/\{.*\}/s', $text, $matches);
                if (!empty($matches[0])) {
                    $jsonData = json_decode($matches[0], true);
                    // Clean markdown from all text fields in the JSON
                    if ($jsonData) {
                        array_walk_recursive($jsonData, function(&$value) {
                            if (is_string($value)) {
                                $value = $this->cleanMarkdownFormatting($value);
                            }
                        });
                    }
                    return $jsonData;
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Section Content Generation Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get content specifications for each section type
     */
    protected function getSectionContentSpecs($sectionType)
    {
        return match($sectionType) {
            'hero' => "Generate a compelling hero section with:\n- heading (max 60 chars, powerful, SEO-friendly)\n- subheading (max 120 chars, value proposition)\n- cta_text (max 25 chars, action-oriented)\n- cta_link (use #contact as default)",
            
            'about' => "Generate an about section with:\n- heading (max 50 chars)\n- content (200-300 words, engaging story, SEO-optimized, include keywords)\n- mission (50-80 words)\n- vision (50-80 words)",
            
            'services' => "Generate services section with:\n- heading (max 50 chars)\n- description (80-120 words)\n- services (array of 3-6 service objects with: title, description, icon_suggestion)",
            
            'features' => "Generate features section with:\n- heading (max 50 chars)\n- features (array of 4-6 feature objects with: title, description, icon_suggestion)",
            
            'team' => "Generate team section with:\n- heading (max 50 chars)\n- description (60-100 words)",
            
            'testimonials' => "Generate testimonials section with:\n- heading (max 50 chars)\n- testimonials (array of 3-5 testimonial objects with: quote, author, role, rating)",
            
            'contact' => "Generate contact section with:\n- heading (max 50 chars)\n- description (60-100 words, encourage contact)\n- map_embed_hint",
            
            'gallery' => "Generate gallery section with:\n- heading (max 50 chars)\n- description (60-100 words)",
            
            'pricing' => "Generate pricing section with:\n- heading (max 50 chars)\n- description (60-100 words)\n- plans (array of 3 pricing plans with: name, price, features array, recommended boolean)",
            
            'cta' => "Generate call-to-action section with:\n- heading (max 60 chars, urgent, compelling)\n- text (60-100 words, benefits-focused)\n- button_text (max 25 chars)\n- button_link (use #contact)",
            
            default => "Generate appropriate content for {$sectionType} section with heading and relevant fields",
        };
    }

    /**
     * Generate complete website guidance for user
     */
    public function generateWebsiteGuidance($businessData, $selectedTheme = null)
    {
        try {
            $businessName = $businessData['name'] ?? 'Your Business';
            $businessType = $businessData['type'] ?? 'general';
            
            $prompt = "You are a professional website consultant guiding a business owner through website creation.\n\n";
            $prompt .= "Business: {$businessName} ({$businessType})\n";
            if ($selectedTheme) {
                $prompt .= "Selected Theme: {$selectedTheme}\n";
            }
            $prompt .= "\nTask: Provide a comprehensive, step-by-step guide for building their website.\n\n";
            $prompt .= "Include:\n";
            $prompt .= "1. Recommended pages to create (Home, About, Services, Contact, etc.)\n";
            $prompt .= "2. Essential sections for each page\n";
            $prompt .= "3. Content strategy tips specific to their business type\n";
            $prompt .= "4. SEO best practices\n";
            $prompt .= "5. Call-to-action placement recommendations\n";
            $prompt .= "6. Mobile optimization tips\n";
            $prompt .= "7. Color scheme suggestions based on business type\n";
            $prompt .= "8. Key features to highlight\n\n";
            $prompt .= "IMPORTANT: Write in plain text format. Do NOT use markdown formatting like asterisks, underscores, or hashtags. Do NOT use bold or italic formatting. Write naturally as a human would.\n\n";
            $prompt .= "Make it actionable, encouraging, and tailored to {$businessType} businesses in Kenya.";

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 3000,
                'temperature' => 0.7,
                'messages' => [['role' => 'user', 'content' => $prompt]]
            ]);

            if ($response->successful()) {
                $text = $response->json()['content'][0]['text'] ?? '';
                // Clean any markdown formatting
                return $this->cleanMarkdownFormatting($text);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Website Guidance Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate SEO metadata for website pages
     */
    public function generateSEOMetadata($pageData, $businessData)
    {
        try {
            $pageName = $pageData['name'] ?? 'Page';
            $businessName = $businessData['name'] ?? 'Business';
            $businessType = $businessData['type'] ?? 'general';
            $location = $businessData['location'] ?? 'Kenya';

            $prompt = "Generate SEO metadata for a website page.\n\n";
            $prompt .= "**Business:** {$businessName} ({$businessType}) in {$location}\n";
            $prompt .= "**Page:** {$pageName}\n\n";
            $prompt .= "**Generate:**\n";
            $prompt .= "1. meta_title (max 60 chars, include business name and keywords)\n";
            $prompt .= "2. meta_description (max 155 chars, compelling, keyword-rich)\n";
            $prompt .= "3. meta_keywords (10-15 relevant keywords, comma-separated)\n";
            $prompt .= "4. og_title (social media optimized)\n";
            $prompt .= "5. og_description (social media optimized)\n\n";
            $prompt .= "**Output:** JSON object only, no explanations.\n";

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 512,
                'temperature' => 0.6,
                'messages' => [['role' => 'user', 'content' => $prompt]]
            ]);

            if ($response->successful()) {
                $text = $response->json()['content'][0]['text'] ?? '';
                preg_match('/\{.*\}/s', $text, $matches);
                if (!empty($matches[0])) {
                    return json_decode($matches[0], true);
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('SEO Metadata Generation Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Suggest AI-generated image prompts for website sections
     */
    public function suggestImagePrompts($sectionType, $businessData)
    {
        try {
            $businessName = $businessData['name'] ?? 'Business';
            $businessType = $businessData['type'] ?? 'general';

            $prompt = "Generate professional image descriptions for an AI image generator (like DALL-E or Midjourney).\n\n";
            $prompt .= "**Business:** {$businessName} ({$businessType})\n";
            $prompt .= "**Section:** {$sectionType}\n\n";
            $prompt .= "**Task:** Create 3-5 detailed image prompts suitable for this section.\n\n";
            $prompt .= "**Requirements:**\n";
            $prompt .= "- Professional quality\n";
            $prompt .= "- Relevant to {$businessType} business\n";
            $prompt .= "- Suitable for Kenyan context where relevant\n";
            $prompt .= "- Diverse representation\n";
            $prompt .= "- Modern, clean aesthetic\n\n";
            $prompt .= "**Output:** JSON array of objects with 'prompt' and 'description' fields.";

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 1024,
                'temperature' => 0.8,
                'messages' => [['role' => 'user', 'content' => $prompt]]
            ]);

            if ($response->successful()) {
                $text = $response->json()['content'][0]['text'] ?? '';
                preg_match('/\[.*\]/s', $text, $matches);
                if (!empty($matches[0])) {
                    return json_decode($matches[0], true);
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Image Prompt Generation Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate complete website structure with pages and sections (Enterprise feature)
     */
    public function generateCompleteWebsite($businessData, $selectedTheme = null)
    {
        try {
            $businessName = $businessData['name'] ?? 'Business';
            $businessType = $businessData['type'] ?? 'general';
            $description = $businessData['description'] ?? '';
            $location = $businessData['location'] ?? 'Kenya';

            $prompt = "You are an expert website architect and content strategist. Create a rich, publish-ready website for this business.\n\n";
            $prompt .= "**Business Information:**\n";
            $prompt .= "- Name: {$businessName}\n";
            $prompt .= "- Type: {$businessType}\n";
            $prompt .= "- Location: {$location}\n";
            if ($description) {
                $prompt .= "- Description: {$description}\n";
            }
            if ($selectedTheme) {
                $prompt .= "- Selected Theme: {$selectedTheme}\n";
            }
            $prompt .= "\n**CRITICAL RULES:**\n";
            $prompt .= "1. Page titles: 1-2 words max (Home, About, Services, Products, Contact)\n";
            $prompt .= "2. Footer about text: max 25 words, single sentence\n";
            $prompt .= "3. Every section MUST have \"image_query\": a 5-8 word phrase describing the ideal photo for that section, SPECIFIC to the business products/services (e.g. for a bedding shop: \"luxury duvet pillows bed linen store\", for a greenhouse: \"greenhouse plants seedlings nursery wholesale\")\n";
            $prompt .= "4. All cta_link values: anchor format only (#contact, #services, #about) — NEVER /contact or /about\n";
            $prompt .= "5. All content must be unique and SPECIFIC to this business — no generic filler text\n\n";
            $prompt .= "**HOMEPAGE MUST HAVE EXACTLY THESE 9 SECTIONS IN ORDER:**\n";
            $prompt .= "1. hero — power headline + subheading + 2 CTAs + 4 business stats (numbers that prove credibility)\n";
            $prompt .= "2. features — 6-8 key selling points (icon + title) specific to what this business offers\n";
            $prompt .= "3. about — 3-paragraph story about the business + 5 bullet points proving quality + 3 achievement stats\n";
            $prompt .= "4. services — 5-6 service/product category cards (icon + title + 2-sentence description)\n";
            $prompt .= "5. stats — standalone achievements: 4 impressive numbers (customers served, years experience, products, ratings etc.)\n";
            $prompt .= "6. testimonials — 3 realistic customer testimonials specific to this business type (name + quote + role)\n";
            $prompt .= "7. cta — strong call-to-action with urgency/offer specific to this business\n";
            $prompt .= "8. products — heading + subheading (even if no products in DB yet, include this section)\n";
            $prompt .= "9. contact — heading + subheading + email + phone\n\n";
            $prompt .= "**EXACT JSON SCHEMA — follow this precisely:**\n";
            $prompt .= "```json\n{\n  \"pages\": [\n    {\n";
            $prompt .= "      \"title\": \"Home\",\n      \"slug\": \"home\",\n      \"is_homepage\": true,\n";
            $prompt .= "      \"meta_description\": \"155-char SEO description\",\n      \"meta_keywords\": \"kw1, kw2, kw3\",\n";
            $prompt .= "      \"sections\": [\n";
            $prompt .= "        {\"type\": \"hero\", \"content\": {\"image_query\": \"SPECIFIC 5-8 word photo description\", \"heading\": \"Powerful headline\", \"subheading\": \"Value proposition 1-2 sentences\", \"cta_text\": \"Primary CTA\", \"cta_link\": \"#contact\", \"cta_text_2\": \"Secondary CTA\", \"cta_link_2\": \"#services\", \"stats\": [{\"value\": \"500+\", \"label\": \"Happy Customers\"}, {\"value\": \"10+\", \"label\": \"Years Experience\"}, {\"value\": \"50+\", \"label\": \"Product Varieties\"}, {\"value\": \"4.9★\", \"label\": \"Rating\"}]}},\n";
            $prompt .= "        {\"type\": \"features\", \"content\": {\"image_query\": \"...\", \"heading\": \"Why Choose Us\", \"subheading\": \"Brief subtitle\", \"items\": [{\"icon\": \"fas fa-star\", \"title\": \"Feature Name\"}, {\"icon\": \"fas fa-truck\", \"title\": \"Feature Name\"}, {\"icon\": \"fas fa-shield-alt\", \"title\": \"Feature Name\"}, {\"icon\": \"fas fa-tags\", \"title\": \"Feature Name\"}, {\"icon\": \"fas fa-headset\", \"title\": \"Feature Name\"}, {\"icon\": \"fas fa-award\", \"title\": \"Feature Name\"}]}},\n";
            $prompt .= "        {\"type\": \"about\", \"content\": {\"image_query\": \"...\", \"heading\": \"About {BusinessName}\", \"text\": \"3 detailed paragraphs about the business story, expertise and mission. Total 150-200 words. SPECIFIC to this business.\", \"features\": [\"Quality assurance detail\", \"Experience/expertise detail\", \"Customer service detail\", \"Product/service detail\", \"Value/pricing detail\"], \"stats\": [{\"value\": \"2012\", \"label\": \"Founded\"}, {\"value\": \"1000+\", \"label\": \"Orders Delivered\"}, {\"value\": \"98%\", \"label\": \"Satisfaction Rate\"}]}},\n";
            $prompt .= "        {\"type\": \"services\", \"content\": {\"image_query\": \"...\", \"heading\": \"What We Offer\", \"subheading\": \"Brief description\", \"items\": [{\"icon\": \"fas fa-box\", \"title\": \"Category/Service\", \"description\": \"2-sentence description specific to this business\"}, {\"icon\": \"fas fa-handshake\", \"title\": \"Category/Service\", \"description\": \"2-sentence description\"}, {\"icon\": \"fas fa-shipping-fast\", \"title\": \"Category/Service\", \"description\": \"2-sentence description\"}, {\"icon\": \"fas fa-tools\", \"title\": \"Category/Service\", \"description\": \"2-sentence description\"}, {\"icon\": \"fas fa-certificate\", \"title\": \"Category/Service\", \"description\": \"2-sentence description\"}]}},\n";
            $prompt .= "        {\"type\": \"stats\", \"content\": {\"image_query\": \"...\", \"heading\": \"Our Numbers Speak\", \"subheading\": \"Brief subtitle\", \"items\": [{\"icon\": \"fas fa-users\", \"value\": \"500+\", \"label\": \"Customers Served\"}, {\"icon\": \"fas fa-calendar\", \"value\": \"10+\", \"label\": \"Years in Business\"}, {\"icon\": \"fas fa-box\", \"value\": \"1000+\", \"label\": \"Products Delivered\"}, {\"icon\": \"fas fa-star\", \"value\": \"4.9/5\", \"label\": \"Average Rating\"}]}},\n";
            $prompt .= "        {\"type\": \"testimonials\", \"content\": {\"image_query\": \"...\", \"heading\": \"What Customers Say\", \"items\": [{\"name\": \"Customer Name\", \"role\": \"Customer role/location\", \"quote\": \"Specific, detailed testimonial about the product/service. 2-3 sentences.\"}, {\"name\": \"Customer Name\", \"role\": \"Customer role/location\", \"quote\": \"Specific, detailed testimonial. 2-3 sentences.\"}, {\"name\": \"Customer Name\", \"role\": \"Customer role/location\", \"quote\": \"Specific, detailed testimonial. 2-3 sentences.\"}]}},\n";
            $prompt .= "        {\"type\": \"cta\", \"content\": {\"image_query\": \"...\", \"heading\": \"Compelling CTA headline\", \"subheading\": \"1-sentence urgency/offer\", \"cta_text\": \"Get Started Now\", \"cta_link\": \"#contact\"}},\n";
            $prompt .= "        {\"type\": \"products\", \"content\": {\"image_query\": \"...\", \"heading\": \"Featured Products\", \"subheading\": \"A preview of our collection\", \"show_price\": true, \"max_products\": 4}},\n";
            $prompt .= "        {\"type\": \"contact\", \"content\": {\"image_query\": \"...\", \"heading\": \"Get In Touch\", \"subheading\": \"We'd love to hear from you\", \"show_form\": true, \"email\": \"info@{business}.com\", \"phone\": \"phone number\"}}\n";
            $prompt .= "      ]\n    },\n";
            $prompt .= "    {\"title\": \"About\", \"slug\": \"about\", \"is_homepage\": false, \"meta_description\": \"...\", \"meta_keywords\": \"...\", \"sections\": [about section, team section, values section]},\n";
            $prompt .= "    {\"title\": \"Services\", \"slug\": \"services\", \"is_homepage\": false, \"meta_description\": \"...\", \"meta_keywords\": \"...\", \"sections\": [services section with 6+ items, features section, cta section]},\n";
            $prompt .= "    {\"title\": \"Products\", \"slug\": \"products\", \"is_homepage\": false, \"meta_description\": \"...\", \"meta_keywords\": \"...\", \"sections\": [{\"type\": \"products\", \"content\": {\"image_query\": \"...\", \"heading\": \"All Products\", \"subheading\": \"Full collection\", \"show_price\": true, \"max_products\": 50}}]},\n";
            $prompt .= "    {\"title\": \"Contact\", \"slug\": \"contact\", \"is_homepage\": false, \"meta_description\": \"...\", \"meta_keywords\": \"...\", \"sections\": [contact section with full details]}\n";
            $prompt .= "  ]\n}```\n\n";
            $prompt .= "Fill ALL placeholder text with real, specific content for {$businessName} ({$businessType}) in {$location}.\n";
            $prompt .= "Use FontAwesome icon classes (fas fa-*). Return ONLY valid JSON, no explanations.";

            if (empty($this->apiKey)) {
                Log::error('Claude API key is not configured for website generation');
                throw new \Exception('Claude API key is not configured');
            }

            Log::info('Calling Claude API for complete website generation');

            $maxRetries = 3;
            $retryDelay = 2; // seconds
            $response = null;

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    $response = Http::withHeaders([
                        'x-api-key' => $this->apiKey,
                        'anthropic-version' => '2023-06-01',
                        'Content-Type' => 'application/json',
                    ])->timeout(120)->post($this->baseUrl, [
                        'model' => $this->model,
                        'max_tokens' => 8192, // Increased from 4096 to accommodate full website structure
                        'temperature' => 0.7,
                        'messages' => [['role' => 'user', 'content' => $prompt]]
                    ]);

                    if ($response->successful()) {
                        break; // Success, exit retry loop
                    }

                    // Check for rate limiting or overload errors
                    $status = $response->status();
                    if ($status === 529 || $status === 429) {
                        Log::warning("Claude API attempt {$attempt}/{$maxRetries} failed with status {$status}");
                        if ($attempt < $maxRetries) {
                            sleep($retryDelay * $attempt); // Exponential backoff
                            continue;
                        }
                    }

                    // For other errors, don't retry
                    break;
                } catch (\Exception $e) {
                    Log::warning("Claude API attempt {$attempt}/{$maxRetries} threw exception", [
                        'error' => $e->getMessage()
                    ]);
                    if ($attempt < $maxRetries) {
                        sleep($retryDelay);
                        continue;
                    }
                    throw $e;
                }
            }

            Log::info('Claude API response received', [
                'status' => $response->status(),
                'successful' => $response->successful()
            ]);

            if ($response->successful()) {
                $text = $response->json()['content'][0]['text'] ?? '';
                Log::info('Claude API raw response length: ' . strlen($text));
                
                // Save full response for debugging
                Log::debug('Claude API full response:', ['text' => $text]);
                
                // Extract JSON from markdown code blocks or raw JSON
                preg_match('/```json\s*(.*?)\s*```/s', $text, $matches);
                if (!empty($matches[1])) {
                    $jsonText = trim($matches[1]);
                } else {
                    // Try to find JSON object in the text
                    $jsonText = trim($text);
                    // If it starts with explanation text, try to extract just the JSON part
                    if (preg_match('/(\{[\s\S]*\})/s', $text, $matches)) {
                        $jsonText = trim($matches[1]);
                    }
                }
                
                if ($jsonText) {
                    $decoded = json_decode($jsonText, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        Log::info('Successfully decoded JSON from Claude API', [
                            'pages_count' => count($decoded['pages'] ?? [])
                        ]);
                        return $decoded;
                    } else {
                        Log::error('Failed to decode JSON from Claude API', [
                            'error' => json_last_error_msg(),
                            'json_length' => strlen($jsonText),
                            'json_text_start' => substr($jsonText, 0, 200),
                            'json_text_end' => substr($jsonText, -200)
                        ]);
                    }
                } else {
                    Log::error('No JSON found in Claude API response', [
                        'text_preview' => substr($text, 0, 500)
                    ]);
                }
            } else {
                Log::error('Claude API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Complete Website Generation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return null;
        }
    }

    /**
     * Generate marketing content from keywords using Claude AI
     */
    public function generateMarketingContent($keywords, $businessName, $businessType, $title = null, $existingHashtags = null)
    {
        try {
            $prompt = "IMPORTANT: Write in plain text format. Do NOT use markdown formatting, asterisks, bold text, or any special formatting characters. Write naturally as if typing a social media post directly.

You are a professional social media content creator. Generate an engaging social media post based on the following information:

Business Name: {$businessName}
Business Type: {$businessType}
Keywords/Topics: {$keywords}";

            if ($title) {
                $prompt .= "\nPost Title/Theme: {$title}";
            }

            if ($existingHashtags) {
                $prompt .= "\nExisting Hashtags: {$existingHashtags}";
            }

            $prompt .= "\n\nCreate a compelling social media post that:
1. Is engaging and conversational
2. Includes a strong hook in the first line
3. Has clear calls-to-action
4. Is platform-appropriate (suitable for Facebook, Instagram, Twitter/X, LinkedIn)
5. Is between 100-300 words for optimal engagement
6. Uses natural language without obvious AI markers
7. Incorporates the keywords naturally
8. Includes 5-10 relevant hashtags (separate from main content)

Return your response in this EXACT format:

CONTENT:
[Your generated post content here - plain text only, no formatting]

HASHTAGS:
[Suggested hashtags separated by spaces]";

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 2048,
                'temperature' => 0.8,
                'messages' => [['role' => 'user', 'content' => $prompt]]
            ]);

            if ($response->successful()) {
                $rawText = $response->json()['content'][0]['text'] ?? '';
                $text = $this->cleanMarkdownFormatting($rawText);
                
                // Parse content and hashtags
                $content = '';
                $hashtags = '';
                
                if (preg_match('/CONTENT:\s*(.*?)\s*HASHTAGS:/s', $text, $matches)) {
                    $content = trim($matches[1]);
                }
                
                if (preg_match('/HASHTAGS:\s*(.*)$/s', $text, $matches)) {
                    $hashtags = trim($matches[1]);
                }
                
                // Fallback if parsing fails
                if (empty($content)) {
                    $content = $text;
                }
                
                return [
                    'content' => $content,
                    'hashtags' => $hashtags
                ];
            }

            throw new \Exception('Failed to get response from Claude API');

        } catch (\Exception $e) {
            Log::error('Marketing Content Generation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Enhance existing marketing content using Claude AI
     */
    public function enhanceMarketingContent($content, $businessName, $businessType)
    {
        try {
            $prompt = "IMPORTANT: Write in plain text format. Do NOT use markdown formatting, asterisks, bold text, or any special formatting characters. Write naturally as if typing a social media post directly.

You are a professional social media content strategist. Enhance the following social media post to make it more engaging and effective:

Business Name: {$businessName}
Business Type: {$businessType}

CURRENT POST:
{$content}

Improve this post by:
1. Making the hook more compelling and attention-grabbing
2. Enhancing readability and flow
3. Adding stronger calls-to-action
4. Improving emotional engagement
5. Making it more conversational and relatable
6. Optimizing length for social media (aim for 100-300 words)
7. Ensuring it sounds natural and authentic, not robotic
8. Keeping the core message but making it more impactful

Return ONLY the enhanced post content in plain text format - no explanations, no formatting, just the improved post.";

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 2048,
                'temperature' => 0.7,
                'messages' => [['role' => 'user', 'content' => $prompt]]
            ]);

            if ($response->successful()) {
                $rawText = $response->json()['content'][0]['text'] ?? '';
                return $this->cleanMarkdownFormatting($rawText);
            }

            throw new \Exception('Failed to get response from Claude API');

        } catch (\Exception $e) {
            Log::error('Marketing Content Enhancement Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate AI image prompts for marketing post
     */
    public function generateMarketingImagePrompts($content, $title, $businessName, $businessType)
    {
        try {
            $prompt = "IMPORTANT: Write in plain text format. Do NOT use markdown formatting, asterisks, bold text, or any special formatting characters.

You are a creative director specializing in AI image generation. Based on the following social media post, suggest 5 detailed image prompts suitable for AI image generators like DALL-E, Midjourney, or Stable Diffusion.

Business Name: {$businessName}
Business Type: {$businessType}";

            if ($title) {
                $prompt .= "\nPost Title: {$title}";
            }

            $prompt .= "\n\nPOST CONTENT:
{$content}

Generate 5 creative and detailed image prompts that:
1. Visually represent the post's message and theme
2. Are appropriate for the business type
3. Would grab attention on social media feeds
4. Include specific details about composition, style, mood, and colors
5. Are 100-200 characters each
6. Are suitable for AI image generation

Return ONLY the 5 prompts, each on a new line, numbered 1-5. No explanations or additional text.";

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 2048,
                'temperature' => 0.9,
                'messages' => [['role' => 'user', 'content' => $prompt]]
            ]);

            if ($response->successful()) {
                $rawText = $response->json()['content'][0]['text'] ?? '';
                $text = $this->cleanMarkdownFormatting($rawText);
                
                // Parse prompts - split by lines and clean up
                $lines = explode("\n", $text);
                $prompts = [];
                
                foreach ($lines as $line) {
                    $line = trim($line);
                    // Remove numbering (1., 2., etc.) and clean
                    $line = preg_replace('/^\d+[\.\)]\s*/', '', $line);
                    if (!empty($line) && strlen($line) > 20) {
                        $prompts[] = $line;
                    }
                }
                
                // Return up to 5 prompts
                return array_slice($prompts, 0, 5);
            }

            throw new \Exception('Failed to get response from Claude API');

        } catch (\Exception $e) {
            Log::error('Image Prompts Generation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Enhance user's basic image prompt with AI to make it more detailed and effective
     */
    public function enhanceImagePromptForUser($userPrompt, $businessName, $businessType, $postContent = null, $style = null)
    {
        try {
            $prompt = "IMPORTANT: Write in plain text format. Do NOT use markdown formatting, asterisks, bold text, or any special formatting characters.

You are an expert at writing prompts for AI image generation. Take the user's basic prompt and enhance it with:
- Specific visual details (lighting, colors, composition, textures)
- Artistic style keywords and techniques
- Technical quality indicators (8k, professional, high-resolution)
- Mood and atmosphere descriptors
- Camera angles or perspectives (if relevant)

User's Business: {$businessName} ({$businessType})
User's Basic Prompt: {$userPrompt}";

            if ($postContent) {
                $contentContext = substr($postContent, 0, 200);
                $prompt .= "\nPost Context: {$contentContext}";
            }

            if ($style) {
                $styleDescriptions = [
                    'realistic' => 'photorealistic, professional photography style',
                    'digital-art' => 'digital art, modern artistic style',
                    'illustration' => 'illustration, hand-drawn artistic style',
                    '3d-render' => '3D rendered, CGI style',
                    'minimalist' => 'minimalist, clean design style',
                    'vibrant' => 'vibrant, bold and colorful style'
                ];
                $styleDesc = $styleDescriptions[$style] ?? 'professional style';
                $prompt .= "\nDesired Style: {$styleDesc}";
            }

            $prompt .= "\n\nEnhance this prompt to create a highly detailed, effective prompt for AI image generation.
Make it 100-200 characters long. Include:
- Specific visual details (lighting, colors, composition)
- Style and mood descriptors
- Quality indicators (professional, high-quality, etc.)
- Any relevant technical details

Keep it focused and suitable for {$businessType} business marketing.
Return ONLY the enhanced prompt, no explanations or additional text.";

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 512,
                'temperature' => 0.7,
                'messages' => [['role' => 'user', 'content' => $prompt]]
            ]);

            if ($response->successful()) {
                $rawText = $response->json()['content'][0]['text'] ?? '';
                $enhancedPrompt = $this->cleanMarkdownFormatting($rawText);
                
                // Clean up any extra whitespace or line breaks
                $enhancedPrompt = trim(preg_replace('/\s+/', ' ', $enhancedPrompt));
                
                return $enhancedPrompt;
            }

            throw new \Exception('Failed to get response from Claude API');

        } catch (\Exception $e) {
            Log::error('Image Prompt Enhancement Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate marketing image using free AI image generation service
     * Uses Pollinations.AI - a free, open-source image generation API
     */
    public function generateMarketingImage($prompt, $style, $size, $businessName, $businessType, $postContent = null, $postTitle = null)
    {
        try {
            Log::info('Starting image generation', [
                'prompt' => substr($prompt, 0, 100),
                'style' => $style,
                'size' => $size,
                'business' => $businessName
            ]);

            // Enhance the prompt with style, business context, and post content
            $enhancedPrompt = $this->enhanceImagePrompt($prompt, $style, $businessName, $businessType, $postContent, $postTitle);
            
            Log::info('Prompt enhanced', ['enhanced_prompt' => substr($enhancedPrompt, 0, 150)]);
            
            // Clean and encode the prompt properly
            $cleanPrompt = trim($enhancedPrompt);
            $encodedPrompt = rawurlencode($cleanPrompt);
            
            // Extract width and height from size (e.g., "1024x1024")
            list($width, $height) = explode('x', $size);
            
            // Use Pollinations.AI - free, no API key required
            // Format: https://image.pollinations.ai/prompt/{prompt}?width={w}&height={h}&nologo=true&model=flux
            $imageUrl = "https://image.pollinations.ai/prompt/{$encodedPrompt}?width={$width}&height={$height}&nologo=true&model=flux";
            
            Log::info('Generating AI image via Pollinations.AI', [
                'prompt' => substr($prompt, 0, 100),
                'size' => $size,
                'url_length' => strlen($imageUrl),
                'url_preview' => substr($imageUrl, 0, 150) . '...'
            ]);
            
            // Try to download and store the image with retry logic
            $maxRetries = 2;
            $downloadResult = null;
            
            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    Log::info("Download attempt {$attempt}/{$maxRetries}");
                    $downloadResult = $this->downloadAndStoreImage($imageUrl, $businessName);
                    
                    if ($downloadResult) {
                        break; // Success, exit retry loop
                    }
                    
                    if ($attempt < $maxRetries) {
                        Log::warning("Attempt {$attempt} failed, retrying...");
                        sleep(2); // Wait 2 seconds before retry
                    }
                } catch (\Exception $e) {
                    Log::error("Download attempt {$attempt} exception: " . $e->getMessage());
                    if ($attempt === $maxRetries) {
                        throw $e;
                    }
                    sleep(2);
                }
            }
            
            if ($downloadResult) {
                Log::info('AI image generated and stored successfully', [
                    'public_url' => $downloadResult['public_url'],
                    'local_path' => $downloadResult['local_path']
                ]);
                return $downloadResult;
            }
            
            // If download fails, throw error instead of returning URL
            throw new \Exception('Failed to download and store generated image after ' . $maxRetries . ' attempts. The image service may be temporarily unavailable.');

        } catch (\Exception $e) {
            Log::error('Marketing Image Generation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'prompt' => substr($prompt ?? '', 0, 100)
            ]);
            throw $e;
        }
    }

    /**
     * Enhance image prompt with style and business context
     */
    private function enhanceImagePrompt($prompt, $style, $businessName, $businessType, $postContent = null, $postTitle = null)
    {
        $styleModifiers = [
            'realistic' => 'photorealistic, high quality, professional photography, 8k, detailed',
            'digital-art' => 'digital art, vibrant colors, modern, artistic, creative',
            'illustration' => 'illustration, hand-drawn style, artistic, colorful',
            '3d-render' => '3D render, CGI, modern, sleek, high quality',
            'minimalist' => 'minimalist, clean, simple, modern design, elegant',
            'vibrant' => 'vibrant colors, energetic, bold, eye-catching, dynamic'
        ];

        $modifier = $styleModifiers[$style] ?? $styleModifiers['realistic'];
        
        // Build enhanced prompt with all context
        $enhancedPrompt = $prompt;
        
        // Add post context if available (helps match the image to the post)
        if ($postContent && strlen($postContent) > 20) {
            $contentContext = substr($postContent, 0, 100);
            $enhancedPrompt .= " (context: {$contentContext})";
        }
        
        // Add style and business context
        $enhancedPrompt .= ", {$modifier}, professional marketing image for {$businessType} business";
        
        return $enhancedPrompt;
    }

    /**
     * Download and store image locally
     * Returns array with both public_url and local_path
     */
    private function downloadAndStoreImage($imageUrl, $businessName, $subDirectory = 'generated-images')
    {
        try {
            // Create storage directory if it doesn't exist
            $directory = storage_path('app/public/marketing/' . $subDirectory);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Generate unique filename
            $filename = 'ai-' . Str::slug($businessName) . '-' . time() . '-' . uniqid() . '.png';
            $filePath = $directory . '/' . $filename;
            $relativePath = 'marketing/' . $subDirectory . '/' . $filename;

            Log::info('Downloading AI generated image', [
                'source_url' => substr($imageUrl, 0, 100),
                'target_path' => $filePath,
                'subdirectory' => $subDirectory
            ]);

            // Download the image with longer timeout and follow redirects
            $response = Http::timeout(120)
                ->withOptions([
                    'verify' => false, // Skip SSL verification if needed
                    'allow_redirects' => ['max' => 5], // Follow redirects
                ])
                ->get($imageUrl);
            
            // Check if request was successful
            if (!$response->successful()) {
                Log::error('Failed to download image: HTTP error', [
                    'status' => $response->status(),
                    'url' => substr($imageUrl, 0, 100)
                ]);
                return null;
            }
            
            $imageContent = $response->body();
            
            // Validate that we have content
            if (!$imageContent || strlen($imageContent) < 100) {
                Log::warning('Failed to download image: empty or too small content', [
                    'size' => strlen($imageContent ?? ''),
                    'url' => substr($imageUrl, 0, 100)
                ]);
                return null;
            }
            
            // Check if response is HTML (error page) instead of image
            if (stripos($imageContent, '<!DOCTYPE') === 0 || stripos($imageContent, '<html') === 0) {
                Log::error('Downloaded content is HTML error page, not an image', [
                    'content_preview' => substr($imageContent, 0, 300),
                    'url' => substr($imageUrl, 0, 100)
                ]);
                return null;
            }
            
            // Validate that it's actually an image by checking magic bytes
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($imageContent);
            
            if (!in_array($mimeType, ['image/png', 'image/jpeg', 'image/jpg', 'image/webp', 'image/gif'])) {
                Log::error('Downloaded content is not a valid image', [
                    'mime_type' => $mimeType,
                    'content_preview' => substr($imageContent, 0, 200),
                    'url' => substr($imageUrl, 0, 100)
                ]);
                return null;
            }
            
            // Save the image
            file_put_contents($filePath, $imageContent);
            
            // Verify the file was written correctly
            if (!file_exists($filePath) || filesize($filePath) === 0) {
                Log::error('Failed to write image file', [
                    'path' => $filePath,
                    'exists' => file_exists($filePath),
                    'size' => file_exists($filePath) ? filesize($filePath) : 0
                ]);
                return null;
            }
            
            Log::info('AI image downloaded successfully', [
                'filename' => $filename,
                'size' => strlen($imageContent),
                'mime_type' => $mimeType,
                'path' => $filePath,
                'subdirectory' => $subDirectory
            ]);
            
            // Return array with both URLs
            return [
                'public_url' => asset('storage/' . $relativePath),
                'local_path' => $relativePath,
                'relative_path' => $relativePath,
                'filename' => $filename
            ];

        } catch (\Exception $e) {
            Log::error('Failed to download and store image', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'url' => substr($imageUrl, 0, 100),
                'subdirectory' => $subDirectory
            ]);
            return null;
        }
    }

    /**
     * Generate video prompts for marketing content
     */
    public function generateMarketingVideoPrompts($content, $title, $businessName, $businessType)
    {
        try {
            $prompt = "IMPORTANT: Write in plain text format. Do NOT use markdown formatting, asterisks, bold text, or any special formatting characters.

You are a creative video director specializing in AI video generation for marketing. Based on the following social media post, suggest 5 detailed video scene descriptions suitable for AI video generators.

Business Name: {$businessName}
Business Type: {$businessType}";

            if ($title) {
                $prompt .= "\nPost Title: {$title}";
            }

            $prompt .= "\n\nPOST CONTENT:
{$content}

Generate 5 creative and detailed video prompts that:
1. Visually represent the post's message through motion and scenes
2. Are appropriate for the business type
3. Would grab attention on social media feeds
4. Include specific details about camera movements, lighting, transitions, and mood
5. Are 100-200 characters each
6. Are suitable for AI video generation (text-to-video or image-to-video)
7. Focus on dynamic, engaging scenes that tell a story

Return ONLY the 5 prompts, each on a new line, numbered 1-5. No explanations or additional text.";

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 2048,
                'temperature' => 0.9,
                'messages' => [['role' => 'user', 'content' => $prompt]]
            ]);

            if ($response->successful()) {
                $rawText = $response->json()['content'][0]['text'] ?? '';
                $text = $this->cleanMarkdownFormatting($rawText);
                
                // Parse prompts - split by lines and clean up
                $lines = explode("\n", $text);
                $prompts = [];
                
                foreach ($lines as $line) {
                    $line = trim($line);
                    // Remove numbering (1., 2., etc.) and clean
                    $line = preg_replace('/^\d+[\.\)]\s*/', '', $line);
                    if (!empty($line) && strlen($line) > 20) {
                        $prompts[] = $line;
                    }
                }
                
                // Return up to 5 prompts
                return array_slice($prompts, 0, 5);
            }

            throw new \Exception('Failed to get response from Claude API');

        } catch (\Exception $e) {
            Log::error('Video Prompts Generation Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Enhance user's basic video prompt with AI to make it more detailed and effective
     */
    public function enhanceVideoPromptForUser($userPrompt, $businessName, $businessType, $postContent = null, $style = null, $duration = null)
    {
        try {
            $prompt = "IMPORTANT: Write in plain text format. Do NOT use markdown formatting, asterisks, bold text, or any special formatting characters.

You are an expert at writing prompts for AI video generation. Take the user's basic prompt and enhance it with:
- Camera movements (pan, zoom, tilt, dolly, tracking shots)
- Lighting details (golden hour, soft lighting, dramatic shadows)
- Scene composition and framing
- Motion and dynamics (slow-motion, time-lapse, smooth transitions)
- Mood and atmosphere
- Technical quality indicators (4k, cinematic, professional)

User's Business: {$businessName} ({$businessType})
User's Basic Prompt: {$userPrompt}";

            if ($postContent) {
                $contentContext = substr($postContent, 0, 200);
                $prompt .= "\nPost Context: {$contentContext}";
            }

            if ($style) {
                $styleDescriptions = [
                    'professional' => 'professional corporate style with smooth camera work',
                    'dynamic' => 'dynamic and energetic with fast cuts and motion',
                    'minimal' => 'minimalist and clean with simple elegant movements',
                    'creative' => 'creative and artistic with unique angles and effects',
                    'social' => 'social media optimized with attention-grabbing elements'
                ];
                $styleDesc = $styleDescriptions[$style] ?? 'professional cinematic style';
                $prompt .= "\nDesired Style: {$styleDesc}";
            }

            if ($duration) {
                $prompt .= "\nVideo Duration: approximately {$duration} seconds";
            }

            $prompt .= "\n\nEnhance this prompt to create a highly detailed, effective prompt for AI video generation.
Make it 100-200 characters long. Include:
- Specific camera movements and angles
- Lighting and visual details
- Motion dynamics and transitions
- Mood and atmosphere
- Quality indicators (cinematic, 4k, professional, etc.)

Keep it focused and suitable for {$businessType} business marketing.
Return ONLY the enhanced prompt, no explanations or additional text.";

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 512,
                'temperature' => 0.7,
                'messages' => [['role' => 'user', 'content' => $prompt]]
            ]);

            if ($response->successful()) {
                $rawText = $response->json()['content'][0]['text'] ?? '';
                $enhancedPrompt = $this->cleanMarkdownFormatting($rawText);
                
                // Clean up any extra whitespace or line breaks
                $enhancedPrompt = trim(preg_replace('/\s+/', ' ', $enhancedPrompt));
                
                return $enhancedPrompt;
            }

            throw new \Exception('Failed to get response from Claude API');

        } catch (\Exception $e) {
            Log::error('Video Prompt Enhancement Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate a business logo using AI
     * Creates a professional logo based on business name and description
     * 
     * @param string $businessName The name of the business
     * @param string $businessDescription Description of the business
     * @param string $businessType Type of business (retail, service, etc.)
     * @param string $style Logo style preference (modern, classic, minimal, etc.)
     * @return array Array containing public_url and local_path of the generated logo
     * @throws \Exception If logo generation fails
     */
    public function generateBusinessLogo($businessName, $businessDescription, $businessType = 'retail', $style = 'modern', $tagline = null)
    {
        try {
            Log::info('Starting business logo generation', [
                'business_name' => $businessName,
                'business_type' => $businessType,
                'style' => $style,
                'has_tagline' => !empty($tagline)
            ]);

            // Generate tagline from description if not provided
            if (empty($tagline) && !empty($businessDescription)) {
                $tagline = $this->generateTaglineFromDescription($businessDescription, $businessType);
                Log::info('Generated tagline from description', ['tagline' => $tagline]);
            }

            // Generate logo with business name and tagline
            return $this->generateLocalLogoWithText($businessName, $tagline, $businessType, $style);

        } catch (\Exception $e) {
            Log::error('Business Logo Generation Error: ' . $e->getMessage(), [
                'business_name' => $businessName
            ]);
            throw $e;
        }
    }

    /**
     * Generate a catchy tagline from business description using Claude AI
     */
    private function generateTaglineFromDescription($description, $businessType)
    {
        try {
            if (empty($this->apiKey)) {
                return $this->generateFallbackTagline($businessType);
            }

            $prompt = "Based on this business description, create a SHORT, catchy tagline (maximum 5 words):

Description: {$description}
Business Type: {$businessType}

Requirements:
- Maximum 5 words
- Memorable and impactful
- Professional tone
- No quotes or punctuation at the end
- Just the tagline text, nothing else

Tagline:";

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(15)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 50,
                'temperature' => 0.8,
                'messages' => [[
                    'role' => 'user',
                    'content' => $prompt
                ]]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $tagline = trim($data['content'][0]['text'] ?? '');
                $tagline = trim($tagline, '."\'');
                
                // Ensure it's under 5 words
                $words = explode(' ', $tagline);
                if (count($words) > 5) {
                    $tagline = implode(' ', array_slice($words, 0, 5));
                }
                
                return $tagline ?: $this->generateFallbackTagline($businessType);
            }
        } catch (\Exception $e) {
            Log::warning('Tagline generation failed, using fallback: ' . $e->getMessage());
        }
        
        return $this->generateFallbackTagline($businessType);
    }

    /**
     * Generate a fallback tagline based on business type
     */
    private function generateFallbackTagline($businessType)
    {
        $taglines = [
            'retail' => 'Quality You Trust',
            'service' => 'Excellence in Service',
            'restaurant' => 'Taste the Difference',
            'salon' => 'Beauty Redefined',
            'tech' => 'Innovation First',
            'health' => 'Your Health Matters',
            'education' => 'Learn & Grow',
            'finance' => 'Smart Money Solutions',
            'real_estate' => 'Find Your Home',
            'automotive' => 'Drive with Confidence',
            'fashion' => 'Style that Speaks',
            'sports' => 'Play to Win'
        ];
        
        return $taglines[$businessType] ?? 'Quality & Trust';
    }

    private function tryPollinationsLogo($businessName, $businessType, $style)
    {
        try {
            // Create business-contextual prompts based on type and style
            $businessIcons = [
                'retail' => 'shopping bag, cart, store front',
                'service' => 'handshake, tools, service badge',
                'restaurant' => 'chef hat, fork knife, food dish',
                'salon' => 'scissors, comb, beauty salon',
                'tech' => 'circuit, code, innovation',
                'health' => 'medical cross, heart, wellness',
                'education' => 'book, graduation cap, learning',
                'finance' => 'money, graph, coins',
                'real estate' => 'house, building, key',
                'automotive' => 'car, wheel, mechanic',
                'fashion' => 'hanger, clothing, style',
                'sports' => 'trophy, ball, fitness',
                'other_hybrid' => 'versatile business icon, professional symbol',
                'other' => 'professional business emblem, company symbol',
                'hybrid' => 'innovative business icon, modern symbol'
            ];

            $styleDescriptions = [
                'modern' => 'clean minimalist flat design, geometric shapes, simple lines',
                'classic' => 'elegant timeless vintage, ornate details, traditional',
                'minimal' => 'ultra simple, single icon, lots of whitespace',
                'bold' => 'strong vibrant colors, thick lines, impactful',
                'playful' => 'fun colorful rounded, cartoon style, energetic',
                'corporate' => 'professional formal blue, structured, trustworthy'
            ];

            $icon = $businessIcons[$businessType] ?? 'professional business emblem';
            $styleDesc = $styleDescriptions[$style] ?? 'modern clean';
            
            // Keep prompt very concise for better results
            $prompt = "{$icon} logo, {$styleDesc}, white background, centered";
            $prompt = substr($prompt, 0, 100);
            
            // Add random seed to prevent caching and get different results on regenerate
            $seed = time() . rand(1000, 9999);
            
            $encoded = rawurlencode($prompt);
            $url = "https://image.pollinations.ai/prompt/{$encoded}?width=512&height=512&nologo=true&model=flux&seed={$seed}";
            
            Log::info('Trying Pollinations.AI', [
                'prompt' => $prompt,
                'business_type' => $businessType,
                'style' => $style
            ]);
            
            // Try twice with delay
            for ($i = 1; $i <= 2; $i++) {
                $result = $this->downloadAndStoreImage($url, $businessName, 'logos');
                if ($result) {
                    Log::info('Pollinations.AI success on attempt ' . $i);
                    return $result;
                }
                if ($i < 2) {
                    Log::warning('Pollinations.AI attempt ' . $i . ' failed, retrying...', [
                        'url' => substr($url, 0, 150),
                        'style' => $style,
                        'business_type' => $businessType
                    ]);
                    sleep(2);
                }
            }
            
            Log::error('Pollinations.AI failed after 2 attempts', [
                'prompt' => $prompt,
                'style' => $style,
                'business_type' => $businessType,
                'url' => substr($url, 0, 150)
            ]);
        } catch (\Exception $e) {
            Log::warning('Pollinations.AI exception: ' . $e->getMessage());
        }
        return null;
    }

    private function tryDiceBearLogo($businessName, $style)
    {
        try {
            $seed = urlencode($businessName);
            
            // Choose DiceBear style based on requested style
            $dicebearStyles = [
                'modern' => 'shapes',
                'classic' => 'bottts',
                'minimal' => 'identicon',
                'bold' => 'shapes',
                'playful' => 'fun-emoji',
                'corporate' => 'initials'
            ];
            
            $dicebearStyle = $dicebearStyles[$style] ?? 'shapes';
            
            // Color schemes by style
            $colorSchemes = [
                'modern' => '4F46E5',
                'classic' => '1F2937',
                'minimal' => 'F3F4F6',
                'bold' => 'DC2626',
                'playful' => 'EC4899',
                'corporate' => '111827'
            ];
            
            $bgColor = $colorSchemes[$style] ?? '4F46E5';
            $url = "https://api.dicebear.com/7.x/{$dicebearStyle}/png?seed={$seed}&size=512&backgroundColor={$bgColor}";
            
            Log::info('Trying DiceBear API', [
                'style' => $dicebearStyle,
                'seed' => $businessName
            ]);
            
            $result = $this->downloadAndStoreImage($url, $businessName, 'logos');
            if ($result) {
                Log::info('DiceBear API success');
                return $result;
            }
            
            Log::error('DiceBear API returned null', [
                'style' => $dicebearStyle,
                'business_name' => $businessName,
                'url' => $url
            ]);
        } catch (\Exception $e) {
            Log::error('DiceBear API exception: ' . $e->getMessage(), [
                'business_name' => $businessName,
                'style' => $style,
                'trace' => $e->getTraceAsString()
            ]);
        }
        return null;
    }

    private function tryUIAvatarsLogo($businessName, $style)
    {
        try {
            // Get business initials
            $words = explode(' ', $businessName);
            $initials = count($words) >= 2 
                ? strtoupper($words[0][0] . $words[1][0]) 
                : strtoupper(substr($businessName, 0, 2));
            
            // Color schemes by style (background/foreground)
            $colorSchemes = [
                'modern' => ['4F46E5', 'FFFFFF'],
                'classic' => ['1F2937', 'FFFFFF'],
                'minimal' => ['F3F4F6', '1F2937'],
                'bold' => ['DC2626', 'FFFFFF'],
                'playful' => ['EC4899', 'FFFFFF'],
                'corporate' => ['111827', 'FFFFFF']
            ];
            
            $colors = $colorSchemes[$style] ?? ['4F46E5', 'FFFFFF'];
            $bgColor = $colors[0];
            $fgColor = $colors[1];
            
            $name = urlencode($initials);
            $url = "https://ui-avatars.com/api/?name={$name}&size=512&background={$bgColor}&color={$fgColor}&bold=true&format=png&rounded=false&font-size=0.4";
            
            Log::info('Trying UI Avatars', [
                'initials' => $initials,
                'style' => $style
            ]);
            
            $result = $this->downloadAndStoreImage($url, $businessName, 'logos');
            if ($result) {
                Log::info('UI Avatars success');
                return $result;
            }
            
            Log::error('UI Avatars returned null', [
                'initials' => $initials,
                'style' => $style,
                'business_name' => $businessName,
                'url' => $url
            ]);
        } catch (\Exception $e) {
            Log::error('UI Avatars exception: ' . $e->getMessage(), [
                'business_name' => $businessName,
                'style' => $style,
                'trace' => $e->getTraceAsString()
            ]);
        }
        return null;
    }

    private function generateLocalLogo($businessName, $businessType, $style)
    {
        $dir = storage_path('app/public/marketing/logos');
        if (!file_exists($dir)) mkdir($dir, 0755, true);

        $filename = 'local-' . Str::slug($businessName) . '-' . time() . '.png';
        $filePath = $dir . '/' . $filename;
        $relativePath = 'marketing/logos/' . $filename;

        $img = imagecreatetruecolor(512, 512);
        
        // Style-specific color schemes
        $colorSchemes = [
            'modern' => ['bg' => [79, 70, 229], 'text' => [255, 255, 255], 'accent' => [139, 92, 246]],
            'classic' => ['bg' => [31, 41, 55], 'text' => [255, 255, 255], 'accent' => [156, 163, 175]],
            'minimal' => ['bg' => [243, 244, 246], 'text' => [31, 41, 55], 'accent' => [209, 213, 219]],
            'bold' => ['bg' => [220, 38, 38], 'text' => [255, 255, 255], 'accent' => [248, 113, 113]],
            'playful' => ['bg' => [236, 72, 153], 'text' => [255, 255, 255], 'accent' => [244, 114, 182]],
            'corporate' => ['bg' => [17, 24, 39], 'text' => [255, 255, 255], 'accent' => [59, 130, 246]]
        ];
        
        $colors = $colorSchemes[$style] ?? $colorSchemes['modern'];
        
        $bgColor = imagecolorallocate($img, $colors['bg'][0], $colors['bg'][1], $colors['bg'][2]);
        $textColor = imagecolorallocate($img, $colors['text'][0], $colors['text'][1], $colors['text'][2]);
        $accentColor = imagecolorallocate($img, $colors['accent'][0], $colors['accent'][1], $colors['accent'][2]);
        
        // Fill background
        imagefilledrectangle($img, 0, 0, 512, 512, $bgColor);
        
        // Add decorative circle for modern look
        if (in_array($style, ['modern', 'playful', 'bold'])) {
            imagefilledellipse($img, 256, 256, 350, 350, $accentColor);
            imagefilledellipse($img, 256, 256, 300, 300, $bgColor);
        }
        
        // Add rectangular frame for corporate/classic
        if (in_array($style, ['corporate', 'classic'])) {
            imagerectangle($img, 80, 80, 432, 432, $accentColor);
            imagerectangle($img, 85, 85, 427, 427, $accentColor);
        }
        
        // Get initials
        $words = explode(' ', $businessName);
        $initials = count($words) >= 2 
            ? strtoupper($words[0][0] . $words[1][0]) 
            : strtoupper(substr($businessName, 0, 2));
        
        // Draw large bold initials using built-in font
        $font = 5; // Largest built-in font
        $charWidth = imagefontwidth($font);
        $charHeight = imagefontheight($font);
        
        // Calculate scaling for larger text
        $scale = 5; // Make text 5x larger
        $textWidth = strlen($initials) * $charWidth * $scale;
        $x = (512 - $textWidth) / 2;
        $y = (512 - $charHeight * $scale) / 2;
        
        // Draw text multiple times for bold, large effect
        for ($i = 0; $i < $scale * 3; $i++) {
            for ($j = 0; $j < $scale * 3; $j++) {
                imagestring($img, $font, $x + $i, $y + $j, $initials, $textColor);
            }
        }
        
        // Add business type indicator at bottom (if not minimal)
        if ($style !== 'minimal') {
            $typeText = strtoupper(substr($businessType, 0, 15));
            $typeWidth = strlen($typeText) * imagefontwidth(2);
            $typeX = (512 - $typeWidth) / 2;
            imagestring($img, 2, $typeX, 460, $typeText, $textColor);
        }
        
        imagepng($img, $filePath);
        imagedestroy($img);
        
        Log::info('Local logo generated with enhanced styling', [
            'path' => $filePath,
            'style' => $style,
            'initials' => $initials
        ]);
        
        return [
            'public_url' => asset('storage/' . $relativePath),
            'local_path' => $relativePath
        ];
    }

    /**
     * Generate a professional logo with business name and tagline
     */
    private function generateLocalLogoWithText($businessName, $tagline, $businessType, $style)
    {
        $dir = storage_path('app/public/marketing/logos');
        if (!file_exists($dir)) mkdir($dir, 0755, true);

        $filename = 'logo-' . Str::slug($businessName) . '-' . time() . '.png';
        $filePath = $dir . '/' . $filename;
        $relativePath = 'marketing/logos/' . $filename;

        // Create larger canvas for text
        $width = 800;
        $height = 400;
        $img = imagecreatetruecolor($width, $height);
        
        // Style-specific color schemes
        $colorSchemes = [
            'modern' => ['bg' => [255, 255, 255], 'primary' => [79, 70, 229], 'secondary' => [139, 92, 246], 'text' => [31, 41, 55]],
            'classic' => ['bg' => [255, 255, 255], 'primary' => [31, 41, 55], 'secondary' => [107, 114, 128], 'text' => [55, 65, 81]],
            'minimal' => ['bg' => [255, 255, 255], 'primary' => [0, 0, 0], 'secondary' => [156, 163, 175], 'text' => [75, 85, 99]],
            'bold' => ['bg' => [255, 255, 255], 'primary' => [220, 38, 38], 'secondary' => [239, 68, 68], 'text' => [127, 29, 29]],
            'playful' => ['bg' => [255, 255, 255], 'primary' => [236, 72, 153], 'secondary' => [244, 114, 182], 'text' => [157, 23, 77]],
            'corporate' => ['bg' => [255, 255, 255], 'primary' => [30, 58, 138], 'secondary' => [59, 130, 246], 'text' => [30, 64, 175]]
        ];
        
        $colors = $colorSchemes[$style] ?? $colorSchemes['modern'];
        
        $bgColor = imagecolorallocate($img, $colors['bg'][0], $colors['bg'][1], $colors['bg'][2]);
        $primaryColor = imagecolorallocate($img, $colors['primary'][0], $colors['primary'][1], $colors['primary'][2]);
        $secondaryColor = imagecolorallocate($img, $colors['secondary'][0], $colors['secondary'][1], $colors['secondary'][2]);
        $textColor = imagecolorallocate($img, $colors['text'][0], $colors['text'][1], $colors['text'][2]);
        
        // Fill background
        imagefilledrectangle($img, 0, 0, $width, $height, $bgColor);
        
        // Add subtle decorative elements based on style
        if (in_array($style, ['modern', 'corporate'])) {
            // Add geometric shapes
            imagefilledrectangle($img, 0, 0, 150, $height, $primaryColor);
            imagefilledellipse($img, 75, 200, 120, 120, $secondaryColor);
        } elseif ($style === 'playful') {
            // Add circles
            imagefilledellipse($img, 100, 100, 80, 80, $primaryColor);
            imagefilledellipse($img, 120, 300, 60, 60, $secondaryColor);
        } elseif ($style === 'bold') {
            // Add bold rectangle
            imagefilledrectangle($img, 0, 0, 120, $height, $primaryColor);
        }
        
        // Use TrueType font if available, otherwise use built-in
        $fontPath = storage_path('fonts/DejaVuSans-Bold.ttf');
        $useTTF = file_exists($fontPath);
        
        if ($useTTF) {
            // Draw business name
            $nameSize = 48;
            $nameBox = imagettfbbox($nameSize, 0, $fontPath, $businessName);
            $nameWidth = abs($nameBox[4] - $nameBox[0]);
            $nameX = ($width - $nameWidth) / 2 + 30;
            $nameY = $height / 2 - 20;
            
            imagettftext($img, $nameSize, 0, $nameX, $nameY, $primaryColor, $fontPath, $businessName);
            
            // Draw tagline
            if (!empty($tagline)) {
                $taglineSize = 20;
                $taglineBox = imagettfbbox($taglineSize, 0, $fontPath, $tagline);
                $taglineWidth = abs($taglineBox[4] - $taglineBox[0]);
                $taglineX = ($width - $taglineWidth) / 2 + 30;
                $taglineY = $nameY + 50;
                
                imagettftext($img, $taglineSize, 0, $taglineX, $taglineY, $textColor, $fontPath, $tagline);
            }
        } else {
            // Fallback to built-in fonts
            $font = 5;
            $charWidth = imagefontwidth($font);
            
            // Draw business name (larger, bold)
            $nameLength = strlen($businessName);
            $nameX = ($width - ($nameLength * $charWidth * 3)) / 2 + 60;
            $nameY = $height / 2 - 40;
            
            for ($i = 0; $i < 3; $i++) {
                for ($j = 0; $j < 3; $j++) {
                    for ($k = 0; $k < $nameLength; $k++) {
                        imagestring($img, $font, $nameX + ($k * $charWidth * 3) + $i, $nameY + $j, $businessName[$k], $primaryColor);
                    }
                }
            }
            
            // Draw tagline
            if (!empty($tagline)) {
                $taglineLength = strlen($tagline);
                $taglineX = ($width - ($taglineLength * $charWidth * 2)) / 2 + 60;
                $taglineY = $nameY + 60;
                
                for ($i = 0; $i < 2; $i++) {
                    for ($j = 0; $j < 2; $j++) {
                        for ($k = 0; $k < $taglineLength; $k++) {
                            imagestring($img, 3, $taglineX + ($k * $charWidth * 2) + $i, $taglineY + $j, $tagline[$k], $textColor);
                        }
                    }
                }
            }
        }
        
        // Add subtle border for classic/corporate styles
        if (in_array($style, ['classic', 'corporate'])) {
            imagerectangle($img, 2, 2, $width-3, $height-3, $primaryColor);
        }
        
        imagepng($img, $filePath);
        imagedestroy($img);
        
        Log::info('Logo with text generated successfully', [
            'path' => $filePath,
            'style' => $style,
            'business_name' => $businessName,
            'tagline' => $tagline,
            'used_ttf' => $useTTF
        ]);
        
        return [
            'public_url' => asset('storage/' . $relativePath),
            'local_path' => $relativePath
        ];
    }

    /**
     * Create a detailed logo design prompt using Claude AI
     * 
     * @param string $businessName
     * @param string $businessDescription
     * @param string $businessType
     * @param string $style
     * @return string The AI-enhanced logo design prompt
     */
    private function createLogoDesignPrompt($businessName, $businessDescription, $businessType, $style)
    {
        try {
            $prompt = "You are an expert logo designer. Create a concise logo design prompt (max 60 words) for an AI image generator.

BUSINESS: {$businessName}
TYPE: {$businessType}
DESCRIPTION: {$businessDescription}
STYLE: {$style}

Create a SHORT prompt that describes:
1. Main visual elements representing the business
2. Style keywords ({$style}: modern=minimalist/clean, classic=elegant/timeless, minimal=simple, bold=vibrant/strong, playful=fun/colorful, corporate=professional/formal)
3. Color palette suggestion
4. Simple composition on clean background

IMPORTANT: Keep it under 60 words. No explanations, just the logo description.";

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 512,
                'temperature' => 0.8,
                'messages' => [['role' => 'user', 'content' => $prompt]]
            ]);

            if ($response->successful()) {
                $logoDesignPrompt = $response->json()['content'][0]['text'] ?? '';
                $logoDesignPrompt = trim($logoDesignPrompt);
                
                return $logoDesignPrompt;
            }

            // Fallback if Claude API fails
            return $this->createFallbackLogoPrompt($businessName, $businessDescription, $businessType, $style);

        } catch (\Exception $e) {
            Log::warning('Failed to create AI-enhanced logo prompt, using fallback', [
                'error' => $e->getMessage()
            ]);
            return $this->createFallbackLogoPrompt($businessName, $businessDescription, $businessType, $style);
        }
    }

    /**
     * Create a fallback logo prompt without Claude AI
     * 
     * @param string $businessName
     * @param string $businessDescription
     * @param string $businessType
     * @param string $style
     * @return string Basic logo design prompt
     */
    private function createFallbackLogoPrompt($businessName, $businessDescription, $businessType, $style)
    {
        $styleDescriptions = [
            'modern' => 'modern, clean, minimalist, contemporary design with flat colors and simple geometric shapes',
            'classic' => 'classic, timeless, elegant design with traditional elements and sophisticated styling',
            'minimal' => 'minimal, ultra-simple, essential elements only with lots of negative space',
            'bold' => 'bold, strong, vibrant design with commanding presence and striking colors',
            'playful' => 'playful, fun, creative design with energetic colors and approachable elements',
            'corporate' => 'corporate, professional, trustworthy design with formal elements'
        ];

        $styleDesc = $styleDescriptions[$style] ?? $styleDescriptions['modern'];
        
        $businessTypeIcons = [
            'retail' => 'shopping bag or storefront',
            'online' => 'digital screen or cursor',
            'fashion' => 'clothing or hanger',
            'service' => 'handshake or gears',
            'restaurant' => 'chef hat or utensils',
            'salon' => 'scissors or comb',
            'healthcare' => 'medical cross',
            'education' => 'book or graduation cap',
            'technology' => 'circuit or chip'
        ];

        $iconSuggestion = $businessTypeIcons[$businessType] ?? 'simple icon';
        
        // Keep it short and simple for URL encoding
        $prompt = "Professional {$styleDesc} logo with {$iconSuggestion} for {$businessName}, clean background, vector style";
        
        return $prompt;
    }

    /**
     * OCR: Extract product inventory data from handwritten/printed image
     * 
     * @param string $imagePath Path to the uploaded image
     * @param string $recordType Type: 'inventory', 'sales', 'services'
     * @return array Structured data extracted from image
     */
    public function extractRecordsFromImage($imagePath, $recordType = 'inventory')
    {
        try {
            Log::info('Starting OCR extraction', [
                'image_path' => $imagePath,
                'record_type' => $recordType
            ]);

            // Read and encode image
            $imageData = file_get_contents($imagePath);
            $base64Image = base64_encode($imageData);
            
            // Detect mime type
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($imagePath);
            
            // Create appropriate prompt based on record type
            $prompt = $this->getOCRPrompt($recordType);
            
            // Call Claude Vision API
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 4096,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'image',
                                'source' => [
                                    'type' => 'base64',
                                    'media_type' => $mimeType,
                                    'data' => $base64Image
                                ]
                            ],
                            [
                                'type' => 'text',
                                'text' => $prompt
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $content = $response->json()['content'][0]['text'] ?? '';
                
                Log::info('OCR extraction successful', [
                    'record_type' => $recordType,
                    'content_length' => strlen($content)
                ]);
                
                // Parse the JSON response
                return $this->parseOCRResponse($content, $recordType);
            }

            throw new \Exception('Failed to extract data from image: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('OCR extraction failed', [
                'error' => $e->getMessage(),
                'image_path' => $imagePath,
                'record_type' => $recordType
            ]);
            throw $e;
        }
    }

    /**
     * Get appropriate OCR prompt based on record type
     */
    private function getOCRPrompt($recordType)
    {
        $prompts = [
            'inventory' => "You are an expert data extraction assistant. Analyze this image containing product inventory records (could be handwritten or printed).

Extract ALL products visible in the image and return them in this EXACT JSON format:

{
  \"success\": true,
  \"records\": [
    {
      \"name\": \"Product Name\",
      \"sku\": \"SKU or leave empty if not visible\",
      \"quantity\": 0,
      \"unit_price\": 0.00,
      \"category\": \"Best guess category\",
      \"notes\": \"Any additional notes\"
    }
  ],
  \"total_items\": 0
}

RULES:
1. Extract product name, quantity, price, SKU if visible
2. If price is not visible, use 0.00
3. If quantity is not visible, use 0
4. Guess appropriate category (e.g., Electronics, Clothing, Food, etc.)
5. Be thorough - extract ALL visible products
6. Return ONLY valid JSON, no additional text
7. If image is unclear or contains no inventory data, return: {\"success\": false, \"error\": \"Could not extract inventory data\"}",

            'sales' => "You are an expert data extraction assistant. Analyze this image containing sales records (could be handwritten or printed).

Extract ALL sales transactions visible and return them in this EXACT JSON format:

{
  \"success\": true,
  \"records\": [
    {
      \"product_name\": \"Product/Service Name\",
      \"quantity\": 1,
      \"unit_price\": 0.00,
      \"total\": 0.00,
      \"customer_name\": \"Customer name if visible, otherwise empty\",
      \"date\": \"YYYY-MM-DD or empty if not visible\",
      \"payment_method\": \"cash/mpesa/card or empty\",
      \"notes\": \"Any additional notes\"
    }
  ],
  \"total_amount\": 0.00,
  \"total_transactions\": 0
}

RULES:
1. Calculate total = quantity × unit_price
2. Extract date if visible (today's date if not specified)
3. Include customer name if visible
4. Sum up total_amount for all transactions
5. Return ONLY valid JSON, no additional text
6. If image is unclear, return: {\"success\": false, \"error\": \"Could not extract sales data\"}",

            'services' => "You are an expert data extraction assistant. Analyze this image containing service booking records (could be handwritten or printed).

Extract ALL service appointments/bookings and return them in this EXACT JSON format:

{
  \"success\": true,
  \"records\": [
    {
      \"customer_name\": \"Customer Name\",
      \"service_name\": \"Service Name\",
      \"date\": \"YYYY-MM-DD\",
      \"time\": \"HH:MM or empty\",
      \"duration\": 60,
      \"price\": 0.00,
      \"staff_name\": \"Staff name if visible\",
      \"phone\": \"Phone number if visible\",
      \"notes\": \"Any additional notes\"
    }
  ],
  \"total_bookings\": 0
}

RULES:
1. Extract service name, customer, date, time if visible
2. Duration in minutes (default 60 if not specified)
3. Include staff assignment if visible
4. Extract phone numbers carefully
5. Return ONLY valid JSON, no additional text
6. If image is unclear, return: {\"success\": false, \"error\": \"Could not extract service data\"}"
        ];

        return $prompts[$recordType] ?? $prompts['inventory'];
    }

    /**
     * Parse Claude's OCR response into structured array
     */
    private function parseOCRResponse($content, $recordType)
    {
        try {
            // Extract JSON from response (Claude might add explanation text)
            if (preg_match('/\{[\s\S]*\}/', $content, $matches)) {
                $jsonString = $matches[0];
            } else {
                $jsonString = $content;
            }
            
            $data = json_decode($jsonString, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON response: ' . json_last_error_msg());
            }
            
            if (!isset($data['success'])) {
                throw new \Exception('Response missing success flag');
            }
            
            return $data;
            
        } catch (\Exception $e) {
            Log::error('Failed to parse OCR response', [
                'error' => $e->getMessage(),
                'content' => substr($content, 0, 500)
            ]);
            
            return [
                'success' => false,
                'error' => 'Failed to parse extracted data: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Batch create products from OCR data
     * 
     * @param array $ocrData Parsed OCR data
     * @param int $businessId Business ID
     * @return array Results summary
     */
    public function createProductsFromOCR($ocrData, $businessId)
    {
        if (!$ocrData['success']) {
            return [
                'success' => false,
                'message' => $ocrData['error'] ?? 'Failed to extract data'
            ];
        }

        $created = 0;
        $failed = 0;
        $errors = [];

        foreach ($ocrData['records'] as $record) {
            try {
                \App\Models\Product::create([
                    'business_id' => $businessId,
                    'name' => $record['name'],
                    'sku' => $record['sku'] ?? 'SKU-' . strtoupper(Str::random(8)),
                    'quantity' => $record['quantity'] ?? 0,
                    'price' => $record['unit_price'] ?? 0,
                    'category' => $record['category'] ?? 'Uncategorized',
                    'description' => $record['notes'] ?? null,
                    'active' => true
                ]);
                $created++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Failed to create {$record['name']}: " . $e->getMessage();
                Log::warning('Failed to create product from OCR', [
                    'product' => $record['name'],
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'success' => true,
            'created' => $created,
            'failed' => $failed,
            'errors' => $errors,
            'message' => "Successfully created {$created} products" . ($failed > 0 ? ", {$failed} failed" : '')
        ];
    }

    /**
     * Create sales records from OCR data
     * 
     * @param array $ocrData Parsed OCR data
     * @param int $businessId Business ID
     * @return array Results summary
     */
    public function createSalesFromOCR($ocrData, $businessId)
    {
        if (!$ocrData['success']) {
            return [
                'success' => false,
                'message' => $ocrData['error'] ?? 'Failed to extract data'
            ];
        }

        $created = 0;
        $failed = 0;
        $errors = [];

        foreach ($ocrData['records'] as $record) {
            try {
                // Find or create customer
                $customer = null;
                if (!empty($record['customer_name'])) {
                    $customer = \App\Models\Customer::firstOrCreate([
                        'business_id' => $businessId,
                        'name' => $record['customer_name']
                    ], [
                        'phone' => $record['phone'] ?? null,
                        'email' => null
                    ]);
                }

                // Find or create product
                $product = \App\Models\Product::firstOrCreate([
                    'business_id' => $businessId,
                    'name' => $record['product_name']
                ], [
                    'sku' => 'OCR-' . strtoupper(Str::random(8)),
                    'price' => $record['unit_price'] ?? 0,
                    'quantity' => 1000, // Default stock
                    'active' => true
                ]);

                // Create order
                $order = \App\Models\Order::create([
                    'business_id' => $businessId,
                    'customer_id' => $customer?->id,
                    'total_amount' => $record['total'] ?? 0,
                    'status' => 'completed',
                    'payment_method' => $record['payment_method'] ?? 'cash',
                    'payment_status' => 'paid',
                    'notes' => $record['notes'] ?? 'Added via OCR',
                    'created_at' => !empty($record['date']) ? $record['date'] : now()
                ]);

                // Create order item
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $record['quantity'] ?? 1,
                    'price' => $record['unit_price'] ?? 0,
                    'subtotal' => $record['total'] ?? 0
                ]);

                $created++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Failed to create sale for {$record['product_name']}: " . $e->getMessage();
                Log::warning('Failed to create sale from OCR', [
                    'product' => $record['product_name'],
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'success' => true,
            'created' => $created,
            'failed' => $failed,
            'errors' => $errors,
            'total_amount' => $ocrData['total_amount'] ?? 0,
            'message' => "Successfully created {$created} sales records" . ($failed > 0 ? ", {$failed} failed" : '')
        ];
    }
}

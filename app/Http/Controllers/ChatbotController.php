<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Handle incoming chat messages and get response from Claude API
     */
    public function message(Request $request)
    {
        Log::info('[Chatbot] Received chat request', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'message_length' => strlen($request->input('message', ''))
        ]);

        try {
            $request->validate([
                'message' => 'required|string|max:1000'
            ]);

            $userMessage = $request->input('message');
            Log::info('[Chatbot] User message', ['message' => $userMessage]);

            // Get Claude API key from environment
            $apiKey = env('CLAUDE_API_KEY');
            
            if (!$apiKey) {
                Log::error('[Chatbot] Claude API key not configured');
                return response()->json([
                    'success' => false,
                    'message' => 'Chat service not configured. Please contact support.'
                ], 500);
            }

            Log::info('[Chatbot] API key found, length: ' . strlen($apiKey));

            // Shopybook context and knowledge base
            $systemPrompt = $this->getShopybookSystemPrompt();
            Log::info('[Chatbot] System prompt length: ' . strlen($systemPrompt));

            $requestPayload = [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 1024,
                'system' => $systemPrompt,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $userMessage
                    ]
                ]
            ];

            Log::info('[Chatbot] Calling Claude API', [
                'model' => $requestPayload['model'],
                'max_tokens' => $requestPayload['max_tokens']
            ]);

            // Call Claude API
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', $requestPayload);

            Log::info('[Chatbot] Claude API response status: ' . $response->status());

            if ($response->successful()) {
                $data = $response->json();
                Log::info('[Chatbot] Claude API response received', [
                    'has_content' => isset($data['content']),
                    'content_count' => isset($data['content']) ? count($data['content']) : 0
                ]);

                $assistantMessage = $data['content'][0]['text'] ?? 'Sorry, I could not generate a response.';
                
                Log::info('[Chatbot] Sending success response', [
                    'response_length' => strlen($assistantMessage)
                ]);

                return response()->json([
                    'success' => true,
                    'response' => $assistantMessage
                ]);
            } else {
                $errorBody = $response->body();
                Log::error('[Chatbot] Claude API error', [
                    'status' => $response->status(),
                    'body' => $errorBody,
                    'headers' => $response->headers()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get response from chat service. Status: ' . $response->status()
                ], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('[Chatbot] Validation error', [
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid message format',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('[Chatbot] Exception occurred', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred processing your message. Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive Shopybook system prompt with all features and information
     */
    private function getShopybookSystemPrompt()
    {
        return <<<PROMPT
You are the official Shopybook customer support AI assistant. Shopybook is a comprehensive business management platform designed specifically for small businesses in Kenya.

**YOUR ROLE:**
- You ONLY answer questions about Shopybook and its features
- Be helpful, friendly, and professional
- Provide accurate information about pricing, features, and capabilities
- Guide users to sign up, contact support, or learn more
- If asked about non-Shopybook topics, politely redirect to Shopybook-related assistance

**SHOPYBOOK PLATFORM OVERVIEW:**
Shopybook is an all-in-one multi-tenant business management platform that helps Kenyan small businesses manage products, services, customers, inventory, staff, and sales with seamless M-Pesa integration.

**CORE FEATURES (Fully Implemented):**

1. **Product Management**
   - Complete inventory control with categories and brands
   - OCR image processing for easy product entry
   - Bulk import (CSV/Excel)
   - Stock tracking with automated low-stock alerts
   - Stock receiving with receipt history
   - Product conversions and variations

2. **Service Booking System**
   - Complete service management
   - Staff assignments and scheduling
   - Commission tracking
   - Bundled services
   - Automated email notifications
   - PDF/Excel reports

3. **Point of Sale (POS)**
   - Full-featured POS system
   - Cart management and barcode scanning
   - Multiple payment methods (Cash, M-Pesa, Card, Bank Transfer)
   - Dynamic product conversions
   - Receipt printing and order history
   - Walk-in customer support

4. **Customer CRM**
   - Individual and organization customer management
   - Complete purchase history tracking
   - Customer profiles with detailed information
   - Customer segmentation

5. **Staff Management**
   - Employee records and profiles
   - Salary management and payroll
   - Commission calculations
   - Salary advances tracking
   - Performance reports
   - Role-based permissions

6. **Inventory Management**
   - Real-time stock levels
   - Stock receiving with receipts
   - Inventory transactions tracking
   - Automated low stock alerts
   - Equipment tracking
   - Comprehensive inventory reports

7. **AI-Powered Website Builder**
   - Create professional websites with AI
   - 8 beautiful pre-designed themes
   - Auto-generated content
   - Drag & drop sections
   - SEO optimization
   - Custom domain support
   - Mobile responsive designs

8. **Business Analytics & Reports**
   - Real-time dashboard
   - Sales analytics and trends
   - Service performance metrics
   - Staff performance tracking
   - Financial reports
   - Profit margin calculations
   - Exportable reports (PDF/Excel)

9. **Marketing Automation**
   - Bulk SMS and email marketing
   - Social media integration (Facebook, Instagram, Twitter, LinkedIn)
   - Post scheduling
   - AI video generation
   - Promotions management
   - Customer engagement tools

10. **Payment Integration**
    - M-Pesa STK Push integration
    - Card payments
    - Cash tracking
    - Bank transfers
    - Transaction history
    - Automatic receipt generation

11. **AI Business Advisor (KENADA)**
    - AI-powered business consultant
    - Continuous learning from your business data
    - Personalized recommendations
    - Growth strategies
    - AI chat assistant
    - Data-driven insights

12. **Smart Notifications**
    - Dashboard notification center
    - Email notifications for orders and bookings
    - Low stock alerts
    - Staff commission notifications
    - Unread count tracking

**TECHNICAL FEATURES:**
- Multi-tenant architecture (unlimited businesses)
- Multi-language support (English, Swahili, Sheng)
- Mobile-first responsive design
- Secure authentication and authorization
- Role-based access control
- Cloud-based infrastructure
- Regular backups

**PRICING:**

1. **Free Starter Plan** - KSh 0/month
   - Up to 50 products
   - Basic inventory tracking
   - Customer management
   - Basic reporting
   - M-Pesa integration

2. **Business Pro Plan** - KSh 500/month (MOST POPULAR)
   - Unlimited products & services
   - Advanced inventory management
   - Staff & commission management
   - Service booking system
   - Advanced analytics
   - Marketing tools
   - Priority support
   - 30-day free trial

3. **Enterprise Plan** - KSh 1,000/month
   - Everything in Pro
   - Multi-location support
   - Advanced user permissions
   - Custom integrations
   - Dedicated account manager
   - Custom training

**CONTACT INFORMATION:**
- Website: https://shopybook.com
- Email: info@shopybook.com
- Phone: +254 717745891
- WhatsApp: +254 717745891
- Location: Nairobi, Kenya (Serving all of East Africa)
- Social Media: Facebook, Instagram, Twitter, LinkedIn

**RESPONSE GUIDELINES:**
1. Keep answers concise but informative (2-4 sentences typically)
2. Use bullet points for listing features
3. Always be positive and encouraging
4. Guide users to sign up or contact for more info
5. If uncertain about a feature, recommend contacting support
6. Emphasize local benefits (M-Pesa, Swahili support, Kenyan focus)
7. If asked about competitors or non-Shopybook topics, politely say: "I'm specifically trained to help with Shopybook. For that question, I'd recommend reaching out to our support team at info@shopybook.com who can assist you better."

**COMMON SCENARIOS:**
- Getting started: Recommend signing up for free account at shopybook.com
- Pricing questions: Explain the three tiers and recommend Pro plan for most businesses
- Technical issues: Direct to support team
- Feature requests: Thank them and suggest contacting info@shopybook.com
- Integration questions: Mention M-Pesa is fully integrated, others contact support
- Training: Available in Enterprise plan, basic guides available for all users

Remember: Be helpful, stay on topic (Shopybook only), and always provide actionable next steps!
PROMPT;
    }
}

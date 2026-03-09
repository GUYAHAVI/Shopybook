<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ClaudeAPIService;
use App\Services\AIMemoryService;
use App\Jobs\ExtractBusinessMemoryJob;
use App\Models\Business;
use App\Models\Product;
use App\Models\Service;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\Employee;
use App\Models\Payment;
use App\Models\CommissionPayout;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AICommunicationController extends Controller
{
    protected $claudeService;
    protected $memoryService;

    public function __construct(ClaudeAPIService $claudeService, AIMemoryService $memoryService)
    {
        $this->claudeService  = $claudeService;
        $this->memoryService  = $memoryService;
    }

    /**
     * Show the AI chat interface
     */
    public function chat()
    {
        $user = Auth::user();
        $business = $user->business;
        
        if (!$business) {
            return redirect()->route('business.choose-type')
                ->with('error', 'Please set up your business first.');
        }
        
        return view('ai.chat', compact('business'));
    }

    /**
     * Process user message and get AI response
     */
    public function processMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        try {
            $user     = Auth::user();
            $message  = $request->input('message');
            $business = $user->business;

            $businessData = $business ? $this->gatherBusinessData($business) : [];

            // ── Session identifier (scopes per-browser-session) ───────────
            $sessionId = $request->session()->getId();

            // ── Build multi-turn messages array ───────────────────────────
            // Retrieve the last N turns for this session
            $history = $business
                ? $this->memoryService->getConversationHistory($business->id, $sessionId)
                : [];

            // Append the new user turn
            $messages   = $history;
            $messages[] = ['role' => 'user', 'content' => $message];

            // ── Build enriched system prompt ──────────────────────────────
            $contextSections = [];
            if ($business) {
                $contextSections = $this->memoryService->buildContextBlock(
                    $business->id,
                    $business->business_type ?? 'general',
                    $sessionId
                );
            }

            $systemPrompt = $this->claudeService->buildSystemPrompt(
                $businessData,
                $business,
                $contextSections
            );

            // ── Call Claude with full conversation history ────────────────
            $response = $this->claudeService->chatWithConversationHistory(
                $systemPrompt,
                $messages
            );

            // ── Persist the new turns ─────────────────────────────────────
            if ($business) {
                $this->memoryService->storeConversationTurn($business->id, $sessionId, 'user',      $message);
                $this->memoryService->storeConversationTurn($business->id, $sessionId, 'assistant', $response);

                // Async: extract & store business memory facets from this turn
                dispatch(new ExtractBusinessMemoryJob($business->id, $message, $response));
            }

            return response()->json([
                'success'       => true,
                'response'      => $response,
                'source'        => 'claude_ai',
                'business_name' => $business ? $business->name : null,
            ]);

        } catch (\Exception $e) {
            Log::error('Chat Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing message: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Gather comprehensive business data for Claude analysis
     */
    protected function gatherBusinessData(Business $business)
    {
        $data = [
            'business_info' => [
                'name' => $business->name,
                'type' => $business->business_type,
                'description' => $business->description,
                'location' => $business->city ?? 'Kenya',
            ],
        ];

        // Products data
        $products = Product::where('business_id', $business->id)->get();
        if ($products->count() > 0) {
            $data['products'] = [
                'total' => $products->count(),
                'active' => $products->where('is_active', true)->count(),
                'low_stock' => $products->filter(function($p) {
                    return $p->stock_quantity <= ($p->low_stock_threshold ?? 5);
                })->count(),
                'out_of_stock' => $products->where('stock_quantity', 0)->count(),
                'top_products' => $products->sortByDesc(function($p) {
                    return $p->orders->sum('pivot.quantity');
                })->take(5)->map(function($p) {
                    $totalSold = $p->orders->sum('pivot.quantity');
                    $revenue = $p->orders->sum(function($order) {
                        return $order->pivot->quantity * $order->pivot->price;
                    });
                    return [
                        'name' => $p->name,
                        'price' => $p->price,
                        'cost' => $p->cost_price,
                        'stock' => $p->stock_quantity,
                        'category' => $p->category,
                        'total_sold' => $totalSold,
                        'revenue' => $revenue,
                    ];
                })->values()->toArray(),
                'by_category' => $products->groupBy('category')->map(function($group, $category) {
                    return [
                        'category' => $category ?: 'Uncategorized',
                        'count' => $group->count(),
                        'avg_price' => $group->avg('price'),
                    ];
                })->values()->toArray(),
            ];
        }

        // Services data
        $services = Service::where('business_id', $business->id)->get();
        if ($services->count() > 0) {
            $data['services'] = [
                'total' => $services->count(),
                'avg_price' => $services->avg('price'),
                'avg_duration' => $services->avg('duration'),
                'top_services' => $services->sortByDesc(function($s) {
                    return $s->bookings->count();
                })->take(5)->map(function($s) {
                    return [
                        'name' => $s->name,
                        'price' => $s->price,
                        'duration' => $s->duration,
                        'bookings' => $s->bookings->count(),
                    ];
                })->values()->toArray(),
            ];
        }

        // Sales data (using Order model)
        $sales = Order::where('business_id', $business->id)->get();
        if ($sales->count() > 0) {
            $last30Days = $sales->where('created_at', '>=', now()->subDays(30));
            $last7Days = $sales->where('created_at', '>=', now()->subDays(7));
            
            $data['sales'] = [
                'total_revenue' => $sales->sum('total_amount'),
                'total_orders' => $sales->count(),
                'avg_order_value' => $sales->avg('total_amount'),
                'last_30_days' => [
                    'revenue' => $last30Days->sum('total_amount'),
                    'orders' => $last30Days->count(),
                    'avg_order' => $last30Days->avg('total_amount'),
                ],
                'last_7_days' => [
                    'revenue' => $last7Days->sum('total_amount'),
                    'orders' => $last7Days->count(),
                ],
                'by_payment_method' => $sales->groupBy('payment_method')->map(function($group, $method) {
                    return [
                        'method' => $method,
                        'count' => $group->count(),
                        'total' => $group->sum('total_amount'),
                    ];
                })->values()->toArray(),
            ];
        }

        // Customer data
        $customers = Customer::where('business_id', $business->id)->get();
        if ($customers->count() > 0) {
            $data['customers'] = [
                'total' => $customers->count(),
                'with_orders' => $customers->filter(function($c) {
                    return $c->orders->count() > 0;
                })->count(),
                'new_this_month' => $customers->where('created_at', '>=', now()->startOfMonth())->count(),
                'top_customers' => $customers->sortByDesc(function($c) {
                    return $c->orders->sum('total_amount');
                })->take(5)->map(function($c) {
                    return [
                        'name' => $c->name,
                        'orders' => $c->orders->count(),
                        'total_spent' => $c->orders->sum('total_amount'),
                    ];
                })->values()->toArray(),
            ];
        }

        // Staff/Employees data
        $staff = Staff::where('business_id', $business->id)->get();
        $employees = Employee::where('business_id', $business->id)->get();
        
        if ($staff->count() > 0 || $employees->count() > 0) {
            $allStaff = $staff->concat($employees);
            $totalSalaries = $staff->sum('salary') + $employees->sum('salary');
            $totalCommissions = CommissionPayout::where('business_id', $business->id)->sum('amount');
            
            $data['workforce'] = [
                'total_staff' => $staff->count(),
                'total_employees' => $employees->count(),
                'total_workforce' => $allStaff->count(),
                'total_monthly_salaries' => $totalSalaries,
                'total_commissions_paid' => $totalCommissions,
                'total_labor_cost' => $totalSalaries + $totalCommissions,
            ];

            // Staff details
            if ($staff->count() > 0) {
                $data['workforce']['staff_breakdown'] = $staff->map(function($s) {
                    $commission = $s->total_commission ?? 0;
                    return [
                        'name' => $s->name,
                        'role' => $s->role,
                        'salary' => $s->salary,
                        'commission_earned' => $commission,
                        'total_earnings' => ($s->salary ?? 0) + $commission,
                    ];
                })->toArray();

                $data['workforce']['staff_by_role'] = $staff->groupBy('role')->map(function($group, $role) {
                    return [
                        'role' => $role,
                        'count' => $group->count(),
                        'total_salary' => $group->sum('salary'),
                    ];
                })->values()->toArray();
            }

            // Employee details
            if ($employees->count() > 0) {
                $data['workforce']['employees_breakdown'] = $employees->map(function($e) {
                    return [
                        'name' => $e->first_name . ' ' . $e->last_name,
                        'position' => $e->position,
                        'department' => $e->department,
                        'employment_type' => $e->employment_type,
                        'salary' => $e->salary,
                        'hourly_rate' => $e->hourly_rate,
                        'status' => $e->status,
                    ];
                })->toArray();

                $data['workforce']['employees_by_department'] = $employees->groupBy('department')->map(function($group, $dept) {
                    return [
                        'department' => $dept ?: 'Unassigned',
                        'count' => $group->count(),
                        'total_salary' => $group->sum('salary'),
                    ];
                })->values()->toArray();

                $data['workforce']['employees_by_type'] = $employees->groupBy('employment_type')->map(function($group, $type) {
                    return [
                        'type' => $type,
                        'count' => $group->count(),
                    ];
                })->values()->toArray();
            }
        }

        // Payment & Financial data
        $payments = Payment::where('business_id', $business->id)->get();
        if ($payments->count() > 0) {
            $data['payments'] = [
                'total_received' => $payments->where('status', 'completed')->sum('amount'),
                'pending_payments' => $payments->where('status', 'pending')->sum('amount'),
                'failed_payments' => $payments->where('status', 'failed')->count(),
                'by_method' => $payments->groupBy('payment_method')->map(function($group, $method) {
                    return [
                        'method' => $method,
                        'count' => $group->count(),
                        'total' => $group->sum('amount'),
                        'completed' => $group->where('status', 'completed')->sum('amount'),
                    ];
                })->values()->toArray(),
                'by_status' => $payments->groupBy('status')->map(function($group, $status) {
                    return [
                        'status' => $status,
                        'count' => $group->count(),
                        'total' => $group->sum('amount'),
                    ];
                })->values()->toArray(),
            ];
        }

        // Suppliers data
        try {
            $suppliers = Supplier::where('business_id', $business->id)->get();
            if ($suppliers->count() > 0) {
                $data['suppliers'] = [
                    'total' => $suppliers->count(),
                    'active' => $suppliers->where('status', 'active')->count(),
                    'suppliers_list' => $suppliers->map(function($s) {
                        return [
                            'name' => $s->name,
                            'contact' => $s->contact_person ?? 'N/A',
                            'email' => $s->email,
                            'phone' => $s->phone,
                            'status' => $s->status,
                        ];
                    })->toArray(),
                ];
            }
        } catch (\Exception $e) {
            // Suppliers table might not exist
        }

        // Costs & Expenses calculation
        $inventoryCosts = 0;
        $productCosts = $products->sum(function($p) {
            return ($p->cost_price ?? 0) * ($p->stock_quantity ?? 0);
        });
        
        $returns = 0;
        try {
            $returns = $business->getTotalReturnsAttribute() ?? 0;
        } catch (\Exception $e) {
            // Returns might not be available
        }

        $data['costs_expenses'] = [
            'inventory_value' => $productCosts,
            'monthly_salaries' => $totalSalaries ?? 0,
            'commissions' => $totalCommissions ?? 0,
            'returns_refunds' => $returns,
            'total_monthly_expenses' => ($totalSalaries ?? 0) + ($totalCommissions ?? 0),
        ];

        // Profit calculations
        if (isset($data['sales'])) {
            $totalRevenue = $data['sales']['total_revenue'];
            $totalExpenses = $data['costs_expenses']['total_monthly_expenses'];
            $costOfGoodsSold = 0;
            
            // Calculate COGS from products sold
            foreach ($products as $product) {
                $soldQty = $product->orders->sum('pivot.quantity');
                $costOfGoodsSold += ($product->cost_price ?? 0) * $soldQty;
            }

            $data['profitability'] = [
                'total_revenue' => $totalRevenue,
                'cost_of_goods_sold' => $costOfGoodsSold,
                'gross_profit' => $totalRevenue - $costOfGoodsSold,
                'gross_profit_margin' => $totalRevenue > 0 ? round((($totalRevenue - $costOfGoodsSold) / $totalRevenue) * 100, 2) : 0,
                'operating_expenses' => $totalExpenses,
                'net_profit' => $totalRevenue - $costOfGoodsSold - $totalExpenses,
                'net_profit_margin' => $totalRevenue > 0 ? round((($totalRevenue - $costOfGoodsSold - $totalExpenses) / $totalRevenue) * 100, 2) : 0,
            ];
        }

        return $data;
    }

    /**
     * Get conversation history for the current session
     */
    public function getHistory(Request $request)
    {
        try {
            $user     = Auth::user();
            $business = $user->business;

            if (! $business) {
                return response()->json(['success' => true, 'history' => []]);
            }

            $sessionId = $request->session()->getId();
            $history   = $this->memoryService->getConversationHistory($business->id, $sessionId, 20);

            return response()->json([
                'success' => true,
                'history' => $history,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting history: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear conversation history for the current session
     */
    public function clearHistory(Request $request)
    {
        try {
            $user     = Auth::user();
            $business = $user->business;

            if ($business) {
                $sessionId = $request->session()->getId();
                $this->memoryService->clearSession($business->id, $sessionId);
            }

            return response()->json([
                'success' => true,
                'message' => 'Conversation history cleared successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error clearing history: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Format analysis results for chat response
     */
    private function formatAnalysisForChat($analysis, $userMessage)
    {
        $response = "Based on KENADA analysis of your business:\n\n";
        
        if (isset($analysis['financial_health_score'])) {
            $response .= "📊 **Financial Health Score:** {$analysis['financial_health_score']}/100\n";
        }
        
        if (isset($analysis['growth_potential'])) {
            $response .= "📈 **Growth Potential:** {$analysis['growth_potential']}\n\n";
        }
        
        if (isset($analysis['recommendations']) && is_array($analysis['recommendations'])) {
            $response .= "💡 **Key Recommendations:**\n";
            foreach (array_slice($analysis['recommendations'], 0, 3) as $recommendation) {
                $response .= "• " . $recommendation . "\n";
            }
        }
        
        if (isset($analysis['market_comparison'])) {
            $response .= "\n🏆 **Market Position:** {$analysis['market_comparison']}\n";
        }
        
        return $response;
    }

    /**
     * Get AI suggestions based on business type
     */
    public function getSuggestions(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id'
        ]);

        try {
            $business = Business::findOrFail($request->business_id);
            
            $suggestions = $this->getBusinessSuggestions($business);

            return response()->json([
                'success' => true,
                'suggestions' => $suggestions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting suggestions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get business-specific suggestions
     */
    protected function getBusinessSuggestions($business)
    {
        $suggestions = [
            "What are my top selling products?",
            "Which products or services need better pricing?",
            "How can I improve my business growth?",
            "What items are running low in stock?",
            "Suggest better suppliers for my products available online"
        ];

        // Add business-specific suggestions
        switch ($business->business_type) {
            case 'retail':
            case 'shop':
                $suggestions[] = "Which products should I stock more of?";
                $suggestions[] = "What are the best-selling categories?";
                $suggestions[] = "How can I reduce out-of-stock situations?";
                $suggestions[] = "Compare my prices with market rates";
                break;
            case 'service':
            case 'salon':
            case 'barber':
                $suggestions[] = "Which services are most profitable?";
                $suggestions[] = "How can I increase service bookings?";
                $suggestions[] = "What service packages should I create?";
                $suggestions[] = "How can I improve customer retention?";
                break;
            case 'restaurant':
            case 'food':
                $suggestions[] = "What menu items are most popular?";
                $suggestions[] = "How can I optimize my menu pricing?";
                $suggestions[] = "What items have the best profit margins?";
                $suggestions[] = "Suggest cost-effective food suppliers in Kenya";
                break;
            case 'technology':
            case 'electronics':
                $suggestions[] = "What tech products are trending in Kenya?";
                $suggestions[] = "Which items should I promote more?";
                $suggestions[] = "Compare my tech prices with online marketplaces";
                $suggestions[] = "What warranty or service packages should I offer?";
                break;
            default:
                $suggestions[] = "What are the latest trends in my industry?";
                $suggestions[] = "How can I improve customer satisfaction?";
                $suggestions[] = "What marketing strategies work best for my business type?";
                $suggestions[] = "Compare my performance to similar businesses in Kenya";
        }

        return $suggestions;
    }

    /**
     * Get AI system status
     */
    public function getStatus()
    {
        try {
            // Check if knowledge data exists
            $knowledgeCount = \DB::table('knowledge_data')->count();
            $businessCount = Business::where('user_id', Auth::id())->count();

            $status = [
                'knowledge_available' => $knowledgeCount > 0,
                'knowledge_count' => $knowledgeCount,
                'businesses_count' => $businessCount,
                'system_ready' => true
            ];

            return response()->json([
                'success' => true,
                'status' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get quick insights for dashboard
     */
    public function getQuickInsights(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id'
        ]);

        try {
            $businessId = $request->business_id;
            
            // Get sales insights
            $salesData = \DB::table('sales')
                ->where('business_id', $businessId)
                ->select('total_amount', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(30)
                ->get();

            $totalSales = $salesData->sum('total_amount');
            $avgOrder = $salesData->count() > 0 ? $totalSales / $salesData->count() : 0;

            // Get market insights
            $marketData = \DB::table('knowledge_data')
                ->whereIn('data_type', ['news', 'market_data'])
                ->select('sentiment_score', 'relevance_score')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            $avgSentiment = $marketData->count() > 0 ? $marketData->avg('sentiment_score') : 0;

            $insights = [
                'sales' => [
                    'total' => number_format($totalSales, 2),
                    'avg_order' => number_format($avgOrder, 2),
                    'orders_count' => $salesData->count()
                ],
                'market' => [
                    'sentiment' => round($avgSentiment, 2),
                    'trend' => $avgSentiment > 0.1 ? 'positive' : ($avgSentiment < -0.1 ? 'negative' : 'neutral')
                ],
                'recommendations' => $this->getQuickRecommendations($businessId, $totalSales, $avgSentiment)
            ];

            return response()->json([
                'success' => true,
                'insights' => $insights
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting insights: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get quick recommendations based on data
     */
    protected function getQuickRecommendations($businessId, $totalSales, $avgSentiment)
    {
        $recommendations = [];

        if ($totalSales == 0) {
            $recommendations[] = "Start recording your sales to get detailed insights";
        } else {
            $recommendations[] = "Continue tracking sales to identify trends";
        }

        if ($avgSentiment > 0.2) {
            $recommendations[] = "Market sentiment is positive - consider expanding";
        } elseif ($avgSentiment < -0.2) {
            $recommendations[] = "Market sentiment is negative - focus on cost optimization";
        } else {
            $recommendations[] = "Market is stable - maintain current strategies";
        }

        $recommendations[] = "Regular analysis helps identify improvement opportunities";

        return $recommendations;
    }
}

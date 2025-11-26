<?php

namespace App\Http\Controllers;

use App\Models\OrderReturn;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnsController extends Controller
{
    /**
     * Display list of all returns
     */
    public function index(Request $request)
    {
        $business = auth()->user()->business;
        
        $query = OrderReturn::where('business_id', $business->id)
            ->with(['order', 'customer', 'processedBy']);
        
        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        // Search by return number or customer
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('return_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('customer', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $returns = $query->latest()->paginate(20);
        
        // Summary statistics
        $stats = [
            'total' => OrderReturn::where('business_id', $business->id)->count(),
            'pending' => OrderReturn::where('business_id', $business->id)->where('status', 'pending')->count(),
            'approved' => OrderReturn::where('business_id', $business->id)->where('status', 'approved')->count(),
            'completed' => OrderReturn::where('business_id', $business->id)->where('status', 'completed')->count(),
            'total_refunded' => OrderReturn::where('business_id', $business->id)
                ->where('status', 'completed')
                ->sum('refund_amount'),
            'this_month_refunded' => OrderReturn::where('business_id', $business->id)
                ->where('status', 'completed')
                ->thisMonth()
                ->sum('refund_amount'),
        ];
        
        return view('business.returns.index', compact('returns', 'stats'));
    }

    /**
     * Show form to create a return
     */
    public function create(Request $request)
    {
        $business = auth()->user()->business;
        $orderId = $request->input('order_id');
        
        $order = null;
        if ($orderId) {
            $order = Order::where('business_id', $business->id)
                ->with(['items.product', 'customer'])
                ->findOrFail($orderId);
        }
        
        // Get recent orders for selection
        $recentOrders = Order::where('business_id', $business->id)
            ->where('status', 'completed')
            ->with('customer')
            ->latest()
            ->take(50)
            ->get();
        
        return view('business.returns.create', compact('order', 'recentOrders'));
    }

    /**
     * Store a new return
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'return_type' => 'required|in:full,partial',
            'reason_category' => 'required|in:defective,wrong_item,not_as_described,customer_changed_mind,damaged_in_shipping,other',
            'reason' => 'required|string',
            'items' => 'required_if:return_type,partial|array',
            'items.*.product_id' => 'required_with:items|exists:products,id',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.refund_amount' => 'required_with:items|numeric|min:0',
            'restocking_fee' => 'nullable|numeric|min:0',
            'return_to_stock' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $business = auth()->user()->business;
            $order = Order::findOrFail($validated['order_id']);
            
            // Verify order belongs to business
            if ($order->business_id !== $business->id) {
                abort(403, 'Unauthorized');
            }
            
            // Calculate refund amount
            $refundAmount = 0;
            if ($validated['return_type'] === 'full') {
                $refundAmount = $order->total_amount;
            } else {
                $refundAmount = collect($validated['items'])->sum('refund_amount');
            }
            
            // Apply restocking fee if any
            $restockingFee = $validated['restocking_fee'] ?? 0;
            $refundAmount -= $restockingFee;
            
            // Create return record
            $return = OrderReturn::create([
                'business_id' => $business->id,
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'return_type' => $validated['return_type'],
                'status' => 'pending',
                'reason' => $validated['reason'],
                'reason_category' => $validated['reason_category'],
                'original_amount' => $order->total_amount,
                'refund_amount' => $refundAmount,
                'restocking_fee' => $restockingFee,
                'return_to_stock' => $validated['return_to_stock'] ?? true,
                'items_data' => $validated['items'] ?? null,
                'notes' => $validated['notes'],
            ]);
            
            DB::commit();
            
            return redirect()->route('returns.show', $return)
                ->with('success', 'Return request created successfully! Return Number: ' . $return->return_number);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create return: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display single return details
     */
    public function show(OrderReturn $return)
    {
        $this->authorize('view', $return);
        
        $return->load(['order.items.product', 'customer', 'processedBy']);
        
        return view('business.returns.show', compact('return'));
    }

    /**
     * Approve a return
     */
    public function approve(OrderReturn $return)
    {
        $this->authorize('update', $return);
        
        $return->update([
            'status' => 'approved',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);
        
        return back()->with('success', 'Return approved successfully!');
    }

    /**
     * Reject a return
     */
    public function reject(Request $request, OrderReturn $return)
    {
        $this->authorize('update', $return);
        
        $validated = $request->validate([
            'internal_notes' => 'required|string',
        ]);
        
        $return->update([
            'status' => 'rejected',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
            'internal_notes' => $validated['internal_notes'],
        ]);
        
        return back()->with('success', 'Return rejected.');
    }

    /**
     * Complete a return (process refund and return stock)
     */
    public function complete(Request $request, OrderReturn $return)
    {
        $this->authorize('update', $return);
        
        if ($return->status !== 'approved') {
            return back()->withErrors(['error' => 'Return must be approved before completion.']);
        }
        
        $validated = $request->validate([
            'refund_method' => 'required|in:cash,card,mobile_money,bank_transfer,store_credit',
        ]);
        
        DB::beginTransaction();
        try {
            // Return stock if applicable
            if ($return->return_to_stock && !$return->stock_returned) {
                if ($return->return_type === 'full') {
                    // Return all items from order
                    foreach ($return->order->items as $item) {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            $product->increment('stock_quantity', $item->quantity);
                        }
                    }
                } else {
                    // Return specific items
                    if ($return->items_data) {
                        foreach ($return->items_data as $item) {
                            $product = Product::find($item['product_id']);
                            if ($product) {
                                $product->increment('stock_quantity', $item['quantity']);
                            }
                        }
                    }
                }
                
                $return->stock_returned = true;
            }
            
            // Mark refund as processed
            $return->update([
                'status' => 'completed',
                'refund_method' => $validated['refund_method'],
                'refund_processed' => true,
                'refund_processed_at' => now(),
            ]);
            
            DB::commit();
            
            return back()->with('success', 'Return completed! Refund of ' . $return->formatted_refund_amount . ' processed via ' . ucfirst($validated['refund_method']) . '.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to complete return: ' . $e->getMessage()]);
        }
    }

    /**
     * Get return statistics for dashboard
     */
    public function stats()
    {
        $business = auth()->user()->business;
        
        $stats = [
            'today_returns' => OrderReturn::where('business_id', $business->id)
                ->whereDate('created_at', today())
                ->count(),
            'today_refunds' => OrderReturn::where('business_id', $business->id)
                ->where('status', 'completed')
                ->whereDate('refund_processed_at', today())
                ->sum('refund_amount'),
            'pending_returns' => OrderReturn::where('business_id', $business->id)
                ->where('status', 'pending')
                ->count(),
            'month_refunds' => OrderReturn::where('business_id', $business->id)
                ->where('status', 'completed')
                ->thisMonth()
                ->sum('refund_amount'),
        ];
        
        return response()->json($stats);
    }
}

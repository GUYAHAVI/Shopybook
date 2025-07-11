<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function pos()
    {
        $products = auth()->user()->business->products()->active()->inStock()->get();
        $customers = auth()->user()->business->customers()->get();
        
        return view('sales.pos', compact('products', 'customers'));
    }

    public function orders()
    {
        $orders = auth()->user()->business->orders()
            ->with(['customer', 'items.product'])
            ->latest()
            ->paginate(15);
            
        return view('sales.orders', compact('orders'));
    }

    public function orderDetails(Order $order)
    {
        $this->authorize('view', $order);
        
        return view('sales.order-details', compact('order'));
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card,mobile_money,bank_transfer',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            $order = new Order();
            $order->business_id = auth()->user()->business->id;
            $order->customer_id = $request->customer_id;
            $order->order_number = 'ORD-' . strtoupper(uniqid());
            $order->status = 'pending';
            $order->payment_method = $request->payment_method;
            $order->notes = $request->notes;
            $order->save();

            $total = 0;
            
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }
                
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'total' => $product->price * $item['quantity'],
                ]);
                
                // Update stock
                $product->stock_quantity -= $item['quantity'];
                $product->save();
                
                $total += $product->price * $item['quantity'];
            }
            
            $order->total_amount = $total;
            $order->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled'
        ]);
        
        $order->status = $request->status;
        $order->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully'
        ]);
    }

    public function invoices()
    {
        $invoices = auth()->user()->business->invoices()
            ->with(['order.customer'])
            ->latest()
            ->paginate(15);
            
        return view('sales.invoices', compact('invoices'));
    }

    public function generateInvoice(Order $order)
    {
        $this->authorize('view', $order);
        
        $invoice = new Invoice();
        $invoice->business_id = auth()->user()->business->id;
        $invoice->order_id = $order->id;
        $invoice->invoice_number = 'INV-' . strtoupper(uniqid());
        $invoice->amount = $order->total_amount;
        $invoice->status = 'pending';
        $invoice->due_date = now()->addDays(30);
        $invoice->save();
        
        return redirect()->route('sales.invoices')->with('success', 'Invoice generated successfully');
    }

    public function customers()
    {
        $customers = auth()->user()->business->customers()
            ->withCount('orders')
            ->latest()
            ->paginate(15);
            
        return view('sales.customers', compact('customers'));
    }

    public function customerDetails(Customer $customer)
    {
        $this->authorize('view', $customer);
        
        $orders = $customer->orders()->with('items.product')->latest()->paginate(10);
        
        return view('sales.customer-details', compact('customer', 'orders'));
    }

    public function storeCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);

        $customer = new Customer($request->all());
        $customer->business_id = auth()->user()->business->id;
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully',
            'customer' => $customer
        ]);
    }

    public function salesReport(Request $request)
    {
        $period = $request->get('period', '30');
        $startDate = now()->subDays($period);
        
        $sales = auth()->user()->business->orders()
            ->where('created_at', '>=', $startDate)
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $topProducts = auth()->user()->business->orders()
            ->where('created_at', '>=', $startDate)
            ->where('status', 'completed')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('products.name, SUM(order_items.quantity) as total_sold, SUM(order_items.total) as revenue')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();
            
        return view('sales.report', compact('sales', 'topProducts', 'period'));
    }
}

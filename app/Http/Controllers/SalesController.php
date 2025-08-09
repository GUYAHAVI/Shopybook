<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\OrganizationCustomer;
use Illuminate\Support\Facades\Hash;
// date

class SalesController extends Controller
{
    public function pos()
    {
        $business = auth()->user()->business;
        $products = $business->products()->active()->inStock()->get();
        $customers = $business->customers()->get();
        
        // Debug logging
        \Log::info('POS Debug', [
            'business_id' => $business->id,
            'business_name' => $business->name,
            'total_products' => $business->products()->count(),
            'active_products' => $business->products()->active()->count(),
            'in_stock_products' => $products->count(),
            'customers_count' => $customers->count()
        ]);
        
        return view('sales.pos', compact('products', 'customers'));
    }

    public function orders()
    {
        $orders = auth()->user()->business->orders()
            ->with(['customer', 'items.product', 'product'])
            ->latest()
            ->paginate(15);
            
        return view('sales.orders', compact('orders'));
    }

    public function orderDetails(Order $order)
    {
        \Log::info('OrderDetails called', [
            'order_id' => $order->id,
            'business_id' => $order->business_id,
            'user_business_id' => auth()->user()->business->id ?? 'null'
        ]);
        
        $this->authorize('view', $order);
        
        return view('sales.order-details', compact('order'));
    }

    public function createOrder(Request $request)
    {
        \Log::info('CreateOrder called with data:', $request->all());
        
        try {
            $validated = $request->validate([
                'customer_id' => 'nullable|integer|exists:customers,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|integer|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'notes' => 'nullable|string',
                'subtotal' => 'required|numeric|min:0',
                'tax' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0'
            ]);

            \Log::info('Validation passed');

            DB::beginTransaction();
            
            $order = new Order();
            $order->business_id = auth()->user()->business->id;
            if (empty($validated['customer_id'])) {
                // Assign to Walk-in Customer if not provided
                $walkin = Customer::where('business_id', auth()->user()->business->id)
                    ->where('name', 'Walk-in Customer')->first();
                $order->customer_id = ($walkin && isset($walkin->id)) ? $walkin->id : null;
            } else {
                $order->customer_id = $validated['customer_id'];
            }
            $order->order_number = 'ORD-' . strtoupper(uniqid());
                    $order->status = 'completed'; // POS orders are automatically completed
        $order->payment_status = 'paid'; // POS orders are automatically paid
        $order->payment_method = $validated['payment_method'];
        $order->notes = $validated['notes'];
            $order->subtotal = $validated['subtotal'];
            $order->tax = $validated['tax'];
            $order->total_amount = $validated['total'];
            $order->save();

            \Log::info('Order created with ID: ' . $order->id);

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }
                
                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                ]);
                
                // Update stock
                $product->stock_quantity -= $item['quantity'];
                $product->save();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'order_number' => $order->order_number,
                'order' => $order
            ]);
            
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error creating order: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        \Log::info('UpdateOrderStatus called', [
            'order_id' => $order->id,
            'business_id' => $order->business_id,
            'user_business_id' => auth()->user()->business->id ?? 'null',
            'request_status' => $request->input('status')
        ]);
        
        $this->authorize('update', $order);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'password' => 'required|string'
        ]);

        // Verify user password (same as service deletion)
        if (!Hash::check($validated['password'], auth()->user()->getAuthPassword())) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid password. Please check your password and try again.'
            ], 422);
        }
        
        $order->update(['status' => $validated['status']]);
        
        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully'
        ]);
    }

    public function printReceipt(Order $order)
    {
        $this->authorize('view', $order);
        
        $business = auth()->user()->business;
        
        return view('sales.receipt', compact('order', 'business'));
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

    public function organizationCustomers()
    {
        $organizations = auth()->user()->business->organizationCustomers()->latest()->paginate(15);
        return view('sales.organization-customers', compact('organizations'));
    }

    public function createOrganizationCustomer()
    {
        return view('sales.customers.add-organization-customer');
    }

    public function storeOrganizationCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'required|string|max:20',
            'kra_pin' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
        ]);
        $org = new OrganizationCustomer($request->all());
        $org->business_id = auth()->user()->business->id;
        $org->save();
        return redirect()->route('sales.organization-customers')->with('success', 'Organization customer added successfully');
    }

    public function showOrganizationCustomer(OrganizationCustomer $organizationCustomer)
    {
        $this->authorize('view', $organizationCustomer);
        // Future: fetch purchases/orders
        return view('sales.organization-customer-details', compact('organizationCustomer'));
    }

    public function customers()
    {
        $customers = auth()->user()->business->customers()->withCount('orders')->latest()->paginate(15);
        $organizations = auth()->user()->business->organizationCustomers()->latest()->paginate(15);
        return view('sales.customers', compact('customers', 'organizations'));
    }

    public function showCustomer(Request $request, $type, $id)
    {
        if ($type === 'organization') {
            $customer = OrganizationCustomer::findOrFail($id);
            // Future: fetch organization orders if implemented
            $orders = collect(); // Placeholder, implement if orgs can have orders
        } else {
            $customer = Customer::findOrFail($id);
            $orders = $customer->orders()->latest()->get();
        }
        return view('sales.customers.show', compact('type', 'customer', 'orders'));
    }

    public function storeCustomer(Request $request)
    {
        try {
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

            // Handle AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer created successfully!',
                    'customer' => $customer
                ]);
            }

            return redirect()
                ->route('sales.customers')
                ->with('success', 'Customer created successfully!');
                
        } catch (ValidationException $e) {
            // Handle AJAX validation errors
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                    'message' => 'Validation failed'
                ], 422);
            }
            
            // For regular form submissions, let Laravel handle it normally
            throw $e;
        }
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

    public function createCustomer()
    {
        return view('sales.customers.add-individual-customer');
    }
}

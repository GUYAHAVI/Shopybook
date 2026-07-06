<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\CustomerDebt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\OrganizationCustomer;
use Illuminate\Support\Facades\Hash;
use App\Services\NotificationService;
// date

class SalesController extends Controller
{
    public function pos()
    {
        $business = auth()->user()->business;
        $products = $business->products()->active()->inStock()->get();
        $customers = $business->customers()->get();
        
        // Check if business is eligible for dynamic conversions
        $isEligibleForDynamicConversions = $business->isEligibleForDynamicConversions();
        
        // Debug logging
        \Log::info('POS Debug', [
            'business_id' => $business->id,
            'business_name' => $business->name,
            'total_products' => $business->products()->count(),
            'active_products' => $business->products()->active()->count(),
            'in_stock_products' => $products->count(),
            'customers_count' => $customers->count(),
            'dynamic_conversions_eligible' => $isEligibleForDynamicConversions
        ]);
        
        return view('sales.pos', compact('products', 'customers', 'business', 'isEligibleForDynamicConversions'));
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

    /**
     * Calculate dynamic conversion for POS
     */
    public function calculateDynamicConversion(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'from_unit' => 'required|string',
            'to_unit' => 'required|string'
        ]);

        $business = auth()->user()->business;
        
        // Check if business is eligible for dynamic conversions
        if (!$business->isEligibleForDynamicConversions()) {
            return response()->json([
                'success' => false,
                'message' => 'Dynamic Conversion System is only available for Havi\'s Greenhouse Materials.'
            ], 403);
        }

        $product = $business->products()->findOrFail($request->product_id);
        
        $dynamicService = new \App\Services\DynamicConversionService();
        $result = $dynamicService->calculateConversion(
            $product,
            $request->quantity,
            $request->from_unit,
            $request->to_unit
        );

        return response()->json($result);
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
                'items.*.conversion' => 'nullable|array',
                'items.*.conversion.sell_unit' => 'nullable|string',
                'items.*.conversion.material_type' => 'nullable|string',
                'items.*.conversion.original_quantity' => 'nullable|numeric',
                'items.*.conversion.converted_quantity' => 'nullable|numeric',
                'items.*.conversion.converted_unit' => 'nullable|string',
                'items.*.conversion.price_per_unit' => 'nullable|numeric',
                'cart_data' => 'nullable|array', // Add validation for cart_data
                'cart_data.*.id' => 'nullable|integer',
                'cart_data.*.name' => 'nullable|string',
                'cart_data.*.quantity' => 'nullable|integer',
                'cart_data.*.price' => 'nullable|numeric',
                'cart_data.*.conversion' => 'nullable|array',
                'cart_data.*.stockRequired' => 'nullable|numeric',
                'payment_method' => 'required|string',
                'payment_status' => 'nullable|string|in:paid,unpaid,partial',
                'amount_paid' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'subtotal' => 'required|numeric|min:0',
                'tax' => 'required|numeric|min:0',
                'total' => 'required|numeric|min:0'
            ]);

            \Log::info('Validation passed');

            DB::beginTransaction();
            
            $business = auth()->user()->business;
            $taxSettings = $business->taxSettings;
            
            // Calculate or verify tax
            $subtotal = $validated['subtotal'];
            $taxAmount = $validated['tax'];
            $total = $validated['total'];
            
            // If tax is enabled, verify/recalculate tax amounts and store metadata
            if ($taxSettings && $taxSettings->tax_enabled) {
                // Server-side tax calculation for verification
                $calculatedTax = $taxSettings->calculateTax($subtotal);
                $calculatedTotal = $taxSettings->getTotalWithTax($subtotal);
                
                // Use server calculation (more reliable)
                $taxAmount = $calculatedTax;
                $total = $calculatedTotal;
                
                \Log::info('Tax calculation', [
                    'subtotal' => $subtotal,
                    'client_tax' => $validated['tax'],
                    'server_tax' => $calculatedTax,
                    'tax_rate' => $taxSettings->tax_rate,
                    'tax_inclusive' => $taxSettings->tax_inclusive
                ]);
            }
            
            $order = new Order();
            $order->business_id = $business->id;
            if (empty($validated['customer_id'])) {
                // Assign to Walk-in Customer if not provided
                $walkin = Customer::where('business_id', $business->id)
                    ->where('name', 'Walk-in Customer')->first();
                $order->customer_id = ($walkin && isset($walkin->id)) ? $walkin->id : null;
            } else {
                $order->customer_id = $validated['customer_id'];
            }
            $order->order_number = 'ORD-' . strtoupper(uniqid());
            $order->status = 'completed'; // POS orders are automatically completed
            $order->payment_status = $validated['payment_status'] ?? 'paid'; // Default to paid
            $order->payment_method = $validated['payment_method'];
            $order->notes = $validated['notes'];
            $order->subtotal = $subtotal;
            $order->tax = $taxAmount;
            $order->total_amount = $total;
            
            // Handle payment status and amounts
            $paymentStatus = $validated['payment_status'] ?? 'paid';
            $amountPaid = isset($validated['amount_paid']) ? (float) $validated['amount_paid'] : $total;
            
            // Adjust based on payment status
            if ($paymentStatus === 'unpaid') {
                $amountPaid = 0;
            } elseif ($paymentStatus === 'paid') {
                $amountPaid = $total;
            }
            
            $balanceDue = $total - $amountPaid;
            
            // Set final payment status
            if ($balanceDue <= 0) {
                $paymentStatus = 'paid';
                $balanceDue = 0;
                $amountPaid = $total;
            } elseif ($amountPaid > 0 && $balanceDue > 0) {
                $paymentStatus = 'partial';
            } elseif ($amountPaid <= 0) {
                $paymentStatus = 'unpaid';
            }
            
            $order->payment_status = $paymentStatus;
            $order->amount_paid = $amountPaid;
            $order->balance_due = $balanceDue;
            
            // Generate invoice number if payment is not full
            if ($paymentStatus !== 'paid') {
                $order->invoice_number = 'INV-' . date('Y') . '-' . str_pad(time(), 6, '0', STR_PAD_LEFT);
                $order->invoice_generated_at = now();
            }
            
            // Store tax metadata
            if ($taxSettings && $taxSettings->tax_enabled) {
                $order->tax_rate = $taxSettings->tax_rate;
                $order->tax_inclusive = $taxSettings->tax_inclusive;
                $order->tax_type = $taxSettings->tax_type;
            }
            
            $order->save();

            \Log::info('Order created with ID: ' . $order->id);
            
            // Create customer debt record if there's a balance
            if ($balanceDue > 0 && $order->customer_id) {
                // Map payment status to debt status (unpaid -> pending for debt table)
                $debtStatus = $paymentStatus === 'unpaid' ? 'pending' : $paymentStatus;
                
                CustomerDebt::create([
                    'business_id' => $business->id,
                    'customer_id' => $order->customer_id,
                    'order_id' => $order->id,
                    'total_amount' => $total,
                    'amount_paid' => $amountPaid,
                    'balance' => $balanceDue,
                    'due_date' => now()->addDays(30), // Default 30 days
                    'status' => $debtStatus,
                ]);
            }
            
            // Create payment record if amount was paid
            if ($amountPaid > 0) {
                Payment::create([
                    'business_id' => $business->id,
                    'order_id' => $order->id,
                    'payment_number' => Payment::generatePaymentNumber(),
                    'amount' => $amountPaid,
                    'payment_method' => $order->payment_method,
                    'payment_date' => now(),
                    'notes' => $paymentStatus === 'partial' ? 'Partial payment at checkout' : 'Full payment at checkout',
                    'received_by' => auth()->id(),
                ]);
            }

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                
                // Calculate stock to deduct based on conversion
                $stockToDeduct = $item['quantity'];
                
                // If this is a converted item, calculate the equivalent kg to deduct
                if (isset($item['conversion']) && is_array($item['conversion'])) {
                    $conversion = $item['conversion'];
                    $sellUnit = $conversion['sell_unit'] ?? null;
                    $materialType = $conversion['material_type'] ?? null;
                    $originalQuantity = $conversion['original_quantity'] ?? null;
                    
                    // If selling in sqm, convert to kg for stock deduction
                    if ($sellUnit === 'sqm' && $materialType && $originalQuantity) {
                        // Extract micron value from material type (e.g., "greenhouse_0.2" -> 0.2)
                        $micronMatch = preg_match('/(\d+\.\d+)/', $materialType, $matches);
                        if ($micronMatch) {
                            $microns = floatval($matches[1]);
                            // Convert sqm to kg: sqm * microns = kg
                            $stockToDeduct = $originalQuantity * $microns;
                        }
                    }
                    // If selling in kg, use original quantity directly
                    else if ($sellUnit === 'kg') {
                        $stockToDeduct = $originalQuantity;
                    }
                }
                
                // Check if sufficient stock is available
                if ($product->stock_quantity < $stockToDeduct) {
                    $sellUnitText = isset($sellUnit) ? " ({$sellUnit})" : "";
                    throw new \Exception("Insufficient stock for {$product->name}{$sellUnitText}. Required: {$stockToDeduct} kg, Available: {$product->stock_quantity} kg");
                }
                
                // Prepare order item data
                $orderItemData = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                ];
                
                // Add conversion data if present
                if (isset($item['conversion']) && is_array($item['conversion'])) {
                    $conversion = $item['conversion'];
                    $orderItemData['sell_unit'] = $conversion['sell_unit'] ?? null;
                    $orderItemData['material_type'] = $conversion['material_type'] ?? null;
                    $orderItemData['original_quantity'] = $conversion['original_quantity'] ?? null;
                    $orderItemData['converted_quantity'] = $conversion['converted_quantity'] ?? null;
                    $orderItemData['converted_unit'] = $conversion['converted_unit'] ?? null;
                    $orderItemData['price_per_unit'] = $conversion['price_per_unit'] ?? null;
                    $orderItemData['conversion_data'] = json_encode($conversion);
                }
                
                $order->items()->create($orderItemData);
                
                // Update stock with calculated deduction
                $product->stock_quantity -= $stockToDeduct;
                $product->save();
                
                \Log::info('Stock updated', [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'original_stock' => $product->stock_quantity + $stockToDeduct,
                    'stock_deducted' => $stockToDeduct,
                    'new_stock' => $product->stock_quantity,
                    'sell_unit' => $sellUnit ?? 'regular',
                    'material_type' => $materialType ?? 'N/A'
                ]);
                
                // Check for low stock and send notification if needed
                try {
                    $notificationService = new \App\Services\NotificationService();
                    $notificationService->checkAndNotifyLowStock($product);
                } catch (\Exception $e) {
                    \Log::error('Failed to check low stock', [
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            // Store receipt data for reprinting
            $receiptNumber = $this->storeReceiptData($order, $validated, $validated['cart_data'] ?? $validated['items']);
            
            // Send notifications after successful order creation
            try {
                $notificationService = new NotificationService();
                $notificationService->notifyNewOrder($order);
            } catch (\Exception $e) {
                // Log error but don't fail the order creation
                \Log::error('Failed to send order notifications', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'order_number' => $order->order_number,
                'receipt_number' => $receiptNumber,
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
        
        $oldStatus = $order->status;
        $newStatus = $validated['status'];
        
        // If order is being marked as completed and wasn't completed before, reduce stock
        if ($newStatus === 'completed' && $oldStatus !== 'completed') {
            DB::beginTransaction();
            try {
                // For public orders (single product)
                if ($order->order_type === 'public_order' && $order->product_id) {
                    $product = Product::find($order->product_id);
                    if ($product) {
                        // Check if sufficient stock is available
                        if ($product->stock_quantity < $order->quantity) {
                            return response()->json([
                                'success' => false,
                                'message' => "Insufficient stock for {$product->name}. Required: {$order->quantity}, Available: {$product->stock_quantity}"
                            ], 422);
                        }
                        
                        $product->stock_quantity -= $order->quantity;
                        $product->save();
                        
                        \Log::info('Stock reduced for public order', [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'quantity_deducted' => $order->quantity,
                            'new_stock' => $product->stock_quantity
                        ]);
                        
                        // Check for low stock and send notification
                        try {
                            $notificationService = new NotificationService();
                            $notificationService->checkAndNotifyLowStock($product);
                        } catch (\Exception $e) {
                            \Log::error('Failed to check low stock: ' . $e->getMessage());
                        }
                    }
                }
                // For regular orders with order items
                else if ($order->items) {
                    foreach ($order->items as $item) {
                        $product = Product::find($item->product_id);
                        if ($product) {
                            // Calculate stock to deduct (considering conversions)
                            $stockToDeduct = $item->quantity;
                            
                            // If this is a converted item, calculate the equivalent kg to deduct
                            if ($item->sell_unit && $item->material_type && $item->original_quantity) {
                                $sellUnit = $item->sell_unit;
                                $materialType = $item->material_type;
                                $originalQuantity = $item->original_quantity;
                                
                                // If selling in sqm, convert to kg for stock deduction
                                if ($sellUnit === 'sqm' && $materialType) {
                                    // Extract micron value from material type (e.g., "greenhouse_0.2" -> 0.2)
                                    $micronMatch = preg_match('/(\d+\.\d+)/', $materialType, $matches);
                                    if ($micronMatch) {
                                        $microns = floatval($matches[1]);
                                        // Convert sqm to kg: sqm * microns = kg
                                        $stockToDeduct = $originalQuantity * $microns;
                                    }
                                }
                                // If selling in kg, use original quantity directly
                                else if ($sellUnit === 'kg') {
                                    $stockToDeduct = $originalQuantity;
                                }
                            }
                            
                            // Check if sufficient stock is available
                            if ($product->stock_quantity < $stockToDeduct) {
                                DB::rollBack();
                                return response()->json([
                                    'success' => false,
                                    'message' => "Insufficient stock for {$product->name}. Required: {$stockToDeduct}, Available: {$product->stock_quantity}"
                                ], 422);
                            }
                            
                            $product->stock_quantity -= $stockToDeduct;
                            $product->save();
                            
                            \Log::info('Stock reduced for order item', [
                                'product_id' => $product->id,
                                'product_name' => $product->name,
                                'stock_deducted' => $stockToDeduct,
                                'new_stock' => $product->stock_quantity
                            ]);
                            
                            // Check for low stock and send notification
                            try {
                                $notificationService = new NotificationService();
                                $notificationService->checkAndNotifyLowStock($product);
                            } catch (\Exception $e) {
                                \Log::error('Failed to check low stock: ' . $e->getMessage());
                            }
                        }
                    }
                }
                
                $order->update(['status' => $newStatus]);
                DB::commit();
                
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error updating order status: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update order status: ' . $e->getMessage()
                ], 500);
            }
        } else {
            // Just update the status without stock changes
            $order->update(['status' => $newStatus]);
        }
        
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

    /**
     * Generate invoice for unpaid order (PDF download or view)
     */
    public function generateInvoice($orderId)
    {
        try {
            $order = Order::with(['customer', 'items.product', 'product'])->findOrFail($orderId);
            
            // Check authorization
            $this->authorize('view', $order);
            
            $business = auth()->user()->business;
            
            // Generate invoice number if not exists
            if (!$order->invoice_number) {
                $order->invoice_number = 'INV-' . date('Y') . '-' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
                $order->invoice_generated_at = now();
                $order->save();
            }
            
            // Return HTML view for printing (PDF generation requires additional package)
            return view('sales.invoice', compact('order', 'business'));
            
        } catch (\Exception $e) {
            \Log::error('Failed to generate invoice: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate invoice: ' . $e->getMessage());
        }
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

    /**
     * Store receipt data for reprinting
     */
    private function storeReceiptData($order, $validated, $cartData)
    {
        try {
            $business = auth()->user()->business;
            $customer = $order->customer;
            
            // Debug: Log what data we're receiving
            \Log::info('storeReceiptData called with:', [
                'order_id' => $order->id,
                'cartData_exists' => isset($cartData),
                'cartData_type' => gettype($cartData),
                'cartData_count' => is_array($cartData) ? count($cartData) : 'not_array',
                'cartData_sample' => is_array($cartData) && count($cartData) > 0 ? $cartData[0] : 'no_data',
                'validated_keys' => array_keys($validated)
            ]);
            
            // Calculate totals
            $subtotal = $validated['subtotal'];
            $taxAmount = $validated['tax'];
            $totalAmount = $validated['total'];
            
            // Check if this order has converted items
            $hasConvertedItems = false;
            foreach ($validated['items'] as $item) {
                if (isset($item['conversion']) && is_array($item['conversion'])) {
                    $hasConvertedItems = true;
                    break;
                }
            }
            
            // Prepare receipt data
            $receiptData = [
                'order_number' => $order->order_number,
                'order_date' => $order->created_at->format('Y-m-d H:i:s'),
                'business_name' => $business->name,
                'customer_name' => $customer ? $customer->name : 'Walk-in Customer',
                'customer_phone' => $customer ? $customer->phone : null,
                'customer_email' => $customer ? $customer->email : null,
                'customer_address' => $customer ? $customer->address : null,
                'payment_method' => $validated['payment_method'],
                'items' => $validated['items'],
                'cart_items' => $cartData, // Store the cart data for detailed receipt
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'currency_symbol' => 'KSh ',
                'notes' => $validated['notes'] ?? '',
                'is_eligible_for_conversions' => $business->isEligibleForDynamicConversions()
            ];
            
            // Create receipt record
            $receipt = new \App\Models\Receipt();
            $receipt->order_id = $order->id;
            $receipt->receipt_number = \App\Models\Receipt::generateReceiptNumber();
            $receipt->receipt_data = $receiptData;
            $receipt->business_name = $business->name;
            $receipt->customer_name = $customer ? $customer->name : 'Walk-in Customer';
            $receipt->customer_phone = $customer ? $customer->phone : null;
            $receipt->subtotal = $subtotal;
            $receipt->tax_amount = $taxAmount;
            $receipt->total_amount = $totalAmount;
            $receipt->payment_method = $validated['payment_method'];
            $receipt->currency_symbol = 'KSh ';
            $receipt->is_converted_order = $hasConvertedItems;
            $receipt->save();
            
            \Log::info('Receipt data stored', [
                'order_id' => $order->id,
                'receipt_number' => $receipt->receipt_number,
                'has_converted_items' => $hasConvertedItems
            ]);
            
            return $receipt->receipt_number;
            
        } catch (\Exception $e) {
            \Log::error('Error storing receipt data: ' . $e->getMessage());
            // Don't throw the error as it shouldn't prevent order completion
            return null;
        }
    }

    /**
     * Reprint receipt
     */
    public function reprintReceipt($receiptNumber)
    {
        $receipt = \App\Models\Receipt::where('receipt_number', $receiptNumber)->firstOrFail();
        
        // Check if user has access to this receipt
        $this->authorize('view', $receipt->order);
        
        return view('sales.receipt', [
            'receipt' => $receipt,
            'order' => $receipt->order,
            'business' => auth()->user()->business
        ]);
    }

    /**
     * Search receipts
     */
    public function searchReceipts(Request $request)
    {
        $query = $request->get('query');
        $business = auth()->user()->business;
        
        $receipts = \App\Models\Receipt::where('business_name', $business->name)
            ->where(function($q) use ($query) {
                $q->where('receipt_number', 'like', "%{$query}%")
                  ->orWhere('customer_name', 'like', "%{$query}%")
                  ->orWhere('customer_phone', 'like', "%{$query}%")
                  ->orWhereHas('order', function($orderQuery) use ($query) {
                      $orderQuery->where('order_number', 'like', "%{$query}%");
                  });
            })
            ->with('order')
            ->latest()
            ->paginate(15);
            
        return view('sales.receipts', compact('receipts', 'query'));
    }

    /**
     * Show credit note creation form
     */
    public function createCreditNote(Order $order)
    {
        $business = auth()->user()->business;
        
        // Ensure order belongs to user's business
        if ($order->business_id !== $business->id) {
            abort(403);
        }
        
        // Only allow credit notes for invoiced orders
        if (!$order->invoice_number) {
            return redirect()->back()->with('error', 'Credit notes can only be created for orders with invoices.');
        }
        
        return view('sales.credit-note-create', compact('order', 'business'));
    }

    /**
     * Store credit note request
     */
    public function storeCreditNote(Request $request, Order $order)
    {
        $business = auth()->user()->business;
        
        if ($order->business_id !== $business->id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
            'amount' => 'required|numeric|min:0|max:' . $order->total_amount,
        ]);
        
        $creditNote = \App\Models\CreditNote::create([
            'order_id' => $order->id,
            'credit_note_number' => \App\Models\CreditNote::generateCreditNoteNumber(),
            'invoice_number' => $order->invoice_number,
            'reason' => $validated['reason'],
            'amount' => $validated['amount'],
            'status' => 'pending',
            'requested_by' => auth()->id(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Credit note request created. Admin approval required.',
            'credit_note_id' => $creditNote->id,
        ]);
    }

    /**
     * Send OTP for credit note approval
     */
    public function sendCreditNoteOtp(\App\Models\CreditNote $creditNote)
    {
        $business = auth()->user()->business;
        
        if ($creditNote->order->business_id !== $business->id) {
            abort(403);
        }
        
        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store OTP with 10 minute expiry
        $creditNote->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);
        
        // Send OTP via email or SMS (implement your notification logic)
        // For now, we'll just return it (in production, send via email/SMS)
        $adminEmail = auth()->user()->email;
        
        try {
            \Mail::raw("Your OTP for credit note {$creditNote->credit_note_number} approval is: {$otp}. Valid for 10 minutes.", function($message) use ($adminEmail) {
                $message->to($adminEmail)
                        ->subject('Credit Note Approval OTP');
            });
            
            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your email address.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'message' => 'OTP generated. For testing: ' . $otp,
                'otp' => $otp, // Remove this in production
            ]);
        }
    }

    /**
     * Verify OTP and approve/reject credit note
     */
    public function verifyCreditNoteOtp(Request $request, \App\Models\CreditNote $creditNote)
    {
        $business = auth()->user()->business;
        
        if ($creditNote->order->business_id !== $business->id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'otp' => 'required|string|size:6',
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|string|max:500',
        ]);
        
        // Verify OTP
        if (!$creditNote->isOtpValid($validated['otp'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
            ], 422);
        }
        
        if ($validated['action'] === 'approve') {
            $creditNote->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'otp_code' => null,
                'otp_expires_at' => null,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Credit note approved successfully.',
            ]);
        } else {
            $creditNote->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => $validated['rejection_reason'],
                'otp_code' => null,
                'otp_expires_at' => null,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Credit note rejected.',
            ]);
        }
    }

    /**
     * List all credit notes
     */
    public function listCreditNotes()
    {
        $business = auth()->user()->business;
        
        $creditNotes = \App\Models\CreditNote::whereHas('order', function($query) use ($business) {
            $query->where('business_id', $business->id);
        })
        ->with(['order', 'requestedBy', 'approvedBy'])
        ->latest()
        ->paginate(15);
        
        return view('sales.credit-notes-list', compact('creditNotes'));
    }

    /**
     * View credit note details
     */
    public function viewCreditNote(\App\Models\CreditNote $creditNote)
    {
        $business = auth()->user()->business;
        
        if ($creditNote->order->business_id !== $business->id) {
            abort(403);
        }
        
        return view('sales.credit-note-view', compact('creditNote', 'business'));
    }

    /**
     * Archive a single order
     */
    public function archiveOrder(Order $order)
    {
        $business = auth()->user()->business;
        
        if ($order->business_id !== $business->id) {
            abort(403);
        }
        
        // Only archive paid invoices
        if ($order->payment_status !== 'paid' || !$order->invoice_number) {
            return response()->json([
                'success' => false,
                'message' => 'Only paid invoices can be archived.',
            ], 422);
        }
        
        $order->update([
            'is_archived' => true,
            'archived_at' => now(),
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Order archived successfully.',
        ]);
    }

    /**
     * Bulk archive paid orders
     */
    public function bulkArchiveOrders(Request $request)
    {
        $business = auth()->user()->business;
        
        $validated = $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
        ]);
        
        $orders = Order::whereIn('id', $validated['order_ids'])
            ->where('business_id', $business->id)
            ->where('payment_status', 'paid')
            ->whereNotNull('invoice_number')
            ->get();
        
        $archived = 0;
        foreach ($orders as $order) {
            $order->update([
                'is_archived' => true,
                'archived_at' => now(),
            ]);
            $archived++;
        }
        
        return response()->json([
            'success' => true,
            'message' => "{$archived} order(s) archived successfully.",
            'archived_count' => $archived,
        ]);
    }

    /**
     * View archived orders
     */
    public function archivedOrders()
    {
        $business = auth()->user()->business;
        
        $orders = Order::where('business_id', $business->id)
            ->where('is_archived', true)
            ->with(['customer', 'items.product'])
            ->latest('archived_at')
            ->paginate(15);
        
        return view('sales.archived-orders', compact('orders'));
    }

    /**
     * Record payment for an invoice
     */
    public function recordPayment(Request $request, Order $order)
    {
        $business = auth()->user()->business;
        
        if ($order->business_id !== $business->id) {
            abort(403);
        }
        
        // Validate that order has an invoice
        if (!$order->invoice_number) {
            return response()->json([
                'success' => false,
                'message' => 'This order does not have an invoice.',
            ], 422);
        }
        
        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'This invoice has already been paid.',
            ], 422);
        }
        
        $validated = $request->validate([
            'payment_method' => 'required|string|in:cash,mpesa,bank_transfer,card,cheque',
            'amount_paid' => 'required|numeric|min:0.01',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $amountPaid = (float) $validated['amount_paid'];
        $currentBalance = (float) $order->balance_due ?: (float) $order->total_amount;
        
        // Validate amount doesn't exceed balance
        if ($amountPaid > $currentBalance) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount cannot exceed the balance due of ' . number_format($currentBalance, 2),
            ], 422);
        }
        
        DB::beginTransaction();
        
        try {
            // Create payment record
            $payment = Payment::create([
                'business_id' => $business->id,
                'order_id' => $order->id,
                'payment_number' => Payment::generatePaymentNumber(),
                'amount' => $amountPaid,
                'payment_method' => $validated['payment_method'],
                'transaction_reference' => $validated['transaction_reference'],
                'payment_date' => now(),
                'notes' => $validated['notes'],
                'received_by' => auth()->id(),
            ]);
            
            // Update order amounts
            $totalAmountPaid = (float) $order->amount_paid + $amountPaid;
            $newBalance = (float) $order->total_amount - $totalAmountPaid;
            
            // Determine payment status
            $paymentStatus = 'unpaid';
            if ($newBalance <= 0) {
                $paymentStatus = 'paid';
                $newBalance = 0;
            } elseif ($totalAmountPaid > 0) {
                $paymentStatus = 'partial';
            }
            
            $order->update([
                'amount_paid' => $totalAmountPaid,
                'balance_due' => $newBalance,
                'payment_status' => $paymentStatus,
            ]);
            
            // Handle customer debt record
            if ($order->customer_id) {
                $debt = CustomerDebt::where('order_id', $order->id)->first();
                
                if ($paymentStatus === 'paid') {
                    // Mark debt as paid
                    if ($debt) {
                        $debt->update([
                            'amount_paid' => $order->total_amount,
                            'balance' => 0,
                            'status' => 'paid',
                        ]);
                    }
                } else {
                    // Create or update debt record
                    if ($debt) {
                        $debt->update([
                            'amount_paid' => $totalAmountPaid,
                            'balance' => $newBalance,
                        ]);
                        $debt->updateStatus();
                    } else {
                        $debt = CustomerDebt::create([
                            'business_id' => $business->id,
                            'customer_id' => $order->customer_id,
                            'order_id' => $order->id,
                            'total_amount' => $order->total_amount,
                            'amount_paid' => $totalAmountPaid,
                            'balance' => $newBalance,
                            'due_date' => now()->addDays(30), // Default 30 days
                            'status' => 'pending',
                        ]);
                        $debt->updateStatus();
                    }
                }
            }
            
            DB::commit();
            
            $message = $paymentStatus === 'paid' 
                ? 'Payment recorded successfully. Invoice marked as paid.'
                : 'Partial payment of ' . number_format($amountPaid, 2) . ' recorded. Balance due: ' . number_format($newBalance, 2);
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'payment_number' => $payment->payment_number,
                    'amount_paid' => $amountPaid,
                    'total_paid' => $totalAmountPaid,
                    'balance_due' => $newBalance,
                    'payment_status' => $paymentStatus,
                ],
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function customerDebts(Request $request)
    {
        $business = auth()->user()->business;
        
        $query = CustomerDebt::with(['customer', 'order'])
            ->where('business_id', $business->id);
        
        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Search by customer name
        if ($request->has('search') && $request->search) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }
        
        $debts = $query->orderBy('due_date', 'asc')->paginate(20);
        
        // Calculate summary statistics
        $summary = [
            'total_debts' => CustomerDebt::where('business_id', $business->id)->sum('total_amount'),
            'total_paid' => CustomerDebt::where('business_id', $business->id)->sum('amount_paid'),
            'total_balance' => CustomerDebt::where('business_id', $business->id)->sum('balance'),
            'overdue_count' => CustomerDebt::where('business_id', $business->id)->where('status', 'overdue')->count(),
            'pending_count' => CustomerDebt::where('business_id', $business->id)->where('status', 'pending')->count(),
        ];
        
        return view('sales.customer-debts', compact('debts', 'summary'));
    }
    
    public function supplierDebts(Request $request)
    {
        $business = auth()->user()->business;
        
        $query = DB::table('supplier_debts')
            ->where('business_id', $business->id);
        
        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Search by supplier name or reference
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('supplier_name', 'like', '%' . $request->search . '%')
                  ->orWhere('reference_number', 'like', '%' . $request->search . '%');
            });
        }
        
        $debts = $query->orderBy('due_date', 'asc')->paginate(20);
        
        // Calculate summary statistics
        $summary = [
            'total_debts' => DB::table('supplier_debts')->where('business_id', $business->id)->sum('total_amount'),
            'total_paid' => DB::table('supplier_debts')->where('business_id', $business->id)->sum('amount_paid'),
            'total_balance' => DB::table('supplier_debts')->where('business_id', $business->id)->sum('balance'),
            'overdue_count' => DB::table('supplier_debts')->where('business_id', $business->id)->where('status', 'overdue')->count(),
            'pending_count' => DB::table('supplier_debts')->where('business_id', $business->id)->where('status', 'pending')->count(),
        ];
        
        return view('sales.supplier-debts', compact('debts', 'summary'));
    }
    
    public function storeSupplierDebt(Request $request)
    {
        $business = auth()->user()->business;
        
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_phone' => 'nullable|string|max:20',
            'supplier_email' => 'nullable|email|max:255',
            'reference_number' => 'required|string|max:100|unique:supplier_debts,reference_number',
            'total_amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date|after_or_equal:today',
            'description' => 'nullable|string|max:1000',
        ]);
        
        DB::table('supplier_debts')->insert([
            'business_id' => $business->id,
            'supplier_name' => $validated['supplier_name'],
            'supplier_phone' => $validated['supplier_phone'],
            'supplier_email' => $validated['supplier_email'],
            'reference_number' => $validated['reference_number'],
            'total_amount' => $validated['total_amount'],
            'amount_paid' => 0,
            'balance' => $validated['total_amount'],
            'due_date' => $validated['due_date'],
            'status' => 'pending',
            'description' => $validated['description'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return redirect()->route('sales.supplier-debts')->with('success', 'Supplier debt recorded successfully.');
    }
    
    public function recordSupplierPayment(Request $request, $debtId)
    {
        $business = auth()->user()->business;
        
        $debt = DB::table('supplier_debts')
            ->where('id', $debtId)
            ->where('business_id', $business->id)
            ->first();
        
        if (!$debt) {
            return response()->json([
                'success' => false,
                'message' => 'Debt not found.',
            ], 404);
        }
        
        if ($debt->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'This debt has already been paid.',
            ], 422);
        }
        
        $validated = $request->validate([
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:cash,mpesa,bank_transfer,card,cheque',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $amountPaid = (float) $validated['amount_paid'];
        $currentBalance = (float) $debt->balance;
        
        if ($amountPaid > $currentBalance) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount cannot exceed the balance due of ' . number_format($currentBalance, 2),
            ], 422);
        }
        
        $totalAmountPaid = (float) $debt->amount_paid + $amountPaid;
        $newBalance = (float) $debt->total_amount - $totalAmountPaid;
        
        $paymentStatus = 'pending';
        if ($newBalance <= 0) {
            $paymentStatus = 'paid';
            $newBalance = 0;
        } elseif ($totalAmountPaid > 0) {
            $paymentStatus = 'partial';
        }
        
        // Check if overdue
        if ($paymentStatus !== 'paid' && now()->gt($debt->due_date)) {
            $paymentStatus = 'overdue';
        }
        
        DB::table('supplier_debts')
            ->where('id', $debtId)
            ->update([
                'amount_paid' => $totalAmountPaid,
                'balance' => $newBalance,
                'status' => $paymentStatus,
                'updated_at' => now(),
            ]);
        
        $message = $paymentStatus === 'paid' 
            ? 'Payment recorded successfully. Debt marked as paid.'
            : 'Partial payment of ' . number_format($amountPaid, 2) . ' recorded. Balance due: ' . number_format($newBalance, 2);
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'amount_paid' => $amountPaid,
                'total_paid' => $totalAmountPaid,
                'balance_due' => $newBalance,
                'payment_status' => $paymentStatus,
            ],
        ]);
    }
}


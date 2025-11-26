<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $order->order_number }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
            background: white;
            color: #333;
        }
        
        .receipt-container {
            max-width: 350px;
            margin: 0 auto;
            border: 2px solid #2c3e50;
            border-radius: 8px;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #3498db;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .business-logo {
            max-width: 80px;
            max-height: 80px;
            margin: 0 auto 10px;
            display: block;
        }
        
        .business-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .business-tagline {
            font-size: 11px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        
        .business-contact-details {
            margin: 10px 0;
            padding: 8px 0;
            border-top: 1px solid #ecf0f1;
            border-bottom: 1px solid #ecf0f1;
        }
        
        .contact-item {
            font-size: 10px;
            color: #34495e;
            margin-bottom: 3px;
            text-align: center;
        }
        
        .contact-item:last-child {
            margin-bottom: 0;
        }
        
        .receipt-title {
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0 10px;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .receipt-info {
            background: #ecf0f1;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .info-label {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .info-value {
            color: #34495e;
        }
        

        
        .customer-info {
            background: #e8f4fd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #3498db;
        }
        
        .items-section {
            margin: 15px 0;
        }
        
        .item {
            border-bottom: 1px solid #ecf0f1;
            padding: 8px 0;
        }
        
        .item:last-child {
            border-bottom: none;
        }
        
        .item-name {
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 3px;
        }
        
        .item-details {
            font-size: 10px;
            color: #7f8c8d;
            margin-bottom: 3px;
        }
        
        .item-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
        }
        
        .totals-section {
            border-top: 2px solid #3498db;
            padding-top: 15px;
            margin-top: 15px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 12px;
        }
        
        .total-row.final {
            font-weight: bold;
            font-size: 16px;
            border-top: 1px solid #bdc3c7;
            padding-top: 10px;
            margin-top: 10px;
            color: #2c3e50;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ecf0f1;
            color: #7f8c8d;
            font-size: 10px;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .print-btn:hover {
            background: #2980b9;
        }
        
        .conversion-details {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 3px;
            padding: 5px;
            margin-top: 5px;
            font-size: 9px;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        
        .print-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .print-btn:hover {
            background: #0056b3;
        }
        
        .action-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }
        
        .action-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-download {
            background: #28a745;
            color: white;
        }
        
        .btn-download:hover {
            background: #218838;
        }
        
        .btn-print {
            background: #007bff;
            color: white;
        }
        
        .btn-print:hover {
            background: #0056b3;
        }
        
        .btn-share {
            background: #17a2b8;
            color: white;
        }
        
        .btn-share:hover {
            background: #138496;
        }
    </style>
</head>
<body>
    <!-- Action Buttons (hidden when printing) -->
    <div class="action-buttons no-print">
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i> Print
        </button>
        <button onclick="downloadPDF()" class="btn-download">
            <i class="fas fa-download"></i> Download
        </button>
        <button onclick="shareReceipt()" class="btn-share">
            <i class="fas fa-share-alt"></i> Share
        </button>
    </div>
    
    @if(isset($receipt))
    {{-- Receipt from stored data --}}
    @php
        $receiptData = $receipt->receipt_data;
        $cartItems = $receiptData['cart_items'] ?? [];
        $isEligibleForConversions = $receiptData['is_eligible_for_conversions'] ?? false;
        
        // If cart items are empty, try to use order items as fallback
        if (empty($cartItems) && isset($receiptData['items'])) {
            $cartItems = $receiptData['items'];
        }
        
        // Get business details
        $business = auth()->user()->business;
        
        // Debug: Log the cart items structure
        \Log::info('Receipt cart items:', [
            'receipt_number' => $receipt->receipt_number,
            'cart_items_count' => count($cartItems),
            'cart_items' => $cartItems,
            'receipt_data_keys' => array_keys($receiptData),
            'has_cart_items' => isset($receiptData['cart_items']),
            'cart_items_type' => gettype($receiptData['cart_items'] ?? 'not_set'),
            'customer_name' => $receiptData['customer_name'] ?? 'not_set',
            'customer_phone' => $receiptData['customer_phone'] ?? 'not_set',
            'customer_email' => $receiptData['customer_email'] ?? 'not_set',
            'customer_address' => $receiptData['customer_address'] ?? 'not_set'
        ]);
    @endphp
    
    <div class="receipt-container">
        <div class="header">
            @if($userBusiness && $userBusiness->logo)
                <img src="{{ asset('storage/' . $userBusiness->logo) }}" alt="{{ $userBusiness->name }}" class="business-logo">
            @endif
            <div class="business-name">{{ $receiptData['business_name'] ?? $userBusiness->name ?? 'Business Name' }}</div>
            <div class="business-tagline">Quality Products & Services</div>
            
            {{-- Business Contact Details in Header --}}
            <div class="business-contact-details">
                @if($userBusiness && $userBusiness->phone)
                    <div class="contact-item">{{ $userBusiness->phone }}</div>
                @endif
                @if($userBusiness && $userBusiness->email)
                    <div class="contact-item">{{ $userBusiness->email }}</div>
                @endif
                @if($userBusiness && $userBusiness->address)
                    <div class="contact-item">{{ $userBusiness->address }}</div>
                @endif
            </div>
            
            <div class="receipt-title">Sales Receipt</div>
        </div>
        
        <div class="receipt-info">
            <div class="info-row">
                <span class="info-label">Receipt #:</span>
                <span class="info-value">{{ $receipt->receipt_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Order #:</span>
                <span class="info-value">{{ $receiptData['order_number'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($receiptData['order_date'])->format('M d, Y H:i') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment:</span>
                <span class="info-value">{{ ucfirst(str_replace('_', ' ', $receiptData['payment_method'])) }}</span>
            </div>
        </div>
        
        @if($receiptData['customer_name'] && $receiptData['customer_name'] !== 'Walk-in Customer')
            <div class="customer-info">
                <div class="info-row">
                    <span class="info-label">Customer:</span>
                    <span class="info-value">{{ $receiptData['customer_name'] }}</span>
                </div>
                @if($receiptData['customer_phone'])
                    <div class="info-row">
                        <span class="info-label">Phone:</span>
                        <span class="info-value">{{ $receiptData['customer_phone'] }}</span>
                    </div>
                @endif
                @if($receiptData['customer_email'] ?? null)
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $receiptData['customer_email'] }}</span>
                    </div>
                @endif
                @if($receiptData['customer_address'] ?? null)
                    <div class="info-row">
                        <span class="info-label">Address:</span>
                        <span class="info-value">{{ $receiptData['customer_address'] }}</span>
                    </div>
                @endif
            </div>
        @endif
            
        <div class="items-section">
            @foreach($cartItems as $item)
                @if(isset($item['name']) && isset($item['quantity']) && isset($item['price']))
                                         @if($isEligibleForConversions && isset($item['conversion']))
                         {{-- Converted item display --}}
                         @php
                             $conversion = $item['conversion'];
                             $sellUnit = $conversion['sell_unit'] ?? 'unit';
                             $materialType = $conversion['material_type'] ?? 'material';
                             $originalQuantity = $conversion['original_quantity'] ?? $item['quantity'];
                             $pricePerUnit = $conversion['price_per_unit'] ?? $item['price'];
                             $conversionDetails = "({$originalQuantity} {$sellUnit} - " . str_replace('_', ' ', $materialType) . ")";
                         @endphp
                         <div class="item">
                             <div class="item-name">{{ $item['name'] }}</div>
                             <div class="item-details">{{ $conversionDetails }}</div>
                             <div class="item-details">Qty: {{ $item['quantity'] }} | Price: {{ $receiptData['currency_symbol'] }}{{ number_format($pricePerUnit, 2) }}/{{ $sellUnit }}</div>
                             <div class="item-total">
                                 <span>Total:</span>
                                 <span>{{ $receiptData['currency_symbol'] }}{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                             </div>
                         </div>
                    @else
                        {{-- Regular item display --}}
                        <div class="item">
                            <div class="item-name">{{ $item['name'] }}</div>
                            <div class="item-details">Quantity: {{ $item['quantity'] }} | Unit Price: {{ $receiptData['currency_symbol'] }}{{ number_format($item['price'], 2) }}</div>
                            <div class="item-total">
                                <span>Total:</span>
                                <span>{{ $receiptData['currency_symbol'] }}{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            </div>
                        </div>
                    @endif
                @else
                    {{-- Fallback for incomplete item data - try to get product name from database --}}
                    @php
                        $productName = 'Unknown Product';
                        if (isset($item['id'])) {
                            $product = \App\Models\Product::find($item['id']);
                            if ($product) {
                                $productName = $product->name;
                            }
                        }
                    @endphp
                    <div class="item">
                        <div class="item-name">{{ $productName }}</div>
                        <div class="item-details">Quantity: {{ $item['quantity'] ?? 1 }} | Unit Price: {{ $receiptData['currency_symbol'] }}{{ number_format($item['price'] ?? 0, 2) }}</div>
                        <div class="item-total">
                            <span>Total:</span>
                            <span>{{ $receiptData['currency_symbol'] }}{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}</span>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
            

        
        <div class="totals-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>{{ $receiptData['currency_symbol'] }}{{ number_format($receiptData['subtotal'], 2) }}</span>
            </div>
            
            @if($isEligibleForConversions)
                <div class="total-row">
                    <span>Tax:</span>
                    <span>{{ $receiptData['currency_symbol'] }}{{ number_format($receiptData['tax_amount'], 2) }}</span>
                </div>
            @else
                <div class="total-row">
                    <span>Tax (16%):</span>
                    <span>{{ $receiptData['currency_symbol'] }}{{ number_format($receiptData['tax_amount'], 2) }}</span>
                </div>
            @endif
            
            <div class="total-row final">
                <span>Total:</span>
                <span>{{ $receiptData['currency_symbol'] }}{{ number_format($receiptData['total_amount'], 2) }}</span>
            </div>
            
            @if(isset($order->amount_paid) && $order->payment_status !== 'paid')
                <div class="total-row" style="color: #27ae60; font-weight: 600;">
                    <span>Amount Paid:</span>
                    <span>{{ $receiptData['currency_symbol'] }}{{ number_format($order->amount_paid ?? 0, 2) }}</span>
                </div>
                <div class="total-row" style="color: #e74c3c; font-weight: 600;">
                    <span>Balance Due:</span>
                    <span>{{ $receiptData['currency_symbol'] }}{{ number_format($order->balance_due ?? 0, 2) }}</span>
                </div>
            @endif
        </div>
        
        @if(isset($order->payment_status) && $order->payment_status !== 'paid')
            <div class="conversion-details" style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 10px; margin: 15px 0; border-radius: 4px;">
                <strong style="color: #856404;">Payment Status:</strong> 
                <span style="text-transform: uppercase; font-weight: 600; color: #856404;">
                    {{ $order->payment_status === 'partial' ? 'PARTIAL PAYMENT' : 'UNPAID' }}
                </span>
                @if($order->invoice_number)
                    <br><small style="color: #856404;">Invoice #{{ $order->invoice_number }} generated for balance payment</small>
                @endif
            </div>
        @endif
        
        @if($receiptData['notes'])
            <div class="conversion-details">
                <strong>Notes:</strong> {{ $receiptData['notes'] }}
            </div>
        @endif
        
        <div class="footer">
            <p>Thank you for your business!</p>
            <p>For any queries, please contact us</p>
            <p>Generated on {{ now()->format('M d, Y H:i:s') }}</p>
        </div>
    </div>
    @else
        {{-- Receipt from order data (for backward compatibility) --}}
        <div class="receipt-container">
            <div class="header">
                @if($userBusiness && $userBusiness->logo)
                    <img src="{{ asset('storage/' . $userBusiness->logo) }}" alt="{{ $userBusiness->name }}" class="business-logo">
                @endif
                <div class="business-name">{{ $userBusiness->name }}</div>
                <div class="business-tagline">Quality Products & Services</div>
                
                {{-- Business Contact Details in Header --}}
                <div class="business-contact-details">
                    @if($userBusiness && $userBusiness->phone)
                        <div class="contact-item">{{ $userBusiness->phone }}</div>
                    @endif
                    @if($userBusiness && $userBusiness->email)
                        <div class="contact-item">{{ $userBusiness->email }}</div>
                    @endif
                    @if($userBusiness && $userBusiness->address)
                        <div class="contact-item">{{ $userBusiness->address }}</div>
                    @endif
                </div>
                
                <div class="receipt-title">Sales Receipt</div>
            </div>
            
            <div class="receipt-info">
                <div class="info-row">
                    <span class="info-label">Order #:</span>
                    <span class="info-value">{{ $order->order_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span class="info-value">{{ $order->created_at->format('M d, Y H:i') }}</span>
                </div>
            </div>
            
            {{-- Customer Information --}}
            @if($order->customer && $order->customer->name !== 'Walk-in Customer')
                <div class="customer-info">
                    <div class="info-row">
                        <span class="info-label">Customer:</span>
                        <span class="info-value">{{ $order->customer->name }}</span>
                    </div>
                    @if($order->customer->phone)
                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ $order->customer->phone }}</span>
                        </div>
                    @endif
                    @if($order->customer->email)
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ $order->customer->email }}</span>
                        </div>
                    @endif
                    @if($order->customer->address)
                        <div class="info-row">
                            <span class="info-label">Address:</span>
                            <span class="info-value">{{ $order->customer->address }}</span>
                        </div>
                    @endif
                </div>
            @endif
            
            <div class="items-section">
                @foreach($order->items as $item)
                    <div class="item">
                        <div class="item-name">{{ $item->product->name }}</div>
                        <div class="item-details">Quantity: {{ $item->quantity }} | Unit Price: KSh {{ number_format($item->price, 2) }}</div>
                        <div class="item-total">
                            <span>Total:</span>
                            <span>KSh {{ number_format($item->total, 2) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="totals-section">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span>KSh {{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="total-row">
                    <span>Tax:</span>
                    <span>KSh {{ number_format($order->tax, 2) }}</span>
                </div>
                <div class="total-row final">
                    <span>Total:</span>
                    <span>KSh {{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
            
            <div class="footer">
                <p>Thank you for your business!</p>
                <p>For any queries, please contact us</p>
                <p>Generated on {{ now()->format('M d, Y H:i:s') }}</p>
            </div>
        </div>
    @endif

    <script>
        function downloadPDF() {
            window.print();
        }
        
        function shareReceipt() {
            const receiptUrl = window.location.href;
            const receiptNumber = '{{ $order->order_number ?? "Receipt" }}';
            
            if (navigator.share) {
                navigator.share({
                    title: `Receipt ${receiptNumber}`,
                    text: `View receipt ${receiptNumber}`,
                    url: receiptUrl
                }).catch(error => console.log('Error sharing:', error));
            } else {
                // Fallback: Copy link to clipboard
                navigator.clipboard.writeText(receiptUrl).then(() => {
                    alert('Receipt link copied to clipboard!');
                }).catch(() => {
                    alert('Receipt URL: ' + receiptUrl);
                });
            }
        }
    </script>

</body>
</html>






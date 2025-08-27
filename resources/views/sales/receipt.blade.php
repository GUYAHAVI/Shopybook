<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $order->order_number }}</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
            background: white;
            color: black;
        }
        
        .receipt {
            max-width: 300px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 10px;
        }
        
        .header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .business-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .business-details {
            font-size: 10px;
            margin-bottom: 5px;
        }
        
        .receipt-title {
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .order-info {
            margin-bottom: 10px;
        }
        
        .order-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .items {
            margin: 10px 0;
        }
        
        .item {
            margin-bottom: 5px;
        }
        
        .item-name {
            font-weight: bold;
        }
        
        .item-details {
            display: flex;
            justify-content: space-between;
            margin-left: 10px;
        }
        
        .totals {
            border-top: 1px solid #000;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .total-row.final {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
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
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Print Receipt
    </button>
    
    <div class="receipt">
        <div class="header">
            <div class="business-name">{{ $business->name }}</div>
            <div class="business-details">
                @if($business->address)
                    {{ $business->address }}<br>
                @endif
                @if($business->city)
                    {{ $business->city }}, {{ $business->country ?? 'Kenya' }}<br>
                @endif
                @if($business->phone)
                    Tel: {{ $business->phone }}<br>
                @endif
                @if($business->email)
                    Email: {{ $business->email }}<br>
                @endif
                @if($business->kra_pin)
                    KRA PIN: {{ $business->kra_pin }}
                @endif
            </div>
        </div>
        
        <div class="receipt-title">SALES RECEIPT</div>
        
        <div class="order-info">
            <div class="order-row">
                <span>Receipt No:</span>
                <span>{{ $order->order_number }}</span>
            </div>
            <div class="order-row">
                <span>Date:</span>
                <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="order-row">
                <span>Customer:</span>
                <span>{{ $order->customer ? $order->customer->name : 'Walk-in Customer' }}</span>
            </div>
            <div class="order-row">
                <span>Payment:</span>
                <span>{{ ucfirst($order->payment_method) }}</span>
            </div>
        </div>
        
        <div class="items">
            @if($order->order_type === 'public_order')
                <div class="item">
                    <div class="item-name">{{ $order->product ? $order->product->name : 'Product' }}</div>
                    <div class="item-details">
                        <span>{{ $order->quantity }} x KSh {{ number_format($order->unit_price, 2) }}</span>
                        <span>KSh {{ number_format($order->total_price, 2) }}</span>
                    </div>
                </div>
            @else
                @foreach($order->items as $item)
                    <div class="item">
                        <div class="item-name">{{ $item->product->name }}</div>
                        <div class="item-details">
                            <span>{{ $item->quantity }} x KSh {{ number_format($item->price, 2) }}</span>
                            <span>KSh {{ number_format($item->total, 2) }}</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        
        <div class="totals">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>KSh {{ number_format($order->subtotal ?? $order->total_amount, 2) }}</span>
            </div>
            @if(isset($order->tax) && $order->tax > 0)
                <div class="total-row">
                    <span>Tax (16%):</span>
                    <span>KSh {{ number_format($order->tax, 2) }}</span>
                </div>
            @endif
            <div class="total-row final">
                <span>TOTAL:</span>
                <span>KSh {{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
        
        <div class="footer">
            <div>Thank you for your purchase!</div>
            <div>Please keep this receipt for your records</div>
            <div>For any queries, contact us</div>
        </div>
    </div>
</body>
</html>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #13e8e9;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #020258;
            margin: 0;
            font-size: 24px;
        }
        .business-name {
            color: #13e8e9;
            font-size: 18px;
            margin-top: 5px;
        }
        .order-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .order-info h3 {
            color: #020258;
            margin-top: 0;
            border-bottom: 1px solid #13e8e9;
            padding-bottom: 10px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #020258;
        }
        .value {
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background: #ffeaa7;
            color: #e17055;
        }
        .status-processing {
            background: #74b9ff;
            color: #0984e3;
        }
        .status-completed {
            background: #00b894;
            color: white;
        }
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .products-table th,
        .products-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .products-table th {
            background-color: #13e8e9;
            color: #020258;
            font-weight: bold;
        }
        .total-amount {
            background: #020258;
            color: white;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            margin: 20px 0;
        }
        .total-amount h3 {
            margin: 0;
            font-size: 20px;
        }
        .customer-details {
            background: #e8f8f5;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .cta-button {
            display: inline-block;
            background: #13e8e9;
            color: #020258;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🛒 New Order Received!</h1>
            <div class="business-name">{{ $business->business_name }}</div>
        </div>

        <div class="order-info">
            <h3>📋 Order Details</h3>
            <div class="info-row">
                <span class="label">Order ID:</span>
                <span class="value">#{{ $order->id }}</span>
            </div>
            @if($order->order_number)
            <div class="info-row">
                <span class="label">Order Number:</span>
                <span class="value">{{ $order->order_number }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="label">Date & Time:</span>
                <span class="value">{{ $order->created_at->format('M d, Y - g:i A') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Status:</span>
                <span class="value">
                    <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                </span>
            </div>
            @if($order->payment_method)
            <div class="info-row">
                <span class="label">Payment Method:</span>
                <span class="value">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
            </div>
            @endif
            @if($order->payment_status)
            <div class="info-row">
                <span class="label">Payment Status:</span>
                <span class="value">{{ ucfirst($order->payment_status) }}</span>
            </div>
            @endif
        </div>

        @if($order->order_type === 'public_order')
            <!-- Public Order Customer Details -->
            <div class="customer-details">
                <h3>👤 Customer Information</h3>
                <div class="info-row">
                    <span class="label">Name:</span>
                    <span class="value">{{ $order->customer_name }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Phone:</span>
                    <span class="value">{{ $order->customer_phone }}</span>
                </div>
                @if($order->customer_email)
                <div class="info-row">
                    <span class="label">Email:</span>
                    <span class="value">{{ $order->customer_email }}</span>
                </div>
                @endif
                @if($order->delivery_address)
                <div class="info-row">
                    <span class="label">Delivery Address:</span>
                    <span class="value">{{ $order->delivery_address }}</span>
                </div>
                @endif
            </div>

            <!-- Single Product for Public Orders -->
            @if($product)
            <h3>📦 Ordered Product</h3>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>KSh {{ number_format($order->unit_price, 2) }}</td>
                        <td>KSh {{ number_format($order->total_price, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="total-amount">
                <h3>Total Amount: KSh {{ number_format($order->total_price, 2) }}</h3>
            </div>
            @endif
        @else
            <!-- Regular Order Customer Details -->
            @if($customer)
            <div class="customer-details">
                <h3>👤 Customer Information</h3>
                <div class="info-row">
                    <span class="label">Name:</span>
                    <span class="value">{{ $customer->name }}</span>
                </div>
                @if($customer->phone)
                <div class="info-row">
                    <span class="label">Phone:</span>
                    <span class="value">{{ $customer->phone }}</span>
                </div>
                @endif
                @if($customer->email)
                <div class="info-row">
                    <span class="label">Email:</span>
                    <span class="value">{{ $customer->email }}</span>
                </div>
                @endif
            </div>
            @endif

            <!-- Multiple Products for Regular Orders -->
            @if($orderItems->count() > 0)
            <h3>📦 Ordered Items</h3>
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orderItems as $item)
                    <tr>
                        <td>{{ $item->product->name ?? 'Product' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>KSh {{ number_format($item->price, 2) }}</td>
                        <td>KSh {{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total-amount">
                <h3>Total Amount: KSh {{ number_format($order->total_amount, 2) }}</h3>
            </div>
            @endif
        @endif

        @if($order->notes)
        <div class="order-info">
            <h3>📝 Order Notes</h3>
            <p>{{ $order->notes }}</p>
        </div>
        @endif

        <div style="text-align: center;">
            <a href="{{ url('/sales/orders/' . $order->id . '/details') }}" class="cta-button">
                View Order Details
            </a>
        </div>

        <div class="footer">
            <p>This is an automated notification from {{ config('app.name') }}.</p>
            <p>Please log in to your dashboard to manage this order.</p>
        </div>
    </div>
</body>
</html>


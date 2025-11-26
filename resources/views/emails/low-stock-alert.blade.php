<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Alert</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: {{ $isOutOfStock ? '#dc3545' : '#ffc107' }};
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .alert-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
        }
        .alert-box {
            background: {{ $isOutOfStock ? '#f8d7da' : '#fff3cd' }};
            border-left: 4px solid {{ $isOutOfStock ? '#dc3545' : '#ffc107' }};
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-box h2 {
            margin: 0 0 10px 0;
            color: {{ $isOutOfStock ? '#721c24' : '#856404' }};
            font-size: 18px;
        }
        .product-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #495057;
        }
        .detail-value {
            color: #212529;
        }
        .stock-info {
            text-align: center;
            padding: 20px;
            background: {{ $isOutOfStock ? '#dc3545' : '#ffc107' }};
            color: #ffffff;
            border-radius: 5px;
            margin: 20px 0;
        }
        .stock-info .stock-number {
            font-size: 36px;
            font-weight: bold;
            margin: 10px 0;
        }
        .action-button {
            display: inline-block;
            padding: 12px 30px;
            background: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .recommendations {
            background: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .recommendations h3 {
            margin: 0 0 10px 0;
            color: #004085;
            font-size: 16px;
        }
        .recommendations ul {
            margin: 0;
            padding-left: 20px;
        }
        .recommendations li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="alert-icon">⚠️</div>
            <h1>{{ $isOutOfStock ? 'OUT OF STOCK ALERT' : 'LOW STOCK ALERT' }}</h1>
            <p style="margin: 5px 0 0 0;">{{ $businessName }}</p>
        </div>

        <div class="content">
            <div class="alert-box">
                <h2>{{ $isOutOfStock ? 'Product is completely out of stock!' : 'Product stock is running low!' }}</h2>
                <p>Immediate action required to avoid sales disruption and lost revenue.</p>
            </div>

            <div class="product-details">
                <h3 style="margin-top: 0;">Product Information</h3>
                <div class="detail-row">
                    <span class="detail-label">Product Name:</span>
                    <span class="detail-value">{{ $productName }}</span>
                </div>
                @if($sku)
                <div class="detail-row">
                    <span class="detail-label">SKU:</span>
                    <span class="detail-value">{{ $sku }}</span>
                </div>
                @endif
                @if($category)
                <div class="detail-row">
                    <span class="detail-label">Category:</span>
                    <span class="detail-value">{{ $category }}</span>
                </div>
                @endif
                @if($brand)
                <div class="detail-row">
                    <span class="detail-label">Brand:</span>
                    <span class="detail-value">{{ $brand }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="detail-label">Low Stock Threshold:</span>
                    <span class="detail-value">{{ $lowStockThreshold }} units</span>
                </div>
            </div>

            <div class="stock-info">
                <div style="font-size: 14px; margin-bottom: 5px;">CURRENT STOCK LEVEL</div>
                <div class="stock-number">{{ $currentStock }}</div>
                <div style="font-size: 14px;">{{ $currentStock == 1 ? 'unit' : 'units' }} remaining</div>
            </div>

            <div class="recommendations">
                <h3>📋 Recommended Actions:</h3>
                <ul>
                    <li><strong>Reorder immediately</strong> to maintain stock levels</li>
                    <li>Contact your supplier to expedite delivery</li>
                    <li>Review recent sales trends to adjust order quantities</li>
                    @if($isOutOfStock)
                    <li><strong>Update customers</strong> about product availability</li>
                    <li>Consider promoting alternative products</li>
                    @endif
                </ul>
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/products/receive') }}" class="action-button">
                    🚚 Receive Stock Now
                </a>
            </div>

            <p style="margin-top: 30px; font-size: 14px; color: #6c757d;">
                This is an automated alert from your Shopybook inventory management system. 
                You're receiving this because stock levels have fallen below your defined threshold.
            </p>
        </div>

        <div class="footer">
            <p style="margin: 0;">
                © {{ date('Y') }} Shopybook. All rights reserved.<br>
                <a href="{{ url('/products/inventory') }}" style="color: #007bff;">View Inventory</a> | 
                <a href="{{ url('/products') }}" style="color: #007bff;">Manage Products</a>
            </p>
        </div>
    </div>
</body>
</html>








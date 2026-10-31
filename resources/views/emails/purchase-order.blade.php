<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order {{ $po->po_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 640px;
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
            border-bottom: 2px solid #4F46E5;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .header h1 { color: #4F46E5; margin: 0; font-size: 22px; }
        .header p { color: #666; margin: 5px 0 0; }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table.items th, table.items td {
            border: 1px solid #e5e7eb;
            padding: 10px 12px;
            text-align: left;
            font-size: 14px;
        }
        table.items th { background: #f3f4f6; }
        .totals { text-align: right; margin-top: 10px; font-size: 16px; }
        .meta { margin: 15px 0; }
        .meta td { padding: 4px 10px 4px 0; font-size: 14px; }
        .notes {
            background: #f9fafb;
            border-left: 3px solid #4F46E5;
            padding: 12px 15px;
            margin: 20px 0;
            font-size: 14px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            color: #666;
            font-size: 13px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ $business->business_name }}</h1>
            <p>Purchase Order</p>
        </div>

        <p>
            Dear {{ $supplier->contact_person ?? $supplier->name }},
        </p>
        <p>
            Please find our purchase order below. Kindly confirm receipt and advise on availability
            and delivery timelines.
        </p>

        <table class="meta">
            <tr><td><strong>PO Number:</strong></td><td>{{ $po->po_number }}</td></tr>
            <tr><td><strong>Order Date:</strong></td><td>{{ $po->order_date->format('d M Y') }}</td></tr>
            @if($po->expected_date)
                <tr><td><strong>Expected Delivery:</strong></td><td>{{ $po->expected_date->format('d M Y') }}</td></tr>
            @endif
        </table>

        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Unit Cost</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($po->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ rtrim(rtrim(number_format($item->quantity_ordered, 3), '0'), '.') }}</td>
                        <td>KSh {{ number_format($item->unit_cost, 2) }}</td>
                        <td>KSh {{ number_format($item->total_cost, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <strong>Total: KSh {{ number_format($po->total_amount, 2) }}</strong>
        </div>

        @if($po->notes)
            <div class="notes">
                <strong>Notes:</strong> {{ $po->notes }}
            </div>
        @endif

        <div class="footer">
            <strong>{{ $business->business_name }}</strong><br>
            @if($business->email){{ $business->email }}@endif
            @if($business->phone) · {{ $business->phone }}@endif
            <br><br>
            <small>Sent via Shopybook</small>
        </div>
    </div>
</body>
</html>

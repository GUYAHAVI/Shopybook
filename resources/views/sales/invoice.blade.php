<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $order->invoice_number }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 0;
            }
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        .action-buttons {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        
        .action-buttons button {
            margin: 0 5px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
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
        .invoice-header {
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-info {
            text-align: left;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        .invoice-title {
            text-align: right;
            float: right;
        }
        .invoice-title h1 {
            font-size: 28px;
            color: #007bff;
            margin: 0;
        }
        .invoice-title .status {
            background: #ffc107;
            color: #fff;
            padding: 5px 15px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }
        .invoice-title .status.paid {
            background: #28a745;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        .invoice-details {
            margin-bottom: 30px;
        }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-details td {
            padding: 8px 10px;
            vertical-align: top;
        }
        .invoice-details .label {
            font-weight: bold;
            color: #555;
            width: 150px;
        }
        .customer-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .customer-info h3 {
            margin-top: 0;
            color: #007bff;
            font-size: 16px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table thead {
            background: #007bff;
            color: #fff;
        }
        .items-table th {
            padding: 12px 10px;
            text-align: left;
            font-weight: bold;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .items-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-section {
            float: right;
            width: 300px;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        .totals-table .label {
            text-align: left;
            font-weight: bold;
        }
        .totals-table .amount {
            text-align: right;
        }
        .totals-table .total-row {
            background: #007bff;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
        }
        .totals-table .total-row td {
            padding: 12px 10px;
            border: none;
        }
        .payment-info {
            margin-top: 50px;
            padding: 15px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 3px;
        }
        .payment-info h3 {
            margin-top: 0;
            color: #856404;
            font-size: 16px;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #777;
        }
        .notes {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .notes h3 {
            margin-top: 0;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
    <!-- Action Buttons (hidden when printing) -->
    <div class="action-buttons no-print">
        <button onclick="window.print()" class="btn-print">
            <i class="fas fa-print"></i> Print Invoice
        </button>
        <button onclick="downloadPDF()" class="btn-download">
            <i class="fas fa-download"></i> Download PDF
        </button>
        <button onclick="shareInvoice()" class="btn-share">
            <i class="fas fa-share-alt"></i> Share
        </button>
    </div>
    
    <div class="invoice-container">
    <div class="invoice-header clearfix">
        <div class="company-info">
            <div class="company-name">{{ $business->name }}</div>
            <div>{{ $business->address ?? 'Business Address' }}</div>
            <div>Phone: {{ $business->phone ?? 'N/A' }}</div>
            <div>Email: {{ $business->email ?? 'N/A' }}</div>
            @if($business->kra_pin)
            <div>KRA PIN: {{ $business->kra_pin }}</div>
            @endif
        </div>
        <div class="invoice-title">
            <h1>INVOICE</h1>
            <div class="status {{ $order->payment_status === 'paid' ? 'paid' : '' }}">
                {{ strtoupper($order->payment_status ?? 'UNPAID') }}
            </div>
        </div>
    </div>

    <div class="invoice-details">
        <table>
            <tr>
                <td class="label">Invoice Number:</td>
                <td>{{ $order->invoice_number }}</td>
                <td class="label">Order Number:</td>
                <td>{{ $order->order_number }}</td>
            </tr>
            <tr>
                <td class="label">Invoice Date:</td>
                <td>{{ $order->invoice_generated_at ? \Carbon\Carbon::parse($order->invoice_generated_at)->format('d M, Y') : now()->format('d M, Y') }}</td>
                <td class="label">Order Date:</td>
                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M, Y') }}</td>
            </tr>
            @if($order->payment_status !== 'paid')
            <tr>
                <td class="label">Due Date:</td>
                <td colspan="3">{{ now()->addDays(30)->format('d M, Y') }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="customer-info">
        <h3>Bill To:</h3>
        @if($order->order_type === 'public_order')
            <strong>{{ $order->customer_name }}</strong><br>
            Phone: {{ $order->customer_phone }}<br>
            @if($order->customer_email)
            Email: {{ $order->customer_email }}<br>
            @endif
            @if($order->delivery_address)
            Address: {{ $order->delivery_address }}
            @endif
        @else
            <strong>{{ $order->customer ? $order->customer->name : 'Walk-in Customer' }}</strong><br>
            @if($order->customer)
                @if($order->customer->phone)
                Phone: {{ $order->customer->phone }}<br>
                @endif
                @if($order->customer->email)
                Email: {{ $order->customer->email }}<br>
                @endif
                @if($order->customer->address)
                Address: {{ $order->customer->address }}
                @endif
            @endif
        @endif
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Item Description</th>
                <th class="text-center">Quantity</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @if($order->order_type === 'public_order')
                <tr>
                    <td>1</td>
                    <td>
                        <strong>{{ $order->product ? $order->product->name : 'Product' }}</strong>
                    </td>
                    <td class="text-center">{{ $order->quantity }}</td>
                    <td class="text-right">KSh {{ number_format($order->unit_price, 2) }}</td>
                    <td class="text-right">KSh {{ number_format($order->total_price, 2) }}</td>
                </tr>
            @else
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product->name }}</strong>
                        @if($item->sell_unit)
                            <br><small>Unit: {{ ucfirst($item->sell_unit) }}</small>
                        @endif
                        @if($item->material_type)
                            <br><small>Type: {{ str_replace('_', ' ', ucfirst($item->material_type)) }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">KSh {{ number_format($item->price, 2) }}</td>
                    <td class="text-right">KSh {{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="clearfix">
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="amount">KSh {{ number_format($order->subtotal ?? $order->total_amount, 2) }}</td>
                </tr>
                @if($order->tax && $order->tax > 0)
                <tr>
                    <td class="label">Tax ({{ $order->tax_rate ?? 16 }}%):</td>
                    <td class="amount">KSh {{ number_format($order->tax, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="label">TOTAL:</td>
                    <td class="amount">KSh {{ number_format($order->total_amount, 2) }}</td>
                </tr>
                @if(isset($order->amount_paid) && $order->amount_paid > 0)
                <tr style="color: #27ae60;">
                    <td class="label">Amount Paid:</td>
                    <td class="amount">KSh {{ number_format($order->amount_paid, 2) }}</td>
                </tr>
                @endif
                @if(isset($order->balance_due) && $order->balance_due > 0)
                <tr style="color: #e74c3c; font-weight: bold; font-size: 1.1em;">
                    <td class="label">BALANCE DUE:</td>
                    <td class="amount">KSh {{ number_format($order->balance_due, 2) }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>
    
    @if(isset($order->payment_status) && in_array($order->payment_status, ['partial', 'unpaid']))
    <div class="notes" style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin-top: 20px;">
        <h3 style="color: #856404; margin-top: 0;">Payment Status</h3>
        <p style="color: #856404; margin: 0;">
            <strong>Status:</strong> <span style="text-transform: uppercase;">{{ $order->payment_status === 'partial' ? 'PARTIAL PAYMENT RECEIVED' : 'PAYMENT PENDING' }}</span>
        </p>
        @if($order->balance_due > 0)
        <p style="color: #856404; margin: 10px 0 0 0;">
            <strong>Please remit the balance of KSh {{ number_format($order->balance_due, 2) }} at your earliest convenience.</strong>
        </p>
        @endif
    </div>
    @endif

    @if($order->notes)
    <div class="notes">
        <h3>Notes:</h3>
        <p>{{ $order->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>This is a computer-generated invoice and does not require a signature.</p>
        <p>Generated by {{ $business->name }} - {{ now()->format('d M, Y H:i:s') }}</p>
    </div>
    </div>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script>
        function downloadPDF() {
            // Use browser's print to PDF functionality
            window.print();
        }
        
        function shareInvoice() {
            const invoiceUrl = window.location.href;
            const invoiceNumber = '{{ $order->invoice_number }}';
            const totalAmount = 'KSh {{ number_format($order->total_amount, 2) }}';
            
            // Check if Web Share API is available
            if (navigator.share) {
                navigator.share({
                    title: `Invoice ${invoiceNumber}`,
                    text: `Invoice ${invoiceNumber} for ${totalAmount} from {{ $business->name }}`,
                    url: invoiceUrl
                })
                .then(() => console.log('Invoice shared successfully'))
                .catch((error) => console.log('Error sharing:', error));
            } else {
                // Fallback: Copy link to clipboard
                navigator.clipboard.writeText(invoiceUrl)
                .then(() => {
                    alert('Invoice link copied to clipboard! You can now share it.');
                })
                .catch(() => {
                    // Final fallback: Show the URL
                    prompt('Share this invoice link:', invoiceUrl);
                });
            }
        }
        
        // Email invoice function
        function emailInvoice() {
            const subject = encodeURIComponent('Invoice {{ $order->invoice_number }}');
            const body = encodeURIComponent(`Please find attached invoice {{ $order->invoice_number }} for KSh {{ number_format($order->total_amount, 2) }}.\n\nView invoice: ${window.location.href}`);
            const mailtoLink = `mailto:?subject=${subject}&body=${body}`;
            window.location.href = mailtoLink;
        }
    </script>
</body>
</html>

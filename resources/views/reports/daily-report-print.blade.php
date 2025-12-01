<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Report - {{ $report_date->format('Y-m-d') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .business-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 18px;
            color: #666;
        }
        .report-date {
            font-size: 16px;
            margin: 10px 0;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .summary-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
        }
        .summary-card.sales {
            border-left: 4px solid #28a745;
        }
        .summary-card.expenses {
            border-left: 4px solid #dc3545;
        }
        .summary-card h3 {
            margin: 0 0 15px 0;
            font-size: 16px;
        }
        .metric {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .metric:last-child {
            margin-bottom: 0;
        }
        .metric-label {
            font-weight: bold;
        }
        .metric-value {
            color: #666;
        }
        .daily-summary {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        .daily-summary h3 {
            margin: 0 0 20px 0;
            color: #333;
        }
        .summary-metrics {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        .summary-metric {
            text-align: center;
        }
        .summary-metric .value {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .summary-metric .label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-info { color: #17a2b8; }
        .details-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .details-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
        }
        .details-card h4 {
            margin: 0 0 15px 0;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
        }
        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 13px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="business-name">{{ $business->name }}</div>
        <div class="report-title">Daily Sales & Expense Report</div>
        <div class="report-date">{{ $report_date->format('l, F j, Y') }}</div>
    </div>

    <div class="summary-grid">
        <!-- Sales Summary -->
        <div class="summary-card sales">
            <h3>📈 Sales Summary</h3>
            <div class="metric">
                <span class="metric-label">Services Sold:</span>
                <span class="metric-value">{{ $sales['count'] }}</span>
            </div>
            <div class="metric">
                <span class="metric-label">Total Revenue:</span>
                <span class="metric-value">KSh {{ number_format($sales['total_amount'], 2) }}</span>
            </div>
            <div class="metric">
                <span class="metric-label">Staff Commission:</span>
                <span class="metric-value">KSh {{ number_format($sales['commission_total'], 2) }}</span>
            </div>
        </div>

        <!-- Expenses Summary -->
        <div class="summary-card expenses">
            <h3>📋 Expenses Summary</h3>
            <div class="metric">
                <span class="metric-label">Total Expenses:</span>
                <span class="metric-value">{{ $expenses['count'] }}</span>
            </div>
            <div class="metric">
                <span class="metric-label">Total Cost:</span>
                <span class="metric-value">KSh {{ number_format($expenses['total_amount'], 2) }}</span>
            </div>
            <div class="metric">
                <span class="metric-label">Net Amount:</span>
                <span class="metric-value {{ $net_amount >= 0 ? 'text-success' : 'text-danger' }}">
                    KSh {{ number_format($net_amount, 2) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Daily Summary -->
    <div class="daily-summary">
        <h3>💰 Daily Summary</h3>
        <div class="summary-metrics">
            <div class="summary-metric">
                <div class="value text-success">KSh {{ number_format($sales['total_amount'], 2) }}</div>
                <div class="label">Total Sales</div>
            </div>
            <div class="summary-metric">
                <div class="value text-danger">KSh {{ number_format($expenses['total_amount'], 2) }}</div>
                <div class="label">Total Expenses</div>
            </div>
            <div class="summary-metric">
                <div class="value {{ $net_amount >= 0 ? 'text-success' : 'text-danger' }}">
                    KSh {{ number_format(abs($net_amount), 2) }}
                </div>
                <div class="label">{{ $net_amount >= 0 ? 'Net Profit' : 'Net Loss' }}</div>
            </div>
            <div class="summary-metric">
                <div class="value text-info">KSh {{ number_format($sales['commission_total'], 2) }}</div>
                <div class="label">Staff Commission</div>
            </div>
        </div>
    </div>

    <!-- Detailed Breakdown -->
    <div class="details-section">
        <div class="details-grid">
            <!-- Payment Methods -->
            <div class="details-card">
                <h4>💳 Payment Methods</h4>
                @forelse($sales['payment_methods'] as $method)
                    <div class="detail-item">
                        <span>{{ $method['method'] }}:</span>
                        <span>KSh {{ number_format($method['amount'], 2) }}</span>
                    </div>
                @empty
                    <div class="detail-item">
                        <span>No payments recorded</span>
                        <span>-</span>
                    </div>
                @endforelse
            </div>

            <!-- Top Services -->
            <div class="details-card">
                <h4>🏆 Top Services</h4>
                @forelse($sales['top_services'] as $service)
                    <div class="detail-item">
                        <span>{{ $service['name'] }} ({{ $service['count'] }}x):</span>
                        <span>KSh {{ number_format($service['total'], 2) }}</span>
                    </div>
                @empty
                    <div class="detail-item">
                        <span>No services recorded</span>
                        <span>-</span>
                    </div>
                @endforelse
            </div>

            <!-- Expense Categories -->
            <div class="details-card">
                <h4>📊 Expense Categories</h4>
                @forelse($expenses['by_type'] as $type)
                    <div class="detail-item">
                        <span>{{ $type['type'] }}:</span>
                        <span>KSh {{ number_format($type['amount'], 2) }}</span>
                    </div>
                @empty
                    <div class="detail-item">
                        <span>No expenses recorded</span>
                        <span>-</span>
                    </div>
                @endforelse
            </div>

            <!-- Additional Info -->
            <div class="details-card">
                <h4>ℹ️ Report Information</h4>
                <div class="detail-item">
                    <span>Report Date:</span>
                    <span>{{ $report_date->format('Y-m-d') }}</span>
                </div>
                <div class="detail-item">
                    <span>Generated:</span>
                    <span>{{ now()->format('Y-m-d H:i:s') }}</span>
                </div>
                <div class="detail-item">
                    <span>Business:</span>
                    <span>{{ $business->name }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This report was generated automatically by {{ $business->name }} on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p class="no-print">
            <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
                🖨️ Print Report
            </button>
            <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
                ❌ Close
            </button>
        </p>
    </div>
</body>
</html>



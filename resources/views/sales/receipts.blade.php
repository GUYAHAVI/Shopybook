@extends('layouts.dash')

@section('content')
<div class="container-fluid">
    <!-- Sub-navigation for Sales -->
    <div class="sub-navigation mb-4">
        <div class="nav-tabs">
            <a href="{{ route('sales.customers') }}" class="nav-tab">
                <i class="fas fa-users me-1"></i> Customers
            </a>
            <a href="{{ route('sales.orders') }}" class="nav-tab">
                <i class="fas fa-shopping-cart me-1"></i> Orders
            </a>
            <a href="{{ route('sales.pos') }}" class="nav-tab">
                <i class="fas fa-cash-register me-1"></i> POS
            </a>
            <a href="{{ route('sales.search-receipts') }}" class="nav-tab active">
                <i class="fas fa-receipt me-1"></i> Receipts
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                    <h5 class="mb-0" style="color: var(--text-primary);">
                        <i class="fas fa-receipt me-2"></i>Receipt Management
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Search Form -->
                    <form action="{{ route('sales.search-receipts') }}" method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" 
                                           name="query" 
                                           value="{{ $query ?? '' }}"
                                           class="form-control" 
                                           placeholder="Search by receipt number, customer name, phone, or order number..."
                                           style="border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-primary);">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search me-1"></i>Search
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('sales.search-receipts') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-list me-1"></i>View All Receipts
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Receipts List -->
                    @if(isset($receipts) && $receipts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead style="background: var(--bg-tertiary);">
                                    <tr>
                                        <th style="color: var(--text-primary);">Receipt #</th>
                                        <th style="color: var(--text-primary);">Order #</th>
                                        <th style="color: var(--text-primary);">Customer</th>
                                        <th style="color: var(--text-primary);">Date</th>
                                        <th style="color: var(--text-primary);">Total</th>
                                        <th style="color: var(--text-primary);">Type</th>
                                        <th style="color: var(--text-primary);">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($receipts as $receipt)
                                        <tr>
                                            <td style="color: var(--text-primary);">
                                                <strong>{{ $receipt->receipt_number }}</strong>
                                            </td>
                                            <td style="color: var(--text-primary);">
                                                {{ $receipt->order->order_number }}
                                            </td>
                                            <td style="color: var(--text-primary);">
                                                <div>{{ $receipt->customer_name }}</div>
                                                @if($receipt->customer_phone)
                                                    <small class="text-muted">{{ $receipt->customer_phone }}</small>
                                                @endif
                                            </td>
                                            <td style="color: var(--text-primary);">
                                                {{ $receipt->created_at->format('M d, Y H:i') }}
                                            </td>
                                            <td style="color: var(--text-primary);">
                                                <strong>{{ $receipt->formatted_total }}</strong>
                                            </td>
                                            <td>
                                                @if($receipt->is_converted_order)
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-exchange-alt me-1"></i>Converted
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-shopping-cart me-1"></i>Regular
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('sales.reprint-receipt', $receipt->receipt_number) }}" 
                                                       target="_blank"
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fas fa-print me-1"></i>Print
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-info"
                                                            onclick="viewReceiptDetails('{{ $receipt->receipt_number }}')">
                                                        <i class="fas fa-eye me-1"></i>View
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $receipts->appends(['query' => $query ?? ''])->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                            <h5 style="color: var(--text-primary);">No Receipts Found</h5>
                            <p style="color: var(--text-muted);">
                                @if(isset($query) && $query)
                                    No receipts found matching "{{ $query }}"
                                @else
                                    No receipts have been generated yet.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Details Modal -->
<div class="modal fade" id="receiptDetailsModal" tabindex="-1" aria-labelledby="receiptDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title" id="receiptDetailsModalLabel" style="color: var(--text-primary);">
                    <i class="fas fa-receipt me-2"></i>Receipt Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="receiptDetailsContent" style="color: var(--text-primary);">
                <!-- Receipt details will be loaded here -->
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="printReceiptBtn">
                    <i class="fas fa-print me-2"></i>Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function viewReceiptDetails(receiptNumber) {
    // Load receipt details via AJAX
    fetch(`/sales/receipts/${receiptNumber}/reprint`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('receiptDetailsContent').innerHTML = html;
            const modal = new bootstrap.Modal(document.getElementById('receiptDetailsModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error loading receipt details:', error);
            alert('Error loading receipt details');
        });
}

// Handle print button in modal
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('printReceiptBtn').addEventListener('click', function() {
        const printWindow = window.open('', '_blank');
        const content = document.getElementById('receiptDetailsContent').innerHTML;
        printWindow.document.write(`
            <html>
                <head>
                    <title>Receipt</title>
                    <style>
                        body { font-family: Arial, sans-serif; font-size: 12px; margin: 10px; }
                        .header { text-align: center; margin-bottom: 20px; }
                        .line { border-bottom: 1px dashed #000; margin: 10px 0; }
                        .total { font-weight: bold; font-size: 14px; }
                        .flex-between { display: flex; justify-content: space-between; margin: 5px 0; }
                    </style>
                </head>
                <body>
                    ${content}
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    });
});
</script>
@endsection

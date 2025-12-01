@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Receipt Details</h1>
            <p class="text-muted">Receipt #{{ $receipt->receipt_number }}</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fas fa-print me-2"></i>Print Receipt
            </button>
            <a href="{{ route('products.receive.history') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to History
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Receipt Card -->
            <div class="card shadow mb-4" id="receipt-card">
                <div class="card-header py-3 bg-primary text-white">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="m-0 font-weight-bold">
                                <i class="fas fa-receipt me-2"></i>Stock Receipt
                            </h5>
                        </div>
                        <div class="col-md-6 text-end">
                            <h6 class="m-0">{{ $receipt->receipt_number }}</h6>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Receipt Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Receipt Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="fw-bold" width="150">Receipt Date:</td>
                                    <td>{{ $receipt->receipt_date->format('F d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Receipt Type:</td>
                                    <td>
                                        @if($receipt->receipt_type === 'new_product')
                                            <span class="badge bg-success">New Product</span>
                                        @else
                                            <span class="badge bg-info">Existing Product</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Received By:</td>
                                    <td>{{ $receipt->receivedBy->name ?? 'Unknown' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Created At:</td>
                                    <td>{{ $receipt->created_at->format('F d, Y g:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Supplier Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="fw-bold" width="150">Supplier:</td>
                                    <td>{{ $receipt->supplier ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Invoice/PO #:</td>
                                    <td>{{ $receipt->invoice_number ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Product Details -->
                    <h6 class="text-primary mb-3">Product Details</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th class="text-center">Quantity Received</th>
                                    <th class="text-end">Unit Cost</th>
                                    <th class="text-end">Total Cost</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>{{ $receipt->product_name }}</strong>
                                        @if($receipt->product)
                                            <br>
                                            <small class="text-muted">
                                                @if($receipt->product->sku)
                                                    SKU: {{ $receipt->product->sku }}
                                                @endif
                                                @if($receipt->product->category)
                                                    | Category: {{ $receipt->product->category }}
                                                @endif
                                            </small>
                                            <br>
                                            <a href="{{ route('products.show', $receipt->product) }}" class="btn btn-sm btn-link p-0">
                                                View Product Details <i class="fas fa-arrow-right"></i>
                                            </a>
                                        @else
                                            <br>
                                            <span class="badge bg-danger">Product Deleted</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <strong class="h5">{{ number_format($receipt->quantity_received) }}</strong>
                                    </td>
                                    <td class="text-end">{{ $receipt->formatted_unit_cost }}</td>
                                    <td class="text-end">
                                        <strong>{{ $receipt->formatted_total_cost }}</strong>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total Receipt Value:</td>
                                    <td class="text-end fw-bold h5 text-success mb-0">
                                        {{ $receipt->formatted_total_cost }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Additional Information -->
                    @if($receipt->receipt_type === 'new_product' && $receipt->additional_data)
                        <h6 class="text-primary mb-3">Additional Product Information</h6>
                        <div class="alert alert-info">
                            <div class="row">
                                @if(isset($receipt->additional_data['sku']))
                                    <div class="col-md-6 mb-2">
                                        <strong>SKU:</strong> {{ $receipt->additional_data['sku'] }}
                                    </div>
                                @endif
                                @if(isset($receipt->additional_data['barcode']))
                                    <div class="col-md-6 mb-2">
                                        <strong>Barcode:</strong> {{ $receipt->additional_data['barcode'] }}
                                    </div>
                                @endif
                                @if(isset($receipt->additional_data['category']))
                                    <div class="col-md-6 mb-2">
                                        <strong>Category:</strong> {{ $receipt->additional_data['category'] }}
                                    </div>
                                @endif
                                @if(isset($receipt->additional_data['brand']))
                                    <div class="col-md-6 mb-2">
                                        <strong>Brand:</strong> {{ $receipt->additional_data['brand'] }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Notes -->
                    @if($receipt->notes)
                        <h6 class="text-primary mb-3">Notes</h6>
                        <div class="alert alert-secondary">
                            <i class="fas fa-sticky-note me-2"></i>{{ $receipt->notes }}
                        </div>
                    @endif
                </div>
                <div class="card-footer text-muted text-center">
                    <small>This is an official stock receipt record. Keep for your records.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .sidebar, .navbar, .card-footer {
        display: none !important;
    }
    
    #receipt-card {
        box-shadow: none !important;
        border: 1px solid #000 !important;
    }
}
</style>
@endsection




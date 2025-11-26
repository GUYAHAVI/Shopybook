@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Stock Receipt History</h1>
            <p class="text-muted">View all product receiving records</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.receive') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>New Receipt
            </a>
            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Products
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Units Received</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalReceived) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Value Received</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">KSh {{ number_format($totalValue, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Receipts (Last 30 Days)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $recentReceipts }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Receipts</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('products.receive.history') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Receipt #, Product, Supplier...">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="receipt_type" class="form-label">Receipt Type</label>
                        <select class="form-select" id="receipt_type" name="receipt_type">
                            <option value="all" {{ request('receipt_type') == 'all' ? 'selected' : '' }}>All Types</option>
                            <option value="existing_product" {{ request('receipt_type') == 'existing_product' ? 'selected' : '' }}>Existing Products</option>
                            <option value="new_product" {{ request('receipt_type') == 'new_product' ? 'selected' : '' }}>New Products</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-1 mb-3">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Receipts Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Receipt Records</h6>
            <span class="badge bg-primary">{{ $receipts->total() }} Total Receipts</span>
        </div>
        <div class="card-body">
            @if($receipts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Receipt #</th>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Type</th>
                                <th>Supplier</th>
                                <th>Quantity</th>
                                <th>Unit Cost</th>
                                <th>Total Cost</th>
                                <th>Received By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receipts as $receipt)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary">{{ $receipt->receipt_number }}</span>
                                    </td>
                                    <td>{{ $receipt->receipt_date->format('M d, Y') }}</td>
                                    <td>
                                        @if($receipt->product)
                                            <a href="{{ route('products.show', $receipt->product) }}" class="text-decoration-none">
                                                {{ $receipt->product_name }}
                                            </a>
                                        @else
                                            {{ $receipt->product_name }}
                                            <span class="badge bg-danger ms-1">Deleted</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($receipt->receipt_type === 'new_product')
                                            <span class="badge bg-success">New Product</span>
                                        @else
                                            <span class="badge bg-info">Existing Product</span>
                                        @endif
                                    </td>
                                    <td>{{ $receipt->supplier ?? 'N/A' }}</td>
                                    <td><strong>{{ number_format($receipt->quantity_received) }}</strong></td>
                                    <td>{{ $receipt->formatted_unit_cost }}</td>
                                    <td><strong>{{ $receipt->formatted_total_cost }}</strong></td>
                                    <td>{{ $receipt->receivedBy->name ?? 'Unknown' }}</td>
                                    <td>
                                        <a href="{{ route('products.receive.show', $receipt) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $receipts->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-600">No receipts found</h5>
                    <p class="text-muted">Start receiving products to see them here.</p>
                    <a href="{{ route('products.receive') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Record First Receipt
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection




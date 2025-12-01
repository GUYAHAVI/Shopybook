@extends('layouts.dash')

@section('content')
<div class="container-fluid py-4">
    <div class="card mb-4">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5>📦 Inventory Analysis Report</h5>
                    <p class="text-sm mb-0">Monitor stock levels, turnover rates, and identify slow/fast movers</p>
                </div>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>All Reports
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-gradient-primary text-white">
                        <div class="card-body">
                            <h6 class="text-white">Total Stock Value</h6>
                            <h3 class="text-white">KSh {{ number_format($totalStockValue, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-gradient-warning text-white">
                        <div class="card-body">
                            <h6 class="text-white">Low Stock Items</h6>
                            <h3 class="text-white">{{ $lowStockCount }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-gradient-danger text-white">
                        <div class="card-body">
                            <h6 class="text-white">Out of Stock</h6>
                            <h3 class="text-white">{{ $outOfStockCount }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-gradient-success text-white">
                        <div class="card-body">
                            <h6 class="text-white">Received (30 days)</h6>
                            <h3 class="text-white">{{ number_format($totalReceived) }} units</h3>
                        </div>
                    </div>
                </div>
            </div>

            <h6 class="mb-3">Items Needing Reorder</h6>
            <div class="table-responsive mb-4">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-end">Current Stock</th>
                            <th class="text-end">Threshold</th>
                            <th class="text-end">Stock Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reorderItems->take(20) as $product)
                            <tr>
                                <td><strong>{{ $product['name'] }}</strong></td>
                                <td>{{ $product['sku'] }}</td>
                                <td class="text-end">
                                    <span class="badge bg-{{ $product['current_stock'] == 0 ? 'danger' : 'warning' }}">
                                        {{ number_format($product['current_stock']) }}
                                    </span>
                                </td>
                                <td class="text-end">{{ number_format($product['low_threshold']) }}</td>
                                <td class="text-end">KSh {{ number_format($product['stock_value'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection








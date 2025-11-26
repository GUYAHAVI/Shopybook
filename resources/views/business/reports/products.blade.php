@extends('layouts.dash')

@section('content')
<div class="container-fluid py-4">
    <div class="card mb-4">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5>📦 Product Performance Report</h5>
                    <p class="text-sm mb-0">Analyze product sales, profitability, and stock turnover</p>
                </div>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>All Reports
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-5">
                    <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-5">
                    <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-gradient-primary text-white">
                        <div class="card-body">
                            <h6 class="text-white">Total Products</h6>
                            <h3 class="text-white">{{ $productSales->count() }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-gradient-success text-white">
                        <div class="card-body">
                            <h6 class="text-white">Total Revenue</h6>
                            <h3 class="text-white">KSh {{ number_format($productSales->sum('revenue'), 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-gradient-info text-white">
                        <div class="card-body">
                            <h6 class="text-white">Total Profit</h6>
                            <h3 class="text-white">KSh {{ number_format($productSales->sum('profit'), 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <h6 class="mb-3">Top 20 Products by Revenue</h6>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th class="text-end">Qty Sold</th>
                            <th class="text-end">Revenue</th>
                            <th class="text-end">Profit</th>
                            <th class="text-end">Margin %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $product)
                            <tr>
                                <td><strong>{{ $product['product_name'] }}</strong></td>
                                <td>{{ $product['sku'] }}</td>
                                <td>{{ $product['category'] }}</td>
                                <td class="text-end">{{ number_format($product['quantity_sold']) }}</td>
                                <td class="text-end">KSh {{ number_format($product['revenue'], 2) }}</td>
                                <td class="text-end">KSh {{ number_format($product['profit'], 2) }}</td>
                                <td class="text-end">{{ number_format($product['profit_margin'], 1) }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection







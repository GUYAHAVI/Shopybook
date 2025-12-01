@extends('layouts.dash')

@section('content')
<div class="container-fluid py-4">
    <div class="card mb-4">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5>💰 Profit & Loss Statement</h5>
                    <p class="text-sm mb-0">Comprehensive P&L with revenue, costs, and profitability analysis</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>All Reports
                    </a>
                    <button class="btn btn-sm btn-success" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                </div>
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

            <div class="card border">
                <div class="card-body">
                    <h6 class="mb-4">Income Statement</h6>
                    
                    <div class="mb-3">
                        <h6 class="text-primary">REVENUE</h6>
                        <div class="d-flex justify-content-between ps-3">
                            <span>Product Sales</span>
                            <strong>KSh {{ number_format($productRevenue, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between ps-3">
                            <span>Service Revenue</span>
                            <strong>KSh {{ number_format($serviceRevenue, 2) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total Revenue</strong>
                            <strong class="text-primary">KSh {{ number_format($totalRevenue, 2) }}</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-warning">COST OF GOODS SOLD</h6>
                        <div class="d-flex justify-content-between ps-3">
                            <span>Inventory Purchases</span>
                            <strong>KSh {{ number_format($cogs, 2) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Gross Profit</strong>
                            <strong class="text-success">KSh {{ number_format($grossProfit, 2) }} ({{ number_format($grossMargin, 1) }}%)</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="text-info">OPERATING EXPENSES</h6>
                        <div class="d-flex justify-content-between ps-3">
                            <span>Business Expenses</span>
                            <strong>KSh {{ number_format($operatingExpenses, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between ps-3">
                            <span>Salaries</span>
                            <strong>KSh {{ number_format($salaryExpenses, 2) }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total Operating Expenses</strong>
                            <strong class="text-danger">KSh {{ number_format($totalOperatingExpenses, 2) }}</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <hr class="border-dark">
                        <div class="d-flex justify-content-between">
                            <h5>NET PROFIT/LOSS</h5>
                            <h5 class="text-{{ $netProfit >= 0 ? 'success' : 'danger' }}">
                                KSh {{ number_format($netProfit, 2) }} ({{ number_format($netMargin, 1) }}%)
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection








@extends('layouts.dash')

@section('content')
<div class="container-fluid py-4">
    <div class="card mb-4">
        <div class="card-header pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5>👥 Customer Analytics Report</h5>
                    <p class="text-sm mb-0">Understand customer behavior and identify top customers</p>
                </div>
                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>All Reports
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>VIP Customers</h6>
                            <h3 class="text-primary">{{ $segments['vip'] }}</h3>
                            <small class="text-muted">&gt; KSh 50,000</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>Regular</h6>
                            <h3 class="text-success">{{ $segments['regular'] }}</h3>
                            <small class="text-muted">5+ orders</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>Occasional</h6>
                            <h3 class="text-info">{{ $segments['occasional'] }}</h3>
                            <small class="text-muted">2-4 orders</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>One-time</h6>
                            <h3 class="text-warning">{{ $segments['one_time'] }}</h3>
                            <small class="text-muted">1 order</small>
                        </div>
                    </div>
                </div>
            </div>

            <h6 class="mb-3">Top 20 Customers by Spending</h6>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th class="text-end">Orders</th>
                            <th class="text-end">Total Spent</th>
                            <th class="text-end">Avg Order</th>
                            <th>Last Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topCustomers as $customer)
                            <tr>
                                <td><strong>{{ $customer['name'] }}</strong></td>
                                <td>{{ $customer['phone'] ?? $customer['email'] ?? 'N/A' }}</td>
                                <td class="text-end">{{ $customer['total_orders'] }}</td>
                                <td class="text-end">KSh {{ number_format($customer['total_spent'], 2) }}</td>
                                <td class="text-end">KSh {{ number_format($customer['average_order_value'], 2) }}</td>
                                <td>{{ $customer['last_order_date'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection








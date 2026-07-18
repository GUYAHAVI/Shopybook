@extends('layouts.master')

@section('title', 'Subscriptions - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-credit-card text-warning me-2"></i>Subscriptions</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Dashboard</a>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-2">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">Free</div>
                <div class="fs-4 fw-bold">{{ $stats['free'] }}</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">Premium</div>
                <div class="fs-4 fw-bold text-primary">{{ $stats['premium'] }}</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">Enterprise</div>
                <div class="fs-4 fw-bold text-success">{{ $stats['enterprise'] }}</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">Active Trials</div>
                <div class="fs-4 fw-bold text-warning">{{ $stats['trial_active'] }}</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">Expired Trials</div>
                <div class="fs-4 fw-bold text-danger">{{ $stats['trial_expired'] }}</div>
            </div></div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="free" {{ request('status') === 'free' ? 'selected' : '' }}>Free</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="trial" {{ request('status') === 'trial' ? 'selected' : '' }}>Active Trial</option>
                        <option value="trial_expired" {{ request('status') === 'trial_expired' ? 'selected' : '' }}>Expired Trial</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Plan</label>
                    <select name="plan" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="free" {{ request('plan') === 'free' ? 'selected' : '' }}>Free</option>
                        <option value="premium" {{ request('plan') === 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="enterprise" {{ request('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-filter me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Businesses table --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Business</th>
                            <th>Owner</th>
                            <th>Plan</th>
                            <th>Trial Ends</th>
                            <th>Upgraded</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($businesses as $biz)
                        <tr>
                            <td>
                                <a href="{{ route('admin.businesses.show', $biz) }}" class="text-decoration-none fw-semibold">{{ $biz->name }}</a>
                            </td>
                            <td class="small">{{ $biz->user?->email }}</td>
                            <td><span class="badge bg-{{ $biz->plan === 'free' ? 'secondary' : ($biz->plan === 'premium' ? 'primary' : 'success') }} text-capitalize">{{ $biz->plan }}</span></td>
                            <td class="small">
                                @if($biz->trial_ends_at)
                                    {{ $biz->trial_ends_at->format('M j, Y') }}
                                    @if($biz->on_trial && $biz->trial_ends_at->isPast())
                                        <span class="badge bg-danger ms-1">Expired</span>
                                    @elseif($biz->on_trial)
                                        <span class="badge bg-warning ms-1">{{ $biz->trial_ends_at->diffInDays(now()) }}d left</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="small">{{ $biz->upgraded_at?->format('M j, Y') ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.businesses.show', $biz) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-cog"></i> Manage</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No businesses found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $businesses->links() }}</div>

    {{-- Recent payments --}}
    @if($recentPayments->isNotEmpty())
    <div class="card shadow-sm mt-4">
        <div class="card-header"><i class="fas fa-receipt me-2"></i>Recent Subscription Payments</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>Date</th><th>Business</th><th>User</th><th>Plan</th><th>Amount</th><th>Status</th><th>Receipt</th></tr>
                    </thead>
                    <tbody>
                        @foreach($recentPayments as $payment)
                        <tr>
                            <td class="small">{{ \Carbon\Carbon::parse($payment->created_at)->format('M j, Y') }}</td>
                            <td class="small">{{ $payment->business_name }}</td>
                            <td class="small">{{ $payment->user_email }}</td>
                            <td><span class="badge bg-primary text-capitalize">{{ $payment->plan }}</span></td>
                            <td>KSh {{ number_format($payment->amount, 2) }}</td>
                            <td><span class="badge bg-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">{{ $payment->status }}</span></td>
                            <td class="small">{{ $payment->mpesa_receipt_number ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

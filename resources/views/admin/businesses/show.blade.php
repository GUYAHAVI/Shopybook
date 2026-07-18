@extends('layouts.master')

@section('title', $business->name . ' - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0"><i class="fas fa-store text-primary me-2"></i>{{ $business->name }}</h2>
            <div class="text-muted small">{{ $business->slug }} &middot; {{ $business->business_type }}</div>
        </div>
        <a href="{{ route('admin.businesses.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </div>

    {{-- Business info cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-info-circle me-2"></i>Business Info</div>
                <div class="card-body small">
                    <div class="mb-2"><strong>Owner:</strong> {{ $business->user?->name }}</div>
                    <div class="mb-2"><strong>Email:</strong> {{ $business->user?->email }}</div>
                    <div class="mb-2"><strong>Phone:</strong> {{ $business->phone ?? 'N/A' }}</div>
                    <div class="mb-2"><strong>Type:</strong> {{ $business->business_type }}</div>
                    <div class="mb-2"><strong>City:</strong> {{ $business->city ?? 'N/A' }}</div>
                    <div class="mb-2"><strong>Created:</strong> {{ $business->created_at?->format('M j, Y H:i') }}</div>
                    <div class="mb-2"><strong>Last Active:</strong> {{ $lastActive?->created_at?->diffForHumans() ?? 'Never' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-credit-card me-2"></i>Subscription</div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Plan:</strong>
                        <span class="badge bg-{{ $business->plan === 'free' ? 'secondary' : ($business->plan === 'premium' ? 'primary' : 'success') }} text-capitalize">{{ $business->plan }}</span>
                    </div>
                    @if($business->on_trial)
                        <div class="mb-2">
                            <strong>Trial Status:</strong>
                            @if($business->trial_ends_at && $business->trial_ends_at->isFuture())
                                <span class="badge bg-warning">{{ $business->trial_ends_at->diffInDays(now()) }} days left</span>
                            @else
                                <span class="badge bg-danger">Expired</span>
                            @endif
                        </div>
                        <div class="mb-2 small text-muted">Trial ends: {{ $business->trial_ends_at?->format('M j, Y') }}</div>
                    @endif
                    <div class="mb-2 small"><strong>Upgraded:</strong> {{ $business->upgraded_at?->format('M j, Y') ?? 'Never' }}</div>

                    <hr>
                    <form method="POST" action="{{ route('admin.businesses.extend-subscription', $business) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small mb-1">Extend Subscription</label>
                            <select name="plan" class="form-select form-select-sm mb-2">
                                <option value="premium">Premium</option>
                                <option value="enterprise">Enterprise</option>
                                <option value="free">Reset to Free</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <input type="number" name="days" value="30" min="1" max="365" class="form-control form-control-sm mb-2" placeholder="Days to extend">
                        </div>
                        <div class="mb-2">
                            <input type="text" name="reason" class="form-control form-control-sm mb-2" placeholder="Reason (optional)">
                        </div>
                        <button type="submit" class="btn btn-sm btn-warning w-100"><i class="fas fa-plus me-1"></i>Extend</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-chart-bar me-2"></i>Stats</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2"><span>Products:</span><strong>{{ $business->products->count() }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Orders:</span><strong>{{ $business->orders->count() }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Customers:</span><strong>{{ $business->customers->count() }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Website:</span><strong>{{ $business->website ? 'Yes' : 'No' }}</strong></div>
                    @if($business->website)
                    <a href="{{ route('public.website', $business->slug) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2"><i class="fas fa-external-link-alt me-1"></i>View Website</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Page visits --}}
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-chart-line me-2"></i>Page Visits</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr><th>Page</th><th>Visits</th><th>Users</th><th>Avg Duration</th></tr>
                            </thead>
                            <tbody>
                                @forelse($pageVisits as $visit)
                                <tr>
                                    <td class="small">{{ $visit->page }}</td>
                                    <td>{{ $visit->visits }}</td>
                                    <td>{{ $visit->unique_users }}</td>
                                    <td class="small">{{ $visit->avg_duration ? round($visit->avg_duration) . 'ms' : '-' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">No visits recorded</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-list me-2"></i>Recent Activity</div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 350px; overflow-y: auto;">
                        @forelse($activityLogs as $log)
                            <div class="list-group-item small">
                                <div class="d-flex justify-content-between">
                                    <span><strong>{{ $log->user?->name ?? 'System' }}</strong> {{ $log->description }}</span>
                                    <span class="text-muted" style="font-size: 11px;">{{ $log->created_at?->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-muted text-center py-3">No activity</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Subscription payments --}}
    @if($subscriptionPayments->isNotEmpty())
    <div class="card shadow-sm mt-3">
        <div class="card-header"><i class="fas fa-receipt me-2"></i>Subscription Payments</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>Date</th><th>Plan</th><th>Amount</th><th>Status</th><th>Receipt</th></tr>
                    </thead>
                    <tbody>
                        @foreach($subscriptionPayments as $payment)
                        <tr>
                            <td class="small">{{ \Carbon\Carbon::parse($payment->created_at)->format('M j, Y') }}</td>
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

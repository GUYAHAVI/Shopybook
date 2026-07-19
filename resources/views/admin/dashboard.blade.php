@extends('layouts.master')

@section('title', 'Super Admin Dashboard - Shopybook')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="mb-0"><i class="fas fa-shield-alt text-danger me-2"></i>Super Admin Dashboard</h2>
        <span class="text-muted small">Logged in as {{ auth()->user()->name }}</span>
    </div>

    {{-- Overview cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100 text-decoration-none text-dark" style="cursor:pointer" onclick="window.location='{{ route('admin.users.index') }}'">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Total Users</div>
                            <div class="fs-3 fw-bold">{{ number_format($totalUsers) }}</div>
                            <div class="small text-success">+{{ $newUsersThisWeek }} this week</div>
                        </div>
                        <i class="fas fa-users fa-2x text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100 text-decoration-none text-dark" style="cursor:pointer" onclick="window.location='{{ route('admin.businesses.index') }}'">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Total Businesses</div>
                            <div class="fs-3 fw-bold">{{ number_format($totalBusinesses) }}</div>
                            <div class="small text-success">+{{ $newBusinessesThisWeek }} this week</div>
                        </div>
                        <i class="fas fa-store fa-2x text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Active Today</div>
                            <div class="fs-3 fw-bold">{{ number_format($activeToday) }}</div>
                            <div class="small text-muted">{{ $totalUsers > 0 ? round($activeToday / $totalUsers * 100) : 0 }}% of users</div>
                        </div>
                        <i class="fas fa-chart-line fa-2x text-info opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Online Now</div>
                            <div class="fs-3 fw-bold {{ $onlineCount > 0 ? 'text-success' : 'text-muted' }}">
                                @if($onlineCount > 0)
                                    <span class="badge bg-success rounded-pill">{{ $onlineCount }}</span>
                                @else
                                    0
                                @endif
                            </div>
                            <div class="small text-muted">Active sessions (30 min)</div>
                        </div>
                        <i class="fas fa-circle fa-2x {{ $onlineCount > 0 ? 'text-success' : 'text-muted' }} opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick links --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.businesses.index') }}" class="card shadow-sm h-100 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-store fa-2x text-primary mb-2"></i>
                    <div class="fw-semibold">Manage Businesses</div>
                    <div class="small text-muted">View & manage all businesses</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.users.index') }}" class="card shadow-sm h-100 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x text-success mb-2"></i>
                    <div class="fw-semibold">Manage Users</div>
                    <div class="small text-muted">View, delete, grant admin</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.subscriptions.index') }}" class="card shadow-sm h-100 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-credit-card fa-2x text-warning mb-2"></i>
                    <div class="fw-semibold">Subscriptions</div>
                    <div class="small text-muted">Extend trials & plans</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.usage.index') }}" class="card shadow-sm h-100 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-chart-bar fa-2x text-info mb-2"></i>
                    <div class="fw-semibold">Usage Analytics</div>
                    <div class="small text-muted">Page visits & engagement</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.ai-analysis.index') }}" class="card shadow-sm h-100 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-brain fa-2x text-danger mb-2"></i>
                    <div class="fw-semibold">AI Behavior Analysis</div>
                    <div class="small text-muted">Claude-powered insights</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.users.dormant') }}" class="card shadow-sm h-100 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-user-slash fa-2x text-secondary mb-2"></i>
                    <div class="fw-semibold">Dormant Users</div>
                    <div class="small text-muted">Find & clean inactive</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.website-builder.index') }}" class="card shadow-sm h-100 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-globe fa-2x text-primary mb-2"></i>
                    <div class="fw-semibold">Website Builder</div>
                    <div class="small text-muted">Help users build sites</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('admin.analytics.index') }}" class="card shadow-sm h-100 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-tachometer-alt fa-2x text-success mb-2"></i>
                    <div class="fw-semibold">Platform Analytics</div>
                    <div class="small text-muted">Detailed user analytics</div>
                </div>
            </a>
        </div>
    </div>

    {{-- Plan distribution --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-tags me-2"></i>Plan Distribution</div>
                <div class="card-body">
                    @php $total = max(array_sum($planDistribution), 1); @endphp
                    @foreach(['free' => 'secondary', 'premium' => 'primary', 'enterprise' => 'success'] as $planName => $color)
                        @if(isset($planDistribution[$planName]))
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-capitalize">{{ $planName }}</span>
                            <span class="badge bg-{{ $color }}">{{ $planDistribution[$planName] }}</span>
                        </div>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-{{ $color }}" style="width: {{ round($planDistribution[$planName] / $total * 100) }}%"></div>
                        </div>
                        @endif
                    @endforeach
                    @if(isset($planDistribution['free']) && $planDistribution['free'] > 0)
                    <div class="small text-muted">{{ round($planDistribution['free'] / $total * 100) }}% on free plan</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-globe me-2"></i>Website Stats</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Total Websites</span>
                        <span class="fs-4 fw-bold">{{ number_format($totalWebsites) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Active Businesses</span>
                        <span class="fs-4 fw-bold text-success">{{ number_format($activeBusinesses) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Inactive Businesses</span>
                        <span class="fs-4 fw-bold text-danger">{{ number_format($totalBusinesses - $activeBusinesses) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Login activity chart --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header"><i class="fas fa-sign-in-alt me-2"></i>User Login Activity (Last 30 Days)</div>
                <div class="card-body">
                    <canvas id="loginTrendChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Online users & Recent logins --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-circle text-success me-2" style="font-size: 10px;"></i>Online Now</span>
                    <span class="badge bg-success rounded-pill">{{ $onlineCount }} active</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 350px; overflow-y: auto;">
                        @forelse($onlineUsers as $session)
                            <div class="list-group-item small">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-circle text-success me-2" style="font-size: 8px;"></i>
                                        <strong>{{ $session->name }}</strong>
                                        <div class="text-muted" style="font-size: 11px;">{{ $session->email }} &middot; {{ $session->ip_address }}</div>
                                    </div>
                                    <span class="text-muted" style="font-size: 11px;">
                                        {{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-muted text-center py-4">
                                <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                                No users currently online
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-history me-2"></i>Recent Logins (7 days)</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 350px; overflow-y: auto;">
                        @forelse($recentLogins as $login)
                            <div class="list-group-item small">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $login->user_name }}</strong>
                                        <div class="text-muted" style="font-size: 11px;">
                                            {{ $login->user_email }} &middot; {{ $login->visit_count }} visits
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-muted" style="font-size: 11px;">Last seen {{ \Carbon\Carbon::parse($login->last_seen)->diffForHumans() }}</div>
                                        <div class="text-muted" style="font-size: 10px;">First {{ \Carbon\Carbon::parse($login->first_visit)->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-muted text-center py-4">
                                <i class="fas fa-user-slash fa-2x mb-2 d-block"></i>
                                No recent logins recorded
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent activity --}}
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-clock me-2"></i>Recent Activity</span>
                    <a href="{{ route('admin.analytics.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                        @forelse($recentActivity as $log)
                            <div class="list-group-item small">
                                <div class="d-flex justify-content-between">
                                    <span>
                                        <i class="fas fa-circle text-{{ $log->actionColor ?? 'secondary' }} me-1" style="font-size: 8px;"></i>
                                        <strong>{{ $log->user?->name ?? 'System' }}</strong>
                                        <span class="text-muted">{{ $log->description }}</span>
                                    </span>
                                    <span class="text-muted" style="font-size: 11px;">{{ $log->created_at?->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-muted text-center py-4">No recent activity</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-store me-2"></i>Recent Businesses</span>
                    <a href="{{ route('admin.businesses.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                        @forelse($recentBusinesses as $biz)
                            <div class="list-group-item small">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="{{ route('admin.businesses.show', $biz) }}" class="text-decoration-none fw-semibold">{{ $biz->name }}</a>
                                        <div class="text-muted" style="font-size: 11px;">
                                            {{ $biz->user?->email }} &middot; <span class="badge bg-secondary text-capitalize">{{ $biz->plan }}</span>
                                        </div>
                                    </div>
                                    <span class="text-muted" style="font-size: 11px;">{{ $biz->created_at?->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-muted text-center py-4">No businesses yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
@php
    $loginDates = $dailyLogins->pluck('date')->map(function($d) { return \Carbon\Carbon::parse($d)->format('M j'); });
    $loginUsers = $dailyLogins->pluck('unique_users');
    $loginVisits = $dailyLogins->pluck('total_visits');
@endphp
new Chart(document.getElementById('loginTrendChart'), {
    type: 'line',
    data: {
        labels: @json($loginDates),
        datasets: [
            {
                label: 'Unique Users',
                data: @json($loginUsers),
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                fill: true,
                tension: 0.3,
                pointRadius: 3,
                pointBackgroundColor: '#198754',
            },
            {
                label: 'Total Visits',
                data: @json($loginVisits),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.05)',
                fill: false,
                tension: 0.3,
                pointRadius: 2,
                borderDash: [5, 5],
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } },
            x: { ticks: { maxRotation: 45, autoSkip: true, maxTicksLimit: 15 } }
        },
        plugins: {
            legend: { position: 'top' },
        }
    }
});
</script>
@endsection

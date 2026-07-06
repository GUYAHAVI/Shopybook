@extends('layouts.app')

@section('title', 'Admin – Usage Analytics')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="mb-0"><i class="fas fa-chart-line text-primary me-2"></i>Usage Analytics</h2>
        <form method="GET" class="d-flex align-items-center gap-2">
            <label class="text-muted small mb-0">Period:</label>
            <select name="days" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                @foreach([7, 14, 30, 90] as $d)
                    <option value="{{ $d }}" {{ $days === $d ? 'selected' : '' }}>Last {{ $d }} days</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- ── Overview cards ── --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">Total Users</div>
                <div class="fs-3 fw-bold">{{ number_format($totalUsers) }}</div>
                <div class="small text-success">+{{ number_format($newUsers) }} new in period</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">Active Today</div>
                <div class="fs-3 fw-bold">{{ number_format($activeToday) }}</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">Active in Period</div>
                <div class="fs-3 fw-bold">{{ number_format($activeInPeriod) }}</div>
                <div class="small text-muted">{{ $totalUsers > 0 ? round($activeInPeriod / $totalUsers * 100) : 0 }}% of all users</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">At-Risk Users (7+ days inactive)</div>
                <div class="fs-3 fw-bold text-danger">{{ number_format($inactiveUsers->count()) }}</div>
            </div></div>
        </div>
    </div>

    {{-- ── DAU trend ── --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white"><strong>Daily Active Users &amp; Page Visits</strong></div>
        <div class="card-body"><canvas id="dauChart" height="80"></canvas></div>
    </div>

    <div class="row g-4 mb-4">
        {{-- ── Most visited pages ── --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white"><strong><i class="fas fa-fire text-warning me-1"></i>Most Visited Pages</strong></div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Page</th><th class="text-end">Visits</th><th class="text-end">Users</th><th class="text-end">Avg ms</th></tr></thead>
                        <tbody>
                        @forelse($topPages as $p)
                            <tr>
                                <td class="text-truncate" style="max-width:260px">{{ $p->page }}</td>
                                <td class="text-end">{{ number_format($p->visits) }}</td>
                                <td class="text-end">{{ number_format($p->unique_users) }}</td>
                                <td class="text-end">{{ number_format($p->avg_duration_ms) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted text-center py-3">No visit data yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── Drop-off / exit pages ── --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white"><strong><i class="fas fa-sign-out-alt text-danger me-1"></i>Top Exit Pages (where sessions end)</strong></div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Page</th><th class="text-end">Session Exits</th></tr></thead>
                        <tbody>
                        @forelse($exitPages as $p)
                            <tr>
                                <td class="text-truncate" style="max-width:320px">{{ $p->page }}</td>
                                <td class="text-end">{{ number_format($p->exits) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted text-center py-3">No session data yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- ── Slowest pages ── --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white"><strong><i class="fas fa-hourglass-half text-secondary me-1"></i>Slowest Pages (performance bottlenecks)</strong></div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Page</th><th class="text-end">Visits</th><th class="text-end">Avg ms</th><th class="text-end">Max ms</th></tr></thead>
                        <tbody>
                        @forelse($slowestPages as $p)
                            <tr class="{{ $p->avg_duration_ms > 2000 ? 'table-danger' : ($p->avg_duration_ms > 1000 ? 'table-warning' : '') }}">
                                <td class="text-truncate" style="max-width:240px">{{ $p->page }}</td>
                                <td class="text-end">{{ number_format($p->visits) }}</td>
                                <td class="text-end">{{ number_format($p->avg_duration_ms) }}</td>
                                <td class="text-end">{{ number_format($p->max_duration_ms) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted text-center py-3">Not enough data yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── Error pages ── --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white"><strong><i class="fas fa-exclamation-triangle text-danger me-1"></i>Pages Returning Errors</strong></div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Page</th><th class="text-end">Errors</th><th class="text-end">Status</th></tr></thead>
                        <tbody>
                        @forelse($errorPages as $p)
                            <tr>
                                <td class="text-truncate" style="max-width:280px">{{ $p->page }}</td>
                                <td class="text-end">{{ number_format($p->errors) }}</td>
                                <td class="text-end"><span class="badge bg-danger">{{ $p->sample_status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted text-center py-3">No errors recorded.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- ── Most active users ── --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white"><strong><i class="fas fa-user-check text-success me-1"></i>Most Active Users</strong></div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>User</th><th class="text-end">Visits</th><th class="text-end">Active Days</th><th>Last Seen</th></tr></thead>
                        <tbody>
                        @forelse($mostActiveUsers as $u)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.analytics.user', $u->id) }}">{{ $u->name }}</a>
                                    <small class="d-block text-muted">{{ $u->email }}</small>
                                </td>
                                <td class="text-end">{{ number_format($u->total_visits) }}</td>
                                <td class="text-end">{{ number_format($u->active_days) }}</td>
                                <td><small>{{ $u->last_seen ? \Carbon\Carbon::parse($u->last_seen)->diffForHumans() : 'never' }}</small></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted text-center py-3">No users yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── At-risk / churning users ── --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white"><strong><i class="fas fa-user-clock text-danger me-1"></i>At-Risk Users (inactive 7+ days)</strong></div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>User</th><th class="text-end">Lifetime Visits</th><th>Last Seen</th></tr></thead>
                        <tbody>
                        @forelse($inactiveUsers as $u)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.analytics.user', $u->id) }}">{{ $u->name }}</a>
                                    <small class="d-block text-muted">{{ $u->email }}</small>
                                </td>
                                <td class="text-end">{{ number_format($u->total_visits) }}</td>
                                <td><small class="text-danger">{{ $u->last_seen ? \Carbon\Carbon::parse($u->last_seen)->diffForHumans() : 'never active' }}</small></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted text-center py-3">No at-risk users.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- ── Module activity ── --}}
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white"><strong><i class="fas fa-cubes text-info me-1"></i>Feature Usage by Module</strong></div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light"><tr><th>Module</th><th>Actions</th></tr></thead>
                        <tbody>
                        @forelse($moduleActivity as $module => $actions)
                            <tr>
                                <td class="fw-semibold">{{ $module }}</td>
                                <td>
                                    @foreach($actions as $a)
                                        <span class="badge bg-secondary me-1">{{ $a->action }}: {{ $a->total }}</span>
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted text-center py-3">No activity logged in period.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ── Recent activity feed ── --}}
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white"><strong><i class="fas fa-stream text-primary me-1"></i>Recent User Actions</strong></div>
                <div class="table-responsive" style="max-height:420px;overflow-y:auto">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>User</th><th>Module</th><th>Action</th><th>Description</th><th>When</th></tr></thead>
                        <tbody>
                        @forelse($recentActivity as $log)
                            <tr>
                                <td>
                                    @if($log->user)
                                        <a href="{{ route('admin.analytics.user', $log->user_id) }}">{{ $log->user->name }}</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $log->module }}</td>
                                <td><span class="badge bg-{{ $log->action_color }}">{{ $log->action }}</span></td>
                                <td class="text-truncate" style="max-width:240px">{{ $log->description }}</td>
                                <td><small>{{ $log->created_at->diffForHumans() }}</small></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted text-center py-3">No activity logged yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const dauData = @json($dauTrend);
    new Chart(document.getElementById('dauChart'), {
        type: 'line',
        data: {
            labels: dauData.map(d => d.day),
            datasets: [
                {
                    label: 'Active Users',
                    data: dauData.map(d => d.users),
                    borderColor: '#4F46E5',
                    backgroundColor: 'rgba(79,70,229,0.1)',
                    fill: true,
                    tension: 0.3,
                    yAxisID: 'y',
                },
                {
                    label: 'Page Visits',
                    data: dauData.map(d => d.visits),
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16,185,129,0.1)',
                    fill: true,
                    tension: 0.3,
                    yAxisID: 'y1',
                },
            ],
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: { beginAtZero: true, position: 'left', title: { display: true, text: 'Users' } },
                y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, title: { display: true, text: 'Visits' } },
            },
        },
    });
</script>
@endsection

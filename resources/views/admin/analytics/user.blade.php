@extends('layouts.app')

@section('title', 'Admin – User Activity: ' . $user->name)

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <a href="{{ route('admin.analytics.index') }}" class="text-decoration-none small"><i class="fas fa-arrow-left me-1"></i>Back to Analytics</a>
            <h2 class="mb-0"><i class="fas fa-user text-primary me-2"></i>{{ $user->name }}</h2>
            <div class="text-muted">{{ $user->email }} · registered {{ $user->created_at->diffForHumans() }}</div>
        </div>
        <form method="GET" class="d-flex align-items-center gap-2">
            <label class="text-muted small mb-0">Period:</label>
            <select name="days" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                @foreach([7, 14, 30, 90] as $d)
                    <option value="{{ $d }}" {{ $days === $d ? 'selected' : '' }}>Last {{ $d }} days</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">Lifetime Page Visits</div>
                <div class="fs-3 fw-bold">{{ number_format($totalVisits) }}</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="text-muted small">Last Seen</div>
                <div class="fs-5 fw-bold">{{ $lastSeen ? \Carbon\Carbon::parse($lastSeen)->diffForHumans() : 'Never' }}</div>
            </div></div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white"><strong>Visits per Day</strong></div>
        <div class="card-body"><canvas id="visitChart" height="70"></canvas></div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white"><strong>Top Pages</strong></div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Page</th><th class="text-end">Visits</th><th class="text-end">Avg ms</th></tr></thead>
                        <tbody>
                        @forelse($topPages as $p)
                            <tr>
                                <td class="text-truncate" style="max-width:240px">{{ $p->page }}</td>
                                <td class="text-end">{{ number_format($p->visits) }}</td>
                                <td class="text-end">{{ number_format($p->avg_duration_ms) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted text-center py-3">No visits in period.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white"><strong>Recent Page Visits</strong></div>
                <div class="table-responsive" style="max-height:420px;overflow-y:auto">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th>Path</th><th class="text-end">Status</th><th class="text-end">ms</th><th>When</th></tr></thead>
                        <tbody>
                        @forelse($recentVisits as $v)
                            <tr>
                                <td class="text-truncate" style="max-width:280px">{{ $v->path }}</td>
                                <td class="text-end"><span class="badge bg-{{ $v->status_code >= 400 ? 'danger' : 'success' }}">{{ $v->status_code }}</span></td>
                                <td class="text-end">{{ number_format($v->duration_ms) }}</td>
                                <td><small>{{ $v->created_at->diffForHumans() }}</small></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted text-center py-3">No visits recorded.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white"><strong>Activity Log</strong></div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light"><tr><th>Module</th><th>Action</th><th>Description</th><th>Business</th><th>When</th></tr></thead>
                <tbody>
                @forelse($activityLogs as $log)
                    <tr>
                        <td>{{ $log->module }}</td>
                        <td><span class="badge bg-{{ $log->action_color }}">{{ $log->action }}</span></td>
                        <td class="text-truncate" style="max-width:340px">{{ $log->description }}</td>
                        <td><small>{{ $log->business_id }}</small></td>
                        <td><small>{{ $log->created_at->diffForHumans() }}</small></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-muted text-center py-3">No actions logged for this user.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($activityLogs->hasPages())
        <div class="card-footer bg-white">{{ $activityLogs->links() }}</div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const visitData = @json($visitTrend);
    new Chart(document.getElementById('visitChart'), {
        type: 'bar',
        data: {
            labels: visitData.map(d => d.day),
            datasets: [{
                label: 'Page Visits',
                data: visitData.map(d => d.visits),
                backgroundColor: 'rgba(79,70,229,0.6)',
            }],
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } },
    });
</script>
@endsection

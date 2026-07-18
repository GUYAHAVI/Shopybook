@extends('layouts.master')

@section('title', 'AI Behavior Analysis - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-brain text-danger me-2"></i>AI Behavior Analysis</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Dashboard</a>
    </div>

    <div class="mb-3">
        <form method="GET" class="d-flex align-items-center gap-2">
            <label class="text-muted small mb-0">Period:</label>
            <select name="days" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                @foreach([7, 14, 30, 90] as $d)
                    <option value="{{ $d }}" {{ $days === $d ? 'selected' : '' }}>Last {{ $d }} days</option>
                @endforeach
            </select>
            <button type="submit" name="analyze" value="1" class="btn btn-sm btn-danger">
                <i class="fas fa-brain me-1"></i>Get AI Insights
            </button>
        </form>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-muted small">Total Users</div>
                <div class="fs-4 fw-bold">{{ number_format($totalUsers) }}</div>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-muted small">Active in Period</div>
                <div class="fs-4 fw-bold text-success">{{ number_format($activeInPeriod) }}</div>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-muted small">Engagement Rate</div>
                <div class="fs-4 fw-bold {{ ($totalUsers > 0 ? $activeInPeriod / $totalUsers * 100 : 0) < 30 ? 'text-danger' : 'text-success' }}">{{ $totalUsers > 0 ? round($activeInPeriod / $totalUsers * 100, 1) : 0 }}%</div>
            </div></div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm"><div class="card-body">
                <div class="text-muted small">Inactive Users</div>
                <div class="fs-4 fw-bold text-warning">{{ number_format($totalUsers - $activeInPeriod) }}</div>
            </div></div>
        </div>
    </div>

    {{-- AI Insights (compact) --}}
    @if($analysis)
    <div class="card shadow-sm mb-4 border-danger">
        <div class="card-header bg-danger text-white py-2"><i class="fas fa-brain me-2"></i>AI Insights (Last {{ $days }} days)</div>
        <div class="card-body">
            <div style="white-space: pre-wrap; line-height: 1.7; font-size: 0.9rem;">{{ $analysis }}</div>
        </div>
    </div>
    @elseif($error)
    <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-1"></i>{{ $error }}</div>
    @elseif(request('analyze'))
    <div class="alert alert-info"><i class="fas fa-spinner fa-spin me-1"></i>Analysis is running...</div>
    @endif

    {{-- Charts row 1: Daily trend + Engagement donut --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-chart-line text-primary me-2"></i>Daily Visits Trend</div>
                <div class="card-body">
                    <canvas id="dailyVisitsChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-users text-success me-2"></i>User Engagement</div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="engagementChart" height="160"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts row 2: Top pages + Least visited --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-arrow-up text-success me-2"></i>Top 10 Pages by Visits</div>
                <div class="card-body">
                    <canvas id="topPagesChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-arrow-down text-danger me-2"></i>Least Visited Pages</div>
                <div class="card-body">
                    <canvas id="leastVisitedChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts row 3: Error pages + Exit pages --}}
    <div class="row g-3 mb-3">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Error-Prone Pages</div>
                <div class="card-body">
                    <canvas id="errorPagesChart" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-door-open text-warning me-2"></i>Top Exit Pages</div>
                <div class="card-body">
                    <canvas id="exitPagesChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts row 4: Module activity doughnut + Module breakdown table --}}
    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-cubes text-info me-2"></i>Module Activity</div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="moduleChart" height="160"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-list me-2"></i>Module Breakdown</div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm mb-0">
                            <thead class="table-light sticky-top">
                                <tr><th>Module</th><th>Action</th><th class="text-end">Count</th></tr>
                            </thead>
                            <tbody>
                                @foreach($moduleActivity as $a)
                                <tr>
                                    <td class="small text-capitalize">{{ $a->module }}</td>
                                    <td class="small"><span class="badge bg-secondary text-capitalize">{{ $a->action }}</span></td>
                                    <td class="text-end">{{ number_format($a->total) }}</td>
                                </tr>
                                @endforeach
                                @if($moduleActivity->isEmpty())
                                <tr><td colspan="3" class="text-center text-muted py-3">No activity</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $dailyDates = $dailyVisits->pluck('date')->map(function($d) { return \Carbon\Carbon::parse($d)->format('M j'); });
    $dailyVisitsData = $dailyVisits->pluck('visits');
    $dailyUniqueData = $dailyVisits->pluck('unique_users');
    $topPagesLabels = $topPages->pluck('page')->map(function($p) { return str_replace(['admin.', '.'], ['', ' '], $p); });
    $topPagesVisits = $topPages->pluck('visits');
    $leastLabels = $leastVisited->pluck('page')->map(function($p) { return str_replace(['admin.', '.'], ['', ' '], $p); });
    $leastVisits = $leastVisited->pluck('visits');
    $errorLabels = $errorPages->pluck('page')->map(function($p) { return str_replace(['admin.', '.'], ['', ' '], $p); });
    $errorCounts = $errorPages->pluck('errors');
    $exitLabels = $exitPages->pluck('page')->map(function($p) { return str_replace(['admin.', '.'], ['', ' '], $p); });
    $exitCounts = $exitPages->pluck('exits');
    $moduleLabels = $moduleTotals->pluck('module')->map(function($m) { return ucfirst($m); });
    $moduleCounts = $moduleTotals->pluck('total');
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const dailyDates = @json($dailyDates);
const dailyVisits = @json($dailyVisitsData);
const dailyUnique = @json($dailyUniqueData);

const topPagesLabels = @json($topPagesLabels);
const topPagesVisits = @json($topPagesVisits);

const leastLabels = @json($leastLabels);
const leastVisits = @json($leastVisits);

const errorLabels = @json($errorLabels);
const errorCounts = @json($errorCounts);

const exitLabels = @json($exitLabels);
const exitCounts = @json($exitCounts);

const moduleLabels = @json($moduleLabels);
const moduleCounts = @json($moduleCounts);

const totalUsers = {{ $totalUsers }};
const activeUsers = {{ $activeInPeriod }};
const inactiveUsers = totalUsers - activeUsers;

const chartColors = ['#0d6efd','#198754','#dc3545','#ffc107','#0dcaf0','#6610f2','#fd7e14','#20c997','#d63384','#6c757d'];

new Chart(document.getElementById('dailyVisitsChart'), {
    type: 'line',
    data: {
        labels: dailyDates,
        datasets: [
            { label: 'Total Visits', data: dailyVisits, borderColor: '#0d6efd', backgroundColor: 'rgba(13,110,253,0.1)', fill: true, tension: 0.3 },
            { label: 'Unique Users', data: dailyUnique, borderColor: '#198754', backgroundColor: 'rgba(25,135,84,0.1)', fill: true, tension: 0.3 }
        ]
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } }, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('engagementChart'), {
    type: 'doughnut',
    data: {
        labels: ['Active', 'Inactive'],
        datasets: [{ data: [activeUsers, inactiveUsers], backgroundColor: ['#198754', '#ffc107'], borderWidth: 0 }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: { callbacks: { label: function(ctx) { const pct = totalUsers > 0 ? Math.round(ctx.parsed / totalUsers * 100) : 0; return ctx.label + ': ' + ctx.parsed + ' (' + pct + '%)'; } } }
        }
    }
});

new Chart(document.getElementById('topPagesChart'), {
    type: 'bar',
    data: { labels: topPagesLabels, datasets: [{ label: 'Visits', data: topPagesVisits, backgroundColor: '#198754', borderRadius: 4 }] },
    options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
});

new Chart(document.getElementById('leastVisitedChart'), {
    type: 'bar',
    data: { labels: leastLabels, datasets: [{ label: 'Visits', data: leastVisits, backgroundColor: '#dc3545', borderRadius: 4 }] },
    options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
});

new Chart(document.getElementById('errorPagesChart'), {
    type: 'bar',
    data: { labels: errorLabels, datasets: [{ label: 'Errors', data: errorCounts, backgroundColor: '#dc3545', borderRadius: 4 }] },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('exitPagesChart'), {
    type: 'bar',
    data: { labels: exitLabels, datasets: [{ label: 'Exits', data: exitCounts, backgroundColor: '#ffc107', borderRadius: 4 }] },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('moduleChart'), {
    type: 'doughnut',
    data: { labels: moduleLabels, datasets: [{ data: moduleCounts, backgroundColor: chartColors.slice(0, moduleLabels.length), borderWidth: 0 }] },
    options: { responsive: true, plugins: { legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } } } }
});
</script>
@endsection

@extends('layouts.master')

@section('title', 'Usage Analytics - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-chart-bar text-info me-2"></i>Usage Analytics</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Dashboard</a>
    </div>

    <div class="mb-3">
        <form method="GET" class="d-inline">
            <label class="me-2">Period:</label>
            <select name="days" class="form-select form-select-sm d-inline-block" style="width:auto" onchange="this.form.submit()">
                @foreach([7, 14, 30, 90] as $d)
                    <option value="{{ $d }}" {{ $days === $d ? 'selected' : '' }}>Last {{ $d }} days</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Most visited --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-arrow-up text-success me-2"></i>Most Visited Pages</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr><th>Page</th><th>Visits</th><th>Users</th><th>Avg Duration</th></tr>
                            </thead>
                            <tbody>
                                @forelse($topPages as $page)
                                <tr>
                                    <td class="small">{{ $page->page }}</td>
                                    <td>{{ number_format($page->visits) }}</td>
                                    <td>{{ $page->unique_users }}</td>
                                    <td class="small">{{ $page->avg_duration_ms ? round($page->avg_duration_ms) . 'ms' : '-' }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">No data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-arrow-down text-danger me-2"></i>Least Visited Pages</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr><th>Page</th><th>Visits</th><th>Users</th></tr>
                            </thead>
                            <tbody>
                                @forelse($leastVisitedPages as $page)
                                <tr>
                                    <td class="small">{{ $page->page }}</td>
                                    <td>{{ $page->visits }}</td>
                                    <td>{{ $page->unique_users }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">No data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Error and exit pages --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Error-Prone Pages</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr><th>Page</th><th>Errors</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                @forelse($errorPages as $page)
                                <tr>
                                    <td class="small">{{ $page->page }}</td>
                                    <td><span class="badge bg-danger">{{ $page->errors }}</span></td>
                                    <td class="small">{{ $page->sample_status }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">No errors</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header"><i class="fas fa-door-open text-warning me-2"></i>Top Exit Pages</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr><th>Page</th><th>Exits</th></tr>
                            </thead>
                            <tbody>
                                @forelse($exitPages as $page)
                                <tr>
                                    <td class="small">{{ $page->page }}</td>
                                    <td><span class="badge bg-warning">{{ $page->exits }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="2" class="text-center text-muted py-3">No data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Per-business usage --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header"><i class="fas fa-store me-2"></i>Per-Business Usage</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>Business</th><th>Visits</th><th>Last Active</th></tr>
                    </thead>
                    <tbody>
                        @forelse($perBusinessUsage as $biz)
                        <tr>
                            <td class="small">{{ $biz->name }}</td>
                            <td>{{ number_format($biz->visits) }}</td>
                            <td class="small">{{ $biz->last_active ? \Carbon\Carbon::parse($biz->last_active)->diffForHumans() : 'Never' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">No data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Module activity --}}
    <div class="card shadow-sm">
        <div class="card-header"><i class="fas fa-cubes me-2"></i>Module Activity Breakdown</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>Module</th><th>Action</th><th>Total</th></tr>
                    </thead>
                    <tbody>
                        @foreach($moduleActivity as $module => $actions)
                            @foreach($actions as $action)
                            <tr>
                                <td class="small text-capitalize">{{ $module }}</td>
                                <td class="small"><span class="badge bg-secondary text-capitalize">{{ $action->action }}</span></td>
                                <td>{{ number_format($action->total) }}</td>
                            </tr>
                            @endforeach
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
@endsection

@extends('layouts.dash')

@section('title', 'Rental Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Rental Dashboard</h4>
            <p class="text-muted mb-0">{{ $business->name }} — {{ now()->format('F Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rentals.properties.index') }}" class="btn btn-outline-secondary"><i class="fas fa-city me-1"></i> Properties</a>
            <a href="{{ route('rentals.units.create') }}" class="btn btn-outline-primary"><i class="fas fa-plus me-1"></i> Units</a>
            <a href="{{ route('rentals.tenants.index') }}" class="btn btn-primary"><i class="fas fa-user-plus me-1"></i> Tenant</a>
        </div>
    </div>

    {{-- KPI cards — uses the layout's own .kpi-grid/.kpi-card design --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <small class="text-muted">Units</small>
            <div class="fs-3 fw-bold">{{ $stats['total_units'] }}</div>
            <small class="text-muted">{{ $stats['occupied'] }} occupied · {{ $stats['vacant'] }} vacant</small>
        </div>
        <div class="kpi-card">
            <small class="text-muted">Occupancy</small>
            <div class="fs-3 fw-bold">{{ $stats['occupancy'] }}%</div>
            <div class="progress mt-2" style="height:6px;"><div class="progress-bar" style="width:{{ $stats['occupancy'] }}%"></div></div>
        </div>
        <div class="kpi-card">
            <small class="text-muted">Collected this month</small>
            <div class="fs-3 fw-bold text-success">KES {{ number_format($stats['collected']) }}</div>
            <div class="small text-muted">of {{ number_format($stats['expected']) }} expected</div>
        </div>
        <div class="kpi-card">
            <small class="text-muted">Listed publicly</small>
            <div class="fs-3 fw-bold">{{ $stats['listed'] }}</div>
            <a href="{{ route('houses.index') }}" target="_blank" class="small">View public page <i class="fas fa-external-link-alt"></i></a>
        </div>
    </div>

    <div class="dash-split">
        {{-- Arrears --}}
        <div class="dash-split-main">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-semibold"><i class="fas fa-exclamation-circle text-danger me-2"></i>Rent owing — {{ now()->format('F') }}</span>
                        <a href="{{ route('rentals.payments.index') }}" class="btn btn-sm btn-outline-primary">All payments</a>
                    </div>
                    <table class="table table-hover mb-0">
                        <tbody>
                        @forelse($arrears as $t)
                            <tr>
                                <td class="fw-semibold">{{ $t->name }}<br><small class="text-muted">{{ $t->unit?->label }}</small></td>
                                <td class="text-end text-danger fw-bold">KES {{ number_format($t->balance) }}
                                    <br><small class="text-muted fw-normal">of {{ number_format($t->rent_amount) }}</small></td>
                                <td class="text-end">
                                    <a href="{{ route('rentals.payments.index') }}" class="btn btn-sm btn-outline-success">Collect</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="text-center text-muted py-4"><i class="fas fa-check-circle text-success me-1"></i>Everyone is paid up this month.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent payments --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-0">
                    <div class="p-3 border-bottom fw-semibold">Recent payments</div>
                    <table class="table table-sm mb-0">
                        <tbody>
                        @forelse($recentPayments as $p)
                            <tr>
                                <td>{{ $p->paid_at?->format('d M') ?? $p->created_at->format('d M') }}</td>
                                <td>{{ $p->tenant?->name }} <small class="text-muted">· {{ $p->unit?->label }}</small></td>
                                <td><span class="badge {{ $p->status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($p->status) }}</span></td>
                                <td class="text-end">KES {{ number_format($p->amount) }}</td>
                            </tr>
                        @empty
                            <tr><td class="text-center text-muted py-4">No payments yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Vacant & listed --}}
        <div class="dash-split-side">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-semibold"><i class="fas fa-user-check text-dark me-2"></i>Occupied units</span>
                        <a href="{{ route('rentals.units.index', ['status' => 'occupied']) }}" class="btn btn-sm btn-outline-secondary">All units</a>
                    </div>
                    @forelse($occupiedUnits->take(8) as $u)
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">{{ $u->label }}</div>
                                @if($u->tenant)
                                    <small class="text-muted d-block"><i class="fas fa-user me-1"></i>{{ $u->tenant->name }}
                                        @if($u->tenant->phone) · <a href="tel:{{ $u->tenant->phone }}" class="text-muted">{{ $u->tenant->phone }}</a>@endif
                                    </small>
                                @else
                                    <small class="text-warning">No tenant assigned</small>
                                @endif
                            </div>
                            @php $b = match($u->rentState) { 'paid' => 'bg-success', 'partial' => 'bg-warning text-dark', 'overdue' => 'bg-danger', default => 'bg-info text-dark' }; @endphp
                            <span class="badge {{ $b }}">{{ match($u->rentState) { 'paid' => 'Paid', 'partial' => 'Part-paid', 'overdue' => 'Overdue', default => 'Due' } }}</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">No occupied units.</div>
                    @endforelse
                    @if($occupiedUnits->count() > 8)
                        <div class="p-2 text-center"><a href="{{ route('rentals.units.index', ['status' => 'occupied']) }}" class="small">View all {{ $occupiedUnits->count() }} occupied units</a></div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-0">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-semibold"><i class="fas fa-bullhorn text-info me-2"></i>Vacant units</span>
                        <a href="{{ route('rentals.units.index', ['status' => 'vacant']) }}" class="btn btn-sm btn-outline-secondary">All units</a>
                    </div>
                    @forelse($vacantUnits as $u)
                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">{{ $u->label }}</div>
                                <small class="text-muted">{{ $u->location_text ?: '—' }} · KES {{ number_format($u->rent_amount) }}/mo</small>
                            </div>
                            <div class="d-flex gap-1 align-items-center">
                                @if($u->is_listed)<span class="badge bg-info">Listed</span>@endif
                                <form method="POST" action="{{ route('rentals.units.toggle-listing', $u) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm {{ $u->is_listed ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                        {{ $u->is_listed ? 'Unlist' : 'List' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">No vacant units.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .dash-split { display: flex; gap: 1.5rem; align-items: flex-start; }
    .dash-split-main { flex: 1 1 60%; min-width: 0; }
    .dash-split-side { flex: 0 0 36%; min-width: 0; }
    @media (max-width: 992px) { .dash-split { flex-direction: column; } .dash-split-main, .dash-split-side { width: 100%; flex: none; } }
</style>
@endsection

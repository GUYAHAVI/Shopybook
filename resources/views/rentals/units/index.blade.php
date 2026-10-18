@extends('layouts.dash')

@section('title', 'Properties & Units')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Properties & Units</h4>
            <p class="text-muted mb-0">Manage your rental units and public vacancy listings</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rentals.properties.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-city me-1"></i> Properties
            </a>
            <a href="{{ route('rentals.units.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Add Units
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @if($orphanCount > 0)
        <div class="alert alert-warning d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <i class="fas fa-map-marker-alt me-1"></i>
                <strong>{{ $orphanCount }} unit{{ $orphanCount === 1 ? '' : 's' }}</strong> aren't linked to a property —
                they have no town/estate so they <strong>won't appear in location search</strong>.
            </div>
            @if($properties->isNotEmpty())
                <form method="POST" action="{{ route('rentals.units.assign-property') }}" class="d-flex gap-2 align-items-center">
                    @csrf
                    <select name="rental_property_id" class="form-select form-select-sm" required>
                        @foreach($properties as $prop)
                            <option value="{{ $prop->id }}">{{ $prop->name }}{{ $prop->location ? ' · '.$prop->location : '' }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-dark text-nowrap">Assign all</button>
                </form>
            @else
                <a href="{{ route('rentals.properties.index') }}" class="btn btn-sm btn-dark">Create a property first</a>
            @endif
        </div>
    @endif

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
                <input type="search" name="q" class="form-control" style="max-width:240px;"
                       placeholder="Search unit or tenant…" value="{{ request('q') }}">
                <select name="status" class="form-select" style="max-width:160px;" onchange="this.form.submit()">
                    <option value="">All statuses</option>
                    <option value="vacant" {{ request('status')==='vacant'?'selected':'' }}>Vacant</option>
                    <option value="occupied" {{ request('status')==='occupied'?'selected':'' }}>Occupied</option>
                    <option value="maintenance" {{ request('status')==='maintenance'?'selected':'' }}>Maintenance</option>
                </select>
                <div class="form-check ms-1">
                    <input class="form-check-input" type="checkbox" name="listed" value="1" id="listedOnly"
                           {{ request('listed') ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="form-check-label" for="listedOnly">Publicly listed only</label>
                </div>
                <button class="btn btn-outline-primary btn-sm"><i class="fas fa-search"></i></button>
            </form>

            <hr class="my-2">
            <form method="POST" action="{{ route('rentals.settings.update') }}" class="d-flex gap-2 align-items-center flex-wrap">
                @csrf
                <label class="small text-muted mb-0"><i class="fas fa-calendar-day me-1"></i>Rent due by day</label>
                <input type="number" name="rent_due_day" class="form-control form-control-sm" style="width:80px;"
                       min="1" max="28" value="{{ $dueDay }}">
                <span class="small text-muted">of each month — unpaid units turn red after this.</span>
                <button class="btn btn-sm btn-outline-secondary">Save</button>
            </form>
        </div>
    </div>

    @if($units->isEmpty())
        <div class="card"><div class="card-body text-center py-5 text-muted">
            <i class="fas fa-building fa-3x mb-3 d-block"></i>
            No units match. <a href="{{ route('rentals.units.create') }}">Add your first units</a> — you can create many at once.
        </div></div>
    @else
    <div class="row g-3">
        @foreach($units as $unit)
        @php
            $badge = match($unit->rentState) {
                'paid'    => 'bg-success',
                'partial' => 'bg-warning text-dark',
                'overdue' => 'bg-danger',
                'due'     => 'bg-info text-dark',
                default   => 'bg-secondary',
            };
            $label = match($unit->rentState) {
                'paid'    => 'Paid',
                'partial' => 'Part-paid',
                'overdue' => 'Overdue',
                'due'     => 'Rent due',
                default   => 'No tenant',
            };
        @endphp
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 {{ $unit->rentState === 'overdue' ? 'border-danger' : ($unit->rentState === 'paid' ? 'border-success' : '') }}">
                @if($unit->cover_image_url)
                    <img src="{{ $unit->cover_image_url }}" class="card-img-top" style="height:140px;object-fit:cover;" alt="{{ $unit->label }}">
                @else
                    <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height:140px;">
                        <i class="fas fa-home fa-2x text-muted"></i>
                    </div>
                @endif
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $unit->label }}</h6>
                            <small class="text-muted">
                                {{ $unit->location_text ?: '—' }}
                                @if($unit->bedrooms !== null) · {{ $unit->bedrooms === 0 ? 'Bedsitter' : $unit->bedrooms.' bed' }}@endif
                            </small>
                        </div>
                        <span class="badge {{ $unit->status === 'occupied' ? 'bg-dark' : ($unit->status === 'maintenance' ? 'bg-warning text-dark' : 'bg-secondary') }}">
                            {{ ucfirst($unit->status) }}
                        </span>
                    </div>
                    <div class="mt-2 fw-bold text-primary">KES {{ number_format($unit->rent_amount) }}<small class="text-muted fw-normal">/mo</small></div>

                    @if($unit->tenant)
                        <div class="mt-2 p-2 rounded bg-light">
                            <small class="d-block"><i class="fas fa-user me-1 text-muted"></i><strong>{{ $unit->tenant->name }}</strong></small>
                            @if($unit->tenant->phone)
                                <small class="d-block text-muted"><i class="fas fa-phone me-1"></i>{{ $unit->tenant->phone }}</small>
                            @endif
                            <div class="mt-1">
                                <span class="badge {{ $badge }}">{{ $label }} — {{ \Carbon\Carbon::createFromFormat('Y-m', $period)->format('M Y') }}</span>
                                @if($unit->rentState === 'partial')
                                    <small class="text-muted d-block mt-1">KES {{ number_format($unit->periodPaid) }} paid · {{ number_format(max(0, (float)$unit->rent_amount - $unit->periodPaid)) }} left</small>
                                @endif
                                @if($unit->paidThrough)
                                    <small class="text-success d-block mt-1"><i class="fas fa-forward me-1"></i>Paid through {{ \Carbon\Carbon::createFromFormat('Y-m', $unit->paidThrough)->format('M Y') }}</small>
                                @endif
                            </div>
                        </div>
                    @elseif($unit->status === 'vacant')
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2 w-100"
                                onclick="openTenantModal({{ $unit->id }}, '{{ addslashes($unit->label) }}', {{ (float) $unit->rent_amount }})">
                            <i class="fas fa-user-plus me-1"></i> Add tenant
                        </button>
                    @else
                        <small class="text-muted d-block mt-2">No tenant assigned</small>
                    @endif
                </div>
                <div class="card-footer bg-transparent p-2">
                    <div class="d-flex gap-1">
                        <a href="{{ route('rentals.units.edit', $unit) }}" class="btn btn-sm btn-outline-primary flex-fill">Edit</a>
                        @if($unit->status === 'vacant' || $unit->is_listed)
                        <form method="POST" action="{{ route('rentals.units.toggle-listing', $unit) }}" class="flex-fill">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $unit->is_listed ? 'btn-warning' : 'btn-outline-success' }} w-100" title="Toggle public listing">
                                <i class="fas {{ $unit->is_listed ? 'fa-eye-slash' : 'fa-bullhorn' }}"></i>
                                {{ $unit->is_listed ? 'Unlist' : 'List' }}
                            </button>
                        </form>
                        @endif
                    </div>
                    @if($unit->tenant)
                    <div class="d-flex gap-1 mt-1">
                        @if($unit->rentState === 'paid')
                            <button type="button" class="btn btn-sm btn-outline-danger flex-fill"
                                    onclick="unmarkPaid({{ $unit->tenant->id }}, '{{ addslashes($unit->tenant->name) }}')">
                                <i class="fas fa-undo me-1"></i> Mark unpaid
                            </button>
                        @else
                            <button type="button" class="btn btn-sm btn-success flex-fill"
                                    onclick="openPayModal({{ $unit->tenant->id }}, '{{ addslashes($unit->tenant->name) }}', {{ max(0, (float)$unit->rent_amount - $unit->periodPaid) }})">
                                <i class="fas fa-check me-1"></i> Mark paid
                            </button>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $units->links() }}</div>
    @endif
</div>

{{-- Add tenant modal (unit pre-selected) --}}
<div class="modal fade" id="tenantModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="{{ route('rentals.tenants.store') }}">
            @csrf
            <input type="hidden" name="rental_unit_id" id="tmUnitId">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add Tenant — <span id="tmUnitLabel"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Full name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required autofocus>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control" placeholder="0712345678" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ID / Passport</label>
                        <input type="text" name="id_number" class="form-control">
                    </div>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Rent (KES/mo)</label>
                        <input type="number" name="rent_amount" id="tmRent" class="form-control" min="1">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Move-in date</label>
                    <input type="date" name="move_in_date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="rent_paid" value="1" class="form-check-input" id="tmRentPaid">
                    <label class="form-check-label" for="tmRentPaid">
                        Tenant has <strong>already paid</strong> this month's rent
                    </label>
                </div>
                <div class="mb-3" id="tmPaidMethodWrap" style="display:none;">
                    <label class="form-label">Paid via</label>
                    <select name="paid_method" class="form-select">
                        <option value="cash">Cash</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="bank">Bank</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-1">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Tenant</button>
            </div>
        </form>
    </div></div>
</div>

{{-- Record payment modal --}}
<div class="modal fade" id="payModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Record Payment — <span id="payTenantName"></span></h5>
        <button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="row g-3">
            <div class="col-6"><label class="form-label">From period</label>
                <input type="month" id="payPeriod" class="form-control" value="{{ $period }}"></div>
            <div class="col-6"><label class="form-label">Months covered</label>
                <input type="number" id="payMonths" class="form-control" min="1" max="24" value="1">
                <div class="form-text">&gt;1 splits the amount across months ahead.</div></div>
            <div class="col-6"><label class="form-label">Total amount (KES)</label>
                <input type="number" id="payAmount" class="form-control" min="1"></div>
            <div class="col-6"><label class="form-label">Method</label>
                <select id="payMethod" class="form-select">
                    <option value="cash">Cash</option><option value="mpesa">M-Pesa (manual)</option>
                    <option value="bank_transfer">Bank Transfer</option><option value="card">Card</option><option value="cheque">Cheque</option>
                </select></div>
            <div class="col-12"><label class="form-label">Reference</label>
                <input type="text" id="payRef" class="form-control" placeholder="Receipt / txn no."></div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-success" id="paySubmit" onclick="submitPay()">Save Payment</button>
    </div>
</div></div></div>

<script>
const csrf = document.querySelector('meta[name="csrf-token"]').content;
const period = '{{ $period }}';
let payTenant = null;

function openTenantModal(unitId, label, rent) {
    document.getElementById('tmUnitId').value = unitId;
    document.getElementById('tmUnitLabel').textContent = label;
    document.getElementById('tmRent').value = rent;
    document.getElementById('tmRentPaid').checked = false;
    document.getElementById('tmPaidMethodWrap').style.display = 'none';
    new bootstrap.Modal('#tenantModal').show();
}

document.getElementById('tmRentPaid')?.addEventListener('change', function() {
    document.getElementById('tmPaidMethodWrap').style.display = this.checked ? 'block' : 'none';
});

function openPayModal(id, name, balance) {
    payTenant = id;
    document.getElementById('payTenantName').textContent = name;
    document.getElementById('payAmount').value = balance > 0 ? balance : '';
    document.getElementById('payPeriod').value = period;
    document.getElementById('payMonths').value = 1;
    new bootstrap.Modal('#payModal').show();
}

async function submitPay() {
    const btn = document.getElementById('paySubmit');
    btn.disabled = true;
    const res = await fetch(`/rentals/payments/${payTenant}/record`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: JSON.stringify({
            period: document.getElementById('payPeriod').value,
            months: parseInt(document.getElementById('payMonths').value) || 1,
            amount: document.getElementById('payAmount').value,
            method: document.getElementById('payMethod').value,
            transaction_reference: document.getElementById('payRef').value,
        })
    });
    const data = await res.json();
    if (data.success) { location.reload(); }
    else { alert(data.message || 'Failed'); btn.disabled = false; }
}

async function unmarkPaid(id, name) {
    if (!confirm(`Mark ${name} as UNPAID for this month? The payment row is kept but reversed.`)) return;
    const res = await fetch(`/rentals/payments/${id}/unmark`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: JSON.stringify({ period })
    });
    const data = await res.json();
    if (data.success) { location.reload(); }
    else { alert(data.message || 'Failed'); }
}
</script>
@endsection

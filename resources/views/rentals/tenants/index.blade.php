@extends('layouts.dash')

@section('title', 'Tenants')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Tenants</h4>
            <p class="text-muted mb-0">Record tenants and attach them to rental units</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTenantModal">
            <i class="fas fa-plus me-1"></i> Add Tenant
        </button>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" class="d-flex gap-2 flex-wrap">
                <input type="text" name="search" class="form-control" style="max-width:260px" placeholder="Search name or phone…" value="{{ request('search') }}">
                <select name="status" class="form-select" style="max-width:160px" onchange="this.form.submit()">
                    <option value="active" {{ request('status','active')==='active'?'selected':'' }}>Active</option>
                    <option value="vacated" {{ request('status')==='vacated'?'selected':'' }}>Moved out</option>
                    <option value="" {{ request('status')===''&&request()->has('status')?'selected':'' }}>All</option>
                </select>
                <button class="btn btn-outline-secondary">Search</button>
            </form>
        </div>
    </div>

    <div class="card"><div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Tenant</th><th>Phone</th><th>Unit</th><th class="text-end">Rent/mo</th><th>Moved in</th><th>Status</th><th></th>
            </tr></thead>
            <tbody>
            @forelse($tenants as $t)
                <tr>
                    <td class="fw-semibold">{{ $t->name }}</td>
                    <td>{{ $t->phone }}</td>
                    <td>{{ $t->unit ? $t->unit->label . ($t->unit->property ? ' · '.$t->unit->property->name : '') : '—' }}</td>
                    <td class="text-end">KES {{ number_format($t->rent_amount) }}</td>
                    <td>
                        {{ $t->move_in_date?->format('d M Y') ?? '—' }}
                        @if($t->status === 'vacated' && $t->move_out_date)
                            <div class="text-muted small" @if($t->move_out_reason) title="{{ $t->move_out_reason }}" @endif>Out: {{ $t->move_out_date->format('d M Y') }}</div>
                        @endif
                    </td>
                    <td><span class="badge {{ $t->status==='active' ? 'bg-success' : 'bg-secondary' }}">{{ $t->status === 'active' ? 'Active' : 'Moved out' }}</span></td>
                    <td class="text-end text-nowrap">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTenantModal"
                            data-update-url="{{ route('rentals.tenants.update', $t) }}"
                            data-status="{{ $t->status }}"
                            data-name="{{ $t->name }}"
                            data-phone="{{ $t->phone }}"
                            data-email="{{ $t->email }}"
                            data-id-number="{{ $t->id_number }}"
                            data-rent-amount="{{ (float) $t->rent_amount }}"
                            data-move-in="{{ $t->move_in_date?->format('Y-m-d') }}"
                            data-notes="{{ $t->notes }}"
                            data-unit-id="{{ $t->rental_unit_id }}"
                            data-unit-label="{{ $t->unit ? $t->unit->label . ($t->unit->property ? ' · '.$t->unit->property->name : '') : '' }}">
                            Edit
                        </button>
                        @if($t->status === 'active')
                        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#vacateTenantModal"
                            data-vacate-url="{{ route('rentals.tenants.vacate', $t) }}"
                            data-name="{{ $t->name }}">
                            Mark moved out
                        </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No tenants yet — click "Add Tenant" to record one.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
    <div class="mt-3">{{ $tenants->links() }}</div>
</div>

{{-- Add Tenant Modal --}}
<div class="modal fade" id="addTenantModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="{{ route('rentals.tenants.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add Tenant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Full name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
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
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-7">
                        <label class="form-label">Assign to unit</label>
                        <select name="rental_unit_id" class="form-select" id="tenantUnitSelect">
                            <option value="">— Not yet assigned —</option>
                            @foreach($vacantUnits as $u)
                                <option value="{{ $u->id }}" data-rent="{{ $u->rent_amount }}" {{ request('unit') == $u->id ? 'selected' : '' }}>
                                    {{ $u->label }}{{ $u->property ? ' · '.$u->property->name : '' }} — KES {{ number_format($u->rent_amount) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Rent (KES/mo)</label>
                        <input type="number" name="rent_amount" id="tenantRent" class="form-control" min="1">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Move-in date</label>
                    <input type="date" name="move_in_date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="rent_paid" value="1" class="form-check-input" id="rentPaidCheck">
                    <label class="form-check-label" for="rentPaidCheck">
                        Tenant has <strong>already paid</strong> this month's rent
                    </label>
                </div>
                <div class="mb-3" id="paidMethodWrap" style="display:none;">
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

{{-- Edit Tenant Modal --}}
<div class="modal fade" id="editTenantModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="POST" id="editTenantForm">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Edit Tenant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Full name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ID / Passport</label>
                        <input type="text" name="id_number" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="row g-3 mb-3" id="editUnitRow">
                    <div class="col-md-7">
                        <label class="form-label">Assigned unit</label>
                        <select name="rental_unit_id" class="form-select" id="editTenantUnitSelect">
                            <option value="">— Not assigned —</option>
                            @foreach($vacantUnits as $u)
                                <option value="{{ $u->id }}">
                                    {{ $u->label }}{{ $u->property ? ' · '.$u->property->name : '' }} — KES {{ number_format($u->rent_amount) }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Only vacant units are listed; the current unit is kept unless changed.</div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Rent (KES/mo)</label>
                        <input type="number" name="rent_amount" class="form-control" min="0" step="0.01">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Move-in date</label>
                    <input type="date" name="move_in_date" class="form-control">
                </div>
                <div class="mb-1">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div></div>
</div>

{{-- Move-out Modal --}}
<div class="modal fade" id="vacateTenantModal" tabindex="-1">
    <div class="modal-dialog modal-sm"><div class="modal-content">
        <form method="POST" id="vacateTenantForm">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-door-open me-2"></i>Mark Moved Out</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Record move-out for <strong id="vacateTenantName"></strong>. Their unit becomes vacant.</p>
                <div class="mb-3">
                    <label class="form-label">Move-out date</label>
                    <input type="date" name="move_out_date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="mb-1">
                    <label class="form-label">Reason <span class="text-muted">(optional)</span></label>
                    <input type="text" name="move_out_reason" class="form-control" maxlength="500" placeholder="e.g. Lease ended, eviction…">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning">Confirm</button>
            </div>
        </form>
    </div></div>
</div>

<script>
document.getElementById('tenantUnitSelect')?.addEventListener('change', function() {
    const rent = this.selectedOptions[0]?.dataset.rent;
    if (rent) document.getElementById('tenantRent').value = rent;
});
document.getElementById('rentPaidCheck')?.addEventListener('change', function() {
    document.getElementById('paidMethodWrap').style.display = this.checked ? 'block' : 'none';
});

// Edit modal — populate fields from the clicked row's data attributes
document.getElementById('editTenantModal')?.addEventListener('show.bs.modal', function (e) {
    const b = e.relatedTarget;
    const f = document.getElementById('editTenantForm');
    f.action = b.dataset.updateUrl;
    f.elements.name.value           = b.dataset.name;
    f.elements.phone.value          = b.dataset.phone;
    f.elements.email.value          = b.dataset.email || '';
    f.elements.id_number.value      = b.dataset.idNumber || '';
    f.elements.rent_amount.value    = b.dataset.rentAmount || '';
    f.elements.move_in_date.value   = b.dataset.moveIn || '';
    f.elements.notes.value          = b.dataset.notes || '';

    const unitSel = f.elements.rental_unit_id;
    unitSel.querySelector('option[data-current]')?.remove();
    if (b.dataset.unitId) {
        const opt = new Option(b.dataset.unitLabel + ' — current unit', b.dataset.unitId);
        opt.dataset.current = '1';
        unitSel.insertBefore(opt, unitSel.children[1]);
    }
    unitSel.value = b.dataset.unitId || '';
    document.getElementById('editUnitRow').style.display = b.dataset.status === 'vacated' ? 'none' : '';
});

// Move-out modal — point the form at the right tenant and show their name
document.getElementById('vacateTenantModal')?.addEventListener('show.bs.modal', function (e) {
    const b = e.relatedTarget;
    document.getElementById('vacateTenantForm').action = b.dataset.vacateUrl;
    document.getElementById('vacateTenantName').textContent = b.dataset.name;
});

@if(session('receipt_url'))
window.open('{{ session('receipt_url') }}', '_blank', 'width=420,height=640');
@endif

// ?unit=<id> — opened from a vacant unit card: pre-select it and open the modal
@if(request('unit'))
(function () {
    const sel = document.getElementById('tenantUnitSelect');
    sel.dispatchEvent(new Event('change'));
    new bootstrap.Modal('#addTenantModal').show();
})();
@endif
</script>
@endsection

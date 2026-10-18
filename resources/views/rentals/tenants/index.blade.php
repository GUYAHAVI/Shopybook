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
                    <td>{{ $t->move_in_date?->format('d M Y') ?? '—' }}</td>
                    <td><span class="badge {{ $t->status==='active' ? 'bg-success' : 'bg-secondary' }}">{{ $t->status === 'active' ? 'Active' : 'Moved out' }}</span></td>
                    <td class="text-end">
                        @if($t->status === 'active')
                        <form method="POST" action="{{ route('rentals.tenants.vacate', $t) }}" class="d-inline"
                              onsubmit="return confirm('Mark {{ addslashes($t->name) }} as moved out? The unit becomes vacant.')">
                            @csrf
                            <button class="btn btn-sm btn-outline-warning">Mark moved out</button>
                        </form>
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

<script>
document.getElementById('tenantUnitSelect')?.addEventListener('change', function() {
    const rent = this.selectedOptions[0]?.dataset.rent;
    if (rent) document.getElementById('tenantRent').value = rent;
});
document.getElementById('rentPaidCheck')?.addEventListener('change', function() {
    document.getElementById('paidMethodWrap').style.display = this.checked ? 'block' : 'none';
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

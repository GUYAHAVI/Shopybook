@extends('layouts.dash')

@section('title', 'Rent Payments')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Rent Payments</h4>
            <p class="text-muted mb-0">Collect rent via M-Pesa STK push or record manual payments</p>
        </div>
        <form method="GET" class="d-flex gap-2">
            <input type="month" name="period" class="form-control" value="{{ $period }}" onchange="this.form.submit()">
        </form>
    </div>

    {{-- Summary --}}
    <style>
        .kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
        @media (max-width: 768px) { .kpi-grid { grid-template-columns: 1fr; } }
    </style>
    <div class="kpi-grid mb-4">
        <div class="card border-0 shadow-sm"><div class="card-body">
            <small class="text-muted">Expected ({{ $period }})</small>
            <div class="fs-4 fw-bold">KES {{ number_format($summary['expected']) }}</div>
        </div></div>
        <div class="card border-0 shadow-sm"><div class="card-body">
            <small class="text-muted">Collected</small>
            <div class="fs-4 fw-bold text-success">KES {{ number_format($summary['collected']) }}</div>
        </div></div>
        <div class="card border-0 shadow-sm"><div class="card-body">
            <small class="text-muted">Tenants owing</small>
            <div class="fs-4 fw-bold {{ $summary['owing'] ? 'text-danger' : 'text-success' }}">{{ $summary['owing'] }}</div>
            <div class="small text-danger">KES {{ number_format($summary['outstanding']) }} outstanding</div>
        </div></div>
    </div>

    {{-- Per-tenant status --}}
    <div class="card mb-4"><div class="card-body p-0">
        <div class="p-3 border-bottom fw-semibold">{{ \Carbon\Carbon::parse($period.'-01')->format('F Y') }} — per tenant</div>
        <table class="table table-hover mb-0">
            <thead><tr><th>Tenant</th><th>Unit</th><th class="text-end">Rent</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($tenants as $t)
                <tr>
                    <td class="fw-semibold">{{ $t->name }}<br><small class="text-muted">{{ $t->phone }}</small></td>
                    <td>{{ $t->unit?->label ?? '—' }}</td>
                    <td class="text-end">KES {{ number_format($t->rent_amount) }}</td>
                    <td>
                        @if($t->periodStatus === 'paid')
                            <span class="badge bg-success">Paid</span>
                            <small class="text-muted">{{ $t->periodPayment->transaction_reference }}</small>
                        @elseif($t->periodStatus === 'partial')
                            <span class="badge bg-warning text-dark">Part-paid</span>
                            <br><small class="text-muted">KES {{ number_format($t->periodPaid) }} of {{ number_format($t->rent_amount) }} · <span class="text-danger fw-semibold">{{ number_format($t->balance) }} left</span></small>
                        @elseif($t->periodStatus === 'pending')
                            <span class="badge bg-warning text-dark">STK sent…</span>
                        @else
                            <span class="badge bg-danger">Owing</span>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        @if($t->balance > 0)
                            <button class="btn btn-sm btn-outline-success"
                                onclick="openStkModal({{ $t->id }}, '{{ addslashes($t->name) }}', '{{ $t->phone }}', {{ $t->balance }})">
                                <i class="fas fa-mobile-alt"></i> Send STK
                            </button>
                            <button class="btn btn-sm btn-outline-primary"
                                onclick="openRecordModal({{ $t->id }}, '{{ addslashes($t->name) }}', {{ $t->balance }})">
                                Record
                            </button>
                        @endif
                        @if($t->periodPayment?->status === 'paid')
                            <a href="{{ route('rentals.payments.receipt', $t->periodPayment) }}" target="_blank"
                               class="btn btn-sm btn-outline-secondary" title="Print receipt">
                                <i class="fas fa-receipt"></i>
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No active tenants. <a href="{{ route('rentals.tenants.index') }}">Add tenants</a></td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>

    {{-- History --}}
    <div class="card"><div class="card-body p-0">
        <div class="p-3 border-bottom fw-semibold">Recent payments</div>
        <table class="table table-sm mb-0">
            <thead><tr><th>Date</th><th>Tenant</th><th>Unit</th><th>Period</th><th class="text-end">Amount</th><th>Method</th><th>Ref</th><th></th></tr></thead>
            <tbody>
            @forelse($history as $p)
                <tr>
                    <td>{{ $p->paid_at?->format('d M Y') ?? $p->created_at->format('d M Y') }}</td>
                    <td>{{ $p->tenant?->name ?? '—' }}</td>
                    <td>{{ $p->unit?->label ?? '—' }}</td>
                    <td>{{ $p->period }}</td>
                    <td class="text-end">KES {{ number_format($p->amount) }}</td>
                    <td><span class="badge bg-light text-dark border">{{ strtoupper(str_replace('_',' ',$p->method)) }}</span></td>
                    <td class="text-muted small">{{ $p->transaction_reference ?? ($p->status === 'pending' ? 'pending…' : '—') }}</td>
                    <td>
                        @if($p->status === 'paid')
                            <a href="{{ route('rentals.payments.receipt', $p) }}" target="_blank"
                               class="btn btn-sm btn-outline-secondary" title="Print receipt">
                                <i class="fas fa-receipt"></i>
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No payments recorded yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
</div>

{{-- Record manual payment modal --}}
<div class="modal fade" id="recordModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Record Payment — <span id="recTenantName"></span></h5>
        <button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <div class="row g-3">
            <div class="col-6"><label class="form-label">Period</label>
                <input type="month" id="recPeriod" class="form-control" value="{{ $period }}"></div>
            <div class="col-6"><label class="form-label">Amount (KES)</label>
                <input type="number" id="recAmount" class="form-control" min="1"></div>
            <div class="col-6"><label class="form-label">Method</label>
                <select id="recMethod" class="form-select">
                    <option value="cash">Cash</option><option value="mpesa">M-Pesa (manual)</option>
                    <option value="bank_transfer">Bank Transfer</option><option value="card">Card</option><option value="cheque">Cheque</option>
                </select></div>
            <div class="col-6"><label class="form-label">Months covered</label>
                <input type="number" id="recMonths" class="form-control" min="1" max="24" value="1">
                <div class="form-text">&gt;1 splits it across months ahead.</div></div>
            <div class="col-6"><label class="form-label">Reference</label>
                <input type="text" id="recRef" class="form-control" placeholder="Receipt / txn no."></div>
            <div class="col-12"><label class="form-label">Notes</label>
                <input type="text" id="recNotes" class="form-control"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="recSubmit" onclick="submitRecord()">Save Payment</button>
    </div>
</div></div></div>

{{-- STK push modal --}}
<div class="modal fade" id="stkModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title"><i class="fas fa-mobile-alt me-2"></i>Send Rent STK — <span id="stkTenantName"></span></h5>
        <button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <p class="small text-muted" id="stkHint">An M-Pesa prompt will be sent to the tenant's phone.</p>
        <div class="row g-3">
            <div class="col-6"><label class="form-label">Phone</label>
                <input type="tel" id="stkPhone" class="form-control"></div>
            <div class="col-6"><label class="form-label">Amount (KES)</label>
                <input type="number" id="stkAmount" class="form-control" min="1"></div>
            <div class="col-12"><label class="form-label">Period</label>
                <input type="month" id="stkPeriod" class="form-control" value="{{ $period }}"></div>
        </div>
        <div id="stkStatus" class="alert alert-info mt-3 mb-0 d-none"></div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-success" id="stkSubmit" onclick="submitStk()"><i class="fas fa-mobile-alt me-1"></i>Send STK Push</button>
    </div>
</div></div></div>

<script>
const period = '{{ $period }}';
const csrf = document.querySelector('meta[name="csrf-token"]').content;
let currentTenant = null, currentTenant2 = null;

function openRecordModal(id, name, rent) {
    currentTenant = id;
    document.getElementById('recTenantName').textContent = name;
    document.getElementById('recAmount').value = rent;
    document.getElementById('recPeriod').value = period;
    new bootstrap.Modal('#recordModal').show();
}

async function submitRecord() {
    const btn = document.getElementById('recSubmit');
    btn.disabled = true;
    const res = await fetch(`/rentals/payments/${currentTenant}/record`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: JSON.stringify({
            period: document.getElementById('recPeriod').value,
            months: parseInt(document.getElementById('recMonths').value) || 1,
            amount: document.getElementById('recAmount').value,
            method: document.getElementById('recMethod').value,
            transaction_reference: document.getElementById('recRef').value,
            notes: document.getElementById('recNotes').value,
        })
    });
    const data = await res.json();
    if (data.success) { location.reload(); }
    else { alert(data.message || 'Failed'); btn.disabled = false; }
}

function openStkModal(id, name, phone, rent) {
    currentTenant2 = id;
    document.getElementById('stkTenantName').textContent = name;
    document.getElementById('stkPhone').value = phone;
    document.getElementById('stkAmount').value = rent;
    document.getElementById('stkPeriod').value = period;
    const st = document.getElementById('stkStatus'); st.classList.add('d-none');
    document.getElementById('stkSubmit').disabled = false;
    new bootstrap.Modal('#stkModal').show();
}

async function submitStk() {
    const btn = document.getElementById('stkSubmit');
    const st = document.getElementById('stkStatus');
    btn.disabled = true;
    st.className = 'alert alert-info mt-3 mb-0';
    st.textContent = 'Sending STK push…';

    const res = await fetch(`/rentals/payments/${currentTenant2}/stk`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: JSON.stringify({
            period: document.getElementById('stkPeriod').value,
            amount: document.getElementById('stkAmount').value,
            phone: document.getElementById('stkPhone').value,
        })
    });
    const data = await res.json();

    if (!data.success) {
        st.className = 'alert alert-danger mt-3 mb-0';
        st.textContent = data.message || 'Failed to send STK push.';
        btn.disabled = false;
        return;
    }

    st.textContent = data.message + ' Waiting for payment…';
    pollStk(data.reference, st);
}

function pollStk(ref, st) {
    let tries = 0;
    const timer = setInterval(async () => {
        tries++;
        if (tries > 24) {
            clearInterval(timer);
            st.className = 'alert alert-warning mt-3 mb-0';
            st.textContent = 'No response yet — the payment will be marked automatically when the tenant pays.';
            document.getElementById('stkSubmit').disabled = false;
            return;
        }
        try {
            const r = await fetch(`/payment/mpesa/status/${ref}`);
            const d = await r.json();
            if (d.status === 'success') {
                clearInterval(timer);
                st.className = 'alert alert-success mt-3 mb-0';
                st.textContent = `Payment confirmed! Receipt: ${d.receipt ?? ''}`;
                setTimeout(() => location.reload(), 1500);
            } else if (d.status === 'failed') {
                clearInterval(timer);
                st.className = 'alert alert-danger mt-3 mb-0';
                st.textContent = 'Payment failed: ' + (d.result_desc ?? '');
                document.getElementById('stkSubmit').disabled = false;
            }
        } catch (e) { /* keep polling */ }
    }, 5000);
}
</script>
@endsection

@extends('layouts.admin')

@section('title', 'Payment Settings — Super Admin')
@section('page-title', 'Payment Settings')

@section('content')
<div class="container-fluid py-4" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-landmark text-warning me-2"></i>Payment Settings</h2>
        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-credit-card me-1"></i>Subscriptions</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-1">Where subscription money goes</h5>
                    <p class="text-muted small">
                        Plan upgrades and extra-business purchases are charged via M-Pesa STK push.
                        Choose whether they settle to <strong>your own bank account</strong> (via the bank's
                        public M-Pesa deposit paybill, the same mechanism businesses use) or Paystack.
                    </p>

                    <form method="POST" action="{{ route('admin.payment-settings.update') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Gateway</label>
                            <select name="gateway" id="gateway" class="form-select" onchange="toggleDaraja(this.value)">
                                <option value="paystack" {{ ($dest['gateway'] ?? 'paystack') === 'paystack' ? 'selected' : '' }}>Paystack (external)</option>
                                <option value="daraja" {{ ($dest['gateway'] ?? 'paystack') === 'daraja' ? 'selected' : '' }}>M-Pesa STK → my bank account</option>
                            </select>
                        </div>

                        <div id="daraja-fields" style="{{ ($dest['gateway'] ?? 'paystack') === 'daraja' ? '' : 'display:none;' }}">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Bank</label>
                                <select name="bank" class="form-select">
                                    <option value="">— choose bank —</option>
                                    @foreach($banks as $key => $bank)
                                        <option value="{{ $key }}" {{ $dest['bank'] === $key ? 'selected' : '' }}>
                                            {{ $bank['name'] }} (paybill {{ $bank['paybill'] }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Your bank account number</label>
                                <input type="text" name="account" class="form-control" value="{{ $dest['account'] }}" inputmode="numeric" placeholder="e.g. 1234567890123">
                                <div class="form-text">
                                    Sent as the M-Pesa Account Reference, so funds land on this exact account.
                                    Double-check it — wrong digits send money elsewhere.
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-warning"><i class="fas fa-save me-1"></i> Save payment settings</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm h-100 {{ $dest['ready'] ? 'border-success' : '' }}">
                <div class="card-body">
                    <h5 class="mb-3">Current status</h5>
                    @if($dest['ready'])
                        <p class="mb-2"><span class="badge bg-success">Active — M-Pesa STK</span></p>
                        <ul class="list-unstyled small mb-0">
                            <li><i class="fas fa-landmark text-muted me-2"></i><strong>{{ $dest['bank_name'] }}</strong></li>
                            <li><i class="fas fa-hashtag text-muted me-2"></i>Paybill {{ $dest['paybill'] }}</li>
                            <li><i class="fas fa-credit-card text-muted me-2"></i>Account {{ $dest['account'] }}</li>
                        </ul>
                    @else
                        <p class="mb-2"><span class="badge bg-secondary">{{ ($dest['gateway'] ?? 'paystack') === 'daraja' ? 'Incomplete' : 'Paystack' }}</span></p>
                        @if(($dest['gateway'] ?? 'paystack') === 'daraja')
                            <p class="small text-muted mb-0">Daraja is selected but a bank/account is missing — payments still go through Paystack until you save valid details.</p>
                        @else
                            <p class="small text-muted mb-0">Customers pay through Paystack. Switch above to send money straight to your bank via M-Pesa.</p>
                        @endif
                    @endif
                    <hr>
                    <p class="small text-muted mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        STK pushes are signed with the platform Daraja app
                        (org shortcode + passkey from the environment). A payer's phone gets
                        the PIN prompt; the callback marks the subscription paid automatically.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($recentPayments->isNotEmpty())
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h5>Recent subscription payments</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead><tr>
                        <th>Business</th><th>Plan</th><th>Amount</th><th>Gateway</th><th>Status</th><th>Receipt</th><th>Date</th>
                    </tr></thead>
                    <tbody>
                    @foreach($recentPayments as $p)
                        <tr>
                            <td>{{ $p->business_name ?? '—' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $p->plan)) }}</td>
                            <td>KES {{ number_format($p->amount) }}</td>
                            <td><span class="badge bg-light text-dark border">{{ ucfirst($p->gateway ?? 'paystack') }}</span></td>
                            <td>
                                <span class="badge bg-{{ $p->status === 'completed' ? 'success' : ($p->status === 'failed' ? 'danger' : 'warning text-dark') }}">{{ ucfirst($p->status) }}</span>
                            </td>
                            <td class="small">{{ $p->mpesa_receipt_number ?? '—' }}</td>
                            <td class="small text-muted">{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y, H:i') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function toggleDaraja(v) {
    document.getElementById('daraja-fields').style.display = v === 'daraja' ? '' : 'none';
}
</script>
@endsection

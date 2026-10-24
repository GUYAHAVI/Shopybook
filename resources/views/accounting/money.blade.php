@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <h2 class="fw-bold mb-1" style="color:#7b2e2e;">Money Accounts</h2>
            <p class="text-muted mb-4">Keep your cash, M-Pesa and bank balances matching what you actually have.</p>

            <div class="row mb-4">
                @foreach($accounts as $account)
                <div class="col-md-4 mb-3">
                    <div class="card p-3 text-center">
                        <div class="text-muted small">{{ $account->name }}</div>
                        <div class="fs-4 fw-bold {{ $balances[$account->id] < 0 ? 'text-danger' : '' }}">KSh {{ number_format($balances[$account->id], 2) }}</div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="card p-4 mb-4">
                <ul class="nav nav-pills mb-3" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#kind-capital" type="button">Put money in</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#kind-drawing" type="button">Take money out</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#kind-transfer" type="button">Move between accounts</button></li>
                </ul>
                @error('kind')<div class="alert alert-danger">{{ $message }}</div>@enderror
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="kind-capital">
                        <form method="POST" action="{{ route('money.store') }}">
                            @csrf
                            <input type="hidden" name="kind" value="capital">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4"><label class="form-label">Amount (KSh) *</label>
                                    <input type="number" step="0.01" min="0.01" class="form-control" name="amount" required></div>
                                <div class="col-md-4"><label class="form-label">Into *</label>
                                    <select class="form-select" name="to_account_id">@include('partials.accounting.money-options')</select></div>
                                <div class="col-md-4"><label class="form-label">Date *</label>
                                    <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required></div>
                                <div class="col-md-9"><label class="form-label">Note</label>
                                    <input type="text" class="form-control" name="memo" placeholder="e.g. Added float for the week"></div>
                                <div class="col-md-3"><button class="btn btn-primary w-100">Record</button></div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="kind-drawing">
                        <form method="POST" action="{{ route('money.store') }}">
                            @csrf
                            <input type="hidden" name="kind" value="drawing">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4"><label class="form-label">Amount (KSh) *</label>
                                    <input type="number" step="0.01" min="0.01" class="form-control" name="amount" required></div>
                                <div class="col-md-4"><label class="form-label">From *</label>
                                    <select class="form-select" name="from_account_id">@include('partials.accounting.money-options')</select></div>
                                <div class="col-md-4"><label class="form-label">Date *</label>
                                    <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required></div>
                                <div class="col-md-9"><label class="form-label">Note</label>
                                    <input type="text" class="form-control" name="memo" placeholder="e.g. Owner withdrawal"></div>
                                <div class="col-md-3"><button class="btn btn-primary w-100">Record</button></div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="kind-transfer">
                        <form method="POST" action="{{ route('money.store') }}">
                            @csrf
                            <input type="hidden" name="kind" value="transfer">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3"><label class="form-label">From *</label>
                                    <select class="form-select" name="from_account_id">@include('partials.accounting.money-options')</select></div>
                                <div class="col-md-3"><label class="form-label">To *</label>
                                    <select class="form-select" name="to_account_id">@include('partials.accounting.money-options', ['name' => 'to_account_id'])</select></div>
                                <div class="col-md-3"><label class="form-label">Amount (KSh) *</label>
                                    <input type="number" step="0.01" min="0.01" class="form-control" name="amount" required></div>
                                <div class="col-md-3"><label class="form-label">Charge (KSh)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="charge" placeholder="e.g. M-Pesa fee"></div>
                                <div class="col-md-3"><label class="form-label">Date *</label>
                                    <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required></div>
                                <div class="col-md-6"><label class="form-label">Note</label>
                                    <input type="text" class="form-control" name="memo" placeholder="e.g. Banked cash sales"></div>
                                <div class="col-md-3"><button class="btn btn-primary w-100">Record</button></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card p-4 mb-4">
                <h5 class="mb-2">Import a statement</h5>
                <p class="text-muted text-sm">Upload an M-Pesa or bank CSV — we'll match it against your books automatically.</p>
                <form method="POST" action="{{ route('money.import') }}" enctype="multipart/form-data" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-md-4"><label class="form-label">Account</label>
                        <select class="form-select" name="account_id">@include('partials.accounting.money-options', ['name' => 'account_id'])</select></div>
                    <div class="col-md-5"><label class="form-label">CSV file</label>
                        <input type="file" class="form-control" name="statement" accept=".csv,.txt" required></div>
                    <div class="col-md-3"><button class="btn btn-outline-primary w-100">Import & match</button></div>
                </form>
                <a href="{{ route('money.reconcile') }}" class="text-sm">View unmatched statement lines →</a>
            </div>

            <div class="card p-4 mb-4">
                <h5 class="mb-2">Close your books</h5>
                <p class="text-muted text-sm">Block all changes to dates on or before this day — for example after filing taxes or an audit.</p>
                <form method="POST" action="{{ route('money.lock') }}" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label">Books closed until</label>
                        <input type="date" class="form-control" name="lock_date" max="{{ date('Y-m-d') }}" value="{{ $settings->lock_date?->toDateString() }}">
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-danger">Set lock date</button>
                        @if($settings->lock_date)<span class="text-sm text-muted ms-2">Currently closed through {{ $settings->lock_date->format('d M Y') }}</span>@endif
                    </div>
                </form>
            </div>

            <div class="card p-4">
                <h5 class="mb-3">Recent money movements</h5>
                @if($recent->isEmpty())
                    <p class="text-muted mb-0">No manual movements yet.</p>
                @else
                    <table class="table table-sm">
                        <thead><tr><th>Date</th><th>Entry</th><th>Description</th><th class="text-end">Amount</th></tr></thead>
                        <tbody>
                            @foreach($recent as $entry)
                            <tr>
                                <td>{{ $entry->posting_date->format('d M Y') }}</td>
                                <td>{{ $entry->entry_number }}</td>
                                <td>{{ $entry->description }}</td>
                                <td class="text-end">{{ number_format((float) $entry->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

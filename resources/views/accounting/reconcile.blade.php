@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="card mb-4"><div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div><h5 class="mb-0">Reconcile — {{ $account->name }}</h5>
            <p class="text-sm text-muted mb-0">Statement lines not yet found in your books.</p></div>
        <a href="{{ route('money.index') }}" class="btn btn-sm btn-outline-secondary">Back to Money</a>
    </div></div>

    <div class="card p-4 mb-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6"><label class="form-label">Account</label>
                <select name="account_id" class="form-select">
                    @foreach($accounts as $a)<option value="{{ $a->id }}" @selected($a->id === $account->id)>{{ $a->name }}</option>@endforeach
                </select></div>
            <div class="col-md-6"><button class="btn btn-primary">Switch</button></div>
        </form>
    </div>

    <div class="card p-4">
        <table class="table table-sm">
            <thead><tr><th>Date</th><th>Reference</th><th>Details</th><th class="text-end">Amount</th></tr></thead>
            <tbody>
                @forelse($statements as $sl)
                <tr>
                    <td>{{ $sl->txn_date->format('d M Y') }}</td>
                    <td>{{ $sl->reference }}</td>
                    <td>{{ $sl->memo }}</td>
                    <td class="text-end {{ $sl->amount >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($sl->amount, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-muted">Everything on the statement is matched. ✓</td></tr>
                @endforelse
            </tbody>
        </table>
        <p class="text-muted text-sm mb-0">Tip: a line here usually means a payment you received that was never recorded in Shopybook, or a bank/M-Pesa charge to record on the Money page.</p>
    </div>
</div>
@endsection

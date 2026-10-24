@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center"><div class="col-lg-9">
        <h2 class="fw-bold mb-1" style="color:#7b2e2e;">Review Your Rebuilt Books</h2>
        <p class="text-muted mb-3">This is a preview — nothing is official until you approve it.</p>

        @if(!empty($warnings))
        <div class="alert alert-warning">
            <strong>Things to check:</strong>
            <ul class="mb-0">@foreach($warnings as $w)<li>{{ $w }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="card p-4 mb-4">
            <h5 class="mb-3">Where your books stand</h5>
            <table class="table table-sm">
                <thead><tr><th>Code</th><th>Account</th><th class="text-end">Balance</th></tr></thead>
                <tbody>
                @forelse($balances as $b)
                    <tr><td>{{ $b['code'] }}</td><td>{{ $b['name'] }}</td><td class="text-end">{{ number_format($b['balance'], 2) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="text-muted">No history found to rebuild.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card p-4 mb-4">
            <h5 class="mb-3">Last step — what do you actually have today?</h5>
            <p class="text-muted text-sm">Past records can't tell us your real cash. Enter what's truly in each account right now.</p>
            <form method="POST" action="{{ route('accounting.setup.commit') }}">
                @csrf
                <div class="row g-2 mb-3">
                    @foreach($moneyAccounts as $account)
                    <div class="col-md-4">
                        <label class="form-label">{{ $account->name }} (KSh)</label>
                        <input type="number" step="0.01" min="0" class="form-control" name="opening[{{ $account->id }}]" placeholder="0.00">
                    </div>
                    @endforeach
                </div>
                <button class="btn btn-success">Approve — keep these books</button>
            </form>
            <form method="POST" action="{{ route('accounting.setup.discard') }}" class="mt-2" onsubmit="return confirm('Discard the draft? Nothing was recorded.');">
                @csrf
                <button class="btn btn-outline-danger">Discard draft</button>
            </form>
        </div>
    </div></div>
</div>
@endsection

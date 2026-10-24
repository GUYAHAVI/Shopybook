@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center"><div class="col-lg-9">
        <h2 class="fw-bold mb-1" style="color:#7b2e2e;">Set Up Your Books</h2>
        <p class="text-muted mb-4">One-time step. After this, every sale, payment and expense updates your books automatically.</p>

        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card p-4 h-100">
                    <h5>Rebuild my history</h5>
                    <p class="text-muted">We'll replay all your past sales, payments, stock and expenses into a draft set of books you can review — nothing becomes official until you approve it.</p>
                    <form method="POST" action="{{ route('accounting.setup.backfill') }}">
                        @csrf
                        <button class="btn btn-primary">Rebuild my books</button>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-4 h-100">
                    <h5>Start fresh</h5>
                    <p class="text-muted">Pick a starting date and tell us what you had in the drawer, on M-Pesa, and in the bank. Debts and stock you have today are picked up automatically.</p>
                    <form method="POST" action="{{ route('accounting.setup.open') }}">
                        @csrf
                        <div class="mb-2"><label class="form-label">Books start from *</label>
                            <input type="date" class="form-control" name="ledger_start_date" value="{{ old('ledger_start_date', date('Y-m-01')) }}" max="{{ date('Y-m-d') }}" required></div>
                        <div class="row g-2">
                            <div class="col-6"><label class="form-label">Cash (KSh)</label><input type="number" step="0.01" min="0" class="form-control" name="cash" value="{{ old('cash') }}"></div>
                            <div class="col-6"><label class="form-label">M-Pesa (KSh)</label><input type="number" step="0.01" min="0" class="form-control" name="mpesa" value="{{ old('mpesa') }}"></div>
                            <div class="col-6"><label class="form-label">Bank (KSh)</label><input type="number" step="0.01" min="0" class="form-control" name="bank" value="{{ old('bank') }}"></div>
                            <div class="col-6"><label class="form-label">Card/PayPal (KSh)</label><input type="number" step="0.01" min="0" class="form-control" name="card" value="{{ old('card') }}"></div>
                        </div>
                        <button class="btn btn-success mt-3">Start my books</button>
                    </form>
                </div>
            </div>
        </div>
    </div></div>
</div>
@endsection

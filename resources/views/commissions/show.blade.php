@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h2 class="fw-bold mb-4" style="color:#020258;">Commission Payout Details</h2>
                <dl class="row">
                    <dt class="col-sm-4">Staff</dt>
                    <dd class="col-sm-8">{{ $commission->staff->name ?? '-' }}</dd>
                    <dt class="col-sm-4">Amount</dt>
                    <dd class="col-sm-8">KSh {{ number_format($commission->amount,2) }}</dd>
                    <dt class="col-sm-4">Paid At</dt>
                    <dd class="col-sm-8">{{ $commission->paid_at ? \Carbon\Carbon::parse($commission->paid_at)->format('Y-m-d H:i') : '-' }}</dd>
                    <dt class="col-sm-4">Notes</dt>
                    <dd class="col-sm-8">{{ $commission->notes }}</dd>
                </dl>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('commissions.edit', $commission) }}" class="btn btn-primary me-2">Edit</a>
                    <a href="{{ route('commissions.index') }}" class="btn btn-outline-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
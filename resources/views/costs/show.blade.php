@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h2 class="fw-bold mb-4" style="color:#020258;">Cost Details</h2>
                <dl class="row">
                    <dt class="col-sm-4">Type</dt>
                    <dd class="col-sm-8">{{ ucfirst($cost->type) }}</dd>
                    <dt class="col-sm-4">Amount</dt>
                    <dd class="col-sm-8">KSh {{ number_format($cost->amount,2) }}</dd>
                    <dt class="col-sm-4">Description</dt>
                    <dd class="col-sm-8">{{ $cost->description }}</dd>
                    <dt class="col-sm-4">Date</dt>
                    <dd class="col-sm-8">{{ $cost->date }}</dd>
                </dl>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('costs.edit', $cost) }}" class="btn btn-primary me-2">Edit</a>
                    <a href="{{ route('costs.index') }}" class="btn btn-outline-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">New Purchase Order</h1>
            <p class="text-muted">Order stock from a supplier</p>
        </div>
        <a href="{{ route('purchase-orders.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Purchase Orders
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('purchase-orders.store') }}" method="POST">
                @csrf
                @include('purchase-orders.partials.form')

                <div class="d-flex justify-content-between align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="send" value="1" id="sendCheck">
                        <label class="form-check-label" for="sendCheck">Mark as sent to supplier</label>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Purchase Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

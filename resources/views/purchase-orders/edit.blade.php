@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit {{ $po->po_number }}</h1>
            <p class="text-muted">Draft purchase order — {{ $po->supplier->name ?? 'no supplier' }}</p>
        </div>
        <a href="{{ route('purchase-orders.show', $po) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Order
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('purchase-orders.update', $po) }}" method="POST">
                @csrf
                @method('PUT')
                @include('purchase-orders.partials.form')

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color:#020258;">Costs</h2>
        <a href="{{ route('costs.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add Cost</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card p-3">
        <table class="table table-hover align-middle">
            <thead>
                <tr style="color:#020258;">
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($costs as $cost)
                <tr>
                    <td>{{ ucfirst($cost->type) }}</td>
                    <td>KSh {{ number_format($cost->amount,2) }}</td>
                    <td>{{ $cost->description }}</td>
                    <td>{{ $cost->date }}</td>
                    <td>
                        <a href="{{ route('costs.edit', $cost) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('costs.destroy', $cost) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this cost?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">No costs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection 
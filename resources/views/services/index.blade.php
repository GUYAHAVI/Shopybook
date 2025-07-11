@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color:#020258;">Services</h2>
        <a href="{{ route('services.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add Service</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card p-3">
        <table class="table table-hover align-middle">
            <thead>
                <tr style="color:#020258;">
                    <th>Name</th>
                    <th>Price</th>
                    <th>Duration</th>
                    <th>Commission (%)</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                <tr>
                    <td>{{ $service->name }}</td>
                    <td>KSh {{ number_format($service->price,2) }}</td>
                    <td>{{ $service->duration ? $service->duration.' min' : '-' }}</td>
                    <td>{{ $service->commission_rate ?? '-' }}</td>
                    <td>{{ $service->description }}</td>
                    <td>
                        <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('services.destroy', $service) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this service?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">No services found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection 
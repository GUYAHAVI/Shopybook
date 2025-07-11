@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color:#020258;">Service Records</h2>
        <a href="{{ route('service-records.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Record Service</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card p-3">
        <table class="table table-hover align-middle">
            <thead>
                <tr style="color:#020258;">
                    <th>Service</th>
                    <th>Staff</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Commission</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                <tr>
                    <td>{{ $record->service->name ?? '-' }}</td>
                    <td>{{ $record->staff->name ?? '-' }}</td>
                    <td>{{ $record->customer->name ?? '-' }}</td>
                    <td>KSh {{ number_format($record->amount,2) }}</td>
                    <td>KSh {{ number_format($record->commission,2) }}</td>
                    <td>{{ $record->performed_at ? $record->performed_at->format('Y-m-d H:i') : '-' }}</td>
                    <td>
                        <a href="{{ route('service-records.edit', $record) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('service-records.destroy', $record) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this record?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center">No service records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection 
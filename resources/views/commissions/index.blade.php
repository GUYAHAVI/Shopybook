@extends('layouts.dash')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color:#020258;">Commissions</h2>
        <a href="{{ route('commissions.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add Payout</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card p-3">
        <table class="table table-hover align-middle">
            <thead>
                <tr style="color:#020258;">
                    <th>Staff</th>
                    <th>Amount</th>
                    <th>Paid At</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payouts as $payout)
                <tr>
                    <td>{{ $payout->staff->name ?? '-' }}</td>
                    <td>KSh {{ number_format($payout->amount,2) }}</td>
                    <td>{{ $payout->paid_at ? \Carbon\Carbon::parse($payout->paid_at)->format('Y-m-d H:i') : '-' }}</td>
                    <td>{{ $payout->notes }}</td>
                    <td>
                        <a href="{{ route('commissions.edit', $payout) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('commissions.destroy', $payout) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this payout?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">No commission payouts found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection 
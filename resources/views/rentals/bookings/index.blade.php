@extends('layouts.dash')

@section('title', 'Booking Requests')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="mb-1">Booking Requests</h4>
            <p class="text-muted mb-0">People who booked your listed units from the public houses page or your website.</p>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="mb-3">
        <form method="GET" class="d-flex gap-2">
            <select name="status" class="form-select" style="width:auto;" onchange="this.form.submit()">
                <option value="">All statuses</option>
                @foreach(['new','contacted','confirmed','declined'] as $st)
                    <option value="{{ $st }}" {{ request('status')===$st?'selected':'' }}>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Booker</th>
                        <th>Unit</th>
                        <th>Move-in</th>
                        <th>Message</th>
                        <th>Requested</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($bookings as $b)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $b->name }}</div>
                            <small class="text-muted">{{ $b->phone }}{{ $b->email ? ' · '.$b->email : '' }}</small>
                        </td>
                        <td>
                            <div>{{ $b->unit?->label }}</div>
                            <small class="text-muted">{{ $b->unit?->property?->name }}</small>
                        </td>
                        <td>{{ $b->preferred_move_in?->format('d M Y') ?? '—' }}</td>
                        <td><small class="text-muted">{{ \Illuminate\Support\Str::limit($b->message, 60) }}</small></td>
                        <td><small class="text-muted">{{ $b->created_at->diffForHumans() }}</small></td>
                        <td>
                            <span class="badge {{ ['new'=>'bg-primary','contacted'=>'bg-info text-dark','confirmed'=>'bg-success','declined'=>'bg-secondary'][$b->status] }}">
                                {{ ucfirst($b->status) }}
                            </span>
                        </td>
                        <td>
                            <form method="POST" action="{{ route('rentals.bookings.update', $b) }}" class="d-flex gap-1">
                                @csrf @method('PATCH')
                                <select name="status" class="form-select form-select-sm" style="width:110px;">
                                    @foreach(['new','contacted','confirmed','declined'] as $st)
                                        <option value="{{ $st }}" {{ $b->status===$st?'selected':'' }}>{{ ucfirst($st) }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-outline-primary">Set</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">No booking requests yet. They appear here when someone books a listed unit.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $bookings->links() }}</div>
</div>
@endsection

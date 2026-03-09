@extends('layouts.app')

@section('title', 'Admin – Testimonials')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-star text-warning me-2"></i>Testimonials</h2>
        <span class="badge bg-danger fs-6">{{ $pending->total() }} pending</span>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── PENDING ── --}}
    <h5 class="text-danger mb-3">Pending Review ({{ $pending->total() }})</h5>
    @if($pending->isEmpty())
        <p class="text-muted">No pending testimonials.</p>
    @else
    <div class="table-responsive mb-5">
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>#</th><th>Type / Business</th><th>Name</th><th>Role</th>
                    <th>Quote</th><th>Rating</th><th>Submitted</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pending as $t)
                <tr>
                    <td>{{ $t->id }}</td>
                    <td>
                        <span class="badge {{ $t->type === 'platform' ? 'bg-primary' : 'bg-info text-dark' }}">
                            {{ $t->type }}
                        </span>
                        @if($t->business_id)
                        <small class="d-block text-muted">Biz #{{ $t->business_id }}</small>
                        @endif
                    </td>
                    <td>{{ $t->name }}</td>
                    <td>{{ $t->role ?? '—' }}</td>
                    <td style="max-width:300px;">{{ Str::limit($t->quote, 120) }}</td>
                    <td>{{ $t->rating }}/5</td>
                    <td><small>{{ $t->created_at->diffForHumans() }}</small></td>
                    <td>
                        <form action="{{ route('admin.testimonials.approve', $t) }}" method="POST" class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm">Approve</button>
                        </form>
                        <form action="{{ route('admin.testimonials.destroy', $t) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Delete this testimonial?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $pending->links() }}
    @endif

    {{-- ── APPROVED ── --}}
    <h5 class="text-success mb-3">Approved ({{ $approved->total() }})</h5>
    @if($approved->isEmpty())
        <p class="text-muted">No approved testimonials yet.</p>
    @else
    <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th><th>Type</th><th>Name</th><th>Role</th>
                    <th>Quote</th><th>Rating</th><th>Approved</th><th>Delete</th>
                </tr>
            </thead>
            <tbody>
                @foreach($approved as $t)
                <tr>
                    <td>{{ $t->id }}</td>
                    <td><span class="badge {{ $t->type === 'platform' ? 'bg-primary' : 'bg-info text-dark' }}">{{ $t->type }}</span></td>
                    <td>{{ $t->name }}</td>
                    <td>{{ $t->role ?? '—' }}</td>
                    <td style="max-width:300px;">{{ Str::limit($t->quote, 120) }}</td>
                    <td>{{ $t->rating }}/5</td>
                    <td><small>{{ $t->approved_at?->diffForHumans() ?? '—' }}</small></td>
                    <td>
                        <form action="{{ route('admin.testimonials.destroy', $t) }}" method="POST"
                              onsubmit="return confirm('Delete this testimonial?');">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $approved->links() }}
    @endif
</div>
@endsection

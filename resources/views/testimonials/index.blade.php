@extends('layouts.dash')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: var(--text-primary);">
                <i class="fas fa-star me-2" style="color:#f59e0b;"></i>Customer Reviews
            </h2>
            <p class="mb-0" style="color: var(--text-muted); font-size:.9rem;">
                Manage reviews submitted on your website. Approved reviews appear on your website automatically.
            </p>
        </div>
        <a href="{{ route('website-builder.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-globe me-1"></i> View Website
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Status Tabs --}}
    <div class="mb-4">
        <ul class="nav nav-tabs" style="border-bottom: 2px solid var(--border-color);">
            <li class="nav-item">
                <a class="nav-link {{ $filter === 'pending' ? 'active' : '' }}"
                   href="{{ route('testimonials.owner.index', ['filter' => 'pending']) }}">
                    <i class="fas fa-clock me-1"></i> Pending
                    @if($counts['pending'] > 0)
                        <span class="badge bg-warning text-dark ms-1">{{ $counts['pending'] }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $filter === 'approved' ? 'active' : '' }}"
                   href="{{ route('testimonials.owner.index', ['filter' => 'approved']) }}">
                    <i class="fas fa-check-circle me-1"></i> Approved
                    <span class="badge bg-success ms-1">{{ $counts['approved'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $filter === 'rejected' ? 'active' : '' }}"
                   href="{{ route('testimonials.owner.index', ['filter' => 'rejected']) }}">
                    <i class="fas fa-times-circle me-1"></i> Rejected
                    @if($counts['rejected'] > 0)
                        <span class="badge bg-secondary ms-1">{{ $counts['rejected'] }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $filter === 'deleted' ? 'active' : '' }}"
                   href="{{ route('testimonials.owner.index', ['filter' => 'deleted']) }}">
                    <i class="fas fa-trash me-1"></i> Trash
                    @if($counts['deleted'] > 0)
                        <span class="badge bg-danger ms-1">{{ $counts['deleted'] }}</span>
                    @endif
                </a>
            </li>
        </ul>
    </div>

    {{-- Info bar for current tab --}}
    @if($filter === 'pending')
        <div class="alert alert-info py-2 mb-3" style="font-size:.88rem;">
            <i class="fas fa-info-circle me-1"></i>
            <strong>Pending reviews</strong> are waiting for your approval. Approve to publish on your website, reject to hide them.
        </div>
    @elseif($filter === 'approved')
        <div class="alert alert-success py-2 mb-3" style="font-size:.88rem;">
            <i class="fas fa-check-circle me-1"></i>
            <strong>Approved reviews</strong> are currently displayed on your website.
        </div>
    @elseif($filter === 'rejected')
        <div class="alert alert-secondary py-2 mb-3" style="font-size:.88rem;">
            <i class="fas fa-eye-slash me-1"></i>
            <strong>Rejected reviews</strong> are hidden from your website. You can approve them anytime.
        </div>
    @elseif($filter === 'deleted')
        <div class="alert alert-warning py-2 mb-3" style="font-size:.88rem;">
            <i class="fas fa-trash me-1"></i>
            <strong>Trashed reviews</strong> — restore to bring them back, or permanently delete them.
        </div>
    @endif

    {{-- Reviews List --}}
    @if($testimonials->isEmpty())
        <div class="card text-center py-5" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-body">
                <i class="fas fa-star" style="font-size:3rem; color:#ddd;"></i>
                <p class="mt-3 mb-0" style="color: var(--text-muted);">
                    @if($filter === 'pending') No pending reviews.
                    @elseif($filter === 'approved') No approved reviews yet.
                    @elseif($filter === 'rejected') No rejected reviews.
                    @else No trashed reviews.
                    @endif
                </p>
                @if($filter !== 'pending')
                    <a href="{{ route('testimonials.owner.index', ['filter' => 'pending']) }}" class="btn btn-sm btn-outline-primary mt-3">
                        View Pending Reviews
                    </a>
                @endif
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($testimonials as $review)
            <div class="col-12" id="review-{{ $review->id }}">
                <div class="card shadow-sm" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            {{-- Reviewer Info --}}
                            <div class="d-flex gap-3 align-items-start flex-grow-1">
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:44px; height:44px; background: linear-gradient(135deg,#13e8e9,#020258); color:white; font-weight:bold; font-size:1.2rem;">
                                    {{ strtoupper(substr($review->name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <strong style="color: var(--text-primary);">{{ $review->name }}</strong>
                                        @if($review->role)
                                            <span style="color: var(--text-muted); font-size:.85rem;">· {{ $review->role }}</span>
                                        @endif
                                        <span class="badge
                                            @if($review->status === 'approved') bg-success
                                            @elseif($review->status === 'rejected') bg-secondary
                                            @else bg-warning text-dark
                                            @endif">
                                            {{ ucfirst($filter === 'deleted' ? 'deleted' : $review->status) }}
                                        </span>
                                    </div>
                                    {{-- Stars --}}
                                    <div class="mb-2" style="color:#f59e0b; font-size:1.1rem; letter-spacing:1px;">
                                        @for($i = 1; $i <= 5; $i++)
                                            {{ $i <= $review->rating ? '★' : '☆' }}
                                        @endfor
                                        <span style="color: var(--text-muted); font-size:.8rem; margin-left:4px;">{{ $review->rating }}/5</span>
                                    </div>
                                    {{-- Quote --}}
                                    <p class="mb-1" style="color: var(--text-secondary); font-style:italic;">
                                        "{{ $review->quote }}"
                                    </p>
                                    <small style="color: var(--text-muted);">
                                        @if($filter === 'deleted')
                                            Deleted {{ $review->deleted_at->diffForHumans() }}
                                        @else
                                            Submitted {{ $review->created_at->diffForHumans() }}
                                        @endif
                                    </small>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex gap-2 flex-shrink-0 ms-3 flex-wrap">
                                @if($filter === 'deleted')
                                    {{-- Restore --}}
                                    <form method="POST" action="{{ route('testimonials.owner.restore', $review->id) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-undo me-1"></i>Restore
                                        </button>
                                    </form>
                                    {{-- Permanent delete --}}
                                    <button type="button" class="btn btn-sm btn-danger"
                                            onclick="confirmForceDelete({{ $review->id }})">
                                        <i class="fas fa-trash-alt me-1"></i>Delete Forever
                                    </button>
                                @else
                                    @if($review->status !== 'approved')
                                        <form method="POST" action="{{ route('testimonials.owner.approve', $review->id) }}" class="d-inline action-form">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check me-1"></i>Approve
                                            </button>
                                        </form>
                                    @endif
                                    @if($review->status !== 'rejected')
                                        <form method="POST" action="{{ route('testimonials.owner.reject', $review->id) }}" class="d-inline action-form">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-times me-1"></i>Reject
                                            </button>
                                        </form>
                                    @endif
                                    {{-- Soft delete --}}
                                    <form method="POST" action="{{ route('testimonials.owner.delete', $review->id) }}" class="d-inline action-form">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Move this review to trash?')">
                                            <i class="fas fa-trash me-1"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $testimonials->links() }}
        </div>
    @endif

</div>

{{-- Force Delete Confirmation Modal --}}
<div class="modal fade" id="forceDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title" style="color: var(--text-primary);">Permanently Delete Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="color: var(--text-secondary);">
                <p>This will <strong>permanently delete</strong> the review. This action cannot be undone.</p>
                <p class="mb-0">Are you sure?</p>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="forceDeleteForm" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i>Delete Forever
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmForceDelete(id) {
    document.getElementById('forceDeleteForm').action = `/testimonials/${id}/force-delete`;
    const modal = new bootstrap.Modal(document.getElementById('forceDeleteModal'));
    modal.show();
}

// Show a brief success toast when an action form submits via AJAX
document.querySelectorAll('.action-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(new FormData(this)),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const card = document.getElementById('review-' + extractId(this.action));
                if (card) {
                    card.style.transition = 'opacity .3s';
                    card.style.opacity = '0';
                    setTimeout(() => card.remove(), 350);
                }
                // Update badge counts
                refreshCounts();
            }
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    });
});

function extractId(url) {
    const parts = url.split('/');
    return parts[parts.indexOf('testimonials') + 1];
}

function refreshCounts() {
    // Reload page after a short delay so counts update
    setTimeout(() => location.reload(), 400);
}
</script>
@endpush
@endsection

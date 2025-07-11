@extends('layouts.dash')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Promotions</h1>
        <a href="{{ route('marketing.promotions.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Create Promotion
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Promotions Cards -->
    <div class="row">
        @forelse($promotions as $promotion)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        {{ $promotion->discount_type === 'percentage' ? 'Percentage Discount' : 'Fixed Amount' }}
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $promotion->name }}</div>
                                    <div class="text-sm text-gray-600 mb-2">{{ $promotion->code }}</div>
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-{{ $promotion->isActive() ? 'success' : 'secondary' }}">
                                        {{ $promotion->isActive() ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="text-sm text-gray-600 mb-2">
                                <strong>{{ $promotion->discount_value }}{{ $promotion->discount_type === 'percentage' ? '%' : ' ₦' }}</strong> off
                                @if($promotion->minimum_amount)
                                    on orders above ₦{{ number_format($promotion->minimum_amount) }}
                                @endif
                            </div>
                            
                            <div class="text-xs text-gray-500 mb-2">
                                <i class="fas fa-calendar"></i> {{ $promotion->start_date->format('M d') }} - {{ $promotion->end_date->format('M d, Y') }}
                            </div>
                            
                            @if($promotion->usage_limit)
                            <div class="text-xs text-gray-500 mb-2">
                                <i class="fas fa-users"></i> {{ $promotion->used_count }}/{{ $promotion->usage_limit }} used
                            </div>
                            @endif
                            
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('marketing.promotions.edit', $promotion) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger" onclick="deletePromotion({{ $promotion->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-tag fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">No promotions yet</h5>
                    <p class="text-gray-400">Create your first promotion to start attracting customers</p>
                    <a href="{{ route('marketing.promotions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Promotion
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($promotions->hasPages())
    <div class="d-flex justify-content-center">
        {{ $promotions->links() }}
    </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Promotion</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this promotion? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function deletePromotion(promotionId) {
    if (confirm('Are you sure you want to delete this promotion?')) {
        fetch(`/marketing/promotions/${promotionId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting promotion');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting promotion');
        });
    }
}
</script>
@endpush

<style>
body, .container-fluid, .card, .main-content, .content {
    background: #fff !important;
    color: #020258 !important;
}
.btn-primary {
    background: #020258 !important;
    color: #fff !important;
    border: 2px solid #13e8e9 !important;
}
.btn-primary:hover {
    background: #13e8e9 !important;
    color: #020258 !important;
    border: 2px solid #020258 !important;
}
.form-control {
    background: #f8f9fa !important;
    color: #020258 !important;
    border: 2px solid #13e8e9 !important;
}
.form-control:focus {
    border-color: #020258 !important;
    box-shadow: 0 0 0 3px rgba(19, 232, 233, 0.1) !important;
}
.card-header {
    background: #f8f9fa !important;
    color: #020258 !important;
    border-bottom: 1px solid #13e8e9 !important;
}
</style> 
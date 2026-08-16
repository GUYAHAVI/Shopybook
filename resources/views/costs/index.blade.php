@extends('layouts.dash')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold" style="color:#7b2e2e;">Costs</h2>
        <a href="{{ route('costs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Cost
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if($costs && count($costs) > 0)
                <!-- Desktop Table View -->
                <div class="d-none d-md-block">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr style="color:#7b2e2e;">
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($costs as $cost)
                                <tr>
                                    <td>{{ ucfirst($cost->type ?? 'unknown') }}</td>
                                    <td>KSh {{ number_format($cost->amount ?? 0, 2) }}</td>
                                    <td>{{ $cost->description ?? '-' }}</td>
                                    <td>{{ $cost->date ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('costs.edit', $cost) }}" 
                                           class="btn btn-sm btn-outline-primary me-1" 
                                           title="Edit Cost">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('costs.destroy', $cost) }}" method="POST" class="d-inline">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Delete this cost?')"
                                                    title="Delete Cost">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="d-block d-md-none">
                    @foreach($costs as $cost)
                        <div class="mobile-card card border-0 shadow-sm mb-3">
                            <div class="card-body p-3">
                                <!-- Header Section -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1 text-primary mobile-card-title">
                                            {{ ucfirst($cost->type ?? 'unknown') }}
                                        </h6>
                                    </div>
                                    <span class="badge bg-danger mobile-card-badge">
                                        KSh {{ number_format($cost->amount ?? 0, 2) }}
                                    </span>
                                </div>

                                <!-- Details Section - Vertical Layout -->
                                <div class="mobile-card-details">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <div class="detail-item">
                                                <small class="text-muted fw-medium">Description</small>
                                                <div class="detail-value text-break">{{ $cost->description ?? '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="detail-item">
                                                <small class="text-muted fw-medium">Date</small>
                                                <div class="detail-value">{{ $cost->date ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions Section -->
                                <div class="mobile-card-actions mt-3 pt-3 border-top">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('costs.edit', $cost) }}" 
                                           class="btn btn-outline-primary btn-sm flex-fill mobile-action-btn">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>
                                        <form action="{{ route('costs.destroy', $cost) }}" method="POST" class="flex-fill">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger btn-sm w-100 mobile-action-btn"
                                                    onclick="return confirm('Delete this cost?')">
                                                <i class="fas fa-trash me-1"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                    <h5>No Costs Found</h5>
                    <p class="text-muted">Record your first cost to get started.</p>
                    <a href="{{ route('costs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add Cost
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Enhanced Mobile Card Styles */
.mobile-card {
    transition: all 0.2s ease;
    border-radius: 12px;
    background: #fff;
}

.mobile-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.mobile-card-title {
    font-size: 1rem;
    color: #495057;
    margin-bottom: 0.25rem;
}

.mobile-card-badge {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    font-weight: 600;
}

.mobile-card-details {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 0.75rem;
    margin: 0.75rem 0;
}

.detail-item {
    margin-bottom: 0.5rem;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.detail-item small {
    display: block;
    font-size: 0.75rem;
    margin-bottom: 0.25rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value {
    font-size: 0.875rem;
    color: #212529;
    font-weight: 500;
    line-height: 1.4;
    word-break: break-word;
}

.mobile-card-actions {
    border-top: 1px solid #e9ecef;
}

.mobile-action-btn {
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-weight: 500;
    transition: all 0.2s ease;
}

.mobile-action-btn:hover {
    transform: translateY(-1px);
}

/* Mobile Responsive Enhancements */
@media (max-width: 576px) {
    .mobile-card {
        margin: 0 -0.5rem 0.75rem -0.5rem;
        border-radius: 8px;
    }
    
    .mobile-card-title {
        font-size: 0.9rem;
    }
    
    .mobile-card-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .detail-value {
        font-size: 0.8rem;
    }
    
    .mobile-action-btn {
        font-size: 0.75rem;
        padding: 0.4rem 0.6rem;
    }
}

@media (max-width: 375px) {
    .mobile-card-title {
        font-size: 0.85rem;
    }
    
    .mobile-card-badge {
        font-size: 0.7rem;
    }
    
    .detail-value {
        font-size: 0.75rem;
    }
    
    .mobile-action-btn {
        font-size: 0.7rem;
    }
}
</style>

@endsection 
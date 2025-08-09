@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-robot text-primary"></i>
                        AI Business Intelligence
                    </h4>
                    <p class="card-subtitle text-muted">
                        Automated insights and recommendations for your business
                    </p>
                </div>
                <div class="card-body">
                    <!-- Learning Status -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-brain text-info"></i>
                                        Learning Status
                                    </h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>AI Learning:</span>
                                        <span class="badge badge-{{ $learningStatus['learning_active'] ? 'success' : 'warning' }}">
                                            {{ $learningStatus['learning_active'] ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    @if($learningStatus['last_learned'])
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span>Last Updated:</span>
                                        <small class="text-muted">{{ $learningStatus['last_learned']->diffForHumans() }}</small>
                                    </div>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span>Insights Collected:</span>
                                        <span class="badge badge-info">{{ $learningStatus['insights_count'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-chart-line text-success"></i>
                                        Performance Summary
                                    </h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Total Orders:</span>
                                        <span class="badge badge-primary">{{ $performance['total_orders'] ?? 0 }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span>Revenue:</span>
                                        <span class="badge badge-success">${{ number_format($performance['revenue'] ?? 0) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span>Customers:</span>
                                        <span class="badge badge-info">{{ $performance['total_customers'] ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AI Advice -->
                    <div class="row">
                        <div class="col-12">
                            <h5 class="mb-3">
                                <i class="fas fa-lightbulb text-warning"></i>
                                AI Recommendations
                                <span class="badge badge-primary">{{ count($advice) }}</span>
                            </h5>
                            
                            @if(count($advice) > 0)
                                @foreach($advice as $item)
                                <div class="card mb-3 border-{{ $item->priority_color }}">
                                    <div class="card-header bg-{{ $item->priority_color }} text-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">
                                                <i class="fas fa-{{ $item->priority_icon }}"></i>
                                                {{ $item->title }}
                                            </h6>
                                            <span class="badge badge-light">{{ ucfirst($item->priority) }}</span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <p class="card-text">{{ $item->description }}</p>
                                        
                                        @if($item->action_items)
                                        <div class="mt-3">
                                            <h6 class="text-muted">Action Items:</h6>
                                            <ul class="list-unstyled">
                                                @foreach($item->action_items as $action)
                                                <li class="mb-2">
                                                    <i class="fas fa-check-circle text-success mr-2"></i>
                                                    {{ $action }}
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
                                        
                                        <div class="mt-3">
                                            <strong>Expected Impact:</strong>
                                            <span class="text-success">{{ $item->expected_impact }}</span>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                Generated {{ $item->created_at->diffForHumans() }}
                                            </small>
                                            @if(!$item->is_read)
                                            <span class="badge badge-danger ml-2">New</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-robot fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No AI advice available yet</h5>
                                    <p class="text-muted">
                                        The AI system is learning about your business. 
                                        Check back soon for personalized recommendations.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Learning Settings -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-cog text-secondary"></i>
                                        Learning Settings
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('business.ai-settings.update', $business) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" name="automated_learning_enabled" 
                                                               class="form-check-input" 
                                                               {{ $learningStatus['settings']->automated_learning_enabled ? 'checked' : '' }}>
                                                        Enable Automated Learning
                                                    </label>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" name="competitor_analysis_enabled" 
                                                               class="form-check-input"
                                                               {{ $learningStatus['settings']->competitor_analysis_enabled ? 'checked' : '' }}>
                                                        Competitor Analysis
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" name="market_trends_enabled" 
                                                               class="form-check-input"
                                                               {{ $learningStatus['settings']->market_trends_enabled ? 'checked' : '' }}>
                                                        Market Trends Learning
                                                    </label>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" name="social_media_learning_enabled" 
                                                               class="form-check-input"
                                                               {{ $learningStatus['settings']->social_media_learning_enabled ? 'checked' : '' }}>
                                                        Social Media Learning
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i>
                                            Update Settings
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Manual Learning Trigger -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-sync-alt text-info"></i>
                                        Manual Learning
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">
                                        Trigger immediate learning for your business to get fresh insights.
                                    </p>
                                    <form action="{{ route('business.ai-learning.trigger', $business) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-info">
                                            <i class="fas fa-sync-alt"></i>
                                            Trigger Learning Now
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mark Advice as Read Modal -->
<div class="modal fade" id="markReadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Mark as Read</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Mark this advice as read?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmMarkRead">Mark as Read</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-refresh every 5 minutes
    setInterval(function() {
        location.reload();
    }, 5 * 60 * 1000);
    
    // Mark advice as read
    $('.mark-read-btn').click(function() {
        var adviceId = $(this).data('advice-id');
        $('#confirmMarkRead').data('advice-id', adviceId);
        $('#markReadModal').modal('show');
    });
    
    $('#confirmMarkRead').click(function() {
        var adviceId = $(this).data('advice-id');
        
        $.ajax({
            url: '/business/ai-advice/' + adviceId + '/mark-read',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#markReadModal').modal('hide');
                location.reload();
            },
            error: function() {
                alert('Error marking advice as read');
            }
        });
    });
});
</script>
@endpush

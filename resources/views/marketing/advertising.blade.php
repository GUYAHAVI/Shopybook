@extends('layouts.dash')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Advertising Campaigns</h1>
        <a href="{{ route('marketing.advertising.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus fa-sm"></i> Create Campaign
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

    <!-- Campaigns Overview -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Active Campaigns
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $campaigns->where('status', 'active')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Budget
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₦{{ number_format($campaigns->sum('budget')) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Spent
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ₦{{ number_format($campaigns->sum('spent')) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Completed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $campaigns->where('status', 'completed')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Campaigns List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Campaigns</h6>
        </div>
        <div class="card-body">
            @forelse($campaigns as $campaign)
            <div class="card mb-3 border-left-{{ $campaign->status_color }}">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h6 class="card-title mb-1">{{ $campaign->name }}</h6>
                            <small class="text-muted">{{ ucfirst($campaign->platform) }}</small>
                        </div>
                        <div class="col-md-2">
                            <div class="text-sm">
                                <strong>Budget:</strong> ₦{{ number_format($campaign->budget) }}
                            </div>
                            <div class="text-sm text-muted">
                                <strong>Spent:</strong> ₦{{ number_format($campaign->spent) }}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="progress mb-1" style="height: 6px;">
                                <div class="progress-bar bg-{{ $campaign->status_color }}" 
                                     style="width: {{ $campaign->progress }}%"></div>
                            </div>
                            <small class="text-muted">{{ number_format($campaign->progress, 1) }}% used</small>
                        </div>
                        <div class="col-md-2">
                            <div class="text-sm">
                                <strong>Status:</strong>
                                <span class="badge badge-{{ $campaign->status_color }}">
                                    {{ ucfirst($campaign->status) }}
                                </span>
                            </div>
                            <div class="text-sm text-muted">
                                {{ $campaign->start_date->format('M d') }} - {{ $campaign->end_date->format('M d, Y') }}
                            </div>
                        </div>
                        <div class="col-md-3 text-right">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary" onclick="viewCampaign({{ $campaign->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-outline-success" onclick="editCampaign({{ $campaign->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="deleteCampaign({{ $campaign->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="fas fa-ad fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-500">No campaigns yet</h5>
                <p class="text-gray-400">Create your first advertising campaign to reach more customers</p>
                <a href="{{ route('marketing.advertising.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Campaign
                </a>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($campaigns->hasPages())
    <div class="d-flex justify-content-center">
        {{ $campaigns->links() }}
    </div>
    @endif
</div>

<!-- Campaign Details Modal -->
<div class="modal fade" id="campaignModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Campaign Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="campaignModalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function viewCampaign(campaignId) {
    // Load campaign details via AJAX
    fetch(`/marketing/advertising/${campaignId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('campaignModalBody').innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Campaign Information</h6>
                        <p><strong>Name:</strong> ${data.name}</p>
                        <p><strong>Platform:</strong> ${data.platform}</p>
                        <p><strong>Status:</strong> <span class="badge badge-${data.status_color}">${data.status}</span></p>
                        <p><strong>Budget:</strong> ₦${data.budget}</p>
                        <p><strong>Spent:</strong> ₦${data.spent}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Timeline</h6>
                        <p><strong>Start Date:</strong> ${data.start_date}</p>
                        <p><strong>End Date:</strong> ${data.end_date}</p>
                        <p><strong>Progress:</strong> ${data.progress}%</p>
                    </div>
                </div>
                <div class="mt-3">
                    <h6>Description</h6>
                    <p>${data.description || 'No description provided'}</p>
                </div>
                <div class="mt-3">
                    <h6>Target Audience</h6>
                    <p>${data.target_audience || 'No target audience specified'}</p>
                </div>
            `;
            $('#campaignModal').modal('show');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading campaign details');
        });
}

function editCampaign(campaignId) {
    window.location.href = `/marketing/advertising/${campaignId}/edit`;
}

function deleteCampaign(campaignId) {
    if (confirm('Are you sure you want to delete this campaign?')) {
        fetch(`/marketing/advertising/${campaignId}`, {
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
                alert('Error deleting campaign');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting campaign');
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
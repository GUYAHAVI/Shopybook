@extends('layouts.dash')

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Business Analysis</h1>
            <p class="text-muted">AI-powered insights and recommendations for your business</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="generateAnalysis('general')">
                <i class="fas fa-brain me-2"></i>Generate Analysis
            </button>
            <a href="{{ route('business.analysis.financial') }}" class="btn btn-primary">
                <i class="fas fa-chart-line me-2"></i>Financial Report
            </a>
        </div>
    </div>

    <!-- Analytics Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">KSh {{ $analytics['sales']['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Monthly Sales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">KSh {{ $analytics['sales']['monthly'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Products</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $analytics['products']['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Customers</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $analytics['customers']['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Analysis Section -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-robot me-2"></i>AI Business Analysis
                    </h6>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateAnalysis('general')">General</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateAnalysis('sales')">Sales</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateAnalysis('products')">Products</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateAnalysis('customers')">Customers</button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateAnalysis('financial')">Financial</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="analysisLoading" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Analyzing your business data...</p>
                    </div>
                    
                    <div id="analysisContent">
                        <div class="text-center py-4">
                            <i class="fas fa-chart-bar fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">Ready for Analysis</h5>
                            <p class="text-muted">Click "Generate Analysis" to get AI-powered insights about your business performance.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Average Order Value</span>
                            <span class="font-weight-bold">KSh {{ $analytics['sales']['average'] }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Orders</span>
                            <span class="font-weight-bold">{{ $analytics['sales']['orders'] }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Low Stock Items</span>
                            <span class="font-weight-bold text-warning">{{ $analytics['products']['low_stock'] }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">New Customers (This Month)</span>
                            <span class="font-weight-bold text-success">{{ $analytics['customers']['new'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analysis Tips -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-lightbulb me-2"></i>Analysis Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>Choose specific analysis types for targeted insights</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>Review recommendations regularly for best results</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>Compare monthly trends to track progress</small>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <small>Focus on actionable insights for growth</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateAnalysis(type) {
    const loadingDiv = document.getElementById('analysisLoading');
    const contentDiv = document.getElementById('analysisContent');
    
    // Show loading
    loadingDiv.style.display = 'block';
    contentDiv.innerHTML = '';
    
    // Make API call
    fetch(`{{ route('business.analysis.generate') }}?type=${type}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        loadingDiv.style.display = 'none';
        
        if (data.success) {
            // Format the analysis text
            const formattedAnalysis = data.analysis.replace(/\n/g, '<br>');
            
            contentDiv.innerHTML = `
                <div class="analysis-result">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Analysis Type: ${type.charAt(0).toUpperCase() + type.slice(1)}</h6>
                    </div>
                    <div class="analysis-content">
                        ${formattedAnalysis}
                    </div>
                </div>
            `;
        } else {
            contentDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Unable to generate analysis. Please try again.
                </div>
            `;
        }
    })
    .catch(error => {
        loadingDiv.style.display = 'none';
        contentDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error generating analysis. Please try again.
            </div>
        `;
        console.error('Error:', error);
    });
}
</script>

<style>
.analysis-content {
    line-height: 1.6;
    font-size: 0.95rem;
}

.analysis-content h1, .analysis-content h2, .analysis-content h3 {
    color: #4e73df;
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
}

.analysis-content ul, .analysis-content ol {
    padding-left: 1.5rem;
}

.analysis-content li {
    margin-bottom: 0.5rem;
}

.btn-group .btn {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
}
</style>
@endsection 
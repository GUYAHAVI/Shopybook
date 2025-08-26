@extends('layouts.app')

@section('title', 'Enhanced AI Business Analysis')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-robot mr-2"></i>
                    Enhanced AI Business Analysis
                    <small class="text-muted">Powered by Canadian MSME Model</small>
                </h1>
                <div class="page-subtitle">
                    Advanced business intelligence using AI trained on Canadian small business data
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ number_format($analytics['total_revenue'] ?? 0, 2) }}</h4>
                            <div class="text-white-75">Total Revenue</div>
                        </div>
                        <div class="ml-3">
                            <i class="fas fa-chart-line fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $analytics['total_orders'] ?? 0 }}</h4>
                            <div class="text-white-75">Total Orders</div>
                        </div>
                        <div class="ml-3">
                            <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $analytics['total_customers'] ?? 0 }}</h4>
                            <div class="text-white-75">Customers</div>
                        </div>
                        <div class="ml-3">
                            <i class="fas fa-users fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-gradient-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-0">{{ $analytics['total_products'] ?? 0 }}</h4>
                            <div class="text-white-75">Products</div>
                        </div>
                        <div class="ml-3">
                            <i class="fas fa-box fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Analysis Controls -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-brain mr-2"></i>
                        AI Analysis Controls
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-primary" onclick="generateAnalysis('comprehensive')">
                                    <i class="fas fa-chart-pie mr-2"></i>Comprehensive Analysis
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="generateAnalysis('financial')">
                                    <i class="fas fa-dollar-sign mr-2"></i>Financial Focus
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="generateAnalysis('operational')">
                                    <i class="fas fa-cogs mr-2"></i>Operational Focus
                                </button>
                                <button type="button" class="btn btn-outline-primary" onclick="generateAnalysis('marketing')">
                                    <i class="fas fa-bullhorn mr-2"></i>Growth Focus
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4 text-right">
                            <button type="button" class="btn btn-success" onclick="getBenchmarkComparison()">
                                <i class="fas fa-balance-scale mr-2"></i>Industry Benchmarks
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Analysis Results -->
    @if($latestAnalysis)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-chart-area mr-2"></i>
                        Latest AI Analysis Results
                    </h3>
                    <div class="text-muted">
                        <small>Generated: {{ \Carbon\Carbon::parse($latestAnalysis['timestamp'])->diffForHumans() }}</small>
                        <span class="badge badge-info ml-2">{{ ucfirst($latestAnalysis['analysis_type']) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Current Performance -->
                        <div class="col-md-6">
                            <h5 class="text-primary">
                                <i class="fas fa-chart-bar mr-2"></i>Current Performance
                            </h5>
                            @if(isset($latestAnalysis['current_performance']))
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <td>Monthly Revenue</td>
                                            <td class="text-right font-weight-bold">
                                                KSh {{ number_format($latestAnalysis['current_performance']['monthly_revenue'] ?? 0, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Monthly Expenses</td>
                                            <td class="text-right">
                                                KSh {{ number_format($latestAnalysis['current_performance']['monthly_expenses'] ?? 0, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Net Income</td>
                                            <td class="text-right font-weight-bold text-success">
                                                KSh {{ number_format($latestAnalysis['current_performance']['net_income'] ?? 0, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Profit Margin</td>
                                            <td class="text-right">{{ number_format($latestAnalysis['current_performance']['profit_margin'] ?? 0, 2) }}%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>

                        <!-- AI Predictions -->
                        <div class="col-md-6">
                            <h5 class="text-success">
                                <i class="fas fa-crystal-ball mr-2"></i>AI Predictions
                            </h5>
                            @if(isset($latestAnalysis['predictions']) && $latestAnalysis['predictions']['predicted_net_income'])
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <td>Predicted Income</td>
                                            <td class="text-right font-weight-bold text-primary">
                                                KSh {{ number_format($latestAnalysis['predictions']['predicted_net_income'], 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Improvement Potential</td>
                                            <td class="text-right">
                                                <span class="text-{{ $latestAnalysis['predictions']['improvement_potential'] > 0 ? 'success' : 'warning' }}">
                                                    KSh {{ number_format($latestAnalysis['predictions']['improvement_potential'] ?? 0, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Confidence Level</td>
                                            <td class="text-right">
                                                <span class="badge badge-{{ $latestAnalysis['predictions']['confidence_level'] === 'high' ? 'success' : ($latestAnalysis['predictions']['confidence_level'] === 'medium' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($latestAnalysis['predictions']['confidence_level'] ?? 'unknown') }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>AI Model</td>
                                            <td class="text-right">
                                                <span class="badge badge-info">Canadian MSME</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                No predictions available. Generate a new analysis to get AI-powered insights.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- AI Recommendations -->
    @if($recommendations && $recommendations->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-lightbulb mr-2"></i>
                        AI Recommendations
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($recommendations as $recommendation)
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-{{ $recommendation->priority === 'high' ? 'danger' : ($recommendation->priority === 'medium' ? 'warning' : 'success') }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0">{{ $recommendation->title }}</h6>
                                        <span class="badge badge-{{ $recommendation->priority === 'high' ? 'danger' : ($recommendation->priority === 'medium' ? 'warning' : 'success') }}">
                                            {{ ucfirst($recommendation->priority) }}
                                        </span>
                                    </div>
                                    <p class="card-text text-muted small">{{ $recommendation->description }}</p>
                                    @if($recommendation->action_items)
                                    <div class="mt-2">
                                        <small class="text-muted">Action Items:</small>
                                        <ul class="small mb-0">
                                            @foreach($recommendation->action_items as $item)
                                            <li>{{ $item }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Analysis History -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history mr-2"></i>
                        Analysis History & Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Quick Analysis Options</h6>
                            <div class="list-group">
                                <a href="#" class="list-group-item list-group-item-action" onclick="getFinancialAnalysis()">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    Financial Performance Analysis
                                </a>
                                <a href="#" class="list-group-item list-group-item-action" onclick="getOperationalAnalysis()">
                                    <i class="fas fa-cogs mr-2"></i>
                                    Operational Efficiency Review
                                </a>
                                <a href="#" class="list-group-item list-group-item-action" onclick="getGrowthPredictions()">
                                    <i class="fas fa-rocket mr-2"></i>
                                    Growth Predictions & Opportunities
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Export & Reports</h6>
                            <div class="btn-group-vertical w-100">
                                <button type="button" class="btn btn-outline-primary" onclick="exportReport('pdf')">
                                    <i class="fas fa-file-pdf mr-2"></i>Export PDF Report
                                </button>
                                <button type="button" class="btn btn-outline-success" onclick="exportReport('excel')">
                                    <i class="fas fa-file-excel mr-2"></i>Export Excel Report
                                </button>
                                <button type="button" class="btn btn-outline-info" onclick="scheduleAnalysis()">
                                    <i class="fas fa-clock mr-2"></i>Schedule Regular Analysis
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="analysisLoadingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <h5>AI Analysis in Progress</h5>
                <p class="text-muted">Our Canadian MSME model is analyzing your business data...</p>
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 75%"></div>
                </div>
                <small class="text-muted">This may take a few moments</small>
            </div>
        </div>
    </div>
</div>

<!-- Results Modal -->
<div class="modal fade" id="analysisResultsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">AI Analysis Results</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="analysisResultsContent">
                <!-- Results will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="location.reload()">Refresh Page</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Analysis functions
function generateAnalysis(type) {
    $('#analysisLoadingModal').modal('show');
    
    $.post('{{ route("business.ai-analysis.generate") }}', {
        analysis_type: type,
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
        $('#analysisLoadingModal').modal('hide');
        if (response.success) {
            showSuccessMessage('Analysis completed successfully!');
            setTimeout(() => location.reload(), 2000);
        } else {
            showErrorMessage('Analysis failed: ' + response.message);
        }
    })
    .fail(function(xhr) {
        $('#analysisLoadingModal').modal('hide');
        showErrorMessage('Analysis failed: ' + (xhr.responseJSON?.message || 'Unknown error'));
    });
}

function getFinancialAnalysis() {
    $('#analysisLoadingModal').modal('show');
    
    $.get('{{ route("business.ai-analysis.financial") }}')
    .done(function(response) {
        $('#analysisLoadingModal').modal('hide');
        if (response.success) {
            displayAnalysisResults('Financial Analysis', response.financial_analysis);
        }
    })
    .fail(function(xhr) {
        $('#analysisLoadingModal').modal('hide');
        showErrorMessage('Financial analysis failed: ' + (xhr.responseJSON?.message || 'Unknown error'));
    });
}

function getOperationalAnalysis() {
    $('#analysisLoadingModal').modal('show');
    
    $.get('{{ route("business.ai-analysis.operational") }}')
    .done(function(response) {
        $('#analysisLoadingModal').modal('hide');
        if (response.success) {
            displayAnalysisResults('Operational Analysis', response.operational_analysis);
        }
    })
    .fail(function(xhr) {
        $('#analysisLoadingModal').modal('hide');
        showErrorMessage('Operational analysis failed: ' + (xhr.responseJSON?.message || 'Unknown error'));
    });
}

function getGrowthPredictions() {
    $('#analysisLoadingModal').modal('show');
    
    $.get('{{ route("business.ai-analysis.growth") }}')
    .done(function(response) {
        $('#analysisLoadingModal').modal('hide');
        if (response.success) {
            displayAnalysisResults('Growth Predictions', response.growth_analysis);
        }
    })
    .fail(function(xhr) {
        $('#analysisLoadingModal').modal('hide');
        showErrorMessage('Growth analysis failed: ' + (xhr.responseJSON?.message || 'Unknown error'));
    });
}

function getBenchmarkComparison() {
    $('#analysisLoadingModal').modal('show');
    
    $.get('{{ route("business.ai-analysis.benchmarks") }}')
    .done(function(response) {
        $('#analysisLoadingModal').modal('hide');
        if (response.success) {
            displayAnalysisResults('Industry Benchmarks', response.benchmark_comparison);
        }
    })
    .fail(function(xhr) {
        $('#analysisLoadingModal').modal('hide');
        showErrorMessage('Benchmark analysis failed: ' + (xhr.responseJSON?.message || 'Unknown error'));
    });
}

function displayAnalysisResults(title, data) {
    let html = `<h6>${title}</h6>`;
    html += '<pre class="bg-light p-3">' + JSON.stringify(data, null, 2) + '</pre>';
    
    $('#analysisResultsContent').html(html);
    $('#analysisResultsModal').modal('show');
}

function exportReport(format) {
    $.post('{{ route("business.ai-analysis.export") }}', {
        format: format,
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
        if (response.success) {
            showSuccessMessage('Report export initiated. Check your downloads.');
        } else {
            showErrorMessage('Export failed: ' + response.message);
        }
    })
    .fail(function(xhr) {
        showErrorMessage('Export failed: ' + (xhr.responseJSON?.message || 'Unknown error'));
    });
}

function scheduleAnalysis() {
    showInfoMessage('Scheduled analysis feature coming soon!');
}

// Utility functions
function showSuccessMessage(message) {
    // You can implement your notification system here
    alert('✅ ' + message);
}

function showErrorMessage(message) {
    // You can implement your notification system here
    alert('❌ ' + message);
}

function showInfoMessage(message) {
    // You can implement your notification system here
    alert('ℹ️ ' + message);
}
</script>
@endsection

@section('styles')
<style>
.border-left-danger {
    border-left: 4px solid #dc3545 !important;
}

.border-left-warning {
    border-left: 4px solid #ffc107 !important;
}

.border-left-success {
    border-left: 4px solid #28a745 !important;
}

.page-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e9ecef;
}

.page-title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #333;
}

.page-subtitle {
    color: #6c757d;
    font-size: 0.95rem;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #3494e6 0%, #ec6ead 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>
@endsection

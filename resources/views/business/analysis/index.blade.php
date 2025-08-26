@extends('layouts.dash')

@section('content')
<div class="analysis-container">
    <div class="analysis-header">
        <div class="header-content">
            <div>
                <h1 class="analysis-title">Business Analysis</h1>
                <p class="analysis-subtitle">KENADA-powered insights and recommendations for your business</p>
                <small class="text-muted">Powered by Kenya National Data MSME Intelligence</small>
            </div>
            <div class="header-actions">
                <button class="btn btn-outline-primary" onclick="generateAnalysis('general')">
                    <i class="fas fa-brain me-2"></i><span class="d-none d-sm-inline">Generate Analysis</span><span class="d-sm-none">Analyze</span>
                </button>
                <a href="{{ route('business.analysis.financial') }}" class="btn btn-primary">
                    <i class="fas fa-chart-line me-2"></i><span class="d-none d-sm-inline">Financial Report</span><span class="d-sm-none">Reports</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Analytics Overview -->
    <div class="analytics-grid">
        <div class="analytics-card">
            <div class="card-body">
                <div class="analytics-content">
                    <div class="analytics-info">
                        <div class="analytics-label">Total Sales</div>
                        <div class="analytics-value">KSh {{ $analytics['sales']['total'] }}</div>
                    </div>
                    <div class="analytics-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="analytics-card">
            <div class="card-body">
                <div class="analytics-content">
                    <div class="analytics-info">
                        <div class="analytics-label">Monthly Sales</div>
                        <div class="analytics-value">KSh {{ $analytics['sales']['monthly'] }}</div>
                    </div>
                    <div class="analytics-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="analytics-card">
            <div class="card-body">
                <div class="analytics-content">
                    <div class="analytics-info">
                        <div class="analytics-label">Total Products</div>
                        <div class="analytics-value">{{ $analytics['products']['total'] }}</div>
                    </div>
                    <div class="analytics-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="analytics-card">
            <div class="card-body">
                <div class="analytics-content">
                    <div class="analytics-info">
                        <div class="analytics-label">Total Customers</div>
                        <div class="analytics-value">{{ $analytics['customers']['total'] }}</div>
                    </div>
                    <div class="analytics-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Analysis Section -->
    <div class="analysis-section">
        <div class="row g-4">
            <div class="col-lg-8 col-md-12">
                <div class="analysis-card">
                    <div class="card-header">
                        <h6 class="card-title">
                            <i class="fas fa-robot me-2"></i>AI Business Analysis
                        </h6>
                        <div class="analysis-buttons">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateAnalysis('general')">General</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateAnalysis('sales')">Sales</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateAnalysis('products')">Products</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateAnalysis('customers')">Customers</button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="generateAnalysis('financial')">Financial</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="analysisLoading" class="loading-container" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="loading-text">Analyzing your business data...</p>
                        </div>
                        
                        <div id="analysisContent">
                            <div class="empty-state">
                                <i class="fas fa-chart-bar"></i>
                                <h5>Ready for Analysis</h5>
                                <p>Click "Generate Analysis" to get AI-powered insights about your business performance.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="row g-4">
                    <!-- Quick Stats -->
                    <div class="col-12">
                        <div class="stats-card">
                            <div class="card-header">
                                <h6 class="card-title">Quick Stats</h6>
                            </div>
                            <div class="card-body">
                                <div class="stats-item">
                                    <span class="stats-label">Average Order Value</span>
                                    <span class="stats-value">KSh {{ $analytics['sales']['average'] }}</span>
                                </div>
                                <div class="stats-item">
                                    <span class="stats-label">Total Orders</span>
                                    <span class="stats-value">{{ $analytics['sales']['orders'] }}</span>
                                </div>
                                <div class="stats-item">
                                    <span class="stats-label">Low Stock Items</span>
                                    <span class="stats-value warning">{{ $analytics['products']['low_stock'] }}</span>
                                </div>
                                <div class="stats-item">
                                    <span class="stats-label">New Customers (This Month)</span>
                                    <span class="stats-value success">{{ $analytics['customers']['new'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Analysis Tips -->
                    <div class="col-12">
                        <div class="tips-card">
                            <div class="card-header">
                                <h6 class="card-title">
                                    <i class="fas fa-lightbulb me-2"></i>Analysis Tips
                                </h6>
                            </div>
                            <div class="card-body">
                                <ul class="tips-list">
                                    <li>
                                        <i class="fas fa-check-circle"></i>
                                        <span>Choose specific analysis types for targeted insights</span>
                                    </li>
                                    <li>
                                        <i class="fas fa-check-circle"></i>
                                        <span>Review recommendations regularly for best results</span>
                                    </li>
                                    <li>
                                        <i class="fas fa-check-circle"></i>
                                        <span>Compare monthly trends to track progress</span>
                                    </li>
                                    <li>
                                        <i class="fas fa-check-circle"></i>
                                        <span>Focus on actionable insights for growth</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
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
.analysis-container {
    padding: 0;
}

.analysis-header {
    margin-bottom: 2rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1rem;
}

.analysis-title {
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}

.analysis-subtitle {
    color: var(--text-secondary);
    margin: 0.5rem 0 0 0;
}

.header-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.analytics-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.analytics-card {
    background: var(--card-bg);
    border-radius: 0.75rem;
    border: 1px solid var(--border-color);
    transition: all 0.2s ease;
}

.analytics-card:hover {
    box-shadow: 0 4px 6px -1px var(--shadow-color);
    transform: translateY(-2px);
}

.analytics-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
}

.analytics-info {
    flex: 1;
}

.analytics-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-muted);
    text-transform: uppercase;
    margin-bottom: 0.5rem;
}

.analytics-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
}

.analytics-icon {
    font-size: 2rem;
    color: var(--text-muted);
}

.analysis-section {
    margin-top: 1rem;
}

.analysis-card, .stats-card, .tips-card {
    background: var(--card-bg);
    border-radius: 0.75rem;
    border: 1px solid var(--border-color);
    height: 100%;
}

.card-header {
    padding: 1.5rem 1.5rem 0;
    border-bottom: none;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.analysis-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.analysis-buttons .btn {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
}

.card-body {
    padding: 1.5rem;
}

.loading-container {
    text-align: center;
    padding: 2rem;
}

.loading-text {
    margin-top: 1rem;
    color: var(--text-secondary);
}

.empty-state {
    text-align: center;
    padding: 2rem;
}

.empty-state i {
    font-size: 3rem;
    color: var(--text-muted);
    margin-bottom: 1rem;
}

.empty-state h5 {
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--text-muted);
    margin: 0;
}

.stats-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--border-color);
}

.stats-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.stats-label {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.stats-value {
    font-weight: 600;
    color: var(--text-primary);
}

.stats-value.warning {
    color: var(--warning-color);
}

.stats-value.success {
    color: var(--success-color);
}

.tips-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.tips-list li {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.tips-list li:last-child {
    margin-bottom: 0;
}

.tips-list i {
    color: var(--success-color);
    margin-top: 0.125rem;
    flex-shrink: 0;
}

.analysis-content {
    line-height: 1.6;
    font-size: 0.95rem;
    color: var(--text-primary);
}

.analysis-content h1, .analysis-content h2, .analysis-content h3 {
    color: var(--primary-color);
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
}

.analysis-content ul, .analysis-content ol {
    padding-left: 1.5rem;
}

.analysis-content li {
    margin-bottom: 0.5rem;
}

/* Mobile Responsive Design */
@media (max-width: 1200px) {
    .analytics-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
}

@media (max-width: 768px) {
    .analysis-container {
        padding: 0 1rem;
    }
    
    .header-content {
        flex-direction: column;
        align-items: stretch;
    }
    
    .analysis-title {
        font-size: 1.5rem;
    }
    
    .header-actions {
        justify-content: stretch;
    }
    
    .header-actions .btn {
        flex: 1;
        min-height: 44px;
    }
    
    .analytics-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .analytics-content {
        padding: 1.25rem;
    }
    
    .analytics-value {
        font-size: 1.25rem;
    }
    
    .analytics-icon {
        font-size: 1.5rem;
    }
    
    .card-header {
        padding: 1.25rem 1.25rem 0;
        flex-direction: column;
        align-items: stretch;
    }
    
    .analysis-buttons {
        justify-content: center;
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .loading-container, .empty-state {
        padding: 1.5rem;
    }
    
    .empty-state i {
        font-size: 2.5rem;
    }
    
    .stats-item {
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
    }
    
    .tips-list li {
        margin-bottom: 0.75rem;
        font-size: 0.8rem;
    }
}

@media (max-width: 480px) {
    .analysis-container {
        padding: 0 0.75rem;
    }
    
    .analysis-title {
        font-size: 1.25rem;
    }
    
    .analytics-content {
        padding: 1rem;
    }
    
    .analytics-value {
        font-size: 1.125rem;
    }
    
    .analytics-icon {
        font-size: 1.25rem;
    }
    
    .card-header {
        padding: 1rem 1rem 0;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .loading-container, .empty-state {
        padding: 1rem;
    }
    
    .empty-state i {
        font-size: 2rem;
    }
    
    .tips-list li {
        font-size: 0.75rem;
    }
    
    .analysis-buttons .btn {
        font-size: 0.7rem;
        padding: 0.25rem 0.375rem;
    }
}

/* Touch-friendly improvements */
@media (max-width: 768px) {
    .btn {
        min-height: 44px;
        padding: 0.75rem 1rem;
    }
    
    .analytics-card {
        cursor: pointer;
    }
    
    .analysis-card, .stats-card, .tips-card {
        border-radius: 0.75rem;
    }
}
</style>
@endsection 
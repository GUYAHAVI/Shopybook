@extends('layouts.dash')

@section('content')
<div class="financial-container">
    <div class="financial-header">
        <div class="header-content">
            <div>
                <h1 class="financial-title">Financial Report</h1>
                <p class="financial-subtitle">Comprehensive financial analysis and insights</p>
            </div>
            <a href="{{ route('business.analysis.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i><span class="d-none d-sm-inline">Back to Analysis</span><span class="d-sm-none">Back</span>
            </a>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="financial-grid">
        <div class="financial-card">
            <div class="card-body">
                <div class="financial-content">
                    <div class="financial-info">
                        <div class="financial-label">Total Revenue</div>
                        <div class="financial-value">KSh {{ $financialData['summary']['revenue'] }}</div>
                    </div>
                    <div class="financial-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="financial-card">
            <div class="card-body">
                <div class="financial-content">
                    <div class="financial-info">
                        <div class="financial-label">Total Costs</div>
                        <div class="financial-value">KSh {{ $financialData['summary']['costs'] }}</div>
                    </div>
                    <div class="financial-icon">
                        <i class="fas fa-minus-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="financial-card">
            <div class="card-body">
                <div class="financial-content">
                    <div class="financial-info">
                        <div class="financial-label">Net Profit</div>
                        <div class="financial-value">KSh {{ $financialData['summary']['profit'] }}</div>
                    </div>
                    <div class="financial-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="financial-card">
            <div class="card-body">
                <div class="financial-content">
                    <div class="financial-info">
                        <div class="financial-label">Profit Margin</div>
                        <div class="financial-value">{{ $financialData['summary']['profit_margin'] }}</div>
                    </div>
                    <div class="financial-icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Financial Analysis -->
    <div class="financial-section">
        <div class="row g-4">
            <div class="col-lg-8 col-md-12">
                <div class="analysis-card">
                    <div class="card-header">
                        <h6 class="card-title">
                            <i class="fas fa-robot me-2"></i>AI Financial Analysis
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="financial-report">
                            {!! nl2br(e($report)) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="row g-4">
                    <!-- Top Performing Products -->
                    <div class="col-12">
                        <div class="products-card">
                            <div class="card-header">
                                <h6 class="card-title">
                                    <i class="fas fa-star me-2"></i>Top Performing Products
                                </h6>
                            </div>
                            <div class="card-body">
                                @foreach($financialData['top_products'] as $product)
                                <div class="product-item">
                                    <div class="product-info">
                                        <h6 class="product-name">{{ $product['name'] }}</h6>
                                        <small class="product-category">{{ $product['category'] }}</small>
                                    </div>
                                    <div class="product-stats">
                                        <div class="product-revenue">KSh {{ $product['revenue'] }}</div>
                                        <div class="product-sales">{{ $product['sales'] }} units</div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Financial Insights -->
                    <div class="col-12">
                        <div class="insights-card">
                            <div class="card-header">
                                <h6 class="card-title">
                                    <i class="fas fa-lightbulb me-2"></i>Financial Insights
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="insights-list">
                                    <div class="insight-item">
                                        <i class="fas fa-arrow-up text-success"></i>
                                        <span>Revenue increased by {{ $financialData['insights']['revenue_growth'] }}% this month</span>
                                    </div>
                                    <div class="insight-item">
                                        <i class="fas fa-arrow-down text-danger"></i>
                                        <span>Costs reduced by {{ $financialData['insights']['cost_reduction'] }}%</span>
                                    </div>
                                    <div class="insight-item">
                                        <i class="fas fa-chart-line text-primary"></i>
                                        <span>Profit margin improved to {{ $financialData['insights']['margin_improvement'] }}%</span>
                                    </div>
                                    <div class="insight-item">
                                        <i class="fas fa-trending-up text-info"></i>
                                        <span>Cash flow positive for {{ $financialData['insights']['cash_flow_months'] }} months</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.financial-container {
    padding: 0;
}

.financial-header {
    margin-bottom: 2rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 1rem;
}

.financial-title {
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}

.financial-subtitle {
    color: var(--text-secondary);
    margin: 0.5rem 0 0 0;
}

.financial-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.financial-card {
    background: var(--card-bg);
    border-radius: 0.75rem;
    border: 1px solid var(--border-color);
    transition: all 0.2s ease;
}

.financial-card:hover {
    box-shadow: 0 4px 6px -1px var(--shadow-color);
    transform: translateY(-2px);
}

.financial-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
}

.financial-info {
    flex: 1;
}

.financial-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-muted);
    text-transform: uppercase;
    margin-bottom: 0.5rem;
}

.financial-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
}

.financial-icon {
    font-size: 2rem;
    color: var(--text-muted);
}

.financial-section {
    margin-top: 1rem;
}

.analysis-card, .products-card, .insights-card {
    background: var(--card-bg);
    border-radius: 0.75rem;
    border: 1px solid var(--border-color);
    height: 100%;
}

.card-header {
    padding: 1.5rem 1.5rem 0;
    border-bottom: none;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.card-body {
    padding: 1.5rem;
}

.financial-report {
    line-height: 1.6;
    font-size: 0.95rem;
    color: var(--text-primary);
}

.product-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--border-color);
}

.product-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.product-name {
    font-size: 0.875rem;
    font-weight: 600;
    margin: 0;
    color: var(--text-primary);
}

.product-category {
    color: var(--text-muted);
    font-size: 0.75rem;
}

.product-stats {
    text-align: right;
}

.product-revenue {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.875rem;
}

.product-sales {
    color: var(--text-muted);
    font-size: 0.75rem;
}

.insights-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.insight-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.insight-item:last-child {
    margin-bottom: 0;
}

.insight-item i {
    margin-top: 0.125rem;
    flex-shrink: 0;
}

/* Mobile Responsive Design */
@media (max-width: 1200px) {
    .financial-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
}

@media (max-width: 768px) {
    .financial-container {
        padding: 0 1rem;
    }
    
    .header-content {
        flex-direction: column;
        align-items: stretch;
    }
    
    .financial-title {
        font-size: 1.5rem;
    }
    
    .financial-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .financial-content {
        padding: 1.25rem;
    }
    
    .financial-value {
        font-size: 1.25rem;
    }
    
    .financial-icon {
        font-size: 1.5rem;
    }
    
    .card-header {
        padding: 1.25rem 1.25rem 0;
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .product-item {
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
    }
    
    .insight-item {
        margin-bottom: 0.75rem;
        font-size: 0.8rem;
    }
}

@media (max-width: 480px) {
    .financial-container {
        padding: 0 0.75rem;
    }
    
    .financial-title {
        font-size: 1.25rem;
    }
    
    .financial-content {
        padding: 1rem;
    }
    
    .financial-value {
        font-size: 1.125rem;
    }
    
    .financial-icon {
        font-size: 1.25rem;
    }
    
    .card-header {
        padding: 1rem 1rem 0;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .insight-item {
        font-size: 0.75rem;
    }
}

/* Touch-friendly improvements */
@media (max-width: 768px) {
    .btn {
        min-height: 44px;
        padding: 0.75rem 1rem;
    }
    
    .financial-card {
        cursor: pointer;
    }
    
    .analysis-card, .products-card, .insights-card {
        border-radius: 0.75rem;
    }
}
</style>
@endsection 
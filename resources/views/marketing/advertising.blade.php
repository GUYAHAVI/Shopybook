@extends('layouts.dash')

@section('title', 'Advertising')

@section('content')
<!-- Sub-navigation for Marketing -->
<div class="sub-navigation mb-4">
    <div class="nav-tabs">
        <a href="{{ route('marketing.social-media') }}" class="nav-tab">
            <i class="fas fa-share-alt me-1"></i> Social Media
        </a>
        <a href="{{ route('marketing.promotions') }}" class="nav-tab">
            <i class="fas fa-bullhorn me-1"></i> Promotions
        </a>
        <a href="{{ route('marketing.advertising') }}" class="nav-tab active">
            <i class="fas fa-ad me-1"></i> Advertising
        </a>
        <a href="{{ route('marketing.bulk-sms') }}" class="nav-tab">
            <i class="fas fa-sms me-1"></i> Bulk SMS
        </a>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0" style="color: var(--text-primary);">Advertising Campaigns</h1>
    <a href="{{ route('marketing.create-campaign') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Create Campaign
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h6 class="m-0 font-weight-bold" style="color: var(--text-primary);">Active Campaigns</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="campaign-card card h-100" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="campaign-icon me-3" style="background: var(--primary-color); color: var(--white); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-ad"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0" style="color: var(--text-primary);">Facebook Ads</h6>
                                        <small style="color: var(--text-muted);">Social Media</small>
                                    </div>
                                </div>
                                <div class="campaign-stats mb-3">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="stat-item">
                                                <h5 class="mb-0" style="color: var(--text-primary);">KSh 250</h5>
                                                <small style="color: var(--text-muted);">Spent</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-item">
                                                <h5 class="mb-0" style="color: var(--success-color);">1,250</h5>
                                                <small style="color: var(--text-muted);">Clicks</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-item">
                                                <h5 class="mb-0" style="color: var(--info-color);">45</h5>
                                                <small style="color: var(--text-muted);">Sales</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress mb-3" style="height: 8px; background: var(--bg-tertiary);">
                                    <div class="progress-bar" role="progressbar" style="width: 75%; background: var(--primary-color);" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small style="color: var(--text-muted);">75% of budget used</small>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary">Edit</button>
                                        <button class="btn btn-sm btn-outline-danger">Pause</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="campaign-card card h-100" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="campaign-icon me-3" style="background: var(--warning-color); color: var(--white); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0" style="color: var(--text-primary);">Google Ads</h6>
                                        <small style="color: var(--text-muted);">Search</small>
                                    </div>
                                </div>
                                <div class="campaign-stats mb-3">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="stat-item">
                                                <h5 class="mb-0" style="color: var(--text-primary);">KSh 180</h5>
                                                <small style="color: var(--text-muted);">Spent</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-item">
                                                <h5 class="mb-0" style="color: var(--success-color);">890</h5>
                                                <small style="color: var(--text-muted);">Clicks</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-item">
                                                <h5 class="mb-0" style="color: var(--info-color);">32</h5>
                                                <small style="color: var(--text-muted);">Sales</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress mb-3" style="height: 8px; background: var(--bg-tertiary);">
                                    <div class="progress-bar" role="progressbar" style="width: 60%; background: var(--warning-color);" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small style="color: var(--text-muted);">60% of budget used</small>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary">Edit</button>
                                        <button class="btn btn-sm btn-outline-danger">Pause</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h6 class="m-0 font-weight-bold" style="color: var(--text-primary);">Campaign Performance</h6>
            </div>
            <div class="card-body">
                <div class="performance-stats">
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span style="color: var(--text-primary);">Total Spent</span>
                                                            <span style="color: var(--text-primary); font-weight: 600;">KSh 430</span>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span style="color: var(--text-primary);">Total Clicks</span>
                            <span style="color: var(--success-color); font-weight: 600;">2,140</span>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span style="color: var(--text-primary);">Total Sales</span>
                            <span style="color: var(--info-color); font-weight: 600;">77</span>
                        </div>
                    </div>
                    <div class="stat-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span style="color: var(--text-primary);">ROAS</span>
                            <span style="color: var(--success-color); font-weight: 600;">3.2x</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="d-flex justify-content-between">
                            <span style="color: var(--text-primary);">CTR</span>
                            <span style="color: var(--warning-color); font-weight: 600;">2.1%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card shadow" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h6 class="m-0 font-weight-bold" style="color: var(--text-primary);">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('marketing.create-campaign') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>New Campaign
                    </a>
                    <button class="btn btn-outline-primary">
                        <i class="fas fa-chart-line me-2"></i>View Analytics
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-download me-2"></i>Export Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sub-navigation {
    background: var(--card-bg);
    border-radius: 0.75rem;
    padding: 1rem;
    border: 1px solid var(--border-color);
    margin-bottom: 2rem;
}

.sub-navigation .nav-tabs {
    display: flex;
    gap: 1rem;
    list-style: none;
    margin: 0;
    padding: 0;
    flex-wrap: wrap;
}

.sub-navigation .nav-tab {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-muted);
    text-decoration: none;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.sub-navigation .nav-tab:hover {
    color: var(--text-primary);
    background: var(--bg-tertiary);
}

.sub-navigation .nav-tab.active {
    color: var(--primary-color);
    background: var(--primary-color);
    color: var(--white);
}

.campaign-card {
    transition: all 0.2s ease;
}

.campaign-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px var(--shadow-color);
}

.campaign-icon {
    transition: all 0.2s ease;
}

.campaign-icon:hover {
    transform: scale(1.1);
}

.stat-item {
    padding: 0.5rem;
    border-radius: 0.375rem;
    background: var(--bg-tertiary);
    transition: all 0.2s ease;
}

.stat-item:hover {
    background: var(--border-color);
}

.performance-stats .stat-item {
    padding: 0.75rem;
    border-radius: 0.5rem;
    background: var(--bg-tertiary);
    margin-bottom: 0.5rem;
}

.performance-stats .stat-item:last-child {
    margin-bottom: 0;
}

@media (max-width: 768px) {
    .sub-navigation .nav-tabs {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .sub-navigation .nav-tab {
        text-align: center;
        padding: 0.75rem 1rem;
    }
    
    .campaign-stats .row {
        margin: 0;
    }
    
    .campaign-stats .col-4 {
        padding: 0 0.25rem;
    }
    
    .btn-group {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .btn-group .btn {
        width: 100%;
    }
}
</style>
@endsection 
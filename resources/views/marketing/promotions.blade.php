@extends('layouts.dash')

@section('title', 'Promotions')

@section('content')
<!-- Sub-navigation for Marketing -->
<div class="sub-navigation mb-4">
    <div class="nav-tabs">
        <a href="{{ route('marketing.social-media') }}" class="nav-tab">
            <i class="fas fa-share-alt me-1"></i> Social Media
        </a>
        <a href="{{ route('marketing.promotions') }}" class="nav-tab active">
            <i class="fas fa-bullhorn me-1"></i> Promotions
        </a>
        <a href="{{ route('marketing.advertising') }}" class="nav-tab">
            <i class="fas fa-ad me-1"></i> Advertising
        </a>
        <a href="{{ route('marketing.bulk-sms') }}" class="nav-tab">
            <i class="fas fa-sms me-1"></i> Bulk SMS
        </a>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0" style="color: var(--text-primary);">Promotions</h1>
    <a href="{{ route('marketing.create-promotion') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Create Promotion
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h6 class="m-0 font-weight-bold" style="color: var(--text-primary);">Active Promotions</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" style="color: var(--text-primary);">
                        <thead style="background: var(--bg-tertiary);">
                            <tr>
                                <th style="color: var(--text-primary);">Promotion</th>
                                <th style="color: var(--text-primary);">Discount</th>
                                <th style="color: var(--text-primary);">Status</th>
                                <th style="color: var(--text-primary);">Expires</th>
                                <th style="color: var(--text-primary);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="promotion-icon me-3" style="background: var(--primary-color); color: var(--white); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-tag"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0" style="color: var(--text-primary);">Summer Sale</h6>
                                            <small style="color: var(--text-muted);">All products</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-success" style="background: var(--success-color) !important;">20% OFF</span>
                                </td>
                                <td>
                                    <span class="badge bg-success" style="background: var(--success-color) !important;">Active</span>
                                </td>
                                <td style="color: var(--text-secondary);">Dec 31, 2024</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary">Edit</button>
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="promotion-icon me-3" style="background: var(--warning-color); color: var(--white); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-gift"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0" style="color: var(--text-primary);">Buy One Get One</h6>
                                            <small style="color: var(--text-muted);">Selected items</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-warning" style="background: var(--warning-color) !important;">BOGO</span>
                                </td>
                                <td>
                                    <span class="badge bg-success" style="background: var(--success-color) !important;">Active</span>
                                </td>
                                <td style="color: var(--text-secondary);">Jan 15, 2025</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary">Edit</button>
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h6 class="m-0 font-weight-bold" style="color: var(--text-primary);">Promotion Stats</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="stat-card">
                            <h3 class="mb-0" style="color: var(--text-primary);">2</h3>
                            <small style="color: var(--text-muted);">Active</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="stat-card">
                            <h3 class="mb-0" style="color: var(--text-primary);">15</h3>
                            <small style="color: var(--text-muted);">Total Used</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="stat-card">
                            <h3 class="mb-0" style="color: var(--success-color);">KSh 1,250</h3>
                            <small style="color: var(--text-muted);">Revenue</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="stat-card">
                            <h3 class="mb-0" style="color: var(--warning-color);">KSh 320</h3>
                            <small style="color: var(--text-muted);">Discounts</small>
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
                    <a href="{{ route('marketing.create-promotion') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>New Promotion
                    </a>
                    <button class="btn btn-outline-primary">
                        <i class="fas fa-chart-line me-2"></i>View Analytics
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-download me-2"></i>Export Data
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

.stat-card {
    padding: 1rem;
    border-radius: 0.5rem;
    background: var(--bg-tertiary);
    transition: all 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px var(--shadow-color);
}

.table th {
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
}

.table td {
    vertical-align: middle;
    border-top: 1px solid var(--border-color);
}

.promotion-icon {
    transition: all 0.2s ease;
}

.promotion-icon:hover {
    transform: scale(1.1);
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
    
    .table-responsive {
        font-size: 0.875rem;
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
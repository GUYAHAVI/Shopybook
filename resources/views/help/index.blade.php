@extends('layouts.dash')

@section('content')
<div class="container py-4">
    <!-- Sub-navigation for Help -->
    <div class="sub-navigation mb-4">
        <div class="nav-tabs">
            <a href="{{ route('help.index') }}" class="nav-tab active">
                <i class="fas fa-question-circle me-1"></i> Help Center
            </a>
            <a href="{{ route('marketing.social-media') }}" class="nav-tab">
                <i class="fas fa-share-alt me-1"></i> Social Media Setup
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <h1 style="color: var(--text-primary);">Help Center</h1>
                <p style="color: var(--text-muted);">Get help with Shopybook and social media management</p>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                        <div class="card-body text-center">
                            <i class="fas fa-book fa-3x" style="color: var(--primary-color);" class="mb-3"></i>
                            <h5 style="color: var(--text-primary);">Getting Started</h5>
                            <p style="color: var(--text-secondary);">Learn the basics of social media management</p>
                            <a href="#" class="btn btn-outline-primary">View Guides</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                        <div class="card-body text-center">
                            <i class="fas fa-share-alt fa-3x" style="color: var(--success-color);" class="mb-3"></i>
                            <h5 style="color: var(--text-primary);">Social Media Setup</h5>
                            <p style="color: var(--text-secondary);">Connect your social media accounts</p>
                            <a href="{{ route('marketing.social-media') }}" class="btn btn-outline-success">Go to Setup</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                <div class="card-body">
                    <h5 style="color: var(--text-primary);">Contact Support</h5>
                    <p style="color: var(--text-secondary);">Need additional help? Our support team is here to assist you.</p>
                    <a href="mailto:support@shopybook.com" class="btn btn-primary">
                        <i class="fas fa-envelope me-2"></i>Email Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sub-navigation {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.nav-tabs {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.nav-tab {
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    color: var(--text-muted);
    text-decoration: none;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.nav-tab:hover {
    color: var(--text-primary);
    background: var(--bg-tertiary);
    border-color: var(--border-color);
}

.nav-tab.active {
    color: var(--white);
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.card:hover {
    box-shadow: 0 4px 6px var(--shadow-color);
    transition: box-shadow 0.2s ease;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .nav-tabs {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .nav-tab {
        justify-content: center;
        padding: 0.75rem 1rem;
    }
    
    .row .col-md-6 {
        margin-bottom: 1rem;
    }
}
</style>
@endsection

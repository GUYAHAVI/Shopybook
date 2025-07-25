@extends('layouts.app')

@section('title', 'Help Center - Shopybook')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <h1>Help Center</h1>
                <p class="text-muted">Get help with Shopybook and social media management</p>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-book fa-3x text-primary mb-3"></i>
                            <h5>Getting Started</h5>
                            <p>Learn the basics of social media management</p>
                            <a href="#" class="btn btn-outline-primary">View Guides</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-share-alt fa-3x text-success mb-3"></i>
                            <h5>Social Media Setup</h5>
                            <p>Connect your social media accounts</p>
                            <a href="{{ route('marketing.social-media') }}" class="btn btn-outline-success">Go to Setup</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h5>Contact Support</h5>
                    <p>Need additional help? Our support team is here to assist you.</p>
                    <a href="mailto:support@shopybook.com" class="btn btn-primary">
                        <i class="fas fa-envelope me-2"></i>Email Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

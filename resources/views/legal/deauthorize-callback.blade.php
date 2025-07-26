@extends('layouts.master')

@section('title', 'Account Disconnected - Shopybook')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm text-center">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="fas fa-unlink fa-4x text-warning"></i>
                    </div>
                    
                    <h1 class="h3 mb-3">Social Media Account Disconnected</h1>
                    
                    <p class="text-muted mb-4">
                        Your social media account has been successfully disconnected from Shopybook. 
                        We have automatically deleted all associated data from our systems.
                    </p>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>What happens next:</strong>
                        <ul class="list-unstyled mt-2 mb-0">
                            <li>• Access tokens have been deleted</li>
                            <li>• Scheduled posts for this account have been cancelled</li>
                            <li>• Historical data has been removed</li>
                            <li>• You can reconnect anytime if needed</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2 d-md-block">
                        <a href="{{ route('marketing.social-media') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Connect Another Account
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Back to Dashboard
                        </a>
                    </div>

                    <hr class="my-4">

                    <div class="small text-muted">
                        <p class="mb-2"><strong>Need to delete your entire Shopybook account?</strong></p>
                        <a href="{{ route('data-deletion') }}" class="text-decoration-none">
                            View data deletion instructions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

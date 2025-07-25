@extends('layouts.app')

@section('title', 'Upgrade Plan - Shopybook')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="text-center mb-5">
                <h1 class="display-5 fw-bold">Choose Your Plan</h1>
                <p class="lead text-muted">Unlock more features and grow your business</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4">
                <!-- Free Plan -->
                <div class="col-lg-4">
                    <div class="card h-100 {{ $business->plan === 'free' ? 'border-primary' : '' }}">
                        <div class="card-header text-center py-4 {{ $business->plan === 'free' ? 'bg-primary text-white' : 'bg-light' }}">
                            <h4 class="fw-bold">Free</h4>
                            <h2 class="display-6 fw-bold">$0<small class="fs-6">/month</small></h2>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <ul class="list-unstyled flex-grow-1">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>1 Social media account</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Basic scheduling</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Basic analytics</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Community support</li>
                                <li class="mb-2"><i class="fas fa-times text-muted me-2"></i>Advanced features</li>
                            </ul>
                            @if($business->plan === 'free')
                                <button class="btn btn-primary disabled">Current Plan</button>
                            @else
                                <form method="POST" action="{{ route('billing.process-upgrade') }}">
                                    @csrf
                                    <input type="hidden" name="plan" value="free">
                                    <button type="submit" class="btn btn-outline-primary">Downgrade</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Basic Plan -->
                <div class="col-lg-4">
                    <div class="card h-100 {{ $business->plan === 'basic' ? 'border-success' : '' }}">
                        <div class="card-header text-center py-4 {{ $business->plan === 'basic' ? 'bg-success text-white' : 'bg-light' }}">
                            <h4 class="fw-bold">Basic</h4>
                            <h2 class="display-6 fw-bold">$19<small class="fs-6">/month</small></h2>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <ul class="list-unstyled flex-grow-1">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>5 Social media accounts</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Advanced scheduling</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Detailed analytics</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email support</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Content calendar</li>
                            </ul>
                            @if($business->plan === 'basic')
                                <button class="btn btn-success disabled">Current Plan</button>
                            @else
                                <form method="POST" action="{{ route('billing.process-upgrade') }}">
                                    @csrf
                                    <input type="hidden" name="plan" value="basic">
                                    <button type="submit" class="btn btn-success">
                                        {{ $business->plan === 'free' ? 'Upgrade' : 'Switch Plan' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Premium Plan -->
                <div class="col-lg-4">
                    <div class="card h-100 {{ $business->plan === 'premium' ? 'border-warning' : '' }} position-relative">
                        @if($business->plan !== 'premium')
                            <div class="position-absolute top-0 start-50 translate-middle">
                                <span class="badge bg-warning text-dark px-3 py-2">Most Popular</span>
                            </div>
                        @endif
                        <div class="card-header text-center py-4 {{ $business->plan === 'premium' ? 'bg-warning text-dark' : 'bg-light' }}">
                            <h4 class="fw-bold">Premium</h4>
                            <h2 class="display-6 fw-bold">$49<small class="fs-6">/month</small></h2>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <ul class="list-unstyled flex-grow-1">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Unlimited social accounts</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>AI content suggestions</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Advanced analytics</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Priority support</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Team collaboration</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Custom branding</li>
                            </ul>
                            @if($business->plan === 'premium')
                                <button class="btn btn-warning disabled">Current Plan</button>
                            @else
                                <form method="POST" action="{{ route('billing.process-upgrade') }}">
                                    @csrf
                                    <input type="hidden" name="plan" value="premium">
                                    <button type="submit" class="btn btn-warning text-dark">
                                        {{ $business->plan === 'free' ? 'Upgrade' : 'Switch Plan' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Plan Info -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Current Plan: {{ ucfirst($business->plan ?? 'free') }}</h5>
                            <p class="card-text text-muted">
                                @if($business->plan === 'premium')
                                    You have access to all premium features including unlimited social media accounts.
                                @elseif($business->plan === 'basic')
                                    You can connect up to 5 social media accounts with advanced features.
                                @else
                                    You're on the free plan with 1 social media account connection.
                                @endif
                            </p>
                            
                            @if($business->plan !== 'free')
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>Need to cancel your subscription?</span>
                                    <form method="POST" action="{{ route('billing.cancel') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to cancel your subscription?')">
                                            Cancel Subscription
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Back to Dashboard -->
            <div class="text-center mt-4">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

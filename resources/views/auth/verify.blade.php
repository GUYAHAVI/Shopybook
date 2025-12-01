@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ __('Verify Your Email Address') }}</h4>
                </div>

                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-envelope-open text-primary" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Email Verification Required</h5>
                        <p class="text-muted">Please verify your email address to continue using Shopybook.</p>
                    </div>

                    @if (session('resent'))
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif

                    @if (session('message'))
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="text-center">
                        <p class="mb-4">{{ __('Before proceeding, please check your email for a verification link.') }}</p>
                        
                        <form class="d-inline" method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="fas fa-paper-plane me-2"></i>{{ __('Resend Verification Email') }}
                            </button>
                        </form>
                        
                        <div class="mt-4">
                            <a href="{{ route('logout') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-sign-out-alt me-2"></i>{{ __('Logout') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
    border-bottom: none;
}

.alert {
    border-radius: 10px;
    border: none;
}

.alert-success {
    background-color: #d1edff;
    color: #0c5460;
}

.btn {
    border-radius: 8px;
    font-weight: 500;
}

.btn-lg {
    padding: 12px 24px;
}
</style>
@endsection

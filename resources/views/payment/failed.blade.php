@extends('layouts.dash')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-times-circle fa-5x text-danger"></i>
                    </div>
                    
                    <h3 class="text-danger mb-3">Payment Failed</h3>
                    
                    @if(session('error'))
                        <p class="text-muted mb-4">{{ session('error') }}</p>
                    @else
                        <p class="text-muted mb-4">Sorry, your payment could not be processed. Please try again.</p>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('dashboard') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-tachometer-alt"></i> Go to Dashboard
                            </a>
                        </div>
                        <div class="col-md-6">
                            <button onclick="history.back()" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-arrow-left"></i> Try Again
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
body, .container-fluid, .card, .main-content, .content {
    background: #fff !important;
    color: #020258 !important;
}
.btn-primary {
    background: #020258 !important;
    color: #fff !important;
    border: 2px solid #13e8e9 !important;
}
.btn-primary:hover {
    background: #13e8e9 !important;
    color: #020258 !important;
    border: 2px solid #020258 !important;
}
.form-control {
    background: #f8f9fa !important;
    color: #020258 !important;
    border: 2px solid #13e8e9 !important;
}
.form-control:focus {
    border-color: #020258 !important;
    box-shadow: 0 0 0 3px rgba(19, 232, 233, 0.1) !important;
}
.card-header {
    background: #f8f9fa !important;
    color: #020258 !important;
    border-bottom: 1px solid #13e8e9 !important;
}
</style>
@endsection 
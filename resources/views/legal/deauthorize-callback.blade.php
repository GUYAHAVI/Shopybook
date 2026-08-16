@extends('layouts.public')

@section('title', 'Account Disconnected - Shopybook')

@section('content')

<section class="hero-section text-light">
    <div class="container text-center">
        <h1>Account Disconnected</h1>
        <p>Your social media account has been disconnected from Shopybook.</p>
    </div>
</section>

<section class="sb-section sb-section-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="sb-form-card text-center">
                    <div class="mb-4">
                        <i class="fas fa-unlink fa-4x" style="color:#ff511a;"></i>
                    </div>

                    <h2 class="mb-3" style="font-size:1.5rem;">Social Media Account Disconnected</h2>

                    <p class="text-muted mb-4">
                        Your social media account has been successfully disconnected from Shopybook.
                        We have automatically deleted all associated data from our systems.
                    </p>

                    <div class="sb-tip-box text-start">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>What happens next:</strong>
                            <ul class="list-unstyled mt-2 mb-0">
                                <li>&bull; Access tokens have been deleted</li>
                                <li>&bull; Scheduled posts for this account have been cancelled</li>
                                <li>&bull; Historical data has been removed</li>
                                <li>&bull; You can reconnect anytime if needed</li>
                            </ul>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-center flex-wrap mt-4">
                        <a href="{{ route('marketing.social-media') }}" class="btn1">Connect Another Account</a>
                        <a href="{{ route('dashboard') }}" class="btnb">Back to Dashboard</a>
                    </div>

                    <hr class="my-4">

                    <div class="small text-muted">
                        <p class="mb-2"><strong>Need to delete your entire Shopybook account?</strong></p>
                        <a href="{{ route('data-deletion') }}" style="color:#ff511a;">View data deletion instructions</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

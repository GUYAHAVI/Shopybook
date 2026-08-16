@extends('layouts.public')

@section('title', 'Verify Your Email Address - Shopybook')
@section('meta_description', 'Verify your email address to continue using Shopybook.')

@section('content')

{{-- ═══════════════ HERO ═══════════════ --}}
<section class="hero-section text-light">
    <div class="container text-center">
        <div class="sb-verify-icon">
            <i class="fas fa-envelope-open"></i>
        </div>
        <h1>Verify Your Email Address</h1>
        <p class="mb-0">Please verify your email address to continue using Shopybook.</p>
    </div>
</section>

{{-- ═══════════════ CONTENT ═══════════════ --}}
<section class="sb-section sb-section-gray">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                <div class="sb-form-card">

                    @if (session('resent'))
                        <div class="sb-alert sb-alert-success">
                            <i class="fas fa-check-circle"></i>
                            <span>A fresh verification link has been sent to your email address.</span>
                        </div>
                    @endif

                    @if (session('message'))
                        <div class="sb-alert sb-alert-success">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('message') }}</span>
                        </div>
                    @endif

                    <div class="text-center">
                        <p class="mb-4" style="color:#555;">
                            Before proceeding, please check your email for a verification link.
                        </p>

                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn1" style="padding:0.8rem 2rem;">
                                <i class="fas fa-paper-plane me-2"></i>Resend Verification Email
                            </button>
                        </form>

                        <div class="mt-4">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btnb" style="padding:0.7rem 1.75rem;">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

@endsection

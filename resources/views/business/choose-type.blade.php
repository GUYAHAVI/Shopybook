@extends('layouts.master')

@section('content')
<style>
    .business-type-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-family: 'Poppins', sans-serif;
    }

    .business-type-card {
        background: white;
        border-radius: 20px;
        padding: 3rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        max-width: 900px;
        width: 90%;
        text-align: center;
    }

    .welcome-section {
        margin-bottom: 3rem;
    }

    .welcome-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1rem;
    }

    .welcome-subtitle {
        font-size: 1.1rem;
        color: #718096;
        margin-bottom: 2rem;
    }

    .business-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .business-option {
        background: #f8fafc;
        border: 3px solid #e2e8f0;
        border-radius: 15px;
        padding: 2rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        position: relative;
        overflow: hidden;
    }

    .business-option:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border-color: #020258;
        text-decoration: none;
        color: inherit;
    }

    .business-option.products {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .business-option.services {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .business-option.both {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .business-option::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.6s ease;
    }

    .business-option:hover::before {
        left: 100%;
    }

    .business-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        display: block;
    }

    .business-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .business-description {
        font-size: 0.95rem;
        opacity: 0.9;
        line-height: 1.5;
    }

    .info-section {
        background: #f7fafc;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .info-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
    }

    .info-text {
        font-size: 0.9rem;
        color: #4a5568;
        line-height: 1.6;
    }

    .skip-section {
        padding-top: 2rem;
        border-top: 1px solid #e2e8f0;
    }

    .skip-link {
        color: #718096;
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s ease;
    }

    .skip-link:hover {
        color: #020258;
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .business-type-card {
            padding: 2rem;
            margin: 1rem;
        }

        .welcome-title {
            font-size: 2rem;
        }

        .business-options {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .business-option {
            padding: 1.5rem;
        }
    }
</style>

<div class="business-type-container">
    <div class="business-type-card">
        <div class="welcome-section">
            <h1 class="welcome-title">
                <i class="fas fa-rocket" style="color: #020258;"></i>
                Welcome to Shopybook!
            </h1>
            <p class="welcome-subtitle">
                Let's get you started by understanding your business better. What type of business do you operate?
            </p>
        </div>

        <div class="info-section">
            <h3 class="info-title">
                <i class="fas fa-info-circle" style="color: #020258;"></i>
                Good to know
            </h3>
            <p class="info-text">
                You can have up to 2 businesses on Shopybook. Don't worry - you can always add another business type later or modify your selection.
            </p>
        </div>

        <div class="business-options">
            <a href="{{ route('business.create', ['type' => 'product']) }}" class="business-option products">
                <i class="fas fa-shopping-cart business-icon"></i>
                <h3 class="business-title">I Sell Products</h3>
                <p class="business-description">
                    Perfect for retail stores, online shops, wholesalers, and businesses that sell physical or digital products.
                </p>
            </a>

            <a href="{{ route('business.create', ['type' => 'service']) }}" class="business-option services">
                <i class="fas fa-hands-helping business-icon"></i>
                <h3 class="business-title">I Provide Services</h3>
                <p class="business-description">
                    Ideal for consultants, salons, repair services, freelancers, and businesses that offer services to clients.
                </p>
            </a>

            <a href="{{ route('business.create', ['type' => 'hybrid']) }}" class="business-option both">
                <i class="fas fa-store business-icon"></i>
                <h3 class="business-title">I Do Both</h3>
                <p class="business-description">
                    Great for businesses that sell products AND provide services, like a salon that also sells beauty products.
                </p>
            </a>
        </div>

        <div class="skip-section">
            <p>
                <a href="{{ route('dashboard') }}" class="skip-link">
                    <i class="fas fa-arrow-right"></i>
                    Skip for now - I'll set this up later
                </a>
            </p>
        </div>
    </div>
</div>
@endsection

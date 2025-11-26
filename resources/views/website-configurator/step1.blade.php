@extends('layouts.dash')

@section('title', 'Create Your Website - Step 1')

@section('content')
<style>
    :root {
        --primary-purple: #8b5cf6;
        --dark-bg: #1e293b;
        --darker-bg: #0f172a;
        --card-hover: #334155;
    }
    
    body {
        background: var(--darker-bg);
        color: #e2e8f0;
    }
    
    .configurator-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 3rem 1.5rem;
    }
    
    .hero-section {
        text-align: center;
        margin-bottom: 4rem;
    }
    
    .hero-title {
        font-size: 3.5rem;
        font-weight: 300;
        color: #fff;
        margin-bottom: 1rem;
        line-height: 1.2;
    }
    
    .hero-subtitle {
        font-size: 1.5rem;
        color: #94a3b8;
        font-weight: 300;
    }
    
    .website-types-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    
    .website-type-card {
        background: var(--dark-bg);
        border: 2px solid transparent;
        border-radius: 12px;
        padding: 2.5rem 2rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        position: relative;
    }
    
    .website-type-card:hover {
        border-color: var(--primary-purple);
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(139, 92, 246, 0.2);
        background: var(--card-hover);
    }
    
    .website-type-card.selected {
        border-color: var(--primary-purple);
        background: var(--card-hover);
        box-shadow: 0 20px 40px rgba(139, 92, 246, 0.3);
    }
    
    .type-icon {
        font-size: 4rem;
        margin-bottom: 1.5rem;
        display: block;
    }
    
    .type-name {
        font-size: 1.5rem;
        font-weight: 600;
        color: #fff;
        margin-bottom: 0.75rem;
    }
    
    .type-description {
        font-size: 0.95rem;
        color: #94a3b8;
        line-height: 1.6;
    }
    
    .checkmark {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 32px;
        height: 32px;
        background: var(--primary-purple);
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
    }
    
    .website-type-card.selected .checkmark {
        display: flex;
    }
    
    .action-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 3rem;
    }
    
    .btn-next {
        background: var(--primary-purple);
        color: white;
        padding: 1rem 3rem;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-next:hover:not(:disabled) {
        background: #7c3aed;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(139, 92, 246, 0.3);
    }
    
    .btn-next:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .btn-skip {
        background: transparent;
        color: #94a3b8;
        padding: 1rem 2rem;
        border: none;
        cursor: pointer;
        font-size: 1rem;
        transition: color 0.3s;
    }
    
    .btn-skip:hover {
        color: #0ea5e9;
    }
    
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
        }
        
        .website-types-grid {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            flex-direction: column-reverse;
            gap: 1rem;
        }
        
        .btn-next {
            width: 100%;
        }
    }
</style>

<div class="configurator-container">
    <!-- Hero Section -->
    <div class="hero-section">
        <h1 class="hero-title">Ready to build the<br>perfect website?</h1>
        <p class="hero-subtitle">We'll set you up and running in <strong>4 steps</strong></p>
    </div>

    <!-- Website Types Grid -->
    <form action="{{ route('website-configurator.step1.submit') }}" method="POST" id="step1Form">
        @csrf
        <input type="hidden" name="website_type" id="selectedType">
        
        <div class="website-types-grid">
            <!-- Business Website -->
            <div class="website-type-card" data-type="business">
                <div class="checkmark">
                    <i class="fas fa-check"></i>
                </div>
                <span class="type-icon">🏢</span>
                <h3 class="type-name">Business Website</h3>
                <p class="type-description">Professional site for your company with services, team, and contact info</p>
            </div>

            <!-- Online Store -->
            <div class="website-type-card" data-type="online_store">
                <div class="checkmark">
                    <i class="fas fa-check"></i>
                </div>
                <span class="type-icon">🛍️</span>
                <h3 class="type-name">Online Store</h3>
                <p class="type-description">Sell your products online with shopping cart and payment integration</p>
            </div>

            <!-- Service Business -->
            <div class="website-type-card" data-type="service">
                <div class="checkmark">
                    <i class="fas fa-check"></i>
                </div>
                <span class="type-icon">🛠️</span>
                <h3 class="type-name">Service Business</h3>
                <p class="type-description">Showcase your services with online booking and appointment scheduling</p>
            </div>

            <!-- Restaurant -->
            <div class="website-type-card" data-type="restaurant">
                <div class="checkmark">
                    <i class="fas fa-check"></i>
                </div>
                <span class="type-icon">🍽️</span>
                <h3 class="type-name">Restaurant</h3>
                <p class="type-description">Display your menu, take reservations, and showcase your dining experience</p>
            </div>

            <!-- Portfolio -->
            <div class="website-type-card" data-type="portfolio">
                <div class="checkmark">
                    <i class="fas fa-check"></i>
                </div>
                <span class="type-icon">🎨</span>
                <h3 class="type-name">Portfolio</h3>
                <p class="type-description">Showcase your work, projects, and creative services beautifully</p>
            </div>

            <!-- Blog -->
            <div class="website-type-card" data-type="blog">
                <div class="checkmark">
                    <i class="fas fa-check"></i>
                </div>
                <span class="type-icon">📝</span>
                <h3 class="type-name">Blog</h3>
                <p class="type-description">Share your thoughts, stories, and expertise with the world</p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('website.builder.index') }}" class="btn-skip">
                <i class="fas fa-arrow-left me-2"></i>Skip and start from scratch
            </a>
            
            <button type="submit" class="btn-next" id="nextBtn" disabled>
                Continue <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.website-type-card');
    const selectedTypeInput = document.getElementById('selectedType');
    const nextBtn = document.getElementById('nextBtn');
    
    cards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selection from all cards
            cards.forEach(c => c.classList.remove('selected'));
            
            // Select this card
            this.classList.add('selected');
            
            // Update hidden input
            const type = this.dataset.type;
            selectedTypeInput.value = type;
            
            // Enable next button
            nextBtn.disabled = false;
        });
    });
});
</script>
@endsection

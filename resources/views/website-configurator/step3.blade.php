@extends('layouts.dash')

@section('title', 'Select Pages & Features - Step 3')

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
    
    .alert {
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.95rem;
    }
    
    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #10b981;
    }
    
    .alert-error,
    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #ef4444;
    }
    
    .alert-icon {
        font-size: 1.2rem;
    }
    
    .configurator-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 3rem 1.5rem;
    }
    
    .progress-bar {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 4rem;
        gap: 1rem;
    }
    
    .progress-step {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .step-circle.completed {
        background: #10b981;
        color: white;
    }
    
    .step-circle.active {
        background: var(--primary-purple);
        color: white;
        box-shadow: 0 0 20px rgba(139, 92, 246, 0.5);
    }
    
    .step-circle.pending {
        background: var(--dark-bg);
        color: #64748b;
        border: 2px solid #475569;
    }
    
    .step-label {
        font-size: 0.85rem;
        color: #94a3b8;
    }
    
    .step-label.active {
        color: white;
        font-weight: 600;
    }
    
    .progress-line {
        width: 60px;
        height: 2px;
        background: #475569;
    }
    
    .progress-line.completed {
        background: #10b981;
    }
    
    .header-section {
        text-align: center;
        margin-bottom: 3rem;
    }
    
    .main-title {
        font-size: 2.5rem;
        font-weight: 300;
        color: #fff;
        margin-bottom: 0.5rem;
    }
    
    .main-subtitle {
        font-size: 1.1rem;
        color: #94a3b8;
        font-weight: 300;
    }
    
    .section-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .section-icon {
        width: 28px;
        height: 28px;
        color: var(--primary-purple);
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: white;
    }
    
    .selection-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
        margin-bottom: 3rem;
    }
    
    .selection-card {
        background: var(--dark-bg);
        border: 2px solid #334155;
        border-radius: 10px;
        padding: 1.25rem;
        cursor: pointer;
        transition: all 0.25s ease;
        position: relative;
    }
    
    .selection-card:hover {
        border-color: var(--primary-purple);
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(139, 92, 246, 0.15);
    }
    
    .selection-card.selected {
        border-color: var(--primary-purple);
        background: #2d1b4e;
        box-shadow: 0 10px 25px rgba(139, 92, 246, 0.2);
    }
    
    .selection-card.disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    /* Theme-specific styles */
    .theme-grid {
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    }
    
    .theme-card {
        padding: 0;
        overflow: hidden;
    }
    
    .theme-preview-container {
        width: 100%;
        height: 200px;
        overflow: hidden;
        background: var(--darker-bg);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    @media (min-width: 768px) {
        .theme-preview-container {
            height: 220px;
        }
    }
    
    @media (min-width: 1200px) {
        .theme-preview-container {
            height: 250px;
        }
    }
    
    .theme-preview-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .theme-card:hover .theme-preview-image {
        transform: scale(1.05);
    }
    
    .theme-preview-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .theme-card .card-header {
        padding: 1.25rem;
    }
    
    .theme-style-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .theme-note {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.2);
        border-radius: 8px;
        margin-top: -1.5rem;
        margin-bottom: 3rem;
        font-size: 0.9rem;
        color: #94a3b8;
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
    }
    
    .card-content {
        flex: 1;
    }
    
    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: white;
        margin-bottom: 0.35rem;
    }
    
    .card-description {
        font-size: 0.875rem;
        color: #94a3b8;
        line-height: 1.5;
    }
    
    .card-icon {
        width: 20px;
        height: 20px;
        color: var(--primary-purple);
        flex-shrink: 0;
    }
    
    .checkbox-indicator {
        width: 22px;
        height: 22px;
        border: 2px solid #475569;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    
    .radio-indicator {
        border-radius: 50%;
    }
    
    .selection-card.selected .checkbox-indicator {
        background: var(--primary-purple);
        border-color: var(--primary-purple);
    }
    
    .checkbox-check {
        display: none;
        width: 12px;
        height: 12px;
        color: white;
    }
    
    .selection-card.selected .checkbox-check {
        display: block;
    }
    
    .required-badge {
        display: inline-block;
        background: rgba(139, 92, 246, 0.2);
        color: var(--primary-purple);
        padding: 0.25rem 0.6rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }
    
    .summary-box {
        background: var(--dark-bg);
        border: 2px solid #334155;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .summary-text {
        font-size: 1rem;
        color: #94a3b8;
    }
    
    .summary-count {
        color: var(--primary-purple);
        font-weight: 700;
        font-size: 1.1rem;
    }
    
    .build-time {
        text-align: right;
    }
    
    .build-time-label {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 0.25rem;
    }
    
    .build-time-value {
        font-size: 1.4rem;
        color: var(--primary-purple);
        font-weight: 700;
    }
    
    .action-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
    }
    
    .btn {
        padding: 0.9rem 2.5rem;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-back {
        background: transparent;
        color: #94a3b8;
        border: 2px solid #475569;
    }
    
    .btn-back:hover {
        color: white;
        border-color: #64748b;
    }
    
    .btn-next {
        background: var(--primary-purple);
        color: white;
    }
    
    .btn-next:hover {
        background: #7c3aed;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(139, 92, 246, 0.3);
    }
    
    .btn-icon {
        width: 18px;
        height: 18px;
    }
    
    .tips-box {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.3);
        border-radius: 10px;
        padding: 1.25rem;
        margin-top: 2rem;
    }
    
    .tips-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    
    .tips-icon {
        width: 20px;
        height: 20px;
        color: #60a5fa;
    }
    
    .tips-title {
        font-size: 1rem;
        font-weight: 600;
        color: #93c5fd;
    }
    
    .tips-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .tips-list li {
        font-size: 0.9rem;
        color: #bfdbfe;
        padding: 0.25rem 0;
    }
</style>

<div class="configurator-container">
    <!-- Error/Success Messages -->
    @if(session('success'))
    <div class="alert alert-success">
        <span class="alert-icon">✓</span>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-error">
        <span class="alert-icon">⚠</span>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <span class="alert-icon">⚠</span>
        <div>
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 0.5rem 0 0 1.5rem; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- Progress Bar -->
    <div class="progress-bar">
        <div class="progress-step">
            <div class="step-circle completed">✓</div>
            <span class="step-label">Type</span>
        </div>
        <div class="progress-line completed"></div>
        <div class="progress-step">
            <div class="step-circle completed">✓</div>
            <span class="step-label">Details</span>
        </div>
        <div class="progress-line completed"></div>
        <div class="progress-step">
            <div class="step-circle active">3</div>
            <span class="step-label active">Pages</span>
        </div>
        <div class="progress-line"></div>
        <div class="progress-step">
            <div class="step-circle pending">4</div>
            <span class="step-label">Build</span>
        </div>
    </div>

    <!-- Header -->
    <div class="header-section">
        <h1 class="main-title">Customize Your Website</h1>
        <p class="main-subtitle">Choose your theme, pages, and features</p>
    </div>

    <form id="step3Form" method="POST" action="{{ route('website-configurator.step3.submit') }}">
        @csrf

        <!-- Pages Section -->
        <div>
            <div class="section-header">
                <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <h2 class="section-title">Pages</h2>
            </div>

            <div class="selection-grid">
                @php
                $pages = [
                    ['id' => 'home', 'name' => 'Home', 'description' => 'Landing page', 'required' => true],
                    ['id' => 'about', 'name' => 'About Us', 'description' => 'Company information', 'required' => false],
                    ['id' => 'services', 'name' => 'Services', 'description' => 'What you offer', 'required' => false],
                    ['id' => 'products', 'name' => 'Products', 'description' => 'Product catalog', 'required' => false],
                    ['id' => 'gallery', 'name' => 'Gallery', 'description' => 'Photo showcase', 'required' => false],
                    ['id' => 'blog', 'name' => 'Blog', 'description' => 'Articles & news', 'required' => false],
                    ['id' => 'team', 'name' => 'Our Team', 'description' => 'Meet the team', 'required' => false],
                    ['id' => 'testimonials', 'name' => 'Testimonials', 'description' => 'Customer reviews', 'required' => false],
                    ['id' => 'contact', 'name' => 'Contact', 'description' => 'Get in touch', 'required' => true],
                ];
                @endphp

                @foreach($pages as $page)
                <div class="selection-card {{ $page['required'] ? 'selected disabled' : '' }}" onclick="{{ $page['required'] ? '' : 'toggleSelection(this, \'page_' . $page['id'] . '\')' }}">
                    <input type="checkbox" id="page_{{ $page['id'] }}" name="pages[]" value="{{ $page['id'] }}" style="display: none;" {{ $page['required'] ? 'checked disabled' : '' }}>
                    <div class="card-header">
                        <div class="card-content">
                            <div class="card-title">{{ $page['name'] }}</div>
                            <div class="card-description">{{ $page['description'] }}</div>
                            @if($page['required'])
                            <span class="required-badge">Required</span>
                            @endif
                        </div>
                        <div class="checkbox-indicator">
                            <svg class="checkbox-check" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Theme Selection Section -->
        <div>
            <div class="section-header">
                <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                </svg>
                <h2 class="section-title">Choose Your Theme</h2>
            </div>

            <div class="selection-grid theme-grid">
                @foreach($themes as $theme)
                <div class="selection-card theme-card" data-theme-id="{{ $theme->slug }}" onclick="selectTheme(this, '{{ $theme->slug }}')">
                    <input type="radio" id="theme_{{ $theme->slug }}" name="theme" value="{{ $theme->slug }}" style="display: none;" required>
                    
                    <!-- Theme Preview Image -->
                    <div class="theme-preview-container">
                        @if($theme->thumbnail)
                            <img src="{{ $theme->thumbnail }}" alt="{{ $theme->name }}" class="theme-preview-image">
                        @else
                            <div class="theme-preview-placeholder" style="background: linear-gradient(135deg, {{ $theme->default_colors['primary'] ?? '#4F46E5' }} 0%, {{ $theme->default_colors['secondary'] ?? '#6366F1' }} 100%)">
                                <i class="fas fa-paint-brush fa-2x" style="color: white; opacity: 0.6;"></i>
                            </div>
                        @endif
                    </div>
                    
                    <div class="card-header">
                        <div class="card-content">
                            <div class="card-title">{{ $theme->name }}</div>
                            <div class="card-description">{{ $theme->description }}</div>
                            
                            <!-- Theme Badge -->
                            <div style="margin-top: 0.5rem;">
                                <span class="theme-style-badge" style="background: {{ $theme->default_colors['primary'] ?? '#4F46E5' }}20; color: {{ $theme->default_colors['primary'] ?? '#4F46E5' }};">
                                    {{ ucfirst($theme->style) }}
                                </span>
                            </div>
                        </div>
                        <div class="checkbox-indicator radio-indicator">
                            <svg class="checkbox-check" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="theme-note">
                <svg style="width: 20px; height: 20px; color: #60a5fa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>You can change your theme anytime from the dashboard</span>
            </div>
        </div>

        <!-- Features Section -->
        <div>
            <div class="section-header">
                <svg class="section-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <h2 class="section-title">Features</h2>
            </div>

            <div class="selection-grid">
                @php
                $features = [
                    ['id' => 'contact_form', 'name' => 'Contact Form', 'description' => 'Let visitors reach you'],
                    ['id' => 'newsletter', 'name' => 'Newsletter', 'description' => 'Collect email subscribers'],
                    ['id' => 'social_media', 'name' => 'Social Media Links', 'description' => 'Connect social profiles'],
                    ['id' => 'google_maps', 'name' => 'Google Maps', 'description' => 'Show your location'],
                    ['id' => 'online_booking', 'name' => 'Online Booking', 'description' => 'Accept appointments'],
                    ['id' => 'live_chat', 'name' => 'Live Chat', 'description' => 'Chat with visitors'],
                    ['id' => 'search', 'name' => 'Search', 'description' => 'Site-wide search'],
                    ['id' => 'multilingual', 'name' => 'Multi-language', 'description' => 'Multiple languages'],
                    ['id' => 'analytics', 'name' => 'Analytics', 'description' => 'Track visitor stats'],
                ];
                @endphp

                @foreach($features as $feature)
                <div class="selection-card" onclick="toggleSelection(this, 'feature_{{ $feature['id'] }}')">
                    <input type="checkbox" id="feature_{{ $feature['id'] }}" name="features[]" value="{{ $feature['id'] }}" style="display: none;">
                    <div class="card-header">
                        <div class="card-content">
                            <div class="card-title">{{ $feature['name'] }}</div>
                            <div class="card-description">{{ $feature['description'] }}</div>
                        </div>
                        <div class="checkbox-indicator">
                            <svg class="checkbox-check" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Summary Box -->
        <div class="summary-box">
            <div class="summary-info">
                <span class="summary-text">
                    <span class="summary-count" id="pagesCount">2</span> pages selected, 
                    <span class="summary-count" id="featuresCount">0</span> features selected
                </span>
            </div>
            <div class="build-time">
                <div class="build-time-label">Estimated build time</div>
                <div class="build-time-value" id="buildTime">~30s</div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('website-configurator.step2') }}" class="btn btn-back">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back
            </a>
            <button type="submit" class="btn btn-next">
                Continue to Build
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>

        <!-- Tips Box -->
        <div class="tips-box">
            <div class="tips-header">
                <svg class="tips-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="tips-title">💡 Pro Tips</span>
            </div>
            <ul class="tips-list">
                <li>• You can always add or remove pages later</li>
                <li>• Start simple - you can enable more features as you grow</li>
                <li>• Contact form is recommended for all business websites</li>
            </ul>
        </div>
    </form>
</div>

<script>
function toggleSelection(card, checkboxId) {
    if (card.classList.contains('disabled')) return;
    
    const checkbox = document.getElementById(checkboxId);
    checkbox.checked = !checkbox.checked;
    card.classList.toggle('selected');
    updateSummary();
}

function selectTheme(card, themeId) {
    // Remove selected class from all theme cards
    document.querySelectorAll('.theme-card').forEach(themeCard => {
        themeCard.classList.remove('selected');
    });
    
    // Uncheck all theme radios
    document.querySelectorAll('input[name="theme"]').forEach(radio => {
        radio.checked = false;
    });
    
    // Select this theme
    card.classList.add('selected');
    document.getElementById('theme_' + themeId).checked = true;
}

function updateSummary() {
    const pagesCount = document.querySelectorAll('input[name="pages[]"]:checked').length;
    const featuresCount = document.querySelectorAll('input[name="features[]"]:checked').length;
    
    document.getElementById('pagesCount').textContent = pagesCount;
    document.getElementById('featuresCount').textContent = featuresCount;
    
    // Estimate build time (base 30s + 5s per page + 3s per feature)
    const estimatedTime = 30 + (pagesCount * 5) + (featuresCount * 3);
    document.getElementById('buildTime').textContent = `~${estimatedTime}s`;
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    updateSummary();
    
    document.getElementById('step3Form').addEventListener('submit', function(e) {
        const themeSelected = document.querySelector('input[name="theme"]:checked');
        
        if (!themeSelected) {
            e.preventDefault();
            alert('⚠️ Please select a theme for your website');
            
            // Scroll to theme section
            document.querySelector('.theme-card').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
            return false;
        }
    });
});
</script>
@endsection


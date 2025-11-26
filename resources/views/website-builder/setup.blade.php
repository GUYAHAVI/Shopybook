@extends('layouts.dash')

@section('title', 'Create Your Website')

@section('content')
<style>
    .theme-card-wrapper {
        position: relative;
        cursor: pointer;
    }
    
    .theme-card {
        background: var(--card-bg);
        border: 2px solid var(--border-color);
        border-radius: 0.75rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .theme-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px var(--shadow-color);
    }
    
    .theme-card.selected {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(2, 2, 88, 0.1);
    }
    
    .theme-preview {
        height: 180px;
        position: relative;
        overflow: hidden;
    }
    
    .theme-checkmark {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        background: var(--success-color);
        color: var(--white);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
    }
    
    .theme-card.selected .theme-checkmark {
        display: flex;
    }
    
    @media (max-width: 768px) {
        .theme-preview {
            height: 140px;
        }
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-2" style="color: var(--text-primary); font-weight: 700;">
                    <i class="fas fa-rocket me-2" style="color: var(--primary-color);"></i>Create Your Website
                </h1>
                <p class="text-muted mb-0" style="font-size: 0.95rem;">
                    Build a stunning website for <strong>{{ $business->name }}</strong> in minutes. No coding required!
                </p>
            </div>
            <a href="{{ route('website.builder.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>

    <!-- AI Auto-Build Feature - Visible to All Users -->
    <div class="alert mb-4" style="background: linear-gradient(135deg, {{ $business->isEnterprise() ? '#f093fb 0%, #f5576c' : '#667eea 0%, #764ba2' }} 100%); border: none; color: white;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-start gap-3">
                <i class="fas fa-crown" style="font-size: 2rem;"></i>
                <div>
                    <h5 class="mb-2" style="font-weight: 700;">
                        🚀 {{ $business->isEnterprise() ? 'Enterprise Power: AI Auto-Build' : 'AI Auto-Build - Enterprise Feature' }}
                    </h5>
                    <p class="mb-0" style="font-size: 0.95rem; opacity: 0.95;">
                        Let Claude AI build your ENTIRE website automatically! Complete with 5-7 pages, professional content, 
                        and SEO optimization. Review and edit after creation.
                        @if(!$business->isEnterprise())
                        <span style="font-weight: 600;"> Upgrade to Enterprise to unlock!</span>
                        @endif
                    </p>
                </div>
            </div>
            <button type="button" class="btn btn-light btn-lg" id="autoBuildBtn" 
                    style="font-weight: 600; white-space: nowrap;"
                    data-is-enterprise="{{ $business->isEnterprise() ? 'true' : 'false' }}">
                <i class="fas fa-magic me-2"></i>Auto-Build Complete Website
            </button>
        </div>
    </div>

    <!-- Setup Form -->
    <form action="{{ route('website.builder.create') }}" method="POST" id="websiteSetupForm">
        @csrf

        <!-- Step 1: Choose Theme -->
        <div class="card mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--border-color);">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <span class="badge rounded-circle d-flex align-items-center justify-center me-3" 
                              style="width: 36px; height: 36px; background: var(--primary-color); font-size: 1rem;">1</span>
                        <h2 class="mb-0 h5" style="color: var(--text-primary); font-weight: 600;">Choose Your Theme</h2>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" id="recommendThemeBtn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="fas fa-magic me-2"></i>AI Recommend Theme
                    </button>
                </div>
            </div>
            <div class="card-body p-4">
                <!-- AI Recommendations Section (Initially Hidden) -->
                <div id="aiRecommendations" class="alert alert-info d-none mb-4" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border: 1px solid #667eea;">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-robot me-3 mt-1" style="font-size: 1.5rem; color: #667eea;"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-2" style="color: #667eea; font-weight: 600;">AI Theme Recommendations</h6>
                            <div id="recommendationsContent">
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 mb-0">Claude AI is analyzing your business...</p>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" id="closeRecommendations"></button>
                    </div>
                </div>

                <div class="row g-3 g-md-4">
                        @foreach($themes as $theme)
                        <div class="col-12 col-md-6 col-lg-4">
                            <label class="theme-card-wrapper" for="theme_{{ $theme->id }}">
                                <input type="radio" name="theme_id" value="{{ $theme->id }}" 
                                       id="theme_{{ $theme->id }}" 
                                       class="d-none theme-radio" 
                                       required {{ $loop->first ? 'checked' : '' }}>
                                <div class="theme-card {{ $loop->first ? 'selected' : '' }}">
                                    <!-- Theme Preview -->
                                    <div class="theme-preview" style="background: linear-gradient(135deg, {{ $theme->default_colors['primary'] ?? '#4F46E5' }} 0%, {{ $theme->default_colors['secondary'] ?? '#6366F1' }} 100%)">
                                        <div class="d-flex align-items-center justify-content-center h-100 position-relative">
                                            <!-- Mock Website Preview -->
                                            <div class="w-100 px-3">
                                                <!-- Mock Header -->
                                                <div class="bg-white rounded-top p-2 mb-1" style="opacity: 0.95;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="d-flex gap-1">
                                                            <div style="width: 40px; height: 6px; background: {{ $theme->default_colors['primary'] ?? '#4F46E5' }}; border-radius: 3px;"></div>
                                                            <div style="width: 30px; height: 6px; background: #e5e7eb; border-radius: 3px;"></div>
                                                        </div>
                                                        <div class="d-flex gap-1">
                                                            <div style="width: 20px; height: 6px; background: #e5e7eb; border-radius: 3px;"></div>
                                                            <div style="width: 25px; height: 6px; background: #e5e7eb; border-radius: 3px;"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Mock Content -->
                                                <div class="bg-white p-2" style="opacity: 0.95;">
                                                    <div style="width: 70%; height: 8px; background: {{ $theme->default_colors['primary'] ?? '#4F46E5' }}; border-radius: 4px; margin-bottom: 4px; opacity: 0.9;"></div>
                                                    <div style="width: 50%; height: 5px; background: #d1d5db; border-radius: 3px; margin-bottom: 6px;"></div>
                                                    <div style="width: 35%; height: 12px; background: {{ $theme->default_colors['accent'] ?? '#F59E0B' }}; border-radius: 6px;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Checkmark -->
                                        <div class="theme-checkmark">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Theme Info -->
                                    <div class="p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h3 class="mb-0 h6 fw-bold" style="color: var(--text-primary); font-size: 0.95rem;">
                                                {{ $theme->name }}
                                            </h3>
                                            @if(!$theme->is_free)
                                            <span class="badge" style="background: var(--warning-color); font-size: 0.7rem;">Premium</span>
                                            @else
                                            <span class="badge" style="background: var(--success-color); font-size: 0.7rem;">Free</span>
                                            @endif
                                        </div>
                                        <p class="text-muted mb-2" style="font-size: 0.85rem; line-height: 1.5;">
                                            {{ $theme->description }}
                                        </p>
                                        @if(isset($theme->style) || isset($theme->category))
                                        <div class="d-flex gap-2 flex-wrap mb-2">
                                            @if(isset($theme->style))
                                            <span class="badge bg-light text-dark" style="font-size: 0.7rem; font-weight: 500;">
                                                {{ ucfirst($theme->style) }}
                                            </span>
                                            @endif
                                            @if(isset($theme->category))
                                            <span class="badge bg-light text-dark" style="font-size: 0.7rem; font-weight: 500;">
                                                {{ ucfirst($theme->category) }}
                                            </span>
                                            @endif
                                        </div>
                                        @endif
                                        <!-- Preview Button -->
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary w-100 preview-theme-btn" 
                                                data-theme-id="{{ $theme->id }}"
                                                style="font-size: 0.8rem; padding: 0.4rem 0.75rem;">
                                            <i class="fas fa-eye me-1"></i>Preview Theme
                                        </button>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Basic Information -->
        <div class="card mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-header" style="background: transparent; border-bottom: 1px solid var(--border-color);">
                <div class="d-flex align-items-center">
                    <span class="badge rounded-circle d-flex align-items-center justify-center me-3" 
                          style="width: 36px; height: 36px; background: var(--primary-color); font-size: 1rem;">2</span>
                    <h2 class="mb-0 h5" style="color: var(--text-primary); font-weight: 600;">Website Information</h2>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-medium" style="color: var(--text-secondary); font-size: 0.9rem;">
                            Business Name
                        </label>
                        <input type="text" name="business_name" 
                               value="{{ old('business_name', $business->name) }}" 
                               class="form-control" 
                               style="padding: 0.75rem; font-size: 0.95rem;"
                               required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-medium" style="color: var(--text-secondary); font-size: 0.9rem;">
                            Tagline
                        </label>
                        <input type="text" name="tagline" 
                               value="{{ old('tagline', $business->description) }}" 
                               placeholder="Your catchy tagline..."
                               class="form-control" 
                               style="padding: 0.75rem; font-size: 0.95rem;">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-medium" style="color: var(--text-secondary); font-size: 0.9rem;">
                            Contact Email
                        </label>
                        <input type="email" name="contact_email" 
                               value="{{ old('contact_email', $business->email) }}" 
                               class="form-control" 
                               style="padding: 0.75rem; font-size: 0.95rem;">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-medium" style="color: var(--text-secondary); font-size: 0.9rem;">
                            Contact Phone
                        </label>
                        <input type="text" name="contact_phone" 
                               value="{{ old('contact_phone', $business->phone) }}" 
                               class="form-control" 
                               style="padding: 0.75rem; font-size: 0.95rem;">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium" style="color: var(--text-secondary); font-size: 0.9rem;">
                            About Your Business
                        </label>
                        <textarea name="about_text" rows="4" 
                                  placeholder="Tell your story... What makes your business special?"
                                  class="form-control" 
                                  style="padding: 0.75rem; font-size: 0.95rem; line-height: 1.6;">{{ old('about_text', $business->description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center mb-4">
            <button type="submit" 
                    id="createWebsiteBtn"
                    class="btn btn-lg px-5 py-3" 
                    style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); 
                           border: none; 
                           color: white; 
                           font-weight: 700; 
                           font-size: 1.1rem;
                           box-shadow: 0 4px 12px rgba(2, 2, 88, 0.3);
                           transition: all 0.3s ease;">
                <i class="fas fa-rocket me-2"></i>Create My Website
            </button>
            <p class="text-muted mt-3 mb-0" style="font-size: 0.9rem;">
                Your website will be live at: <strong style="color: var(--primary-color);">{{ $business->slug }}.shopybook.com</strong>
            </p>
        </div>
    </form>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: none; border-radius: 1rem; overflow: hidden;">
                <div class="modal-body text-center p-5">
                    <div class="mb-4">
                        <div class="success-checkmark mx-auto mb-3" style="width: 80px; height: 80px;">
                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                                <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                                <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                            </svg>
                        </div>
                    </div>
                    <h3 class="mb-3" style="color: var(--text-primary); font-weight: 700; font-size: 1.5rem;">
                        🎉 Website Created Successfully!
                    </h3>
                    <p class="text-muted mb-4" style="font-size: 1rem; line-height: 1.6;">
                        Your website is ready! You can preview it now or go back to make changes.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4" id="goBackBtn"
                                style="font-weight: 600;">
                            <i class="fas fa-arrow-left me-2"></i>Go Back & Edit
                        </button>
                        <button type="button" class="btn btn-primary btn-lg px-4" id="continueToPreviewBtn"
                                style="background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); 
                                       border: none; 
                                       font-weight: 600;
                                       box-shadow: 0 4px 12px rgba(2, 2, 88, 0.3);">
                            <i class="fas fa-eye me-2"></i>Continue to Preview
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features -->
    <div class="row g-4 mt-4 mb-5">
        <div class="col-12">
            <h3 class="text-center mb-4 h5" style="color: var(--text-primary); font-weight: 600;">
                What You Get
            </h3>
        </div>
        <div class="col-12 col-md-4">
            <div class="text-center p-4">
                <div style="font-size: 3rem;" class="mb-3">⚡</div>
                <h4 class="h6 fw-bold mb-2" style="color: var(--text-primary);">Lightning Fast</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem; line-height: 1.6;">
                    Your website will load in milliseconds
                </p>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="text-center p-4">
                <div style="font-size: 3rem;" class="mb-3">📱</div>
                <h4 class="h6 fw-bold mb-2" style="color: var(--text-primary);">Mobile Ready</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem; line-height: 1.6;">
                    Looks perfect on all devices
                </p>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="text-center p-4">
                <div style="font-size: 3rem;" class="mb-3">🔍</div>
                <h4 class="h6 fw-bold mb-2" style="color: var(--text-primary);">SEO Optimized</h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem; line-height: 1.6;">
                    Get found on Google easily
                </p>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;">
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
@endif

@push('scripts')
<style>
    /* Success Modal Animation */
    .checkmark__circle {
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-width: 2;
        stroke-miterlimit: 10;
        stroke: #28a745;
        fill: none;
        animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }

    .checkmark {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: block;
        stroke-width: 2;
        stroke: #fff;
        stroke-miterlimit: 10;
        box-shadow: inset 0px 0px 0px #28a745;
        animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;
    }

    .checkmark__check {
        transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        stroke: #28a745;
        stroke-width: 3;
        animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
    }

    @keyframes stroke {
        100% {
            stroke-dashoffset: 0;
        }
    }

    @keyframes scale {
        0%, 100% {
            transform: none;
        }
        50% {
            transform: scale3d(1.1, 1.1, 1);
        }
    }

    @keyframes fill {
        100% {
            box-shadow: inset 0px 0px 0px 30px #28a745;
        }
    }

    /* Loading state for button */
    .btn-loading {
        position: relative;
        pointer-events: none;
    }

    .btn-loading::after {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        top: 50%;
        left: 50%;
        margin-left: -8px;
        margin-top: -8px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spinner 0.6s linear infinite;
    }

    @keyframes spinner {
        to { transform: rotate(360deg); }
    }
</style>

<script>
    // Theme card selection
    document.querySelectorAll('.theme-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove selected class from all cards
            document.querySelectorAll('.theme-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to checked card
            if (this.checked) {
                this.closest('.theme-card-wrapper').querySelector('.theme-card').classList.add('selected');
            }
        });
    });
    
    // Theme Preview - Simple link approach to avoid popup blocker
    document.querySelectorAll('.preview-theme-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const themeId = this.dataset.themeId;
            const previewUrl = `{{ url('/website-builder/preview-theme') }}/${themeId}`;
            
            // Open preview in new tab - this avoids popup blocker since it's a direct user action
            window.open(previewUrl, '_blank', 'noopener,noreferrer');
        });
    });

    // Handle form submission with AJAX
    document.getElementById('websiteSetupForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = document.getElementById('createWebsiteBtn');
        const btnText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.classList.add('btn-loading');
        submitBtn.innerHTML = '<span style="opacity: 0;">Creating...</span>';
        
        // Prepare form data
        const formData = new FormData(form);
        
        // Send AJAX request
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success modal
                const modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();
                
                // Handle "Go Back & Edit" button click
                document.getElementById('goBackBtn').addEventListener('click', function() {
                    modal.hide();
                    // Reset the form to allow editing
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('btn-loading');
                    submitBtn.innerHTML = btnText;
                    // Scroll to top
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
                
                // Handle "Continue to Preview" button click
                document.getElementById('continueToPreviewBtn').addEventListener('click', function() {
                    window.location.href = data.redirect_url;
                });
            } else {
                // Show error message
                showNotification('error', data.message || 'Failed to create website. Please try again.');
                
                // Reset button
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-loading');
                submitBtn.innerHTML = btnText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'An error occurred. Please try again.');
            
            // Reset button
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-loading');
            submitBtn.innerHTML = btnText;
        });
    });

    // Helper function to show notifications
    function showNotification(type, message) {
        const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
        const icon = type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';
        
        const notification = document.createElement('div');
        notification.className = `alert ${alertClass} alert-dismissible fade show`;
        notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
        notification.innerHTML = `
            <i class="fas ${icon} me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    // AI Theme Recommendation
    document.getElementById('recommendThemeBtn').addEventListener('click', function() {
        const recommendationsDiv = document.getElementById('aiRecommendations');
        const contentDiv = document.getElementById('recommendationsContent');
        
        // Show loading state
        recommendationsDiv.classList.remove('d-none');
        contentDiv.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Claude AI is analyzing your business...</p>
            </div>
        `;

        // Call AI API
        fetch('{{ route("website.builder.ai.recommend-theme") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.recommendations && data.recommendations.length > 0) {
                let html = '<div class="mb-2"><strong>Based on your business profile, we recommend:</strong></div>';
                
                data.recommendations.forEach((rec, index) => {
                    const theme = rec.theme;
                    const badge = index === 0 ? '<span class="badge bg-success ms-2">Top Pick</span>' : '';
                    
                    html += `
                        <div class="card mb-2" style="cursor: pointer; border: 2px solid ${index === 0 ? '#667eea' : '#ddd'};" 
                             onclick="selectRecommendedTheme(${theme.id})">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0 me-3">
                                        <img src="${theme.thumbnail || '/images/themes/default.jpg'}" 
                                             alt="${theme.name}" 
                                             class="rounded" 
                                             style="width: 80px; height: 60px; object-fit: cover;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            ${theme.name} ${badge}
                                            <span class="badge bg-secondary ms-1">${theme.category}</span>
                                        </h6>
                                        <p class="text-muted mb-0" style="font-size: 0.875rem;">${rec.reason}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                contentDiv.innerHTML = html;
            } else {
                contentDiv.innerHTML = '<p class="mb-0">Unable to generate recommendations. Please choose a theme manually.</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            contentDiv.innerHTML = '<p class="text-danger mb-0">Failed to get AI recommendations. Please try again.</p>';
        });
    });

    // Close recommendations panel
    document.getElementById('closeRecommendations')?.addEventListener('click', function() {
        document.getElementById('aiRecommendations').classList.add('d-none');
    });

    // Function to select recommended theme
    window.selectRecommendedTheme = function(themeId) {
        const radio = document.getElementById('theme_' + themeId);
        if (radio) {
            radio.checked = true;
            radio.dispatchEvent(new Event('change'));
            
            // Scroll to theme
            radio.closest('.col-12').scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            showNotification('success', 'Theme selected! Continue with the setup.');
        }
    };

    // Enterprise Feature: Auto-Build Complete Website
    document.getElementById('autoBuildBtn')?.addEventListener('click', function() {
        const isEnterprise = this.getAttribute('data-is-enterprise') === 'true';
        
        // Check if user is Enterprise
        if (!isEnterprise) {
            // Show upgrade modal for non-enterprise users
            const upgradeModal = document.createElement('div');
            upgradeModal.className = 'modal fade show';
            upgradeModal.style.display = 'block';
            upgradeModal.style.backgroundColor = 'rgba(0,0,0,0.8)';
            upgradeModal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; border: none;">
                            <h5 class="modal-title">
                                <i class="fas fa-crown me-2"></i>Enterprise Feature
                            </h5>
                            <button type="button" class="btn-close btn-close-white" onclick="this.closest('.modal').remove()"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="text-center mb-4">
                                <i class="fas fa-magic fa-4x mb-3" style="color: #f093fb;"></i>
                                <h4 class="mb-3">AI Auto-Build Website</h4>
                                <p class="text-muted">Let Claude AI build your complete website automatically in just 1-2 minutes!</p>
                            </div>
                            
                            <div class="alert alert-info mb-4">
                                <h6 class="mb-2"><i class="fas fa-sparkles me-2"></i>What You Get:</h6>
                                <ul class="mb-0">
                                    <li>5-7 professionally designed pages</li>
                                    <li>Business-specific content generated by AI</li>
                                    <li>SEO-optimized meta tags</li>
                                    <li>Fully editable and customizable</li>
                                    <li>Ready to publish in minutes</li>
                                </ul>
                            </div>
                            
                            <div class="p-3 mb-3" style="background: linear-gradient(135deg, rgba(240, 147, 251, 0.1) 0%, rgba(245, 87, 108, 0.1) 100%); border-radius: 8px; border-left: 4px solid #f093fb;">
                                <p class="mb-2"><strong><i class="fas fa-crown me-2"></i>Enterprise Plan Required</strong></p>
                                <p class="mb-0 small text-muted">This premium feature is available exclusively for Enterprise subscribers.</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" onclick="this.closest('.modal').remove()">
                                Maybe Later
                            </button>
                            <a href="{{ route('settings.index') }}#subscription" class="btn btn-lg" 
                               style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; color: white; font-weight: 600;">
                                <i class="fas fa-arrow-up me-2"></i>Upgrade to Enterprise
                            </a>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(upgradeModal);
            return;
        }
        
        // Check if theme is selected
        const selectedTheme = document.querySelector('input[name="theme_id"]:checked');
        if (!selectedTheme) {
            showNotification('error', 'Please select a theme first!');
            return;
        }

        // Confirm action
        if (!confirm('🚀 AI will automatically build your complete website with 5-7 pages and professional content.\n\nThis may take 1-2 minutes. Continue?')) {
            return;
        }

        const btn = this;
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Claude AI is building your website...';

        // Create progress modal
        const progressModal = document.createElement('div');
        progressModal.className = 'modal fade show';
        progressModal.style.display = 'block';
        progressModal.style.backgroundColor = 'rgba(0,0,0,0.8)';
        progressModal.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                    <div class="modal-body text-center p-5">
                        <i class="fas fa-magic fa-3x mb-4" style="animation: pulse 2s infinite;"></i>
                        <h4 class="mb-3">AI is Building Your Website...</h4>
                        <p class="mb-4">Claude is creating pages, generating content, and optimizing SEO.</p>
                        <div class="progress mb-3" style="height: 25px; background: rgba(255,255,255,0.2);">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 style="width: 100%; background: rgba(255,255,255,0.9); color: #667eea; font-weight: 600;">
                                Please wait...
                            </div>
                        </div>
                        <small style="opacity: 0.9;">This may take 1-2 minutes</small>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(progressModal);

        // Call AI auto-build endpoint
        fetch('{{ route("website.builder.ai.auto-build") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                theme_id: selectedTheme.value
            })
        })
        .then(response => response.json())
        .then(data => {
            progressModal.remove();

            if (data.success) {
                // Show success modal with details
                const successModal = document.createElement('div');
                successModal.className = 'modal fade show';
                successModal.style.display = 'block';
                successModal.style.backgroundColor = 'rgba(0,0,0,0.8)';
                
                let pagesHTML = '';
                if (data.pages) {
                    pagesHTML = data.pages.map(page => `
                        <div class="d-flex justify-content-between align-items-center p-2 mb-2" 
                             style="background: rgba(16, 185, 129, 0.1); border-radius: 8px;">
                            <span><i class="fas fa-file-alt me-2"></i>${page.title}</span>
                            <span class="badge bg-success">${page.sections_count} sections</span>
                        </div>
                    `).join('');
                }

                successModal.innerHTML = `
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border: none;">
                                <h5 class="modal-title">
                                    <i class="fas fa-check-circle me-2"></i>Website Built Successfully! 🎉
                                </h5>
                            </div>
                            <div class="modal-body p-4">
                                <div class="alert alert-success mb-4">
                                    <strong>${data.website.pages_count} pages</strong> created with professional content and SEO optimization!
                                </div>
                                <h6 class="mb-3">Created Pages:</h6>
                                ${pagesHTML}
                                <div class="mt-4 p-3" style="background: #f8f9fa; border-radius: 8px;">
                                    <p class="mb-2"><strong>What's next?</strong></p>
                                    <ul class="mb-0">
                                        <li>Review and edit any content</li>
                                        <li>Add images to sections</li>
                                        <li>Customize colors and fonts</li>
                                        <li>Publish when ready!</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button onclick="location.href='{{ route('website.builder.index') }}'" 
                                        class="btn btn-primary btn-lg">
                                    <i class="fas fa-edit me-2"></i>Edit Website
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(successModal);

                // Auto-redirect after 5 seconds
                setTimeout(() => {
                    window.location.href = '{{ route('website.builder.index') }}';
                }, 5000);

            } else if (data.upgrade_required) {
                showNotification('error', 'This feature requires Enterprise subscription. Please upgrade!');
            } else if (data.existing_website) {
                showNotification('error', 'You already have a website. Delete it first to use auto-build.');
            } else {
                showNotification('error', data.error || 'Failed to build website. Please try manually.');
            }
        })
        .catch(error => {
            progressModal.remove();
            console.error('Error:', error);
            showNotification('error', 'An error occurred. Please try again or build manually.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        });
    });

</script>
@endpush
@endsection


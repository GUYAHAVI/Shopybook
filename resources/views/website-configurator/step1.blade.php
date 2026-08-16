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
        background: #ff511a;
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
        <p class="hero-subtitle">Choose how you want to create your website</p>
    </div>

    <!-- Build Options - New Section -->
    <div style="margin-bottom: 4rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; max-width: 900px; margin: 0 auto;">
            <!-- AI Auto-Build Option -->
            <div class="build-option-card" id="aiBuildCard" style="background: linear-gradient(135deg, #7b2e2e 0%, #ff511a 100%); border: 3px solid #7b2e2e; border-radius: 16px; padding: 2.5rem; text-align: center; cursor: pointer; transition: all 0.3s ease; position: relative;">
                <div class="recommended-badge" style="position: absolute; top: -12px; right: 20px; background: #f59e0b; color: white; padding: 0.4rem 1rem; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                    ⚡ Recommended
                </div>
                <div style="font-size: 4rem; margin-bottom: 1rem;">🤖</div>
                <h3 style="color: white; font-size: 1.8rem; font-weight: 700; margin-bottom: 1rem;">AI Auto-Build</h3>
                <p style="color: rgba(255,255,255,0.9); font-size: 1rem; margin-bottom: 1.5rem; line-height: 1.6;">
                    Let Claude AI build your complete website automatically in 1-2 minutes!
                </p>
                <ul style="text-align: left; color: rgba(255,255,255,0.95); margin-bottom: 1.5rem; list-style: none; padding: 0;">
                    <li style="margin-bottom: 0.5rem;"><i class="fas fa-check-circle" style="color: #10b981; margin-right: 0.5rem;"></i>5-7 pages created instantly</li>
                    <li style="margin-bottom: 0.5rem;"><i class="fas fa-check-circle" style="color: #10b981; margin-right: 0.5rem;"></i>AI-generated content</li>
                    <li style="margin-bottom: 0.5rem;"><i class="fas fa-check-circle" style="color: #10b981; margin-right: 0.5rem;"></i>SEO optimized</li>
                    <li style="margin-bottom: 0.5rem;"><i class="fas fa-check-circle" style="color: #10b981; margin-right: 0.5rem;"></i>Fully customizable after</li>
                </ul>
                @if($business->isEnterprise())
                <button type="button" class="btn btn-light btn-lg" style="width: 100%; font-weight: 600; padding: 0.875rem;">
                    <i class="fas fa-magic me-2"></i>Build with AI Now
                </button>
                @else
                <button type="button" class="btn btn-light btn-lg" style="width: 100%; font-weight: 600; padding: 0.875rem;">
                    <i class="fas fa-crown me-2"></i>Upgrade to Enterprise
                </button>
                <p style="color: rgba(255,255,255,0.8); font-size: 0.85rem; margin-top: 0.75rem; margin-bottom: 0;">
                    Enterprise feature
                </p>
                @endif
            </div>

            <!-- Manual Build Option -->
            <div class="build-option-card" id="manualBuildCard" style="background: var(--dark-bg); border: 3px solid #475569; border-radius: 16px; padding: 2.5rem; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🛠️</div>
                <h3 style="color: white; font-size: 1.8rem; font-weight: 700; margin-bottom: 1rem;">Build Manually</h3>
                <p style="color: #94a3b8; font-size: 1rem; margin-bottom: 1.5rem; line-height: 1.6;">
                    Choose your website type and customize step-by-step
                </p>
                <ul style="text-align: left; color: #94a3b8; margin-bottom: 1.5rem; list-style: none; padding: 0;">
                    <li style="margin-bottom: 0.5rem;"><i class="fas fa-check" style="color: #64748b; margin-right: 0.5rem;"></i>Select website type</li>
                    <li style="margin-bottom: 0.5rem;"><i class="fas fa-check" style="color: #64748b; margin-right: 0.5rem;"></i>Choose your theme</li>
                    <li style="margin-bottom: 0.5rem;"><i class="fas fa-check" style="color: #64748b; margin-right: 0.5rem;"></i>Add pages manually</li>
                    <li style="margin-bottom: 0.5rem;"><i class="fas fa-check" style="color: #64748b; margin-right: 0.5rem;"></i>More control</li>
                </ul>
                <button type="button" class="btn btn-outline-light btn-lg" style="width: 100%; font-weight: 600; padding: 0.875rem;">
                    <i class="fas fa-tools me-2"></i>Build Manually
                </button>
            </div>
        </div>
    </div>

    <!-- Website Types Grid (Hidden by default, shown when manual is selected) -->
    <div id="manualBuildSection" style="display: none;">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h2 style="color: white; font-size: 2rem; font-weight: 600; margin-bottom: 0.5rem;">Choose Your Website Type</h2>
            <p style="color: #94a3b8; font-size: 1.1rem;">We'll set you up and running in <strong>4 steps</strong></p>
        </div>
        
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
            <button type="button" class="btn-skip" id="backToOptionsBtn" style="cursor: pointer;">
                <i class="fas fa-arrow-left me-2"></i>Back to options
            </button>
            
            <button type="submit" class="btn-next" id="nextBtn" disabled>
                Continue <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </div>
    </form>
    </div>
</div>

<!-- AI Auto-Build Modal -->
<div id="aiBuildModal" class="modal fade" tabindex="-1" style="display: none;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background: var(--dark-bg); border: 2px solid #7b2e2e; border-radius: 16px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #7b2e2e 0%, #ff511a 100%); border: none; border-radius: 14px 14px 0 0;">
                <h5 class="modal-title" style="color: white; font-weight: 700; font-size: 1.5rem;">
                    <i class="fas fa-magic me-2"></i>AI Auto-Build Your Website
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <div id="aiBuildContent">
                    <!-- Initial State -->
                    <div id="aiBuildInitial">
                        <p style="color: #cbd5e1; font-size: 1.1rem; margin-bottom: 2rem; text-align: center;">
                            Claude AI will analyze your business and create a complete professional website with:
                        </p>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                            <div style="text-align: center; padding: 1.5rem; background: rgba(102, 126, 234, 0.1); border-radius: 12px;">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📄</div>
                                <h6 style="color: white; font-weight: 600; margin-bottom: 0.5rem;">5-7 Pages</h6>
                                <p style="color: #94a3b8; font-size: 0.9rem; margin: 0;">Home, About, Services, Contact, etc.</p>
                            </div>
                            <div style="text-align: center; padding: 1.5rem; background: rgba(102, 126, 234, 0.1); border-radius: 12px;">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">✍️</div>
                                <h6 style="color: white; font-weight: 600; margin-bottom: 0.5rem;">Smart Content</h6>
                                <p style="color: #94a3b8; font-size: 0.9rem; margin: 0;">Business-specific text & descriptions</p>
                            </div>
                            <div style="text-align: center; padding: 1.5rem; background: rgba(102, 126, 234, 0.1); border-radius: 12px;">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🎨</div>
                                <h6 style="color: white; font-weight: 600; margin-bottom: 0.5rem;">Professional Design</h6>
                                <p style="color: #94a3b8; font-size: 0.9rem; margin: 0;">Beautiful theme applied</p>
                            </div>
                            <div style="text-align: center; padding: 1.5rem; background: rgba(102, 126, 234, 0.1); border-radius: 12px;">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔍</div>
                                <h6 style="color: white; font-weight: 600; margin-bottom: 0.5rem;">SEO Ready</h6>
                                <p style="color: #94a3b8; font-size: 0.9rem; margin: 0;">Optimized meta tags</p>
                            </div>
                        </div>

                        <div style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981; padding: 1rem 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                            <p style="color: #10b981; margin: 0; font-weight: 600;">
                                <i class="fas fa-info-circle me-2"></i>You can edit everything after the AI builds your website!
                            </p>
                        </div>

                        <!-- Theme Selection -->
                        <div style="margin-bottom: 2rem;">
                            <label style="color: white; font-weight: 600; font-size: 1.1rem; margin-bottom: 1rem; display: block;">
                                <i class="fas fa-palette me-2"></i>Choose a Theme
                            </label>
                            <select id="aiThemeSelect" class="form-select form-select-lg" style="background: var(--darker-bg); color: white; border: 2px solid #475569; padding: 0.875rem;">
                                @foreach($themes as $theme)
                                <option value="{{ $theme->id }}" {{ $loop->first ? 'selected' : '' }}>
                                    {{ $theme->name }} @if(!$theme->is_free)(Premium)@endif
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Progress State -->
                    <div id="aiBuildProgress" style="display: none;">
                        <div style="text-align: center; padding: 2rem 0;">
                            <div style="font-size: 4rem; margin-bottom: 1.5rem;">🤖</div>
                            <h5 style="color: white; font-size: 1.5rem; margin-bottom: 1rem;">AI is Building Your Website...</h5>
                            <div class="progress" style="height: 8px; background: var(--darker-bg); border-radius: 10px; margin-bottom: 1rem;">
                                <div class="progress-bar" id="aiBuildProgressBar" style="background: linear-gradient(90deg, #7b2e2e, #ff511a); width: 0%; transition: width 0.5s ease;"></div>
                            </div>
                            <p id="aiBuildStatus" style="color: #94a3b8; font-size: 1rem;">Analyzing your business...</p>
                        </div>
                    </div>

                    <!-- Success State -->
                    <div id="aiBuildSuccess" style="display: none;">
                        <div style="text-align: center; padding: 2rem 0;">
                            <div style="font-size: 4rem; margin-bottom: 1rem;">🎉</div>
                            <h5 style="color: #10b981; font-size: 1.8rem; margin-bottom: 1rem;">Website Created Successfully!</h5>
                            <p style="color: #cbd5e1; font-size: 1.1rem; margin-bottom: 2rem;">Your website is ready. You can now edit and customize it.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border: none; padding: 1.5rem 2rem;">
                <div id="aiBuildInitialButtons">
                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-lg" id="startAiBuildBtn" style="background: linear-gradient(135deg, #7b2e2e 0%, #ff511a 100%); border: none; color: white; font-weight: 600; padding: 0.75rem 2rem;">
                        <i class="fas fa-magic me-2"></i>Start AI Build
                    </button>
                </div>
                <div id="aiBuildSuccessButtons" style="display: none;">
                    <button type="button" class="btn btn-lg btn-success" id="goToDashboardBtn" style="padding: 0.75rem 2rem; font-weight: 600;">
                        <i class="fas fa-arrow-right me-2"></i>Go to Website Dashboard
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.website-type-card');
    const selectedTypeInput = document.getElementById('selectedType');
    const nextBtn = document.getElementById('nextBtn');
    const aiBuildCard = document.getElementById('aiBuildCard');
    const manualBuildCard = document.getElementById('manualBuildCard');
    const manualBuildSection = document.getElementById('manualBuildSection');
    const backToOptionsBtn = document.getElementById('backToOptionsBtn');
    
    // Handle website type card selection
    cards.forEach(card => {
        card.addEventListener('click', function() {
            cards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            
            const type = this.dataset.type;
            selectedTypeInput.value = type;
            nextBtn.disabled = false;
        });
    });

    // AI Build Card Click
    aiBuildCard.addEventListener('click', function(e) {
        @if($business->isEnterprise())
        // Show AI build modal
        const modal = new bootstrap.Modal(document.getElementById('aiBuildModal'));
        modal.show();
        @else
        // Redirect to subscription page
        window.location.href = '{{ route('settings.index') }}#subscription';
        @endif
    });

    // Manual Build Card Click
    manualBuildCard.addEventListener('click', function() {
        // Hide build options, show manual build section
        document.querySelector('.hero-section p').style.display = 'none';
        manualBuildSection.style.display = 'block';
        aiBuildCard.parentElement.style.display = 'none';
    });

    // Back to options
    backToOptionsBtn.addEventListener('click', function() {
        document.querySelector('.hero-section p').style.display = 'block';
        manualBuildSection.style.display = 'none';
        aiBuildCard.parentElement.style.display = 'grid';
        
        // Clear selection
        cards.forEach(c => c.classList.remove('selected'));
        selectedTypeInput.value = '';
        nextBtn.disabled = true;
    });

    // AI Build Process
    @if($business->isEnterprise())
    const startAiBuildBtn = document.getElementById('startAiBuildBtn');
    const goToDashboardBtn = document.getElementById('goToDashboardBtn');
    
    startAiBuildBtn?.addEventListener('click', function() {
        const themeId = document.getElementById('aiThemeSelect').value;
        
        if (!themeId) {
            alert('Please select a theme');
            return;
        }
        
        // Show progress state
        document.getElementById('aiBuildInitial').style.display = 'none';
        document.getElementById('aiBuildInitialButtons').style.display = 'none';
        document.getElementById('aiBuildProgress').style.display = 'block';
        
        // Start AI build
        startAIBuild(themeId);
    });

    function startAIBuild(themeId) {
        const progressBar = document.getElementById('aiBuildProgressBar');
        const statusText = document.getElementById('aiBuildStatus');
        
        // Simulate progress updates
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            progressBar.style.width = progress + '%';
            
            if (progress < 30) {
                statusText.textContent = 'Analyzing your business...';
            } else if (progress < 60) {
                statusText.textContent = 'Generating pages and content...';
            } else {
                statusText.textContent = 'Applying theme and SEO...';
            }
        }, 800);
        
        // Call AI auto-build endpoint
        fetch('{{ route("website.builder.ai.auto-build") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ theme_id: themeId })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.error || data.message || 'Failed to build website');
                });
            }
            return response.json();
        })
        .then(data => {
            clearInterval(progressInterval);
            progressBar.style.width = '100%';
            
            if (data.success) {
                // Show success state
                setTimeout(() => {
                    document.getElementById('aiBuildProgress').style.display = 'none';
                    document.getElementById('aiBuildSuccess').style.display = 'block';
                    document.getElementById('aiBuildSuccessButtons').style.display = 'block';
                }, 500);
            } else {
                alert(data.error || data.message || 'Failed to build website. Please try again.');
                location.reload();
            }
        })
        .catch(error => {
            clearInterval(progressInterval);
            console.error('Auto-build error:', error);
            console.error('Error details:', error.message);
            alert('Error: ' + error.message + '\n\nPlease check the browser console and Laravel logs for more details.');
            location.reload();
        });
    }

    goToDashboardBtn?.addEventListener('click', function() {
        window.location.href = '{{ route("website.builder.index") }}';
    });
    @endif

    // Build option card hover effects
    document.querySelectorAll('.build-option-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
            this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.3)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
});
</script>
@endsection

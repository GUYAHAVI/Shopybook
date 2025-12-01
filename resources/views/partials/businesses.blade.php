

<style>
/* CRITICAL FIX: Force Font Awesome Icons to Display */
.fas, .fa-solid, .far, .fa-regular, .fab, .fa-brands {
    font-family: "Font Awesome 6 Free" !important;
    font-weight: 900 !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    display: inline-block !important;
    font-style: normal !important;
    font-variant: normal !important;
    text-rendering: auto !important;
    line-height: 1 !important;
}

.far, .fa-regular {
    font-weight: 400 !important;
}

.fab, .fa-brands {
    font-weight: 400 !important;
}

/* Specific icon locations */
.business-type-header .fas,
.card-location .fas,
.card-button .fas,
.empty-message .fas,
.sort-button .fas {
    font-family: "Font Awesome 6 Free" !important;
    display: inline-block !important;
    font-weight: 900 !important;
}

/* CSS Reset for Business Cards Section - Override Global Styles */
.business-type-section,
.business-type-section *:not(.fas):not(.far):not(.fab),
.business-card,
.business-card *:not(.fas):not(.far):not(.fab) {
    /* Reset any inherited styles */
    font-family: "Montserrat", sans-serif !important;
    line-height: normal !important;
}

/* Futuristic Business Cards Design - Out of This World */
.business-card {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 50%, #ffffff 100%) !important;
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.1),
        0 0 0 1px rgba(255, 255, 255, 0.8),
        inset 0 1px 0 rgba(255, 255, 255, 0.9);
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: none;
    position: relative;
    height: 100%;
    backdrop-filter: blur(10px);
    /* Isolate from global styles */
    color: #020258 !important;
}

/* Force business card content to use proper colors */
.business-card .card-body {
    background: linear-gradient(135deg, #ffffff 0%, #fafbff 100%) !important;
    color: #020258 !important;
}

.business-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    background: linear-gradient(90deg, 
        #020258 0%, 
        #13e8e9 25%, 
        #00d4ff 50%, 
        #13e8e9 75%, 
        #020258 100%);
    z-index: 2;
    animation: shimmer 3s ease-in-out infinite;
}

.business-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, 
        rgba(19, 232, 233, 0.05) 0%, 
        rgba(2, 2, 88, 0.05) 50%, 
        rgba(19, 232, 233, 0.05) 100%);
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
}

.business-card:hover {
    transform: translateY(-15px) scale(1.03) rotateX(5deg);
    box-shadow: 
        0 30px 60px rgba(2, 2, 88, 0.2),
        0 0 0 1px rgba(19, 232, 233, 0.3),
        0 0 30px rgba(19, 232, 233, 0.2);
}

.business-card:hover::after {
    opacity: 1;
}

@keyframes shimmer {
    0%, 100% { background-position: -200% 0; }
    50% { background-position: 200% 0; }
}

.card-image-container {
    position: relative;
    height: 220px;
    overflow: hidden;
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
}

.card-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    filter: brightness(1.1) contrast(1.1);
}

.business-card:hover .card-image {
    transform: scale(1.15) rotate(2deg);
    filter: brightness(1.2) contrast(1.2) saturate(1.1);
}

.card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        to bottom,
        transparent 0%,
        rgba(2, 2, 88, 0.1) 30%,
        rgba(2, 2, 88, 0.2) 60%,
        rgba(2, 2, 88, 0.4) 100%
    );
    opacity: 0;
    transition: opacity 0.4s ease;
}

.business-card:hover .card-overlay {
    opacity: 1;
}

.card-body {
    padding: 2rem;
    position: relative;
    background: linear-gradient(135deg, #ffffff 0%, #fafbff 100%);
}

.card-title {
    font-size: 1.4rem;
    font-weight: 800;
    color: #020258;
    margin-bottom: 0.75rem;
    line-height: 1.3;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    letter-spacing: -0.5px;
}

.card-location {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: #495057;
    font-size: 0.95rem;
    margin-bottom: 1.25rem;
    font-weight: 500;
}

.card-location i {
    color: #13e8e9;
    font-size: 1.1rem;
    text-shadow: 0 0 10px rgba(19, 232, 233, 0.5);
}

.card-description {
    color: #212529;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 2rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    font-weight: 400;
    text-shadow: 0 1px 1px rgba(255, 255, 255, 0.8);
}

.card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
    gap: 1rem;
}

.card-badge {
    background: linear-gradient(135deg, #020258, #13e8e9);
    color: white;
    padding: 0.4rem 1rem;
    border-radius: 25px;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    box-shadow: 0 4px 15px rgba(2, 2, 88, 0.3);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    position: relative;
    overflow: hidden;
}

.card-badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.business-card:hover .card-badge::before {
    left: 100%;
}

.card-button {
    background: linear-gradient(135deg, #020258, #13e8e9);
    border: none;
    color: white;
    padding: 0.6rem 1.5rem;
    border-radius: 30px;
    font-weight: 700;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 
        0 6px 20px rgba(2, 2, 88, 0.3),
        0 0 0 1px rgba(19, 232, 233, 0.2);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    position: relative;
    overflow: hidden;
}

.card-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.6s ease;
}

.card-button:hover {
    background: linear-gradient(135deg, #13e8e9, #020258);
    transform: translateY(-3px) scale(1.05);
    box-shadow: 
        0 10px 30px rgba(2, 2, 88, 0.4),
        0 0 0 1px rgba(19, 232, 233, 0.4),
        0 0 20px rgba(19, 232, 233, 0.3);
    color: white;
}

.card-button:hover::before {
    left: 100%;
}

.business-type-header {
    margin: 3rem 0 2.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 4px solid;
    border-image: linear-gradient(90deg, #020258, #13e8e9, #00d4ff, #13e8e9, #020258) 1;
    color: #020258;
    font-weight: 800;
    font-size: 2rem;
    position: relative;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    letter-spacing: -1px;
}

.business-type-header::before {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 0;
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #020258, #13e8e9);
    animation: pulse 2s ease-in-out infinite;
}

.business-type-header i {
    margin-right: 1rem;
    color: #13e8e9;
    text-shadow: 0 0 15px rgba(19, 232, 233, 0.6);
    animation: glow 2s ease-in-out infinite alternate;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

@keyframes glow {
    from { text-shadow: 0 0 15px rgba(19, 232, 233, 0.6); }
    to { text-shadow: 0 0 25px rgba(19, 232, 233, 0.8), 0 0 35px rgba(19, 232, 233, 0.4); }
}

.sort-controls {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border-radius: 20px;
    padding: 1.5rem;
    box-shadow: 
        0 10px 30px rgba(0, 0, 0, 0.1),
        0 0 0 1px rgba(19, 232, 233, 0.1);
    margin-bottom: 2.5rem;
    border: 1px solid rgba(19, 232, 233, 0.2);
}

.sort-button {
    background: linear-gradient(135deg, #020258, #13e8e9);
    border: none;
    color: white;
    border-radius: 15px;
    padding: 0.7rem 1.5rem;
    font-weight: 700;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(2, 2, 88, 0.2);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.sort-button:hover {
    background: linear-gradient(135deg, #13e8e9, #020258);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(2, 2, 88, 0.3);
    color: white;
}

.empty-message {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border-radius: 25px;
    padding: 4rem 3rem;
    text-align: center;
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.1),
        0 0 0 1px rgba(19, 232, 233, 0.1);
    margin: 3rem 0;
    border: 1px solid rgba(19, 232, 233, 0.2);
}

.empty-message h4 {
    color: #020258;
    font-weight: 800;
    margin-bottom: 1.5rem;
    font-size: 1.8rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.empty-message p {
    color: #212529;
    font-size: 1.2rem;
    font-weight: 500;
    line-height: 1.6;
}

.empty-message i {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .business-card {
        margin-bottom: 2rem;
    }
    
    .card-image-container {
        height: 200px;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .card-title {
        font-size: 1.25rem;
    }
    
    .business-type-header {
        font-size: 1.75rem;
        margin: 2.5rem 0 2rem;
    }
    
    .card-footer {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .card-button {
        width: 100%;
        text-align: center;
    }
}

@media (max-width: 576px) {
    .card-body {
        padding: 1.25rem;
    }
    
    .card-title {
        font-size: 1.1rem;
    }
    
    .business-type-header {
        font-size: 1.5rem;
    }
    
    .empty-message {
        padding: 3rem 2rem;
    }
}

/* Enhanced Animation for cards appearing */
.business-card {
    animation: fadeInUp 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    opacity: 0;
    animation-fill-mode: forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Stagger animation for multiple cards */
.business-card:nth-child(1) { animation-delay: 0.1s; }
.business-card:nth-child(2) { animation-delay: 0.2s; }
.business-card:nth-child(3) { animation-delay: 0.3s; }
.business-card:nth-child(4) { animation-delay: 0.4s; }
.business-card:nth-child(5) { animation-delay: 0.5s; }
.business-card:nth-child(6) { animation-delay: 0.6s; }

/* Dropdown menu styling */
.dropdown-menu {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    border: 1px solid rgba(19, 232, 233, 0.2);
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    padding: 0.5rem 0;
}

/* Hover effects for dropdown items */
.dropdown-item {
    transition: all 0.3s ease;
    font-weight: 500;
    color: #020258 !important;
    padding: 0.75rem 1.5rem;
    border: none;
    background: transparent;
}

.dropdown-item:hover {
    background: linear-gradient(135deg, #020258, #13e8e9);
    color: white !important;
    transform: translateX(5px);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.dropdown-divider {
    border-color: rgba(19, 232, 233, 0.2);
    margin: 0.5rem 0;
}

/* Enhanced focus states for accessibility */
.card-button:focus,
.sort-button:focus {
    outline: none;
    box-shadow: 
        0 0 0 3px rgba(19, 232, 233, 0.3),
        0 6px 20px rgba(2, 2, 88, 0.3);
}

/* Ensure all text has proper contrast - More specific selectors to override global styles */
.business-card .card-title,
.business-card .card-location,
.business-card .card-description,
.business-type-header,
.empty-message h4,
.empty-message p {
    color: #020258 !important;
}

.business-card .card-location span {
    color: #495057 !important;
}

.business-card .card-description,
.business-card .card-description.text-dark {
    color: #212529 !important;
}

/* Override any global text color styles for business cards */
.business-card * {
    color: inherit !important;
}

.business-card .card-title {
    color: #020258 !important;
}

.business-card .card-location {
    color: #495057 !important;
}

.business-card .card-location i {
    color: #13e8e9 !important;
}

.business-card .card-description,
.business-card .card-description.text-dark {
    color: #212529 !important;
}

/* Ensure buttons maintain white text on colored backgrounds */
.business-card .card-button,
.sort-button,
.business-card .card-badge {
    color: white !important;
}

.business-card .card-button:hover,
.sort-button:hover {
    color: white !important;
}

/* Override any global styles that might affect business cards */
.business-card h5,
.business-card h5.card-title {
    color: #020258 !important;
}

.business-card p,
.business-card p.card-description,
.business-card p.card-description.text-dark {
    color: #212529 !important;
}

.business-card span,
.business-card .card-location span {
    color: #495057 !important;
}

/* Additional overrides for global styles from index.blade.php and style.css */
.business-card .card-title,
.business-card h5.card-title,
.business-card h5,
.business-card .card-body h5,
.business-card .card-body .card-title {
    color: #020258 !important;
    font-weight: 800 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
    letter-spacing: -0.5px !important;
}

/* Ultra-specific override for business card titles */
.business-card .card-body h5.card-title,
.business-card .card-body .card-title,
.business-card .card-body h5,
.business-card .card-title,
.business-card h5.card-title,
.business-card h5 {
    color: #020258 !important;
    font-weight: 800 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
    letter-spacing: -0.5px !important;
}

.business-card .card-description,
.business-card p.card-description,
.business-card p.card-description.text-dark {
    color: #212529 !important;
    font-weight: 400 !important;
    text-shadow: 0 1px 1px rgba(255, 255, 255, 0.8) !important;
}

.business-card .card-location,
.business-card .card-location span {
    color: #495057 !important;
    font-weight: 500 !important;
}

.business-card .card-location i {
    color: #13e8e9 !important;
    text-shadow: 0 0 10px rgba(19, 232, 233, 0.5) !important;
}

/* Override any global color inheritance */
.business-card,
.business-card .card-body,
.business-card .card-body * {
    color: inherit !important;
}

/* Force specific colors for business card elements */
.business-card .card-title,
.business-card h5,
.business-card .card-body h5,
.business-card .card-body .card-title {
    color: #020258 !important;
}

.business-card .card-description,
.business-card p {
    color: #212529 !important;
}

.business-card .card-location,
.business-card .card-location span {
    color: #495057 !important;
}

/* Nuclear option - override any possible global styles for business card titles */
.business-card .card-body h5.card-title,
.business-card .card-body h5,
.business-card .card-body .card-title,
.business-card h5.card-title,
.business-card h5,
.business-card .card-title {
    color: #020258 !important;
    font-weight: 800 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
    letter-spacing: -0.5px !important;
}

/* CRITICAL FIX - Force business card titles to be dark and readable */
.business-card .card-body h5.card-title,
.business-card .card-body h5,
.business-card .card-body .card-title,
.business-card h5.card-title,
.business-card h5,
.business-card .card-title,
.business-card .card-body .card-title,
.business-card .card-body h5.card-title {
    color: #020258 !important; /* Dark blue for maximum readability */
    font-weight: 800 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
    letter-spacing: -0.5px !important;
}

/* Nuclear option - target every possible selector combination */
.business-card h5,
.business-card .card-title,
.business-card .card-body h5,
.business-card .card-body .card-title,
.business-card .card-body h5.card-title,
.business-card h5.card-title,
.business-card .card-body .card-title,
.business-card .card-body h5.card-title,
.business-card .card-body h5,
.business-card .card-title,
.business-card h5.card-title,
.business-card h5 {
    color: #020258 !important;
    font-weight: 800 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
    letter-spacing: -0.5px !important;
}

/* Additional brand color option - if you want to use your brand cyan color instead */
.business-card .card-body h5.card-title,
.business-card .card-body h5,
.business-card .card-body .card-title,
.business-card h5.card-title,
.business-card h5,
.business-card .card-title {
    color: #13e8e9 !important; /* Your brand cyan color */
    font-weight: 800 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
    letter-spacing: -0.5px !important;
}

/* EMERGENCY FIX - Target the exact HTML structure from the image */
.business-card .card-body h5.card-title,
.business-card .card-body h5,
.business-card .card-body .card-title,
.business-card h5.card-title,
.business-card h5,
.business-card .card-title,
.business-card .card-body .card-title,
.business-card .card-body h5.card-title,
.business-card .card-body h5,
.business-card .card-title,
.business-card h5.card-title,
.business-card h5,
/* Target any h5 element inside business cards */
.business-card .card-body h5,
.business-card .card-body .card-title,
.business-card .card-body h5.card-title {
    color: #020258 !important;
    font-weight: 800 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) !important;
    letter-spacing: -0.5px !important;
}

/* Loading state for images */
.card-image {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

.card-image[src] {
    background: none;
    animation: none;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>

<div class="sort-controls">
  <div class="d-flex justify-content-end">
    <div class="btn-group">
      <button type="button" class="btn sort-button dropdown-toggle" data-bs-toggle="dropdown">
        <i class="fas fa-sort"></i> Sort: {{ ucfirst($sort) }} ({{ $order }})
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="?sort=name&order=asc">Name (A-Z)</a></li>
        <li><a class="dropdown-item" href="?sort=name&order=desc">Name (Z-A)</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="?sort=created_at&order=desc">Newest First</a></li>
        <li><a class="dropdown-item" href="?sort=created_at&order=asc">Oldest First</a></li>
      </ul>
    </div>
  </div>
</div>

@if(count($groupedBusinesses) > 0)
  @foreach($groupedBusinesses as $businessType => $businesses)
    @if(count($businesses) > 0)
      <div class="business-type-section">
        <h3 class="business-type-header">
          <i class="fas fa-building"></i> {{ $businessType }}
        </h3>
        <div class="row">
          @foreach($businesses as $business)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
              <div class="business-card">
                <div class="card-image-container">
                  <img src="{{ $business->logo_path ? asset('storage/'.$business->logo_path) : asset('img/default-business.png') }}" 
                       class="card-image" 
                       alt="{{ $business->name }}">
                  <div class="card-overlay"></div>
                </div>
                <div class="card-body">
                  <h5 class="card-title" style="color: #020258 !important;">{{ $business->name }}</h5>
                  <div class="card-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $business->city }}, {{ $business->country }}</span>
                  </div>
                  <p class="card-description text-dark">{{ Str::limit($business->description, 100) }}</p>
                  <div class="card-footer">
                    <span class="card-badge">{{ $business->business_category ?? 'Business' }}</span>
                    <a href="{{ route('business.show', $business->slug) }}" 
                       class="card-button">
                      <i class="fas fa-eye"></i> View
                    </a>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif
  @endforeach
@else
  <div class="empty-message">
    <i class="fas fa-search fa-3x mb-3" style="color: #13e8e9;"></i>
    <h4>No businesses found</h4>
    <p>There are currently no businesses to display. Check back later for new additions!</p>
  </div>
@endif
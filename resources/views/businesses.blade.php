@extends('layouts.master')
@section('content')

<div id="home" class="container-fluid px-0">
    <div class="hero-section">
        <!-- Background Video -->
        <div class="hero-video-container">
            <video autoplay muted loop playsinline class="hero-video">
                <source src="{{ asset('video/video1.mp4') }}" type="video/mp4">
                <source src="{{ asset('video/video2.mp4') }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        
        <!-- Dark Overlay with Gradient -->
        <div class="hero-overlay"></div>
        
        <!-- Hero Content -->
        <div class="hero-content">
            <div class="container">
                <div class="row align-items-center min-vh-100">
                    <div class="col-lg-8 col-md-10 mx-auto text-center">
                        <h1 class="hero-title">Discover Amazing Businesses</h1>
                        <p class="hero-subtitle">
                            @if($type)
                                Browse {{ ucfirst($type) }} businesses in our community
                            @else
                                Explore our wide range of business solutions tailored to your needs
                            @endif
                        </p>
                        <div class="hero-buttons mt-4">
                            @if($type)
                                <a href="{{ route('businesses') }}" class="btn btn-outline-light btn-lg me-3">View All Businesses</a>
                            @endif
                            <a href="#businesses" class="btn btn-primary btn-lg">Explore Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- BUSINESSES -->

    <section id="businesses" class="businesses">
        <div class="container">
            @if($type)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-filter me-2"></i>
                            Showing {{ ucfirst($type) }} businesses only. 
                            <a href="{{ route('businesses') }}" class="alert-link">View all businesses</a>
                        </div>
                    </div>
                </div>
            @endif
            
            <h3>Need something else?</h3>
            <p class="lead">Explore our wide range of business solutions tailored to your needs.</p>
            <div class="row">
                @include('partials.businesses', ['groupedBusinesses' => $groupedBusinesses])
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Add loaded class to trigger animations
            setTimeout(function () {
                document.querySelector('.brandimgs-container').classList.add('loaded');
                document.querySelector('.feature-bubbles-container').classList.add('loaded');
            }, 500);

            // Hover effects
            const images = document.querySelectorAll('.image-bubble');
            images.forEach(img => {
                img.addEventListener('mouseenter', function () {
                    this.style.transform = 'scale(1.05)';
                    this.style.zIndex = '20';
                    this.style.boxShadow = '0 15px 40px rgba(0,0,0,0.4)';
                });
                img.addEventListener('mouseleave', function () {
                    this.style.transform = 'scale(1)';
                    this.style.zIndex = '10';
                    this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.2)';
                });
            });
        });
    </script>
   
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait for images to load and initial draw
    window.addEventListener('load', function() {
        console.log('Window loaded - attempting to draw connectors');
        connectImagesWithLines();
    });

    // Handle window resize with debounce
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            console.log('Window resized - redrawing connectors');
            connectImagesWithLines();
        }, 250);
    });
});

function connectImagesWithLines() {
    console.log('Starting connectImagesWithLines function');
    
    try {
        // Get all the elements
        const img1 = document.querySelector('.brand-bubble-1');
        const img2 = document.querySelector('.brand-bubble-2');
        const img3 = document.querySelector('.feature-bubble-1');
        const img4 = document.querySelector('.feature-bubble-2');
        
        // Debug: Check if elements exist
        console.log('Elements found:', {
            img1: !!img1,
            img2: !!img2,
            img3: !!img3,
            img4: !!img4
        });
        
        if (!img1 || !img2 || !img3 || !img4) {
            console.error('One or more elements not found!');
            return;
        }
        
        // Remove existing connectors if they exist
        document.querySelectorAll('.custom-connector, .custom-connector-container').forEach(el => {
            console.log('Removing existing connector:', el);
            el.remove();
        });
        
        // Create a new SVG container
        const svgContainer = document.createElement('div');
        svgContainer.className = 'custom-connector-container';
        Object.assign(svgContainer.style, {
            position: 'absolute',
            top: '0',
            left: '0',
            width: '100%',
            height: '100%',
            pointerEvents: 'none',
            overflow: 'visible',
            zIndex: '1'
        });
        
        // Insert the SVG container
        document.body.appendChild(svgContainer); // Changed from #home to body for better compatibility
        
        // Create SVG element
        const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        svg.setAttribute('class', 'custom-connector');
        svg.setAttribute('width', '100%');
        svg.setAttribute('height', '100%');
        Object.assign(svg.style, {
            position: 'absolute',
            top: '0',
            left: '0',
            overflow: 'visible'
        });
        
        svgContainer.appendChild(svg);
        
        // Calculate positions relative to document
        function getAbsolutePosition(el) {
            const rect = el.getBoundingClientRect();
            const position = {
                x: rect.left + window.scrollX,
                y: rect.top + window.scrollY,
                width: rect.width,
                height: rect.height
            };
            console.log('Position for', el.className, position);
            return position;
        }
        
        const img1Pos = getAbsolutePosition(img1);
        const img2Pos = getAbsolutePosition(img2);
        const img3Pos = getAbsolutePosition(img3);
        const img4Pos = getAbsolutePosition(img4);
        
        // Calculate connection points
        const img1BottomLeft = {
            x: img1Pos.x,
            y: img1Pos.y + img1Pos.height
        };
        
        const img2BottomRight = {
            x: img2Pos.x + img2Pos.width,
            y: img2Pos.y + img2Pos.height
        };
        
        const img3TopRight = {
            x: img3Pos.x + img3Pos.width,
            y: img3Pos.y
        };
        
        const img4TopLeft = {
            x: img4Pos.x,
            y: img4Pos.y
        };
        
        // Create first path (img1 bottom-left to img3 top-right)
        const path1 = document.createElementNS("http://www.w3.org/2000/svg", "path");
        const path1MiddleX = (img1BottomLeft.x + img3TopRight.x) / 2;
        const path1Data = `M${img1BottomLeft.x},${img1BottomLeft.y} 
                          C${path1MiddleX},${img1BottomLeft.y} 
                          ${path1MiddleX},${img3TopRight.y} 
                          ${img3TopRight.x},${img3TopRight.y}`;
        path1.setAttribute('d', path1Data);
        Object.assign(path1.style, {
            stroke: 'rgba(255, 255, 255, 0.7)',
            strokeWidth: '2px',
            strokeDasharray: '5,3',
            fill: 'none'
        });
        svg.appendChild(path1);
        
        // Create second path (img2 bottom-right to img4 top-left)
        const path2 = document.createElementNS("http://www.w3.org/2000/svg", "path");
        const path2MiddleX = (img2BottomRight.x + img4TopLeft.x) / 2;
        const path2Data = `M${img2BottomRight.x},${img2BottomRight.y} 
                          C${path2MiddleX},${img2BottomRight.y} 
                          ${path2MiddleX},${img4TopLeft.y} 
                          ${img4TopLeft.x},${img4TopLeft.y}`;
        path2.setAttribute('d', path2Data);
        Object.assign(path2.style, {
            stroke: 'rgba(255, 255, 255, 0.7)',
            strokeWidth: '2px',
            strokeDasharray: '5,3',
            fill: 'none'
        });
        svg.appendChild(path2);
        
        console.log('Connectors created successfully');
        console.log('Path1 data:', path1Data);
        console.log('Path2 data:', path2Data);
        
    } catch (error) {
        console.error('Error in connectImagesWithLines:', error);
    }
}
</script>

<style>
/* Hero Section Styles */
.hero-section {
    position: relative;
    height: 100vh;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-video-container {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.hero-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        135deg,
        rgba(2, 2, 88, 0.8) 0%,
        rgba(19, 232, 233, 0.6) 100%
    );
    z-index: 2;
}

.hero-content {
    position: relative;
    z-index: 3;
    width: 100%;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 1.5rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    animation: fadeInUp 1s ease-out;
}

.hero-subtitle {
    font-size: 1.25rem;
    color: #ffffff;
    margin-bottom: 2rem;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    animation: fadeInUp 1s ease-out 0.3s both;
}

.hero-buttons {
    animation: fadeInUp 1s ease-out 0.6s both;
}

.hero-buttons .btn {
    padding: 12px 30px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 50px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.hero-buttons .btn-primary {
    background: linear-gradient(135deg, #020258, #13e8e9);
    border: none;
    color: #ffffff;
}

.hero-buttons .btn-primary:hover {
    background: linear-gradient(135deg, #13e8e9, #020258);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.hero-buttons .btn-outline-light {
    border: 2px solid #ffffff;
    color: #ffffff;
    background: transparent;
}

.hero-buttons .btn-outline-light:hover {
    background: #ffffff;
    color: #020258;
    transform: translateY(-2px);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .hero-buttons .btn {
        padding: 10px 25px;
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-subtitle {
        font-size: 1rem;
    }
    
    .hero-buttons {
        flex-direction: column;
        gap: 1rem;
    }
    
    .hero-buttons .btn {
        width: 100%;
        max-width: 250px;
        margin: 0 auto;
    }
}

/* General Styles */
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
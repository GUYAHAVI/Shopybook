@extends('layouts.master')
@section('content')

    <style>
        /* Brand Images Animation */
        .brandimgs-container {
            position: relative;
            height: 500px;
            /* Increased height */
            overflow: visible;
            z-index: 10;
            margin-top: 50px;
        }

        /* Feature Bubbles Animation */
        .feature-bubbles-container {
            position: relative;
            height: 500px;
            /* Increased height */
            z-index: 10;
            margin-top: 50px;
        }

        /* Image Wrapper Base Styles */
        .image-bubble {
            position: absolute;
            overflow: hidden;
            border-radius: 50%;
            opacity: 0;
            transform: scale(0.5);
            transition: all 1.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            z-index: 10;
        }

        .image-bubble img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Brand Images (first two) - Doubled in size */
        .brand-bubble-1 {
            width: 300px;
            /* Increased from 150px */
            height: 300px;
            /* Increased from 150px */
            bottom: 60px;
            /* Adjusted position */
            left: 100px;
            /* Adjusted position */
            transition-delay: 2s;
        }

        .brand-bubble-2 {
            width: 260px;
            /* Increased from 130px */
            height: 260px;
            /* Increased from 130px */
            bottom: 120px;
            /* Adjusted position */
            left: 300px;
            /* Adjusted position */
            transition-delay: 3s;
        }

        /* Feature Bubbles (last two) - Doubled in size */
        .feature-bubble-1 {
            width: 280px;
            /* Increased from 140px */
            height: 280px;
            /* Increased from 140px */
            top: 100px;
            /* Adjusted position */
            right: 200px;
            /* Adjusted position */
            transition-delay: 2s;
        }

        .feature-bubble-2 {
            width: 240px;
            /* Increased from 120px */
            height: 240px;
            /* Increased from 120px */
            top: 240px;
            /* Adjusted position */
            right: 100px;
            /* Adjusted position */
            transition-delay: 4s;
        }

        .connector-svg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 5;
            pointer-events: none;
            overflow: visible;
        }

        .connector-line {
            stroke: rgba(255, 255, 255, 0.7);
            stroke-width: 2;
            stroke-dasharray: 5, 5;
            fill: none;
            opacity: 0;
            transition: all 5s ease;
        }

        /* Animation when loaded - Adjusted final sizes */
        .loaded .image-bubble {
            opacity: 1;
            transform: scale(1);
            border-radius: 10px;
            height: 400px;
            /* Increased from 200px */
        }

        .loaded .brand-bubble-1 {
            height: 360px;
            /* Increased from 220px */
            width: 280px;
            /* Increased from 160px */
        }

        .loaded .brand-bubble-2 {
            height: 320px;
            /* Increased from 200px */
            width: 240px;
            /* Increased from 140px */
            bottom: 160px;
            /* Adjusted position */
            left: 255px;
            right: ;
            /* Adjusted position */
        }

        .loaded .feature-bubble-1 {
            height: 360px;
            /* Increased from 180px */
            width: 280px;
            /* Increased from 140px */
        }

        .loaded .feature-bubble-2 {
            height: 320px;
            /* Increased from 160px */
            width: 240px;
            /* Increased from 120px */
        }

        .loaded .connector-line {
            opacity: 1;
            transform: scaleX(1);
        }

        /* Make sure morphing bubbles don't interfere */
        .morphing-bubbles {
            z-index: 1;
        }

        /* Responsive - hide on small screens */
        @media (max-width: 767.98px) {

            .brandimgs-container,
            .feature-bubbles-container {
                display: none;
            }
            .brandtext {
                margin-top: 0 !important;
                padding-top: 0 !important;
                text-align: center;
            }
        }

        .col-md-5 {
            position: relative;
            overflow: visible;
        }
        .brandtext {
            position: relative;
            margin-top: 20rem;
            
            color: white;
        }
        .brandtext .col-md-7 {
            padding-top: 10rem;
        }
    </style>

    <div id="home" class="container-fluid px-0">
        <div>
            <div class="morphing-bubbles-hero">
                <div class="morphing-bubble main-bubble"></div>
                <div class="morphing-bubble secondary-bubble"></div>
                <div class="morphing-bubble accent-bubble"></div>
            </div>
            <div class="brandtext">
                <div class="row">
                    <div class="col-md-7">
                        <h1>Shopybook Pro</h1>
                        <p class="lead">Smart Inventory Management for Modern Businesses</p>
                        <div class="mt-4">
                            <a href="#features" class="btn btn-primary btn-lg me-2">Explore Features</a>
                            <a href="#signup" class="btn btn-outline-light btn-lg">Start Free Trial</a>
                        </div>
                    </div>
                    <div class="col-md-5 position-relative">
                        <div class="brandimgs-container">
                            <div class="image-bubble brand-bubble-1">
                                <img src="{{ asset('img/img-1.jpeg') }}" alt="Product 1">
                            </div>
                            <div class="image-bubble brand-bubble-2">
                                <img src="{{ asset('img/img-2.jpeg') }}" alt="Product 2">
                            </div>
                           

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <section id="features" class="py-5 text-light position-relative">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 position-relative">
                    <div class="feature-bubbles-container">
                        <div class="image-bubble feature-bubble-1">
                            <img src="{{ asset('img/img-3.jpeg') }}" alt="Product 3">
                        </div>
                        <div class="image-bubble feature-bubble-2">
                            <img src="{{ asset('img/img-4.jpeg') }}" alt="Product 4">
                        </div>
                        
                    </div>
                    <div class="morphing-bubbles">
                        <div class="morphing-bubble main-bubble"></div>
                        <div class="morphing-bubble secondary-bubble"></div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h2 class="display-4 mb-4">
                        Why <span class="text-primary">Shopybook Pro?</span>
                    </h2>
                    <p class="lead">Transform how you manage inventory and sales.</p>
                    <p>
                        Shopybook Pro is an all-in-one inventory management solution designed for businesses that deal with
                        measured products like greenhouse films, dam liners, and agricultural nets. Our AI-powered platform
                        helps you track stock, analyze sales, and manage your business from anywhere.
                    </p>
                </div>
            </div>
        </div>
    </section>




    <!-- Key Features Section -->
    <section id="services" class="py-5 bg-dark text-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4">
                    Powerful <span class="text-primary">Features</span>
                </h2>
                <p class="lead">
                    Everything you need to streamline your inventory
                </p>
            </div>

            <div class="row g-4">
                <!-- Inventory Tracking -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div class="uscontent">
                            <h3>Smart Inventory</h3>
                            <p>
                                Track products by weight, area, or quantity with real-time stock alerts and automated
                                replenishment suggestions.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- AI Measurement -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-ruler-combined"></i>
                        </div>
                        <div class="uscontent">
                            <h3>AI Measurement</h3>
                            <p>
                                Upload product photos to automatically estimate dimensions and weight - perfect for films
                                and liners.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Sales Analytics -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <div class="uscontent">
                            <h3>Sales Analytics</h3>
                            <p>
                                Visualize your best-selling products, profit margins, and sales trends with interactive
                                dashboards.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Voice Commands -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-microphone"></i>
                        </div>
                        <div class="uscontent">
                            <h3>Voice Control</h3>
                            <p>
                                "Add 5 rolls of greenhouse film" - manage inventory hands-free with our voice command
                                system.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Multi-Store -->
                <div class="col-md-4">
                    <div class="uscard h-100">
                        <div class="usicon text-center">
                            <i class="fa-solid fa-shop"></i>
                        </div>
                        <div class="uscontent">
                            <h3>Multi-Location</h3>
                            <p>
                                Manage inventory across multiple warehouses or stores from one centralized dashboard.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-5 bg-dark text-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4">
                    What <span class="text-primary">Users Say</span>
                </h2>
                <p class="lead">Businesses thriving with Shopybook Pro</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 bg-green text-light">
                        <div class="card-body">
                            <div class="mb-3 text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="card-text">
                                "The AI measurement tool has saved us hours of manual work. We can now quote prices
                                instantly just by taking a photo of the material."
                            </p>
                            <div class="d-flex align-items-center mt-3">
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" class="rounded-circle me-3"
                                    width="50" alt="Client" />
                                <div>
                                    <h6 class="mb-0">James Mwangi</h6>
                                    <small class="text-muted">Greenhouse Supplies Ltd</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 bg-green text-light">
                        <div class="card-body">
                            <div class="mb-3 text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                            <p class="card-text">
                                "Our inventory accuracy went from 65% to 98% in just two months. The real-time tracking is a
                                game-changer."
                            </p>
                            <div class="d-flex align-items-center mt-3">
                                <img src="https://randomuser.me/api/portraits/women/45.jpg" class="rounded-circle me-3"
                                    width="50" alt="Client" />
                                <div>
                                    <h6 class="mb-0">Wanjiku Kamau</h6>
                                    <small class="text-muted">AgriNet Solutions</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="signup" class="py-5 position-relative text-light">
        <div class="container position-relative" style="z-index: 2">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="display-4 mb-4">
                        Simple <span class="text-primary">Pricing</span>
                    </h2>
                    <p class="lead mb-5">
                        Choose the plan that fits your business needs
                    </p>
                </div>
            </div>

            <div class="row">
                <!-- Basic Plan -->
                <div class="col-md-4 mb-4">
                    <div class="card pricing-card h-100">
                        <div class="card-body text-center">
                            <h3 class="card-title">Starter</h3>
                            <div class="price mb-4">
                                <span class="currency">Ksh</span>
                                <span class="amount">1,500</span>
                                <span class="period">/month</span>
                            </div>
                            <ul class="list-unstyled mb-4">
                                <li>Up to 100 products</li>
                                <li>Basic inventory tracking</li>
                                <li>Sales reports</li>
                                <li>Email support</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary">Start Free Trial</a>
                        </div>
                    </div>
                </div>

                <!-- Pro Plan -->
                <div class="col-md-4 mb-4">
                    <div class="card pricing-card featured h-100">
                        <div class="card-body text-center">
                            <div class="popular-badge">MOST POPULAR</div>
                            <h3 class="card-title">Pro</h3>
                            <div class="price mb-4">
                                <span class="currency">Ksh</span>
                                <span class="amount">3,500</span>
                                <span class="period">/month</span>
                            </div>
                            <ul class="list-unstyled mb-4">
                                <li>Unlimited products</li>
                                <li>AI measurement tools</li>
                                <li>Voice commands</li>
                                <li>Multi-location support</li>
                                <li>Priority support</li>
                            </ul>
                            <a href="#" class="btn btn-primary">Start Free Trial</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="position-relative py-5 text-light">
        <div class="container position-relative" style="z-index: 2">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="display-4 mb-4">
                        Ready to <span class="text-primary">Transform</span> Your Business?
                    </h2>
                    <p class="lead mb-5">
                        Get in touch for a personalized demo
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <form class="glass-card p-4">
                        <div class="mb-3">
                            <input type="text" class="form-control bg-transparent text-light" placeholder="Your Name"
                                required />
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control bg-transparent text-light" placeholder="Your Email"
                                required />
                        </div>
                        <div class="mb-3">
                            <select class="form-select bg-dark text-light">
                                <option>I'm interested in...</option>
                                <option>Starter Plan</option>
                                <option>Pro Plan</option>
                                <option>Enterprise Solution</option>
                                <option>Partnership</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control bg-transparent text-light" rows="5"
                                placeholder="Tell us about your business needs" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            Request Demo
                        </button>
                    </form>
                </div>

                <div class="col-lg-6 mt-4 mt-lg-0">
                    <div class="glass-card h-100 p-4">
                        <h3 class="card-title mb-4 text-primary">Contact Us</h3>
                        <ul class="list-unstyled contact-info">
                            <li class="mb-3">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <span>support@shopybookpro.com</span>
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-phone text-primary me-2"></i>
                                <span>+254 700 123 456</span>
                            </li>
                        </ul>

                        <div class="mt-4">
                            <h5 class="mb-3 text-primary">Follow Our Journey</h5>
                            <div class="social-bubbles">
                                <a href="#" class="social-bubble">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-bubble">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="social-bubble">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
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
 

@endsection
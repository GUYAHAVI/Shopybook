@extends('layouts.master')
@section(	'content')

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
                        <h1>Havi Greenhouse Materials</h1>
                        <p class="lead">Description for Havi Greenhouse Materials</p>
                        <div class="mt-4">
                            <a href="#features" class="btn btn-primary btn-lg me-2">Contact Us</a>
                            <a href="#signup" class="btn btn-outline-light btn-lg">Make a purchase</a>
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

    <!-- PRODUCTS -->

    <section class="businesses">
<h3>Need something else? </h3>
<p class="lead">Explore our wide range of business solutions tailored to your needs.</p>
<div class="row">
  @include('partials.businesses', ['groupedBusinesses' => $groupedBusinesses])
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
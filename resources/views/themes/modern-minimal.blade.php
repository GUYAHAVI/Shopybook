{{-- Modern Minimal Theme - Clean, spacious layout with large imagery --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $website->business->name ?? 'Modern Minimal' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            color: #1F2937;
            line-height: 1.6;
        }
        
        /* Navigation - Fixed, Transparent to Solid on Scroll */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            padding: 20px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #4F46E5;
        }
        .nav-links {
            display: flex;
            gap: 40px;
            list-style: none;
        }
        .nav-links a {
            text-decoration: none;
            color: #374151;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #4F46E5; }
        
        /* Hero - Full Screen with Split Design */
        .hero {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 60px;
            padding: 100px 5% 60px;
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
        }
        .hero-content {
            max-width: 600px;
        }
        .hero h1 {
            font-size: 4rem;
            font-weight: 700;
            line-height: 1.1;
            margin-bottom: 24px;
            color: #111827;
        }
        .hero p {
            font-size: 1.25rem;
            color: #6B7280;
            margin-bottom: 40px;
        }
        .hero-image {
            position: relative;
            height: 600px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
        }
        .hero-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .cta-buttons {
            display: flex;
            gap: 20px;
        }
        .btn {
            padding: 16px 32px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
        }
        .btn-primary {
            background: #4F46E5;
            color: white;
        }
        .btn-primary:hover {
            background: #4338CA;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(79,70,229,0.3);
        }
        .btn-outline {
            background: transparent;
            color: #4F46E5;
            border: 2px solid #4F46E5;
        }
        .btn-outline:hover {
            background: #4F46E5;
            color: white;
        }
        
        /* Features - Card Grid with Icons */
        .features {
            padding: 100px 5%;
            background: white;
        }
        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 60px;
        }
        .section-header h2 {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #111827;
        }
        .section-header p {
            font-size: 1.125rem;
            color: #6B7280;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }
        .feature-card {
            padding: 40px;
            border-radius: 16px;
            background: #F9FAFB;
            transition: all 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            background: white;
        }
        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }
        .feature-icon i {
            font-size: 24px;
            color: white;
        }
        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 12px;
        }
        .feature-card p {
            color: #6B7280;
            line-height: 1.7;
        }
        
        /* Services - Alternating Image/Text Layout */
        .services {
            padding: 100px 5%;
            background: linear-gradient(to bottom, #f9fafb, #ffffff);
        }
        .service-item {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
            margin-bottom: 100px;
        }
        .service-item:nth-child(even) {
            direction: rtl;
        }
        .service-item:nth-child(even) > * {
            direction: ltr;
        }
        .service-image {
            height: 500px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
        }
        .service-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .service-content h3 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #111827;
        }
        .service-content p {
            font-size: 1.125rem;
            color: #6B7280;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .service-features {
            list-style: none;
            margin-bottom: 30px;
        }
        .service-features li {
            padding: 12px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #374151;
        }
        .service-features i {
            color: #10B981;
            font-size: 20px;
        }
        
        /* Stats - Full Width Banner */
        .stats {
            padding: 80px 5%;
            background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);
            color: white;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .stat-label {
            font-size: 1.125rem;
            opacity: 0.9;
        }
        
        /* Testimonials - Card Carousel */
        .testimonials {
            padding: 100px 5%;
            background: white;
        }
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-top: 60px;
        }
        .testimonial-card {
            padding: 40px;
            background: #F9FAFB;
            border-radius: 16px;
            border-left: 4px solid #4F46E5;
        }
        .testimonial-rating {
            color: #F59E0B;
            margin-bottom: 20px;
        }
        .testimonial-text {
            font-size: 1.125rem;
            line-height: 1.8;
            color: #374151;
            margin-bottom: 30px;
        }
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .author-name {
            font-weight: 600;
            color: #111827;
        }
        .author-role {
            color: #6B7280;
            font-size: 0.875rem;
        }
        
        /* CTA Section - Full Width with Background Image */
        .cta-section {
            position: relative;
            padding: 120px 5%;
            text-align: center;
            color: white;
            overflow: hidden;
        }
        .cta-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }
        .cta-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(79,70,229,0.9) 0%, rgba(124,58,237,0.9) 100%);
            z-index: 2;
        }
        .cta-content {
            position: relative;
            z-index: 3;
            max-width: 800px;
            margin: 0 auto;
        }
        .cta-content h2 {
            font-size: 3rem;
            margin-bottom: 24px;
        }
        .cta-content p {
            font-size: 1.25rem;
            margin-bottom: 40px;
            opacity: 0.95;
        }
        
        /* Footer - Clean and Minimal */
        .footer {
            background: #111827;
            color: white;
            padding: 60px 5% 30px;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 60px;
            margin-bottom: 40px;
        }
        .footer-about p {
            color: #9CA3AF;
            margin: 20px 0;
        }
        .footer-links {
            list-style: none;
        }
        .footer-links li {
            margin-bottom: 12px;
        }
        .footer-links a {
            color: #9CA3AF;
            text-decoration: none;
            transition: color 0.3s;
        }
        .footer-links a:hover {
            color: #4F46E5;
        }
        .footer-bottom {
            padding-top: 30px;
            border-top: 1px solid #374151;
            text-align: center;
            color: #9CA3AF;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero {
                grid-template-columns: 1fr;
                padding-top: 80px;
            }
            .hero h1 { font-size: 2.5rem; }
            .hero-image { height: 400px; }
            .service-item {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .testimonials-grid {
                grid-template-columns: 1fr;
            }
            .footer-grid {
                grid-template-columns: 1fr;
            }
            .nav-links {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">{{ $website->business->name ?? 'Company' }}</div>
        <ul class="nav-links">
            <li><a href="#home">Home</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Transform Your Business Today</h1>
            <p>Innovative solutions that drive real results for modern businesses. Experience excellence in every interaction.</p>
            <div class="cta-buttons">
                <a href="#contact" class="btn btn-primary">Get Started</a>
                <a href="#services" class="btn btn-outline">Learn More</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="/webbuilder/business-partners-shaking-hands-agreement.jpg" alt="Business Partnership">
        </div>
    </section>

    <!-- Features -->
    <section class="features" id="features">
        <div class="section-header">
            <h2>Why Choose Us</h2>
            <p>Everything you need to succeed in today's competitive market</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-rocket"></i>
                </div>
                <h3>Fast & Reliable</h3>
                <p>Lightning-fast performance that keeps your business running smoothly 24/7 with minimal downtime.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Secure & Trusted</h3>
                <p>Enterprise-grade security to protect your valuable business data and customer information.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Expert Support</h3>
                <p>Dedicated team ready to help you succeed at every step of your journey with us.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Proven Results</h3>
                <p>Track record of helping businesses achieve their goals and exceed expectations.</p>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section class="services" id="services">
        <div class="section-header">
            <h2>Our Services</h2>
            <p>Comprehensive solutions tailored to your business needs</p>
        </div>
        
        <div class="service-item">
            <div class="service-image">
                <img src="/webbuilder/colleagues-reviewing-plans-tablet.jpg" alt="Strategic Consulting">
            </div>
            <div class="service-content">
                <h3>Strategic Consulting</h3>
                <p>Expert guidance to help you make informed business decisions and achieve sustainable growth in your industry.</p>
                <ul class="service-features">
                    <li><i class="fas fa-check-circle"></i> Market Analysis & Research</li>
                    <li><i class="fas fa-check-circle"></i> Growth Strategy Development</li>
                    <li><i class="fas fa-check-circle"></i> Risk Assessment & Management</li>
                    <li><i class="fas fa-check-circle"></i> Competitive Analysis</li>
                </ul>
                <a href="#contact" class="btn btn-primary">Learn More</a>
            </div>
        </div>

        <div class="service-item">
            <div class="service-image">
                <img src="/webbuilder/young-businessmen-standing-together-holding-laptop-discussing-business.jpg" alt="Digital Transformation">
            </div>
            <div class="service-content">
                <h3>Digital Transformation</h3>
                <p>Modernize your operations with cutting-edge technology solutions tailored to your specific business requirements.</p>
                <ul class="service-features">
                    <li><i class="fas fa-check-circle"></i> Cloud Migration & Infrastructure</li>
                    <li><i class="fas fa-check-circle"></i> Process Automation</li>
                    <li><i class="fas fa-check-circle"></i> Digital Strategy Planning</li>
                    <li><i class="fas fa-check-circle"></i> Technology Integration</li>
                </ul>
                <a href="#contact" class="btn btn-primary">Learn More</a>
            </div>
        </div>

        <div class="service-item">
            <div class="service-image">
                <img src="/webbuilder/pexels-goumbik-669610.jpg" alt="Business Analytics">
            </div>
            <div class="service-content">
                <h3>Business Analytics</h3>
                <p>Data-driven insights to optimize performance and uncover new opportunities for growth and efficiency.</p>
                <ul class="service-features">
                    <li><i class="fas fa-check-circle"></i> Data Analysis & Visualization</li>
                    <li><i class="fas fa-check-circle"></i> Performance Metrics</li>
                    <li><i class="fas fa-check-circle"></i> Custom Reports & Dashboards</li>
                    <li><i class="fas fa-check-circle"></i> Predictive Analytics</li>
                </ul>
                <a href="#contact" class="btn btn-primary">Learn More</a>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="stats">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">500+</div>
                <div class="stat-label">Happy Clients</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">1M+</div>
                <div class="stat-label">Projects Completed</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">99%</div>
                <div class="stat-label">Client Satisfaction</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support Available</div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <div class="section-header">
            <h2>What Our Clients Say</h2>
            <p>Don't just take our word for it</p>
        </div>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text">"Working with this team has transformed our business. Their expertise and dedication are unmatched in the industry."</p>
                <div class="testimonial-author">
                    <div>
                        <div class="author-name">Sarah Johnson</div>
                        <div class="author-role">CEO, Tech Innovations</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text">"Exceptional service and results that exceeded our expectations. Highly recommended for any business!"</p>
                <div class="testimonial-author">
                    <div>
                        <div class="author-name">Michael Chen</div>
                        <div class="author-role">Founder, StartUp Hub</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-rating">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text">"The best investment we've made for our company. The ROI speaks for itself."</p>
                <div class="testimonial-author">
                    <div>
                        <div class="author-name">Emily Rodriguez</div>
                        <div class="author-role">Director, Growth Solutions</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <img src="/webbuilder/african-entrepreneur-start-up-company-reading-charts-documents-paperwork-diverse-team-business-people-analyzing-company-financial-reports-from-computer-successful-corporate-professional-en.jpg" alt="Background" class="cta-background">
        <div class="cta-overlay"></div>
        <div class="cta-content">
            <h2>Ready to Get Started?</h2>
            <p>Join thousands of successful businesses already using our services</p>
            <div class="cta-buttons">
                <a href="#contact" class="btn btn-primary">Start Your Free Trial</a>
                <a href="#" class="btn btn-outline" style="color: white; border-color: white;">Schedule a Demo</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-about">
                <h3>{{ $website->business->name ?? 'Company' }}</h3>
                <p>Building the future, one innovation at a time. Your trusted partner for business success.</p>
            </div>
            <div>
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4>Services</h4>
                <ul class="footer-links">
                    <li><a href="#">Consulting</a></li>
                    <li><a href="#">Digital Transform</a></li>
                    <li><a href="#">Analytics</a></li>
                    <li><a href="#">Support</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact</h4>
                <ul class="footer-links">
                    <li>Email: hello@company.com</li>
                    <li>Phone: +1 (555) 123-4567</li>
                    <li>Address: 123 Business St</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 {{ $website->business->name ?? 'Company' }}. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>

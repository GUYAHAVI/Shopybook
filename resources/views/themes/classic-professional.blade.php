{{-- Classic Professional Theme - Traditional sidebar layout with centered content --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $website->business->name ?? 'Classic Professional' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Open Sans', sans-serif;
            color: #2C3E50;
            background: #F8F9FA;
            line-height: 1.6;
        }
        
        h1, h2, h3, h4, h5 {
            font-family: 'Merriweather', serif;
        }
        
        /* Top Bar */
        .top-bar {
            background: #2C3E50;
            color: white;
            padding: 10px 0;
        }
        .top-bar-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.875rem;
        }
        .top-bar-left i {
            margin-right: 8px;
            color: #3498DB;
        }
        .top-bar-right {
            display: flex;
            gap: 20px;
        }
        .top-bar-right a {
            color: white;
            text-decoration: none;
        }
        
        /* Header */
        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-family: 'Merriweather', serif;
            font-size: 1.75rem;
            font-weight: 900;
            color: #2C3E50;
        }
        .logo span {
            color: #3498DB;
        }
        .nav-menu {
            display: flex;
            gap: 40px;
            list-style: none;
        }
        .nav-menu a {
            color: #2C3E50;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: color 0.3s;
        }
        .nav-menu a:hover {
            color: #3498DB;
        }
        
        /* Hero */
        .hero {
            background: linear-gradient(135deg, #2C3E50 0%, #34495E 100%);
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 40px;
        }
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 25px;
            line-height: 1.2;
        }
        .hero p {
            font-size: 1.25rem;
            margin-bottom: 40px;
            opacity: 0.95;
        }
        .btn-classic {
            padding: 15px 35px;
            background: #3498DB;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-classic:hover {
            background: #2980B9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52,152,219,0.3);
        }
        
        /* Main Container with Sidebar */
        .container {
            max-width: 1400px;
            margin: 80px auto;
            padding: 0 40px;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 60px;
        }
        
        /* Sidebar */
        .sidebar {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            padding: 40px 30px;
            height: fit-content;
            position: sticky;
            top: 100px;
        }
        .sidebar-section {
            margin-bottom: 40px;
        }
        .sidebar-section:last-child {
            margin-bottom: 0;
        }
        .sidebar h3 {
            font-size: 1.125rem;
            margin-bottom: 20px;
            color: #2C3E50;
            padding-bottom: 15px;
            border-bottom: 3px solid #3498DB;
        }
        .sidebar ul {
            list-style: none;
        }
        .sidebar li {
            margin-bottom: 12px;
        }
        .sidebar a {
            color: #7F8C8D;
            text-decoration: none;
            transition: color 0.3s;
            font-size: 0.95rem;
        }
        .sidebar a:hover {
            color: #3498DB;
        }
        .sidebar-bio {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar-bio img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 20px;
            border: 5px solid #3498DB;
        }
        .sidebar-bio h4 {
            margin-bottom: 8px;
            color: #2C3E50;
        }
        .sidebar-bio p {
            font-size: 0.875rem;
            color: #7F8C8D;
        }
        
        /* Main Content */
        .main-content {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            padding: 60px;
        }
        .content-header {
            margin-bottom: 50px;
            padding-bottom: 30px;
            border-bottom: 1px solid #ECF0F1;
        }
        .content-header h2 {
            font-size: 2.5rem;
            color: #2C3E50;
            margin-bottom: 15px;
        }
        .content-meta {
            font-size: 0.875rem;
            color: #95A5A6;
        }
        .content-meta i {
            margin-right: 5px;
            color: #3498DB;
        }
        
        /* Content Sections */
        .content-section {
            margin-bottom: 60px;
        }
        .content-section h3 {
            font-size: 2rem;
            margin-bottom: 25px;
            color: #2C3E50;
        }
        .content-section p {
            font-size: 1.0625rem;
            line-height: 1.8;
            color: #555;
            margin-bottom: 20px;
        }
        .content-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 10px;
            margin: 40px 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        /* Services Grid */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-top: 40px;
        }
        .service-box {
            padding: 35px;
            border: 2px solid #ECF0F1;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .service-box:hover {
            border-color: #3498DB;
            box-shadow: 0 5px 15px rgba(52,152,219,0.1);
        }
        .service-box .icon {
            font-size: 2.5rem;
            color: #3498DB;
            margin-bottom: 20px;
        }
        .service-box h4 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #2C3E50;
        }
        .service-box p {
            font-size: 1rem;
            color: #7F8C8D;
        }
        
        /* Testimonials */
        .testimonial-box {
            background: #F8F9FA;
            padding: 40px;
            border-radius: 10px;
            margin-bottom: 30px;
            border-left: 5px solid #3498DB;
        }
        .testimonial-text {
            font-size: 1.125rem;
            font-style: italic;
            color: #555;
            margin-bottom: 25px;
            line-height: 1.7;
        }
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .testimonial-author img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
        }
        .author-info h5 {
            margin-bottom: 5px;
            color: #2C3E50;
        }
        .author-info p {
            font-size: 0.875rem;
            color: #95A5A6;
            margin: 0;
        }
        
        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            margin: 60px 0;
        }
        .stat-box {
            text-align: center;
            padding: 30px;
            background: #F8F9FA;
            border-radius: 10px;
        }
        .stat-number {
            font-size: 3rem;
            font-weight: 900;
            color: #3498DB;
            margin-bottom: 10px;
            font-family: 'Merriweather', serif;
        }
        .stat-label {
            font-size: 0.95rem;
            color: #7F8C8D;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* CTA Box */
        .cta-box {
            background: linear-gradient(135deg, #3498DB 0%, #2980B9 100%);
            color: white;
            padding: 60px;
            border-radius: 10px;
            text-align: center;
            margin: 60px 0;
        }
        .cta-box h3 {
            font-size: 2.25rem;
            margin-bottom: 20px;
        }
        .cta-box p {
            font-size: 1.125rem;
            margin-bottom: 30px;
            opacity: 0.95;
        }
        .btn-outline {
            padding: 15px 35px;
            background: transparent;
            color: white;
            text-decoration: none;
            border: 2px solid white;
            border-radius: 5px;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-outline:hover {
            background: white;
            color: #3498DB;
        }
        
        /* Footer */
        .footer {
            background: #2C3E50;
            color: white;
            padding: 80px 0 30px;
        }
        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 60px;
            margin-bottom: 60px;
        }
        .footer h4 {
            margin-bottom: 25px;
            font-size: 1.25rem;
        }
        .footer p {
            color: #BDC3C7;
            line-height: 1.7;
        }
        .footer ul {
            list-style: none;
        }
        .footer li {
            margin-bottom: 12px;
        }
        .footer a {
            color: #BDC3C7;
            text-decoration: none;
            transition: color 0.3s;
        }
        .footer a:hover {
            color: #3498DB;
        }
        .footer-bottom {
            padding-top: 30px;
            border-top: 1px solid #34495E;
            text-align: center;
            color: #95A5A6;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .container {
                grid-template-columns: 1fr;
            }
            .sidebar {
                position: relative;
                top: 0;
            }
            .nav-menu {
                display: none;
            }
            .main-content {
                padding: 40px 30px;
            }
            .services-grid,
            .stats-grid {
                grid-template-columns: 1fr;
            }
            .footer-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="top-bar-content">
            <div class="top-bar-left">
                <i class="fas fa-phone"></i> +1 (555) 123-4567
                <span style="margin: 0 15px;">|</span>
                <i class="fas fa-envelope"></i> info@company.com
            </div>
            <div class="top-bar-right">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">{{ $website->business->name ?? 'Classic' }} <span>Professional</span></div>
            <nav>
                <ul class="nav-menu">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero -->
    <section class="hero">
        <div class="hero-content">
            <h1>Excellence in Professional Services</h1>
            <p>Trusted expertise, proven results, and personalized service for over 20 years</p>
            <a href="#contact" class="btn-classic">Get Started</a>
        </div>
    </section>

    <!-- Main Container with Sidebar -->
    <div class="container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-bio">
                <img src="/webbuilder/business-partners-shaking-hands-agreement.jpg" alt="Professional">
                <h4>John Anderson</h4>
                <p>Founder & CEO</p>
            </div>
            
            <div class="sidebar-section">
                <h3>Categories</h3>
                <ul>
                    <li><a href="#">Business Strategy</a></li>
                    <li><a href="#">Financial Planning</a></li>
                    <li><a href="#">Legal Services</a></li>
                    <li><a href="#">Tax Consulting</a></li>
                    <li><a href="#">Corporate Training</a></li>
                </ul>
            </div>
            
            <div class="sidebar-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Our Team</a></li>
                    <li><a href="#">Case Studies</a></li>
                    <li><a href="#">Resources</a></li>
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>
            
            <div class="sidebar-section">
                <h3>Contact Info</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 15px;"><i class="fas fa-map-marker-alt" style="color: #3498DB; margin-right: 10px;"></i> 123 Business Ave</li>
                    <li style="margin-bottom: 15px;"><i class="fas fa-phone" style="color: #3498DB; margin-right: 10px;"></i> +1 555-123-4567</li>
                    <li><i class="fas fa-envelope" style="color: #3498DB; margin-right: 10px;"></i> info@company.com</li>
                </ul>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="content-header">
                <h2>Welcome to Our Professional Services</h2>
                <div class="content-meta">
                    <i class="fas fa-calendar"></i> Established 2004 |
                    <i class="fas fa-map-marker-alt"></i> Nationwide Service |
                    <i class="fas fa-users"></i> 500+ Satisfied Clients
                </div>
            </div>

            <!-- About Section -->
            <div class="content-section">
                <h3>About Our Firm</h3>
                <img src="/webbuilder/colleagues-reviewing-plans-tablet.jpg" alt="Team Meeting" class="content-image">
                <p>For over two decades, we have been providing exceptional professional services to businesses and individuals across the nation. Our commitment to excellence, combined with deep industry expertise, has made us a trusted partner for clients seeking reliable guidance and results.</p>
                <p>Founded in 2004, our firm began with a simple mission: to deliver personalized, expert services that help our clients achieve their goals. Today, we continue to uphold that mission with a team of seasoned professionals who bring decades of combined experience to every engagement.</p>
                <p>We believe in building lasting relationships with our clients, understanding that trust is earned through consistent delivery of outstanding service. Whether you're a small business owner, corporate executive, or individual seeking professional guidance, we have the expertise and dedication to help you succeed.</p>
            </div>

            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-box">
                    <div class="stat-number">20+</div>
                    <div class="stat-label">Years Experience</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Clients Served</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">98%</div>
                    <div class="stat-label">Satisfaction Rate</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">15</div>
                    <div class="stat-label">Team Members</div>
                </div>
            </div>

            <!-- Services Section -->
            <div class="content-section">
                <h3>Our Professional Services</h3>
                <p>We offer a comprehensive range of professional services tailored to meet your unique needs:</p>
                
                <div class="services-grid">
                    <div class="service-box">
                        <div class="icon"><i class="fas fa-chart-line"></i></div>
                        <h4>Business Consulting</h4>
                        <p>Strategic planning and business development services to help your organization grow and thrive in competitive markets.</p>
                    </div>
                    <div class="service-box">
                        <div class="icon"><i class="fas fa-calculator"></i></div>
                        <h4>Financial Advisory</h4>
                        <p>Expert financial planning, analysis, and advisory services to optimize your financial performance and security.</p>
                    </div>
                    <div class="service-box">
                        <div class="icon"><i class="fas fa-balance-scale"></i></div>
                        <h4>Legal Services</h4>
                        <p>Comprehensive legal guidance for business transactions, contracts, compliance, and dispute resolution.</p>
                    </div>
                    <div class="service-box">
                        <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
                        <h4>Tax Preparation</h4>
                        <p>Professional tax preparation and planning services to ensure compliance and maximize your tax efficiency.</p>
                    </div>
                </div>
            </div>

            <img src="/webbuilder/young-businessmen-standing-together-holding-laptop-discussing-business.jpg" alt="Collaboration" class="content-image">

            <!-- Why Choose Us -->
            <div class="content-section">
                <h3>Why Choose Our Firm?</h3>
                <p><strong>Proven Expertise:</strong> Our team brings decades of combined experience across multiple industries and disciplines, ensuring you receive knowledgeable guidance backed by real-world success.</p>
                <p><strong>Personalized Service:</strong> We don't believe in one-size-fits-all solutions. Every client receives customized service tailored to their specific situation, goals, and challenges.</p>
                <p><strong>Results-Oriented:</strong> Our focus is on delivering measurable results that make a real difference to your business or personal objectives.</p>
                <p><strong>Integrity & Trust:</strong> We operate with the highest ethical standards, maintaining confidentiality and putting your interests first in every engagement.</p>
            </div>

            <!-- Testimonials -->
            <div class="content-section">
                <h3>Client Testimonials</h3>
                
                <div class="testimonial-box">
                    <p class="testimonial-text">"Working with this firm transformed our business. Their strategic insights and professional guidance helped us navigate complex challenges and achieve growth beyond our expectations."</p>
                    <div class="testimonial-author">
                        <img src="/webbuilder/pexels-goumbik-669610.jpg" alt="Client">
                        <div class="author-info">
                            <h5>Michael Roberts</h5>
                            <p>CEO, Roberts Enterprises</p>
                        </div>
                    </div>
                </div>
                
                <div class="testimonial-box">
                    <p class="testimonial-text">"The level of expertise and attention to detail is exceptional. They've been our trusted advisors for over 10 years, and we couldn't be more satisfied with their service."</p>
                    <div class="testimonial-author">
                        <img src="/webbuilder/african-entrepreneur-start-up-company-reading-charts-documents-paperwork-diverse-team-business-people-analyzing-company-financial-reports-from-computer-successful-corporate-professional-en.jpg" alt="Client">
                        <div class="author-info">
                            <h5>Jennifer Martinez</h5>
                            <p>Founder, Martinez Consulting</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="cta-box">
                <h3>Ready to Get Started?</h3>
                <p>Contact us today for a complimentary consultation and discover how our professional services can help you achieve your goals.</p>
                <a href="#contact" class="btn-outline">Schedule Consultation</a>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-grid">
                <div>
                    <h4>{{ $website->business->name ?? 'Classic Professional' }}</h4>
                    <p>Providing trusted professional services since 2004. Our commitment to excellence and client satisfaction has made us a leading firm in our field.</p>
                </div>
                <div>
                    <h4>Services</h4>
                    <ul>
                        <li><a href="#">Business Consulting</a></li>
                        <li><a href="#">Financial Advisory</a></li>
                        <li><a href="#">Legal Services</a></li>
                        <li><a href="#">Tax Preparation</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Our Team</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <h4>Contact</h4>
                    <ul>
                        <li>123 Business Avenue</li>
                        <li>Suite 100</li>
                        <li>New York, NY 10001</li>
                        <li style="margin-top: 15px;"><a href="tel:+15551234567">+1 (555) 123-4567</a></li>
                        <li><a href="mailto:info@company.com">info@company.com</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 {{ $website->business->name ?? 'Classic Professional' }}. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

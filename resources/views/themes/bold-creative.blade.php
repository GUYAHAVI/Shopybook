{{-- Bold & Creative Theme - Asymmetric, vibrant layout with overlapping sections --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $website->business->name ?? 'Bold & Creative' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            color: #1F2937;
            overflow-x: hidden;
        }
        
        /* Side Navigation - Vertical */
        .side-nav {
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            width: 80px;
            background: #EC4899;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 0;
        }
        .side-nav .logo {
            font-size: 28px;
            color: white;
            font-weight: 900;
            writing-mode: vertical-rl;
            margin-bottom: 50px;
        }
        .side-nav .nav-dots {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: auto;
            margin-bottom: auto;
        }
        .side-nav .nav-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            cursor: pointer;
            transition: all 0.3s;
        }
        .side-nav .nav-dot.active,
        .side-nav .nav-dot:hover {
            background: white;
            transform: scale(1.5);
        }
        
        /* Hero - Diagonal Split with Animated Background */
        .hero {
            min-height: 100vh;
            padding-left: 80px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #EC4899 0%, #8B5CF6 100%);
        }
        .hero-content-wrapper {
            height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        .hero-content {
            max-width: 600px;
            padding: 0 80px;
            color: white;
        }
        .hero h1 {
            font-size: 5rem;
            font-weight: 900;
            line-height: 0.9;
            margin-bottom: 30px;
            text-transform: uppercase;
        }
        .hero h1 span {
            display: block;
            color: #FDE047;
        }
        .hero p {
            font-size: 1.25rem;
            margin-bottom: 50px;
            opacity: 0.95;
        }
        .hero-image {
            position: absolute;
            right: -10%;
            top: 10%;
            width: 55%;
            height: 80%;
            border-radius: 30px;
            overflow: hidden;
            transform: rotate(5deg);
            box-shadow: 0 30px 80px rgba(0,0,0,0.3);
        }
        .hero-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .hero-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
        }
        .shape-1 {
            width: 300px;
            height: 300px;
            top: -100px;
            right: 20%;
            animation: float 6s ease-in-out infinite;
        }
        .shape-2 {
            width: 200px;
            height: 200px;
            bottom: 10%;
            left: 30%;
            animation: float 8s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-30px); }
        }
        .btn-creative {
            padding: 18px 40px;
            font-size: 16px;
            font-weight: 700;
            background: white;
            color: #EC4899;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-creative:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        /* About - Overlapping Cards */
        .about {
            padding: 150px 80px 150px 160px;
            background: white;
            position: relative;
        }
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 100px;
            align-items: center;
        }
        .about-images {
            position: relative;
            height: 600px;
        }
        .about-image {
            position: absolute;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        .about-image-1 {
            width: 60%;
            height: 70%;
            top: 0;
            left: 0;
            z-index: 2;
        }
        .about-image-2 {
            width: 55%;
            height: 60%;
            bottom: 0;
            right: 0;
            z-index: 1;
        }
        .about-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .about-content h2 {
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 30px;
            color: #111827;
            line-height: 1;
        }
        .about-content h2 span {
            color: #EC4899;
        }
        .about-content p {
            font-size: 1.125rem;
            line-height: 1.8;
            color: #6B7280;
            margin-bottom: 25px;
        }
        
        /* Features - Masonry Grid */
        .features {
            padding: 150px 80px 150px 160px;
            background: linear-gradient(to bottom, #f9fafb, #ffffff);
        }
        .features h2 {
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 80px;
            text-align: center;
        }
        .features-masonry {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }
        .feature-card {
            padding: 50px 40px;
            border-radius: 30px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }
        .feature-card:nth-child(1) {
            background: linear-gradient(135deg, #EC4899 0%, #F97316 100%);
            color: white;
            grid-row: span 2;
        }
        .feature-card:nth-child(2) {
            background: linear-gradient(135deg, #8B5CF6 0%, #EC4899 100%);
            color: white;
        }
        .feature-card:nth-child(3) {
            background: white;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .feature-card:nth-child(4) {
            background: white;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            grid-row: span 2;
        }
        .feature-card:nth-child(5) {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
        }
        .feature-card:nth-child(6) {
            background: linear-gradient(135deg, #F59E0B 0%, #F97316 100%);
            color: white;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .feature-card .icon {
            font-size: 3rem;
            margin-bottom: 30px;
        }
        .feature-card h3 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .feature-card p {
            line-height: 1.7;
            opacity: 0.9;
        }
        
        /* Portfolio - Full Width Slider */
        .portfolio {
            padding: 150px 0 150px 80px;
            background: #111827;
            color: white;
        }
        .portfolio-header {
            padding: 0 80px;
            margin-bottom: 80px;
        }
        .portfolio h2 {
            font-size: 4rem;
            font-weight: 900;
            margin-bottom: 20px;
        }
        .portfolio p {
            font-size: 1.25rem;
            opacity: 0.8;
        }
        .portfolio-slider {
            display: flex;
            gap: 40px;
            overflow-x: auto;
            padding: 0 80px 40px 80px;
            scroll-snap-type: x mandatory;
        }
        .portfolio-item {
            flex: 0 0 500px;
            height: 600px;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            scroll-snap-align: start;
            cursor: pointer;
            transition: all 0.3s;
        }
        .portfolio-item:hover {
            transform: scale(1.05);
        }
        .portfolio-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .portfolio-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 40px;
            background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 100%);
        }
        .portfolio-overlay h3 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .portfolio-overlay p {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        /* Team - Circular Layout */
        .team {
            padding: 150px 80px 150px 160px;
            background: white;
        }
        .team h2 {
            font-size: 4rem;
            font-weight: 900;
            text-align: center;
            margin-bottom: 100px;
        }
        .team-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 60px;
        }
        .team-member {
            text-align: center;
        }
        .team-photo {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            margin: 0 auto 30px;
            overflow: hidden;
            border: 8px solid #EC4899;
            box-shadow: 0 15px 40px rgba(236,72,153,0.3);
            transition: all 0.3s;
        }
        .team-member:hover .team-photo {
            transform: scale(1.1);
            border-color: #8B5CF6;
        }
        .team-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .team-name {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .team-role {
            color: #EC4899;
            font-weight: 600;
        }
        
        /* CTA - Diagonal Background */
        .cta {
            padding: 150px 80px 150px 160px;
            background: linear-gradient(135deg, #8B5CF6 0%, #EC4899 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        .cta-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.2;
        }
        .cta-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 900px;
            margin: 0 auto;
        }
        .cta h2 {
            font-size: 5rem;
            font-weight: 900;
            margin-bottom: 30px;
            line-height: 1;
        }
        .cta p {
            font-size: 1.5rem;
            margin-bottom: 50px;
        }
        
        /* Footer - Bold */
        .footer {
            background: #111827;
            color: white;
            padding: 80px 80px 40px 160px;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 80px;
            margin-bottom: 60px;
        }
        .footer h3 {
            font-size: 2rem;
            margin-bottom: 30px;
            color: #EC4899;
        }
        .footer ul {
            list-style: none;
        }
        .footer li {
            margin-bottom: 15px;
        }
        .footer a {
            color: #9CA3AF;
            text-decoration: none;
            transition: color 0.3s;
        }
        .footer a:hover {
            color: #EC4899;
        }
        .footer-bottom {
            padding-top: 40px;
            border-top: 1px solid #374151;
            text-align: center;
            color: #9CA3AF;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .side-nav { display: none; }
            .hero { padding-left: 0; }
            .hero h1 { font-size: 3rem; }
            .hero-image { position: relative; width: 100%; right: 0; top: 0; margin-top: 40px; }
            .about, .features, .team, .cta, .footer { padding-left: 20px; padding-right: 20px; }
            .about-grid, .team-grid { grid-template-columns: 1fr; }
            .features-masonry { grid-template-columns: 1fr; }
            .portfolio { padding-left: 0; }
        }
    </style>
</head>
<body>
    <!-- Side Navigation -->
    <div class="side-nav">
        <div class="logo">{{ substr($website->business->name ?? 'BC', 0, 2) }}</div>
        <div class="nav-dots">
            <div class="nav-dot active"></div>
            <div class="nav-dot"></div>
            <div class="nav-dot"></div>
            <div class="nav-dot"></div>
            <div class="nav-dot"></div>
        </div>
    </div>

    <!-- Hero -->
    <section class="hero">
        <div class="hero-shape shape-1"></div>
        <div class="hero-shape shape-2"></div>
        <div class="hero-content-wrapper">
            <div class="hero-content">
                <h1>
                    Create
                    <span>Amazing</span>
                    Things
                </h1>
                <p>Bold ideas deserve bold execution. We transform visions into reality with creativity and innovation.</p>
                <a href="#contact" class="btn-creative">Let's Create</a>
            </div>
            <div class="hero-image">
                <img src="/webbuilder/african-entrepreneur-start-up-company-reading-charts-documents-paperwork-diverse-team-business-people-analyzing-company-financial-reports-from-computer-successful-corporate-professional-en.jpg" alt="Creative Team">
            </div>
        </div>
    </section>

    <!-- About -->
    <section class="about">
        <div class="about-grid">
            <div class="about-images">
                <div class="about-image about-image-1">
                    <img src="/webbuilder/young-businessmen-standing-together-holding-laptop-discussing-business.jpg" alt="Our Team">
                </div>
                <div class="about-image about-image-2">
                    <img src="/webbuilder/colleagues-reviewing-plans-tablet.jpg" alt="Our Work">
                </div>
            </div>
            <div class="about-content">
                <h2>
                    We Are <span>Bold.</span>
                    We Are <span>Creative.</span>
                </h2>
                <p>Founded with a vision to revolutionize the industry, we've grown from a small startup to a trusted partner for businesses worldwide.</p>
                <p>Our journey has been driven by innovation, dedication, and an unwavering commitment to excellence. We don't just follow trends—we create them.</p>
                <p>Every project is an opportunity to push boundaries and deliver exceptional results that exceed expectations.</p>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="features">
        <h2>What Makes Us Different</h2>
        <div class="features-masonry">
            <div class="feature-card">
                <div class="icon"><i class="fas fa-lightbulb"></i></div>
                <h3>Creative Innovation</h3>
                <p>We think outside the box to deliver unique solutions that set you apart from the competition.</p>
            </div>
            <div class="feature-card">
                <div class="icon"><i class="fas fa-rocket"></i></div>
                <h3>Fast Execution</h3>
                <p>Quick turnaround without compromising quality or attention to detail.</p>
            </div>
            <div class="feature-card">
                <div class="icon"><i class="fas fa-palette"></i></div>
                <h3>Beautiful Design</h3>
                <p>Aesthetically pleasing designs that captivate and engage your audience.</p>
            </div>
            <div class="feature-card">
                <div class="icon"><i class="fas fa-code"></i></div>
                <h3>Clean Code</h3>
                <p>Well-structured, maintainable code that scales with your business growth and evolving needs.</p>
            </div>
            <div class="feature-card">
                <div class="icon"><i class="fas fa-users"></i></div>
                <h3>Expert Team</h3>
                <p>Talented professionals dedicated to your success.</p>
            </div>
            <div class="feature-card">
                <div class="icon"><i class="fas fa-star"></i></div>
                <h3>5-Star Service</h3>
                <p>Exceptional client care every step of the way.</p>
            </div>
        </div>
    </section>

    <!-- Portfolio -->
    <section class="portfolio">
        <div class="portfolio-header">
            <h2>Our Latest Work</h2>
            <p>Projects we're proud of</p>
        </div>
        <div class="portfolio-slider">
            <div class="portfolio-item">
                <img src="/webbuilder/composition-beauty-industry-products-women.jpg" alt="Project 1">
                <div class="portfolio-overlay">
                    <h3>Brand Identity</h3>
                    <p>Complete rebranding for beauty industry leader</p>
                </div>
            </div>
            <div class="portfolio-item">
                <img src="/webbuilder/medium-shot-man-looking-jewelry.jpg" alt="Project 2">
                <h3>E-Commerce Platform</h3>
                    <p>Luxury retail experience</p>
                </div>
            </div>
            <div class="portfolio-item">
                <img src="/webbuilder/pexels-goumbik-669610.jpg" alt="Project 3">
                <div class="portfolio-overlay">
                    <h3>Analytics Dashboard</h3>
                    <p>Data visualization for enterprise clients</p>
                </div>
            </div>
            <div class="portfolio-item">
                <img src="/webbuilder/business-partners-shaking-hands-agreement.jpg" alt="Project 4">
                <div class="portfolio-overlay">
                    <h3>Corporate Website</h3>
                    <p>Professional web presence</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team -->
    <section class="team">
        <h2>Meet the Creators</h2>
        <div class="team-grid">
            <div class="team-member">
                <div class="team-photo">
                    <img src="/webbuilder/happy-black-woman-holding-skincare-product.jpg" alt="Team Member">
                </div>
                <div class="team-name">Sarah Johnson</div>
                <div class="team-role">Creative Director</div>
            </div>
            <div class="team-member">
                <div class="team-photo">
                    <img src="/webbuilder/portrait-young-beautiful-woman-with-beauty-product.jpg" alt="Team Member">
                </div>
                <div class="team-name">Emily Chen</div>
                <div class="team-role">Lead Designer</div>
            </div>
            <div class="team-member">
                <div class="team-photo">
                    <img src="/webbuilder/beautiful-three-welldressed-afro-american-girls-customers-with-colored-shopping-bags-mobile-phone-shop-choosing-smartphone.jpg" alt="Team Member">
                </div>
                <div class="team-name">Lisa Martinez</div>
                <div class="team-role">Brand Strategist</div>
            </div>
            <div class="team-member">
                <div class="team-photo">
                    <img src="/webbuilder/cosmetics1.jpeg" alt="Team Member">
                </div>
                <div class="team-name">Jessica Lee</div>
                <div class="team-role">Marketing Lead</div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta">
        <img src="/webbuilder/african-entrepreneur-start-up-company-reading-charts-documents-paperwork-diverse-team-business-people-analyzing-company-financial-reports-from-computer-successful-corporate-professional-en.jpg" alt="Background" class="cta-background">
        <div class="cta-content">
            <h2>Let's Create Something Amazing Together</h2>
            <p>Ready to start your project? Get in touch today!</p>
            <a href="#contact" class="btn-creative">Start Your Project</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-grid">
            <div>
                <h3>{{ $website->business->name ?? 'Bold & Creative' }}</h3>
                <p style="color: #9CA3AF; margin-top: 20px;">Creating bold solutions for forward-thinking businesses.</p>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 20px;">Links</h4>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Portfolio</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 20px;">Services</h4>
                <ul>
                    <li><a href="#">Branding</a></li>
                    <li><a href="#">Web Design</a></li>
                    <li><a href="#">Development</a></li>
                    <li><a href="#">Strategy</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 20px;">Connect</h4>
                <ul>
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">LinkedIn</a></li>
                    <li><a href="#">Dribbble</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 {{ $website->business->name ?? 'Bold & Creative' }}. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Smooth scroll for nav dots
        const dots = document.querySelectorAll('.nav-dot');
        const sections = document.querySelectorAll('section');
        
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                sections[index].scrollIntoView({ behavior: 'smooth' });
            });
        });
        
        // Active dot on scroll
        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (pageYOffset >= sectionTop - 200) {
                    current = section.getAttribute('class');
                }
            });
            dots.forEach((dot, index) => {
                dot.classList.remove('active');
                if (sections[index].classList.contains(current)) {
                    dot.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>

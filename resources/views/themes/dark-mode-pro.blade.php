{{-- Dark Mode Pro Theme - Modern dark theme with glassmorphism and parallax --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $website->business->name ?? 'Dark Mode Pro' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --bg-primary: #0a0a0a;
            --bg-secondary: #111111;
            --bg-tertiary: #1a1a1a;
            --accent-cyan: #00D9FF;
            --accent-purple: #A855F7;
            --accent-pink: #EC4899;
            --text-primary: #FFFFFF;
            --text-secondary: #A0A0A0;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            overflow-x: hidden;
        }
        
        /* Fixed Navigation */
        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 20px 80px;
            background: rgba(10, 10, 10, 0.7);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1600px;
            margin: 0 auto;
        }
        .nav-logo {
            font-size: 1.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--accent-cyan) 0%, var(--accent-purple) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .nav-links {
            display: flex;
            gap: 50px;
            list-style: none;
        }
        .nav-links a {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s;
            position: relative;
        }
        .nav-links a:hover {
            color: var(--accent-cyan);
        }
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-cyan), var(--accent-purple));
            transition: width 0.3s;
        }
        .nav-links a:hover::after {
            width: 100%;
        }
        
        /* Hero - Full Screen Parallax */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            padding: 0 80px;
        }
        .hero-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.15;
            filter: grayscale(100%);
        }
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, rgba(0,217,255,0.1) 0%, transparent 70%);
        }
        .hero-grid {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(0,217,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,217,255,0.03) 1px, transparent 1px);
            background-size: 100px 100px;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 1000px;
        }
        .hero h1 {
            font-size: 6rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 30px;
            background: linear-gradient(135deg, #FFFFFF 0%, var(--accent-cyan) 50%, var(--accent-purple) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero p {
            font-size: 1.5rem;
            color: var(--text-secondary);
            margin-bottom: 50px;
            font-weight: 300;
        }
        .btn-glow {
            padding: 18px 50px;
            font-size: 1.0rem;
            font-weight: 600;
            background: linear-gradient(135deg, var(--accent-cyan) 0%, var(--accent-purple) 100%);
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        .btn-glow::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.2);
            transition: left 0.5s;
        }
        .btn-glow:hover::before {
            left: 100%;
        }
        .btn-glow:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(0,217,255,0.4);
        }
        
        /* Glass Cards Section */
        .glass-section {
            padding: 150px 80px;
            position: relative;
        }
        .section-title {
            font-size: 4rem;
            font-weight: 900;
            text-align: center;
            margin-bottom: 80px;
            background: linear-gradient(135deg, #FFFFFF 0%, var(--accent-cyan) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .glass-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            max-width: 1600px;
            margin: 0 auto;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 30px;
            padding: 50px 40px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0,217,255,0.1) 0%, rgba(168,85,247,0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .glass-card:hover::before {
            opacity: 1;
        }
        .glass-card:hover {
            transform: translateY(-10px);
            border-color: rgba(0,217,255,0.3);
            box-shadow: 0 20px 60px rgba(0,217,255,0.2);
        }
        .glass-card .icon {
            font-size: 3.5rem;
            background: linear-gradient(135deg, var(--accent-cyan) 0%, var(--accent-purple) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 30px;
        }
        .glass-card h3 {
            font-size: 1.75rem;
            margin-bottom: 15px;
            font-weight: 700;
        }
        .glass-card p {
            color: var(--text-secondary);
            line-height: 1.7;
            font-size: 1.0rem;
        }
        
        /* Image Gallery with Parallax */
        .gallery-section {
            padding: 100px 0;
            background: var(--bg-secondary);
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            max-width: 1600px;
            margin: 0 auto;
        }
        .gallery-item {
            height: 600px;
            position: relative;
            overflow: hidden;
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        .gallery-item:hover img {
            transform: scale(1.1);
        }
        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 60%);
            display: flex;
            align-items: flex-end;
            padding: 50px;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }
        .gallery-overlay h3 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .gallery-overlay p {
            color: var(--text-secondary);
        }
        
        /* Stats with Neon Effect */
        .stats-section {
            padding: 150px 80px;
            background: var(--bg-primary);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 60px;
            max-width: 1600px;
            margin: 0 auto;
        }
        .stat-card {
            text-align: center;
            padding: 60px 40px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(0,217,255,0.2);
            border-radius: 20px;
            transition: all 0.3s;
        }
        .stat-card:hover {
            background: rgba(0,217,255,0.05);
            border-color: var(--accent-cyan);
            box-shadow: 0 0 40px rgba(0,217,255,0.3);
        }
        .stat-number {
            font-size: 4rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--accent-cyan) 0%, var(--accent-purple) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
        }
        .stat-label {
            font-size: 1.125rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }
        
        /* Testimonials */
        .testimonials-section {
            padding: 150px 80px;
            background: var(--bg-secondary);
        }
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
            max-width: 1600px;
            margin: 80px auto 0;
        }
        .testimonial-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 30px;
            padding: 50px;
            position: relative;
        }
        .quote-icon {
            font-size: 4rem;
            color: var(--accent-cyan);
            opacity: 0.2;
            margin-bottom: 20px;
        }
        .testimonial-text {
            font-size: 1.25rem;
            line-height: 1.8;
            color: var(--text-secondary);
            margin-bottom: 40px;
        }
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid var(--accent-cyan);
        }
        .author-name {
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .author-title {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }
        
        /* CTA Section */
        .cta-section {
            padding: 150px 80px;
            background: var(--bg-primary);
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
            opacity: 0.1;
        }
        .cta-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 900px;
            margin: 0 auto;
        }
        .cta-content h2 {
            font-size: 5rem;
            font-weight: 900;
            margin-bottom: 30px;
            line-height: 1.1;
        }
        .cta-content p {
            font-size: 1.5rem;
            color: var(--text-secondary);
            margin-bottom: 50px;
        }
        
        /* Footer */
        .footer {
            background: var(--bg-tertiary);
            padding: 100px 80px 50px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 80px;
            max-width: 1600px;
            margin: 0 auto 60px;
        }
        .footer h3 {
            font-size: 1.5rem;
            margin-bottom: 25px;
            background: linear-gradient(135deg, var(--accent-cyan) 0%, var(--accent-purple) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .footer p {
            color: var(--text-secondary);
            line-height: 1.8;
        }
        .footer ul {
            list-style: none;
        }
        .footer li {
            margin-bottom: 15px;
        }
        .footer a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s;
        }
        .footer a:hover {
            color: var(--accent-cyan);
        }
        .footer-bottom {
            padding-top: 40px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            text-align: center;
            color: var(--text-secondary);
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .nav { padding: 20px 30px; }
            .nav-links { display: none; }
            .hero { padding: 0 30px; }
            .hero h1 { font-size: 3rem; }
            .glass-section, .stats-section, .testimonials-section, .cta-section, .footer { padding-left: 30px; padding-right: 30px; }
            .glass-grid, .stats-grid, .testimonials-grid, .gallery-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="nav">
        <div class="nav-content">
            <div class="nav-logo">{{ $website->business->name ?? 'DARK PRO' }}</div>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#work">Work</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <img src="/webbuilder/african-entrepreneur-start-up-company-reading-charts-documents-paperwork-diverse-team-business-people-analyzing-company-financial-reports-from-computer-successful-corporate-professional-en.jpg" alt="Background" class="hero-background">
        <div class="hero-overlay"></div>
        <div class="hero-grid"></div>
        <div class="hero-content">
            <h1>The Future is Dark</h1>
            <p>Experience the next generation of digital excellence with cutting-edge technology and innovative design</p>
            <a href="#contact" class="btn-glow">Get Started</a>
        </div>
    </section>

    <!-- Glass Cards -->
    <section class="glass-section">
        <h2 class="section-title">What We Offer</h2>
        <div class="glass-grid">
            <div class="glass-card">
                <div class="icon"><i class="fas fa-code"></i></div>
                <h3>Advanced Development</h3>
                <p>Cutting-edge web applications built with the latest technologies and best practices for optimal performance.</p>
            </div>
            <div class="glass-card">
                <div class="icon"><i class="fas fa-palette"></i></div>
                <h3>Modern Design</h3>
                <p>Stunning visual experiences that captivate users with contemporary aesthetics and intuitive interfaces.</p>
            </div>
            <div class="glass-card">
                <div class="icon"><i class="fas fa-rocket"></i></div>
                <h3>Performance Optimization</h3>
                <p>Lightning-fast load times and seamless interactions through meticulous optimization techniques.</p>
            </div>
            <div class="glass-card">
                <div class="icon"><i class="fas fa-shield-alt"></i></div>
                <h3>Enterprise Security</h3>
                <p>Bank-level security measures to protect your data and ensure compliance with industry standards.</p>
            </div>
            <div class="glass-card">
                <div class="icon"><i class="fas fa-chart-line"></i></div>
                <h3>Data Analytics</h3>
                <p>Powerful insights through advanced analytics and real-time data visualization dashboards.</p>
            </div>
            <div class="glass-card">
                <div class="icon"><i class="fas fa-headset"></i></div>
                <h3>24/7 Support</h3>
                <p>Round-the-clock expert support to ensure your operations run smoothly without interruption.</p>
            </div>
        </div>
    </section>

    <!-- Gallery -->
    <section class="gallery-section">
        <div class="gallery-grid">
            <div class="gallery-item">
                <img src="/webbuilder/business-partners-shaking-hands-agreement.jpg" alt="Partnership">
                <div class="gallery-overlay">
                    <div>
                        <h3>Strategic Partnerships</h3>
                        <p>Building lasting relationships</p>
                    </div>
                </div>
            </div>
            <div class="gallery-item">
                <img src="/webbuilder/colleagues-reviewing-plans-tablet.jpg" alt="Planning">
                <div class="gallery-overlay">
                    <div>
                        <h3>Strategic Planning</h3>
                        <p>Meticulous execution</p>
                    </div>
                </div>
            </div>
            <div class="gallery-item">
                <img src="/webbuilder/young-businessmen-standing-together-holding-laptop-discussing-business.jpg" alt="Collaboration">
                <div class="gallery-overlay">
                    <div>
                        <h3>Team Collaboration</h3>
                        <p>Working together</p>
                    </div>
                </div>
            </div>
            <div class="gallery-item">
                <img src="/webbuilder/pexels-goumbik-669610.jpg" alt="Analytics">
                <div class="gallery-overlay">
                    <div>
                        <h3>Data-Driven Decisions</h3>
                        <p>Analytics that matter</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="stats-section">
        <h2 class="section-title">By The Numbers</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">850+</div>
                <div class="stat-label">Projects</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">300+</div>
                <div class="stat-label">Clients</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">99.9%</div>
                <div class="stat-label">Uptime</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support</div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials-section">
        <h2 class="section-title">Client Success Stories</h2>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
                <p class="testimonial-text">"The level of expertise and innovation exceeded all expectations. They transformed our digital presence and helped us achieve unprecedented growth."</p>
                <div class="testimonial-author">
                    <img src="/webbuilder/composition-beauty-industry-products-women.jpg" alt="Client" class="author-avatar">
                    <div>
                        <div class="author-name">Alexandra Smith</div>
                        <div class="author-title">CEO, Beauty Corp</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
                <p class="testimonial-text">"Outstanding service from start to finish. The team delivered a cutting-edge solution that perfectly aligned with our vision and business goals."</p>
                <div class="testimonial-author">
                    <img src="/webbuilder/medium-shot-man-looking-jewelry.jpg" alt="Client" class="author-avatar">
                    <div>
                        <div class="author-name">Marcus Johnson</div>
                        <div class="author-title">Founder, Luxury Retail</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <img src="/webbuilder/african-entrepreneur-start-up-company-reading-charts-documents-paperwork-diverse-team-business-people-analyzing-company-financial-reports-from-computer-successful-corporate-professional-en.jpg" alt="CTA Background" class="cta-background">
        <div class="cta-content">
            <h2>Ready to Go Dark?</h2>
            <p>Join hundreds of forward-thinking companies leveraging cutting-edge technology</p>
            <a href="#contact" class="btn-glow">Start Your Project</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-grid">
            <div>
                <h3>{{ $website->business->name ?? 'Dark Mode Pro' }}</h3>
                <p>Pioneering the future of digital experiences with innovative technology and exceptional design.</p>
            </div>
            <div>
                <h4 style="color: var(--text-primary); margin-bottom: 20px;">Services</h4>
                <ul>
                    <li><a href="#">Development</a></li>
                    <li><a href="#">Design</a></li>
                    <li><a href="#">Analytics</a></li>
                    <li><a href="#">Support</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: var(--text-primary); margin-bottom: 20px;">Company</h4>
                <ul>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: var(--text-primary); margin-bottom: 20px;">Connect</h4>
                <ul>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">LinkedIn</a></li>
                    <li><a href="#">GitHub</a></li>
                    <li><a href="#">Dribbble</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 {{ $website->business->name ?? 'Dark Mode Pro' }}. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Parallax effect on scroll
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallaxElements = document.querySelectorAll('.hero-background, .cta-background');
            parallaxElements.forEach(el => {
                el.style.transform = `translateY(${scrolled * 0.5}px)`;
            });
        });
    </script>
</body>
</html>

{{-- Portfolio Showcase Theme - Masonry grid with lightbox gallery --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $website->business->name ?? 'Portfolio Showcase' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700;900&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Work Sans', sans-serif;
            color: #18181B;
            line-height: 1.6;
        }
        
        /* Navigation - Minimal Sticky */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px 60px;
            z-index: 1000;
            border-bottom: 1px solid rgba(99, 102, 241, 0.1);
        }
        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-family: 'Raleway', sans-serif;
            font-size: 1.5rem;
            font-weight: 900;
            color: #6366F1;
            text-decoration: none;
        }
        .nav-links {
            display: flex;
            gap: 40px;
            list-style: none;
        }
        .nav-links a {
            text-decoration: none;
            color: #18181B;
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
        }
        .nav-links a:hover {
            color: #6366F1;
        }
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: #6366F1;
            transition: width 0.3s;
        }
        .nav-links a:hover::after {
            width: 100%;
        }
        
        /* Hero - Full Screen with Split Layout */
        .hero {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            padding: 120px 60px 60px;
            background: linear-gradient(135deg, #F5F3FF 0%, #FFFFFF 50%, #ECFEFF 100%);
        }
        .hero-content {
            max-width: 600px;
        }
        .hero-label {
            display: inline-block;
            background: #6366F1;
            color: white;
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .hero h1 {
            font-family: 'Raleway', sans-serif;
            font-size: 4rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 30px;
            color: #18181B;
        }
        .hero h1 .gradient-text {
            background: linear-gradient(135deg, #6366F1 0%, #EC4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero p {
            font-size: 1.25rem;
            color: #52525B;
            margin-bottom: 40px;
        }
        .hero-buttons {
            display: flex;
            gap: 20px;
        }
        .btn {
            padding: 15px 40px;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #6366F1;
            color: white;
        }
        .btn-primary:hover {
            background: #4F46E5;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
        }
        .btn-secondary {
            background: transparent;
            color: #6366F1;
            border: 2px solid #6366F1;
        }
        .btn-secondary:hover {
            background: #6366F1;
            color: white;
        }
        .hero-image {
            position: relative;
        }
        .hero-image img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        /* Portfolio - Masonry Grid */
        .portfolio {
            padding: 100px 60px;
            max-width: 1600px;
            margin: 0 auto;
        }
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }
        .section-label {
            color: #6366F1;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.875rem;
            margin-bottom: 15px;
        }
        .section-header h2 {
            font-family: 'Raleway', sans-serif;
            font-size: 3rem;
            font-weight: 900;
            color: #18181B;
            margin-bottom: 20px;
        }
        .section-header p {
            font-size: 1.125rem;
            color: #52525B;
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Masonry Grid */
        .masonry-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }
        .portfolio-item {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .portfolio-item:hover {
            transform: translateY(-10px);
        }
        .portfolio-item.tall {
            grid-row: span 2;
        }
        .portfolio-item.wide {
            grid-column: span 2;
        }
        .portfolio-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s;
        }
        .portfolio-item:hover img {
            transform: scale(1.1);
        }
        .portfolio-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, transparent 100%);
            padding: 30px;
            transform: translateY(100%);
            transition: transform 0.3s;
        }
        .portfolio-item:hover .portfolio-overlay {
            transform: translateY(0);
        }
        .portfolio-overlay h3 {
            color: white;
            font-family: 'Raleway', sans-serif;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .portfolio-overlay p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
        }
        .portfolio-category {
            display: inline-block;
            background: #6366F1;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.75rem;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        /* Lightbox */
        .lightbox {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.95);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .lightbox.active {
            display: flex;
        }
        .lightbox-content {
            max-width: 1200px;
            max-height: 90vh;
            position: relative;
        }
        .lightbox-content img {
            max-width: 100%;
            max-height: 80vh;
            border-radius: 10px;
        }
        .lightbox-close {
            position: absolute;
            top: -40px;
            right: 0;
            background: transparent;
            border: none;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            width: 40px;
            height: 40px;
        }
        .lightbox-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 2rem;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s;
        }
        .lightbox-nav:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        .lightbox-prev {
            left: 20px;
        }
        .lightbox-next {
            right: 20px;
        }
        
        /* Skills Section */
        .skills {
            padding: 100px 60px;
            background: #F5F3FF;
        }
        .skills-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
        }
        .skill-card {
            text-align: center;
            padding: 40px 30px;
            background: white;
            border-radius: 15px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .skill-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.2);
        }
        .skill-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #6366F1 0%, #EC4899 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2rem;
            color: white;
        }
        .skill-card h3 {
            font-family: 'Raleway', sans-serif;
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #18181B;
        }
        .skill-card p {
            color: #52525B;
        }
        .skill-percentage {
            font-size: 2rem;
            font-weight: 700;
            color: #6366F1;
            margin-top: 15px;
        }
        
        /* Testimonials */
        .testimonials {
            padding: 100px 60px;
            background: white;
        }
        .testimonials-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }
        .testimonial-card {
            background: #F5F3FF;
            padding: 40px;
            border-radius: 20px;
            position: relative;
        }
        .quote-icon {
            font-size: 3rem;
            color: #6366F1;
            opacity: 0.3;
            margin-bottom: 20px;
        }
        .testimonial-text {
            font-size: 1.125rem;
            color: #18181B;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .author-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366F1 0%, #EC4899 100%);
        }
        .author-info h4 {
            font-family: 'Raleway', sans-serif;
            color: #18181B;
            margin-bottom: 5px;
        }
        .author-info p {
            color: #52525B;
            font-size: 0.875rem;
        }
        
        /* CTA Section */
        .cta {
            padding: 100px 60px;
            background: linear-gradient(135deg, #6366F1 0%, #EC4899 100%);
            text-align: center;
            color: white;
        }
        .cta h2 {
            font-family: 'Raleway', sans-serif;
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 30px;
        }
        .cta p {
            font-size: 1.25rem;
            margin-bottom: 50px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            opacity: 0.95;
        }
        .cta .btn-primary {
            background: white;
            color: #6366F1;
        }
        .cta .btn-primary:hover {
            background: #F5F3FF;
        }
        
        /* Contact Section */
        .contact {
            padding: 100px 60px;
            background: white;
        }
        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 80px;
            margin-top: 60px;
        }
        .contact-info h3 {
            font-family: 'Raleway', sans-serif;
            font-size: 2rem;
            margin-bottom: 30px;
            color: #18181B;
        }
        .contact-item {
            display: flex;
            align-items: start;
            gap: 20px;
            margin-bottom: 30px;
        }
        .contact-icon {
            width: 50px;
            height: 50px;
            background: #F5F3FF;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6366F1;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        .contact-item-content h4 {
            color: #18181B;
            margin-bottom: 5px;
        }
        .contact-item-content p {
            color: #52525B;
        }
        .contact-form {
            background: #F5F3FF;
            padding: 50px;
            border-radius: 20px;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #18181B;
            font-weight: 600;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid transparent;
            border-radius: 10px;
            font-family: 'Work Sans', sans-serif;
            font-size: 1rem;
            transition: border-color 0.3s;
            background: white;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #6366F1;
        }
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        /* Footer */
        .footer {
            background: #18181B;
            color: white;
            padding: 60px 60px 30px;
        }
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 60px;
            margin-bottom: 40px;
        }
        .footer-about h3 {
            font-family: 'Raleway', sans-serif;
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #6366F1;
        }
        .footer-about p {
            color: #A1A1AA;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        .social-links {
            display: flex;
            gap: 15px;
        }
        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(99, 102, 241, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6366F1;
            text-decoration: none;
            transition: all 0.3s;
        }
        .social-links a:hover {
            background: #6366F1;
            color: white;
            transform: translateY(-3px);
        }
        .footer-section h4 {
            font-family: 'Raleway', sans-serif;
            margin-bottom: 20px;
        }
        .footer-section ul {
            list-style: none;
        }
        .footer-section ul li {
            margin-bottom: 12px;
        }
        .footer-section ul li a {
            color: #A1A1AA;
            text-decoration: none;
            transition: color 0.3s;
        }
        .footer-section ul li a:hover {
            color: #6366F1;
        }
        .footer-bottom {
            max-width: 1200px;
            margin: 0 auto;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            color: #A1A1AA;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .hero { grid-template-columns: 1fr; text-align: center; }
            .hero-content { max-width: 100%; }
            .hero-buttons { justify-content: center; }
            .hero-image { margin-top: 60px; }
            .masonry-grid { grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); }
            .portfolio-item.wide { grid-column: span 1; }
            .contact-container { grid-template-columns: 1fr; }
            .footer-content { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .navbar { padding: 15px 30px; }
            .nav-links { display: none; }
            .hero { padding: 100px 30px 50px; }
            .hero h1 { font-size: 2.5rem; }
            .portfolio, .skills, .testimonials, .contact, .cta { padding: 60px 30px; }
            .section-header h2 { font-size: 2rem; }
            .masonry-grid { grid-template-columns: 1fr; }
            .portfolio-item.tall { grid-row: span 1; }
            .skills-container, .testimonials-grid { grid-template-columns: 1fr; }
            .footer { padding: 40px 30px 20px; }
            .footer-content { grid-template-columns: 1fr; }
            .cta h2 { font-size: 2rem; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <a href="#" class="logo">{{ $website->business->name ?? 'Portfolio' }}</a>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#portfolio">Portfolio</a></li>
                <li><a href="#skills">Skills</a></li>
                <li><a href="#testimonials">Testimonials</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero" id="home">
        <div class="hero-content">
            <span class="hero-label">Creative Professional</span>
            <h1>
                Crafting <span class="gradient-text">Beautiful</span> Digital Experiences
            </h1>
            <p>Award-winning designer & developer specializing in creating stunning visual identities and interactive experiences that captivate audiences.</p>
            <div class="hero-buttons">
                <a href="#portfolio" class="btn btn-primary">View My Work</a>
                <a href="#contact" class="btn btn-secondary">Get In Touch</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://images.unsplash.com/photo-1542744173-8e7e53415bb0?w=800" alt="Creative workspace">
        </div>
    </section>

    <!-- Portfolio - Masonry Grid -->
    <section class="portfolio" id="portfolio">
        <div class="section-header">
            <div class="section-label">My Work</div>
            <h2>Featured Projects</h2>
            <p>A selection of my recent work showcasing creativity, innovation, and attention to detail</p>
        </div>
        
        <div class="masonry-grid">
            <div class="portfolio-item" data-lightbox="0">
                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600" alt="Project 1">
                <div class="portfolio-overlay">
                    <span class="portfolio-category">Branding</span>
                    <h3>Tech Startup Identity</h3>
                    <p>Complete brand identity design for a cutting-edge SaaS company</p>
                </div>
            </div>
            
            <div class="portfolio-item tall" data-lightbox="1">
                <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=600&h=900" alt="Project 2">
                <div class="portfolio-overlay">
                    <span class="portfolio-category">Web Design</span>
                    <h3>E-commerce Platform</h3>
                    <p>Modern, user-friendly online shopping experience</p>
                </div>
            </div>
            
            <div class="portfolio-item" data-lightbox="2">
                <img src="https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600" alt="Project 3">
                <div class="portfolio-overlay">
                    <span class="portfolio-category">UI/UX</span>
                    <h3>Mobile App Design</h3>
                    <p>Intuitive fitness tracking application</p>
                </div>
            </div>
            
            <div class="portfolio-item wide" data-lightbox="3">
                <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?w=900&h=600" alt="Project 4">
                <div class="portfolio-overlay">
                    <span class="portfolio-category">Photography</span>
                    <h3>Corporate Event Coverage</h3>
                    <p>Professional photography for tech conference</p>
                </div>
            </div>
            
            <div class="portfolio-item" data-lightbox="4">
                <img src="https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=600" alt="Project 5">
                <div class="portfolio-overlay">
                    <span class="portfolio-category">Illustration</span>
                    <h3>Digital Illustrations</h3>
                    <p>Custom artwork for editorial content</p>
                </div>
            </div>
            
            <div class="portfolio-item tall" data-lightbox="5">
                <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=600&h=900" alt="Project 6">
                <div class="portfolio-overlay">
                    <span class="portfolio-category">Development</span>
                    <h3>Dashboard Interface</h3>
                    <p>Analytics platform with real-time data visualization</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Skills -->
    <section class="skills" id="skills">
        <div class="section-header">
            <div class="section-label">What I Do</div>
            <h2>My Expertise</h2>
            <p>Combining technical skills with creative vision to deliver exceptional results</p>
        </div>
        
        <div class="skills-container">
            <div class="skill-card">
                <div class="skill-icon"><i class="fas fa-palette"></i></div>
                <h3>UI/UX Design</h3>
                <p>Creating intuitive and beautiful user interfaces that delight users</p>
                <div class="skill-percentage">95%</div>
            </div>
            
            <div class="skill-card">
                <div class="skill-icon"><i class="fas fa-code"></i></div>
                <h3>Web Development</h3>
                <p>Building responsive websites with modern technologies</p>
                <div class="skill-percentage">90%</div>
            </div>
            
            <div class="skill-card">
                <div class="skill-icon"><i class="fas fa-paint-brush"></i></div>
                <h3>Branding</h3>
                <p>Developing cohesive brand identities that resonate</p>
                <div class="skill-percentage">88%</div>
            </div>
            
            <div class="skill-card">
                <div class="skill-icon"><i class="fas fa-camera"></i></div>
                <h3>Photography</h3>
                <p>Capturing compelling visuals for digital and print media</p>
                <div class="skill-percentage">85%</div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials" id="testimonials">
        <div class="section-header">
            <div class="section-label">Client Feedback</div>
            <h2>What Clients Say</h2>
            <p>Don't just take my word for it - hear from satisfied clients</p>
        </div>
        
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
                <p class="testimonial-text">"Exceptional work! The design exceeded our expectations and perfectly captured our brand vision. Professional, creative, and a pleasure to work with."</p>
                <div class="testimonial-author">
                    <div class="author-avatar"></div>
                    <div class="author-info">
                        <h4>Sarah Johnson</h4>
                        <p>CEO, TechVision Inc.</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
                <p class="testimonial-text">"Outstanding attention to detail and creative problem-solving. Delivered a stunning website that has significantly improved our user engagement."</p>
                <div class="testimonial-author">
                    <div class="author-avatar"></div>
                    <div class="author-info">
                        <h4>Michael Chen</h4>
                        <p>Founder, StartupHub</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="quote-icon"><i class="fas fa-quote-left"></i></div>
                <p class="testimonial-text">"A true professional who understands both design and business needs. Our new brand identity has helped us stand out in a crowded market."</p>
                <div class="testimonial-author">
                    <div class="author-avatar"></div>
                    <div class="author-info">
                        <h4>Emily Rodriguez</h4>
                        <p>Marketing Director, GrowthCo</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta">
        <h2>Let's Create Something Amazing</h2>
        <p>Have a project in mind? I'd love to hear about it and discuss how we can bring your vision to life.</p>
        <a href="#contact" class="btn btn-primary">Start a Project</a>
    </section>

    <!-- Contact -->
    <section class="contact" id="contact">
        <div class="section-header">
            <div class="section-label">Get In Touch</div>
            <h2>Let's Talk</h2>
            <p>Feel free to reach out for collaborations or just a friendly hello</p>
        </div>
        
        <div class="contact-container">
            <div class="contact-info">
                <h3>Contact Information</h3>
                
                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                    <div class="contact-item-content">
                        <h4>Email</h4>
                        <p>{{ $website->business->email ?? 'hello@portfolio.com' }}</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-phone"></i></div>
                    <div class="contact-item-content">
                        <h4>Phone</h4>
                        <p>{{ $website->business->phone ?? '+1 (555) 123-4567' }}</p>
                    </div>
                </div>
                
                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div class="contact-item-content">
                        <h4>Location</h4>
                        <p>{{ $website->business->address ?? 'New York, NY' }}</p>
                    </div>
                </div>
            </div>
            
            <form class="contact-form">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" placeholder="Your name">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" placeholder="your@email.com">
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea placeholder="Tell me about your project..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-about">
                <h3>{{ $website->business->name ?? 'Portfolio' }}</h3>
                <p>Creative professional passionate about design, development, and crafting exceptional digital experiences that make a difference.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-dribbble"></i></a>
                    <a href="#"><i class="fab fa-behance"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#portfolio">Portfolio</a></li>
                    <li><a href="#skills">Skills</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Services</h4>
                <ul>
                    <li><a href="#">Web Design</a></li>
                    <li><a href="#">Branding</a></li>
                    <li><a href="#">Photography</a></li>
                    <li><a href="#">Consulting</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Resources</h4>
                <ul>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Case Studies</a></li>
                    <li><a href="#">Downloads</a></li>
                    <li><a href="#">Newsletter</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} {{ $website->business->name ?? 'Portfolio' }}. All rights reserved.</p>
        </div>
    </footer>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox">
        <div class="lightbox-content">
            <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
            <button class="lightbox-nav lightbox-prev" onclick="navigateLightbox(-1)">&#10094;</button>
            <img src="" alt="Portfolio item" id="lightbox-img">
            <button class="lightbox-nav lightbox-next" onclick="navigateLightbox(1)">&#10095;</button>
        </div>
    </div>

    <script>
        // Lightbox functionality
        let currentLightboxIndex = 0;
        const portfolioImages = document.querySelectorAll('.portfolio-item');
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');

        portfolioImages.forEach((item, index) => {
            item.addEventListener('click', () => {
                currentLightboxIndex = index;
                openLightbox();
            });
        });

        function openLightbox() {
            const img = portfolioImages[currentLightboxIndex].querySelector('img');
            lightboxImg.src = img.src;
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox() {
            lightbox.classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        function navigateLightbox(direction) {
            currentLightboxIndex += direction;
            if (currentLightboxIndex < 0) currentLightboxIndex = portfolioImages.length - 1;
            if (currentLightboxIndex >= portfolioImages.length) currentLightboxIndex = 0;
            openLightbox();
        }

        // Close lightbox on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft') navigateLightbox(-1);
            if (e.key === 'ArrowRight') navigateLightbox(1);
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>

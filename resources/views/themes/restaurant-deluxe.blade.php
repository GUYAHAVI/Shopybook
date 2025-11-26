{{-- Restaurant Deluxe Theme - Full-screen slider with menu showcase --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $website->business->name ?? 'Restaurant Deluxe' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Raleway:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Raleway', sans-serif;
            color: #2C2C2C;
        }
        
        h1, h2, h3 {
            font-family: 'Playfair Display', serif;
        }
        
        /* Full Screen Hero Slider */
        .hero-slider {
            height: 100vh;
            position: relative;
            overflow: hidden;
        }
        .slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s;
        }
        .slide.active {
            opacity: 1;
        }
        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .slide-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.7) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .slide-content {
            text-align: center;
            color: white;
            max-width: 900px;
            padding: 0 30px;
        }
        .slide-content h1 {
            font-size: 6rem;
            margin-bottom: 25px;
            font-weight: 900;
            line-height: 1;
            letter-spacing: 2px;
        }
        .slide-content p {
            font-size: 1.5rem;
            margin-bottom: 40px;
            font-weight: 300;
            letter-spacing: 1px;
        }
        
        /* Navigation - Transparent */
        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            padding: 30px 80px;
            z-index: 1000;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            transition: all 0.3s;
        }
        .nav.scrolled {
            background: rgba(0, 0, 0, 0.95);
            padding: 20px 80px;
        }
        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1600px;
            margin: 0 auto;
        }
        .nav-logo {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            color: white;
            font-weight: 900;
        }
        .nav-menu {
            display: flex;
            gap: 50px;
            list-style: none;
        }
        .nav-menu a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.875rem;
            transition: color 0.3s;
        }
        .nav-menu a:hover {
            color: #D4AF37;
        }
        
        .btn-reserve {
            padding: 15px 40px;
            background: #D4AF37;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.875rem;
            transition: all 0.3s;
            display: inline-block;
        }
        .btn-reserve:hover {
            background: #B8941F;
            transform: scale(1.05);
        }
        
        /* Slider Controls */
        .slider-nav {
            position: absolute;
            bottom: 50px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 15px;
            z-index: 100;
        }
        .slider-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: all 0.3s;
        }
        .slider-dot.active {
            background: #D4AF37;
            transform: scale(1.3);
        }
        
        /* About Section */
        .about {
            padding: 150px 80px;
            background: #F9F7F3;
        }
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 100px;
            max-width: 1600px;
            margin: 0 auto;
            align-items: center;
        }
        .about-content h2 {
            font-size: 4rem;
            margin-bottom: 30px;
            color: #2C2C2C;
            line-height: 1.1;
        }
        .about-content .subtitle {
            color: #D4AF37;
            font-size: 1.125rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 20px;
            font-weight: 700;
        }
        .about-content p {
            font-size: 1.125rem;
            line-height: 1.9;
            color: #666;
            margin-bottom: 25px;
        }
        .about-image {
            height: 600px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.2);
        }
        .about-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Menu Section */
        .menu {
            padding: 150px 80px;
            background: white;
        }
        .menu-header {
            text-align: center;
            margin-bottom: 100px;
        }
        .menu-header .subtitle {
            color: #D4AF37;
            font-size: 1.125rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 20px;
            font-weight: 700;
        }
        .menu-header h2 {
            font-size: 5rem;
            color: #2C2C2C;
            margin-bottom: 25px;
        }
        .menu-header p {
            font-size: 1.25rem;
            color: #666;
            max-width: 700px;
            margin: 0 auto;
        }
        .menu-categories {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 80px;
            flex-wrap: wrap;
        }
        .category-btn {
            padding: 12px 30px;
            background: transparent;
            border: 2px solid #D4AF37;
            color: #D4AF37;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.875rem;
        }
        .category-btn:hover,
        .category-btn.active {
            background: #D4AF37;
            color: white;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 60px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .menu-item {
            display: flex;
            gap: 30px;
            padding-bottom: 40px;
            border-bottom: 1px solid #E0E0E0;
        }
        .menu-item-image {
            width: 150px;
            height: 150px;
            border-radius: 15px;
            overflow: hidden;
            flex-shrink: 0;
        }
        .menu-item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .menu-item-content {
            flex: 1;
        }
        .menu-item-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 15px;
        }
        .menu-item-header h3 {
            font-size: 1.75rem;
            color: #2C2C2C;
        }
        .menu-item-price {
            font-size: 1.5rem;
            color: #D4AF37;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
        }
        .menu-item-description {
            color: #666;
            line-height: 1.7;
            font-size: 1.0rem;
        }
        
        /* Chef Section */
        .chef {
            padding: 150px 80px;
            background: #1A1A1A;
            color: white;
        }
        .chef-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 100px;
            max-width: 1600px;
            margin: 0 auto;
            align-items: center;
        }
        .chef-image {
            height: 700px;
            border-radius: 20px;
            overflow: hidden;
        }
        .chef-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .chef-content .subtitle {
            color: #D4AF37;
            font-size: 1.125rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 20px;
            font-weight: 700;
        }
        .chef-content h2 {
            font-size: 4rem;
            margin-bottom: 30px;
            line-height: 1.1;
        }
        .chef-content p {
            font-size: 1.125rem;
            line-height: 1.9;
            color: #CCC;
            margin-bottom: 25px;
        }
        
        /* Reservation */
        .reservation {
            padding: 150px 80px;
            background: url('/webbuilder/pexels-goumbik-669610.jpg') center/cover fixed;
            position: relative;
        }
        .reservation-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.85);
        }
        .reservation-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
            color: white;
        }
        .reservation-content .subtitle {
            color: #D4AF37;
            font-size: 1.125rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 20px;
            font-weight: 700;
        }
        .reservation-content h2 {
            font-size: 5rem;
            margin-bottom: 30px;
        }
        .reservation-content p {
            font-size: 1.25rem;
            margin-bottom: 50px;
            color: #CCC;
        }
        
        /* Footer */
        .footer {
            background: #0A0A0A;
            color: white;
            padding: 100px 80px 50px;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 80px;
            max-width: 1600px;
            margin: 0 auto 60px;
        }
        .footer h3 {
            font-size: 2rem;
            margin-bottom: 25px;
            color: #D4AF37;
        }
        .footer p {
            color: #999;
            line-height: 1.8;
            margin-bottom: 30px;
        }
        .footer ul {
            list-style: none;
        }
        .footer li {
            margin-bottom: 15px;
        }
        .footer a {
            color: #999;
            text-decoration: none;
            transition: color 0.3s;
        }
        .footer a:hover {
            color: #D4AF37;
        }
        .footer-hours {
            color: #999;
            line-height: 2;
        }
        .footer-hours strong {
            color: white;
        }
        .footer-bottom {
            padding-top: 40px;
            border-top: 1px solid #2C2C2C;
            text-align: center;
            color: #666;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .slide-content h1 { font-size: 3rem; }
            .nav, .about, .menu, .chef, .reservation, .footer { padding: 80px 30px; }
            .about-grid, .chef-grid, .menu-grid { grid-template-columns: 1fr; }
            .chef-grid { grid-template-rows: auto auto; }
            .chef-image { order: -1; }
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="nav">
        <div class="nav-content">
            <div class="nav-logo">{{ $website->business->name ?? 'Deluxe' }}</div>
            <ul class="nav-menu">
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#menu">Menu</a></li>
                <li><a href="#chef">Chef</a></li>
            </ul>
            <a href="#reservation" class="btn-reserve">Reserve Table</a>
        </div>
    </nav>

    <!-- Hero Slider -->
    <section class="hero-slider">
        <div class="slide active">
            <img src="/webbuilder/composition-beauty-industry-products-women.jpg" alt="Dining Experience">
            <div class="slide-overlay">
                <div class="slide-content">
                    <h1>Exquisite Dining</h1>
                    <p>Experience culinary artistry in every dish</p>
                    <a href="#menu" class="btn-reserve">View Menu</a>
                </div>
            </div>
        </div>
        <div class="slide">
            <img src="/webbuilder/happy-black-woman-holding-skincare-product.jpg" alt="Fresh Ingredients">
            <div class="slide-overlay">
                <div class="slide-content">
                    <h1>Fresh & Premium</h1>
                    <p>Only the finest ingredients in every meal</p>
                    <a href="#menu" class="btn-reserve">View Menu</a>
                </div>
            </div>
        </div>
        <div class="slide">
            <img src="/webbuilder/portrait-young-beautiful-woman-with-beauty-product.jpg" alt="Elegant Ambiance">
            <div class="slide-overlay">
                <div class="slide-content">
                    <h1>Elegant Ambiance</h1>
                    <p>Perfect setting for memorable moments</p>
                    <a href="#reservation" class="btn-reserve">Book Now</a>
                </div>
            </div>
        </div>
        <div class="slider-nav">
            <div class="slider-dot active"></div>
            <div class="slider-dot"></div>
            <div class="slider-dot"></div>
        </div>
    </section>

    <!-- About -->
    <section class="about">
        <div class="about-grid">
            <div class="about-content">
                <div class="subtitle">Our Story</div>
                <h2>A Journey of Flavors</h2>
                <p>Since opening our doors in 2010, we've been dedicated to creating extraordinary dining experiences that celebrate the finest ingredients and culinary traditions from around the world.</p>
                <p>Our passion for excellence drives everything we do, from sourcing premium ingredients to crafting innovative dishes that delight and inspire. Each meal is a carefully orchestrated symphony of flavors, textures, and presentations.</p>
                <p>We believe dining is not just about food—it's about creating memories, celebrating moments, and bringing people together in an atmosphere of warmth and elegance.</p>
                <a href="#reservation" class="btn-reserve">Reserve Your Table</a>
            </div>
            <div class="about-image">
                <img src="/webbuilder/colleagues-reviewing-plans-tablet.jpg" alt="Restaurant Interior">
            </div>
        </div>
    </section>

    <!-- Menu -->
    <section class="menu">
        <div class="menu-header">
            <div class="subtitle">Culinary Excellence</div>
            <h2>Our Menu</h2>
            <p>Discover our carefully curated selection of signature dishes, each prepared with artistry and passion</p>
        </div>
        
        <div class="menu-categories">
            <button class="category-btn active">Appetizers</button>
            <button class="category-btn">Main Course</button>
            <button class="category-btn">Desserts</button>
            <button class="category-btn">Beverages</button>
        </div>
        
        <div class="menu-grid">
            <div class="menu-item">
                <div class="menu-item-image">
                    <img src="/webbuilder/composition-beauty-industry-products-women.jpg" alt="Menu Item">
                </div>
                <div class="menu-item-content">
                    <div class="menu-item-header">
                        <h3>Truffle Carpaccio</h3>
                        <div class="menu-item-price">$28</div>
                    </div>
                    <p class="menu-item-description">Thinly sliced beef with black truffle, arugula, and aged parmesan</p>
                </div>
            </div>
            
            <div class="menu-item">
                <div class="menu-item-image">
                    <img src="/webbuilder/medium-shot-man-looking-jewelry.jpg" alt="Menu Item">
                </div>
                <div class="menu-item-content">
                    <div class="menu-item-header">
                        <h3>Seafood Tower</h3>
                        <div class="menu-item-price">$85</div>
                    </div>
                    <p class="menu-item-description">Assorted fresh oysters, lobster, crab, and prawns on ice</p>
                </div>
            </div>
            
            <div class="menu-item">
                <div class="menu-item-image">
                    <img src="/webbuilder/happy-black-woman-holding-skincare-product.jpg" alt="Menu Item">
                </div>
                <div class="menu-item-content">
                    <div class="menu-item-header">
                        <h3>Burrata Salad</h3>
                        <div class="menu-item-price">$22</div>
                    </div>
                    <p class="menu-item-description">Creamy burrata with heirloom tomatoes, basil, and balsamic reduction</p>
                </div>
            </div>
            
            <div class="menu-item">
                <div class="menu-item-image">
                    <img src="/webbuilder/portrait-young-beautiful-woman-with-beauty-product.jpg" alt="Menu Item">
                </div>
                <div class="menu-item-content">
                    <div class="menu-item-header">
                        <h3>Foie Gras</h3>
                        <div class="menu-item-price">$35</div>
                    </div>
                    <p class="menu-item-description">Pan-seared foie gras with fig compote and brioche toast</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Chef -->
    <section class="chef">
        <div class="chef-grid">
            <div class="chef-image">
                <img src="/webbuilder/young-businessmen-standing-together-holding-laptop-discussing-business.jpg" alt="Head Chef">
            </div>
            <div class="chef-content">
                <div class="subtitle">Master Chef</div>
                <h2>Meet Chef Alexander</h2>
                <p>With over 20 years of experience in Michelin-starred restaurants across Europe and Asia, Chef Alexander brings a unique blend of classical techniques and innovative approaches to every dish.</p>
                <p>Trained in Paris and Tokyo, his philosophy centers on respecting ingredients, honoring traditions, and pushing culinary boundaries to create unforgettable dining experiences.</p>
                <p>"Cooking is not just a profession—it's a passion, an art form, and a way to bring joy to people's lives. Every plate that leaves our kitchen tells a story."</p>
            </div>
        </div>
    </section>

    <!-- Reservation CTA -->
    <section class="reservation">
        <div class="reservation-overlay"></div>
        <div class="reservation-content">
            <div class="subtitle">Book Your Experience</div>
            <h2>Reserve a Table</h2>
            <p>Join us for an unforgettable dining experience. Book your table today.</p>
            <a href="#contact" class="btn-reserve">Make Reservation</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-grid">
            <div>
                <h3>{{ $website->business->name ?? 'Restaurant Deluxe' }}</h3>
                <p>Experience the finest in culinary excellence. Our commitment to quality, service, and innovation has made us a destination for food lovers.</p>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 20px;">Quick Links</h4>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Menu</a></li>
                    <li><a href="#">Reservations</a></li>
                    <li><a href="#">Private Events</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 20px;">Hours</h4>
                <div class="footer-hours">
                    <strong>Lunch</strong><br>
                    Mon-Fri: 12pm - 3pm<br><br>
                    <strong>Dinner</strong><br>
                    Mon-Sun: 6pm - 11pm
                </div>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 20px;">Contact</h4>
                <ul>
                    <li>123 Gourmet Street</li>
                    <li>Downtown District</li>
                    <li>City, State 12345</li>
                    <li style="margin-top: 20px;"><a href="tel:+15551234567">+1 (555) 123-4567</a></li>
                    <li><a href="mailto:info@restaurant.com">info@restaurant.com</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 {{ $website->business->name ?? 'Restaurant Deluxe' }}. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Slider functionality
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.slider-dot');
        
        function showSlide(n) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            currentSlide = (n + slides.length) % slides.length;
            slides[currentSlide].classList.add('active');
            dots[currentSlide].classList.add('active');
        }
        
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => showSlide(index));
        });
        
        setInterval(() => showSlide(currentSlide + 1), 5000);
        
        // Nav scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('.nav');
            if (window.scrollY > 100) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>

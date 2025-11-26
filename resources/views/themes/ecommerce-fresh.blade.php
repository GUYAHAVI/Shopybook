{{-- E-Commerce Fresh Theme - Product showcase with shopping features --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $website->business->name ?? 'E-Commerce Fresh' }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Montserrat', sans-serif;
            color: #333;
            background: #FFFFFF;
        }
        
        /* Top Announcement Bar */
        .announcement {
            background: #FF6B6B;
            color: white;
            text-align: center;
            padding: 12px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        /* Sticky Header */
        .header {
            background: white;
            border-bottom: 1px solid #E5E5E5;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .header-top {
            padding: 20px 80px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 2rem;
            font-weight: 900;
            color: #FF6B6B;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .search-bar {
            flex: 1;
            max-width: 600px;
            margin: 0 60px;
            position: relative;
        }
        .search-bar input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 2px solid #E5E5E5;
            border-radius: 50px;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.3s;
        }
        .search-bar input:focus {
            border-color: #FF6B6B;
        }
        .search-bar button {
            position: absolute;
            right: 5px;
            top: 5px;
            bottom: 5px;
            padding: 0 25px;
            background: #FF6B6B;
            border: none;
            border-radius: 50px;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
        }
        .search-bar button:hover {
            background: #FF5252;
        }
        .header-icons {
            display: flex;
            gap: 25px;
            align-items: center;
        }
        .header-icon {
            position: relative;
            cursor: pointer;
            font-size: 1.5rem;
            color: #333;
            transition: color 0.3s;
        }
        .header-icon:hover {
            color: #FF6B6B;
        }
        .badge {
            position: absolute;
            top: -8px;
            right: -10px;
            background: #FF6B6B;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .nav-menu {
            padding: 0 80px;
            display: flex;
            gap: 50px;
            list-style: none;
            border-top: 1px solid #E5E5E5;
        }
        .nav-menu a {
            display: block;
            padding: 20px 0;
            color: #333;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: color 0.3s;
            position: relative;
        }
        .nav-menu a:hover {
            color: #FF6B6B;
        }
        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 0;
            height: 3px;
            background: #FF6B6B;
            transition: width 0.3s;
        }
        .nav-menu a:hover::after {
            width: 100%;
        }
        
        /* Hero Carousel */
        .hero-carousel {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            padding: 20px 80px;
            max-width: 1800px;
            margin: 0 auto;
        }
        .hero-main {
            height: 500px;
            background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            display: flex;
            align-items: center;
            padding: 0 80px;
        }
        .hero-main-image {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 50%;
            object-fit: cover;
            opacity: 0.3;
        }
        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
            max-width: 500px;
        }
        .hero-content .badge-text {
            background: rgba(255,255,255,0.2);
            display: inline-block;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            font-weight: 900;
            line-height: 1.1;
        }
        .hero-content p {
            font-size: 1.125rem;
            margin-bottom: 30px;
        }
        .btn-shop {
            padding: 18px 45px;
            background: white;
            color: #FF6B6B;
            text-decoration: none;
            border-radius: 50px;
            font-weight: 700;
            display: inline-block;
            transition: all 0.3s;
            text-transform: uppercase;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }
        .btn-shop:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .hero-side {
            display: grid;
            grid-template-rows: 1fr 1fr;
            gap: 20px;
        }
        .hero-card {
            background: #F8F9FA;
            border-radius: 20px;
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: transform 0.3s;
        }
        .hero-card:hover {
            transform: scale(1.02);
        }
        .hero-card-content h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .hero-card-content p {
            color: #666;
            margin-bottom: 15px;
        }
        .hero-card-image {
            width: 120px;
            height: 120px;
            border-radius: 15px;
            overflow: hidden;
        }
        .hero-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Category Tiles */
        .categories {
            padding: 80px 80px;
            max-width: 1800px;
            margin: 0 auto;
        }
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }
        .section-header h2 {
            font-size: 3rem;
            margin-bottom: 15px;
            font-weight: 900;
        }
        .section-header p {
            color: #666;
            font-size: 1.125rem;
        }
        .category-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
        }
        .category-card {
            background: #F8F9FA;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .category-card:hover {
            background: #FF6B6B;
            color: white;
            transform: translateY(-5px);
        }
        .category-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        .category-card h3 {
            font-size: 1.25rem;
            margin-bottom: 10px;
        }
        .category-card p {
            font-size: 0.875rem;
            opacity: 0.8;
        }
        
        /* Product Grid */
        .products {
            padding: 80px 80px;
            background: #F8F9FA;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            max-width: 1800px;
            margin: 0 auto;
        }
        .product-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s;
            cursor: pointer;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .product-image {
            width: 100%;
            height: 300px;
            overflow: hidden;
            position: relative;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .product-card:hover .product-image img {
            transform: scale(1.1);
        }
        .product-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #FF6B6B;
            color: white;
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .product-info {
            padding: 25px;
        }
        .product-category {
            color: #999;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .product-title {
            font-size: 1.125rem;
            margin-bottom: 15px;
            font-weight: 700;
        }
        .product-rating {
            margin-bottom: 15px;
        }
        .stars {
            color: #FFB800;
            margin-right: 8px;
        }
        .rating-count {
            color: #999;
            font-size: 0.875rem;
        }
        .product-price {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .price-current {
            font-size: 1.75rem;
            font-weight: 900;
            color: #FF6B6B;
        }
        .price-old {
            font-size: 1.125rem;
            color: #999;
            text-decoration: line-through;
        }
        .btn-add-cart {
            width: 100%;
            padding: 12px;
            background: #FF6B6B;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.5px;
        }
        .btn-add-cart:hover {
            background: #FF5252;
        }
        
        /* Featured Banner */
        .featured-banner {
            margin: 80px 80px;
            height: 400px;
            background: linear-gradient(135deg, #4ECDC4 0%, #44A08D 100%);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 80px;
            position: relative;
            overflow: hidden;
        }
        .featured-banner-image {
            position: absolute;
            right: 100px;
            top: -50px;
            height: 500px;
            width: 500px;
            object-fit: cover;
            border-radius: 20px;
        }
        .featured-content {
            color: white;
            max-width: 500px;
            z-index: 2;
        }
        .featured-content h2 {
            font-size: 3.5rem;
            margin-bottom: 20px;
            font-weight: 900;
        }
        .featured-content p {
            font-size: 1.25rem;
            margin-bottom: 30px;
        }
        
        /* Footer */
        .footer {
            background: #1A1A1A;
            color: white;
            padding: 80px 80px 30px;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 60px;
            max-width: 1800px;
            margin: 0 auto 60px;
        }
        .footer h3 {
            font-size: 1.5rem;
            margin-bottom: 25px;
            color: #FF6B6B;
        }
        .footer p {
            color: #999;
            line-height: 1.8;
            margin-bottom: 25px;
        }
        .footer ul {
            list-style: none;
        }
        .footer li {
            margin-bottom: 12px;
        }
        .footer a {
            color: #999;
            text-decoration: none;
            transition: color 0.3s;
        }
        .footer a:hover {
            color: #FF6B6B;
        }
        .footer-bottom {
            padding-top: 30px;
            border-top: 1px solid #333;
            text-align: center;
            color: #666;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .header-top, .nav-menu, .hero-carousel, .categories, .products, .featured-banner, .footer {
                padding-left: 30px;
                padding-right: 30px;
            }
            .search-bar { display: none; }
            .hero-carousel, .category-grid, .product-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Announcement Bar -->
    <div class="announcement">
        <i class="fas fa-shipping-fast"></i> FREE SHIPPING ON ORDERS OVER $50 | USE CODE: FREESHIP
    </div>

    <!-- Header -->
    <header class="header">
        <div class="header-top">
            <div class="logo">{{ $website->business->name ?? 'SHOP' }}</div>
            <div class="search-bar">
                <input type="text" placeholder="Search for products...">
                <button><i class="fas fa-search"></i></button>
            </div>
            <div class="header-icons">
                <div class="header-icon">
                    <i class="far fa-user"></i>
                </div>
                <div class="header-icon">
                    <i class="far fa-heart"></i>
                    <span class="badge">3</span>
                </div>
                <div class="header-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="badge">2</span>
                </div>
            </div>
        </div>
        <nav>
            <ul class="nav-menu">
                <li><a href="#new">New Arrivals</a></li>
                <li><a href="#women">Women</a></li>
                <li><a href="#men">Men</a></li>
                <li><a href="#accessories">Accessories</a></li>
                <li><a href="#sale">Sale</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Carousel -->
    <section class="hero-carousel">
        <div class="hero-main">
            <img src="/webbuilder/beautiful-three-welldressed-afro-american-girls-customers-with-colored-shopping-bags-mobile-phone-shop-choosing-smartphone.jpg" alt="Shopping" class="hero-main-image">
            <div class="hero-content">
                <div class="badge-text">NEW COLLECTION</div>
                <h1>Spring 2025 Arrivals</h1>
                <p>Discover the latest trends in fashion and style</p>
                <a href="#products" class="btn-shop">Shop Now</a>
            </div>
        </div>
        <div class="hero-side">
            <div class="hero-card">
                <div class="hero-card-content">
                    <h3>Beauty Essentials</h3>
                    <p>Premium skincare</p>
                    <a href="#" style="color: #FF6B6B; font-weight: 700;">Explore →</a>
                </div>
                <div class="hero-card-image">
                    <img src="/webbuilder/composition-beauty-industry-products-women.jpg" alt="Beauty">
                </div>
            </div>
            <div class="hero-card">
                <div class="hero-card-content">
                    <h3>Luxury Accessories</h3>
                    <p>Elegant jewelry</p>
                    <a href="#" style="color: #FF6B6B; font-weight: 700;">Browse →</a>
                </div>
                <div class="hero-card-image">
                    <img src="/webbuilder/medium-shot-man-looking-jewelry.jpg" alt="Jewelry">
                </div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="categories">
        <div class="section-header">
            <h2>Shop By Category</h2>
            <p>Find exactly what you're looking for</p>
        </div>
        <div class="category-grid">
            <div class="category-card">
                <div class="category-icon"><i class="fas fa-tshirt"></i></div>
                <h3>Clothing</h3>
                <p>Latest fashion trends</p>
            </div>
            <div class="category-card">
                <div class="category-icon"><i class="fas fa-gem"></i></div>
                <h3>Jewelry</h3>
                <p>Elegant accessories</p>
            </div>
            <div class="category-card">
                <div class="category-icon"><i class="fas fa-shoe-prints"></i></div>
                <h3>Footwear</h3>
                <p>Comfort meets style</p>
            </div>
            <div class="category-card">
                <div class="category-icon"><i class="fas fa-shopping-bag"></i></div>
                <h3>Bags</h3>
                <p>Designer collection</p>
            </div>
        </div>
    </section>

    <!-- Products -->
    <section class="products">
        <div class="section-header">
            <h2>Trending Products</h2>
            <p>Our most popular items right now</p>
        </div>
        <div class="product-grid">
            <div class="product-card">
                <div class="product-image">
                    <span class="product-badge">NEW</span>
                    <img src="/webbuilder/composition-beauty-industry-products-women.jpg" alt="Product">
                </div>
                <div class="product-info">
                    <div class="product-category">Beauty</div>
                    <h3 class="product-title">Premium Skincare Set</h3>
                    <div class="product-rating">
                        <span class="stars">★★★★★</span>
                        <span class="rating-count">(152)</span>
                    </div>
                    <div class="product-price">
                        <span class="price-current">$89.99</span>
                        <span class="price-old">$129.99</span>
                    </div>
                    <button class="btn-add-cart">Add to Cart</button>
                </div>
            </div>
            
            <div class="product-card">
                <div class="product-image">
                    <span class="product-badge">SALE</span>
                    <img src="/webbuilder/medium-shot-man-looking-jewelry.jpg" alt="Product">
                </div>
                <div class="product-info">
                    <div class="product-category">Jewelry</div>
                    <h3 class="product-title">Gold Necklace</h3>
                    <div class="product-rating">
                        <span class="stars">★★★★☆</span>
                        <span class="rating-count">(89)</span>
                    </div>
                    <div class="product-price">
                        <span class="price-current">$249.99</span>
                        <span class="price-old">$349.99</span>
                    </div>
                    <button class="btn-add-cart">Add to Cart</button>
                </div>
            </div>
            
            <div class="product-card">
                <div class="product-image">
                    <img src="/webbuilder/happy-black-woman-holding-skincare-product.jpg" alt="Product">
                </div>
                <div class="product-info">
                    <div class="product-category">Skincare</div>
                    <h3 class="product-title">Facial Serum</h3>
                    <div class="product-rating">
                        <span class="stars">★★★★★</span>
                        <span class="rating-count">(203)</span>
                    </div>
                    <div class="product-price">
                        <span class="price-current">$54.99</span>
                    </div>
                    <button class="btn-add-cart">Add to Cart</button>
                </div>
            </div>
            
            <div class="product-card">
                <div class="product-image">
                    <span class="product-badge">HOT</span>
                    <img src="/webbuilder/portrait-young-beautiful-woman-with-beauty-product.jpg" alt="Product">
                </div>
                <div class="product-info">
                    <div class="product-category">Beauty</div>
                    <h3 class="product-title">Beauty Bundle</h3>
                    <div class="product-rating">
                        <span class="stars">★★★★★</span>
                        <span class="rating-count">(321)</span>
                    </div>
                    <div class="product-price">
                        <span class="price-current">$129.99</span>
                        <span class="price-old">$199.99</span>
                    </div>
                    <button class="btn-add-cart">Add to Cart</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Banner -->
    <section class="featured-banner">
        <img src="/webbuilder/cosmetics1.jpeg" alt="Featured" class="featured-banner-image">
        <div class="featured-content">
            <h2>Summer Sale</h2>
            <p>Up to 50% off on selected items. Limited time offer!</p>
            <a href="#sale" class="btn-shop">Shop Sale</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-grid">
            <div>
                <h3>{{ $website->business->name ?? 'E-Commerce Fresh' }}</h3>
                <p>Your one-stop shop for fashion, beauty, and lifestyle products. Quality guaranteed, fast shipping, and exceptional customer service.</p>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 20px;">Shop</h4>
                <ul>
                    <li><a href="#">New Arrivals</a></li>
                    <li><a href="#">Best Sellers</a></li>
                    <li><a href="#">Sale</a></li>
                    <li><a href="#">Gift Cards</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 20px;">Help</h4>
                <ul>
                    <li><a href="#">Shipping Info</a></li>
                    <li><a href="#">Returns</a></li>
                    <li><a href="#">Size Guide</a></li>
                    <li><a href="#">FAQ</a></li>
                </ul>
            </div>
            <div>
                <h4 style="color: white; margin-bottom: 20px;">Connect</h4>
                <ul>
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Pinterest</a></li>
                    <li><a href="#">Twitter</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 {{ $website->business->name ?? 'E-Commerce Fresh' }}. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

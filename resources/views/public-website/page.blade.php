<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->getMetaTitle() }} - {{ $website->business->name }}</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $page->getMetaDescription() }}">
    @if($page->meta_keywords)
    <meta name="keywords" content="{{ $page->meta_keywords }}">
    @endif
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $page->url }}">
    <meta property="og:title" content="{{ $page->getMetaTitle() }}">
    <meta property="og:description" content="{{ $page->getMetaDescription() }}">
    @if($page->getOgImage())
    <meta property="og:image" content="{{ asset('storage/' . $page->getOgImage()) }}">
    @endif
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ $page->url }}">
    <meta property="twitter:title" content="{{ $page->getMetaTitle() }}">
    <meta property="twitter:description" content="{{ $page->getMetaDescription() }}">
    
    <!-- Favicon -->
    @if($website->favicon_path)
    <link rel="icon" href="{{ asset('storage/' . $website->favicon_path) }}">
    @endif
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts -->
    @php
    $fonts = $website->getFonts();
    @endphp
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $fonts['heading']) }}:wght@400;600;700&family={{ str_replace(' ', '+', $fonts['body']) }}:wght@400;500;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            @php
            $colors = $website->getColorScheme();
            @endphp
            --color-primary: {{ $colors['primary'] }};
            --color-secondary: {{ $colors['secondary'] }};
            --color-accent: {{ $colors['accent'] }};
            --color-background: {{ $colors['background'] }};
            --color-text: {{ $colors['text'] }};
            --font-heading: '{{ $fonts['heading'] }}', sans-serif;
            --font-body: '{{ $fonts['body'] }}', sans-serif;
        }
        
        body {
            font-family: var(--font-body);
            color: var(--color-text);
            background-color: var(--color-background);
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: var(--font-heading);
            font-weight: 700;
        }
        
        .btn-primary {
            background-color: var(--color-primary);
            color: white;
            padding: 12px 32px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        }
    </style>
    
    @if($isPreview)
    <style>
        body::before {
            content: "PREVIEW MODE";
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #f59e0b;
            color: white;
            text-align: center;
            padding: 8px;
            z-index: 10000;
            font-weight: bold;
        }
        body {
            padding-top: 40px;
        }
    </style>
    @endif
</head>
<body>
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <a href="{{ $website->url }}" class="flex items-center gap-3">
                    @if($website->logo_path || $website->business->logo_path)
                    <img src="{{ asset('storage/' . ($website->logo_path ?? $website->business->logo_path)) }}" 
                         alt="{{ $website->business->name }}" class="h-12">
                    @endif
                    <span class="text-2xl font-bold" style="color: var(--color-primary)">
                        {{ $website->business->name }}
                    </span>
                </a>
                
                <!-- Menu -->
                <div class="hidden md:flex items-center gap-6">
                    @foreach($menuPages as $menuPage)
                    <a href="{{ $menuPage->url }}" 
                       class="text-gray-700 hover:text-gray-900 font-medium transition-colors
                              {{ $page->id === $menuPage->id ? 'text-gray-900 border-b-2' : '' }}"
                       style="border-color: var(--color-primary)">
                        {{ $menuPage->title }}
                    </a>
                    @endforeach
                </div>
                
                <!-- Mobile Menu Button -->
                <button onclick="toggleMobileMenu()" class="md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden mt-4 pb-4 space-y-2">
                @foreach($menuPages as $menuPage)
                <a href="{{ $menuPage->url }}" 
                   class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">
                    {{ $menuPage->title }}
                </a>
                @endforeach
            </div>
        </div>
    </nav>

    <!-- Sections -->
    <main>
        @foreach($page->visibleSections as $section)
            @php
            $content = $section->getContentWithDefaults();
            $settings = $section->getSettingsWithDefaults();
            @endphp
            
            <section class="py-16 @if($section->background_type === 'color') bg-[{{ $section->background_value }}] @endif"
                     style="{{ $section->getBackgroundStyle() }}">
                <div class="container mx-auto px-4">
                    @if($section->type === 'hero')
                        <!-- Hero Section -->
                        <div class="text-center max-w-4xl mx-auto">
                            <h1 class="text-5xl md:text-6xl font-bold mb-6">
                                {{ $content['heading'] ?? 'Welcome' }}
                            </h1>
                            @if(isset($content['subheading']))
                            <p class="text-xl md:text-2xl text-gray-600 mb-8">
                                {{ $content['subheading'] }}
                            </p>
                            @endif
                            @if(isset($content['cta_text']))
                            <a href="{{ $content['cta_link'] ?? '#' }}" class="btn-primary">
                                {{ $content['cta_text'] }}
                            </a>
                            @endif
                        </div>
                    
                    @elseif($section->type === 'about')
                        <!-- About Section -->
                        <div class="grid md:grid-cols-2 gap-12 items-center">
                            <div>
                                <h2 class="text-4xl font-bold mb-6">{{ $content['heading'] ?? 'About Us' }}</h2>
                                <div class="text-lg text-gray-700 prose max-w-none">
                                    {!! nl2br(e($content['text'] ?? '')) !!}
                                </div>
                            </div>
                            @if(isset($content['image']) && $content['image'])
                            <div>
                                <img src="{{ asset('storage/' . $content['image']) }}" 
                                     alt="{{ $content['heading'] }}" 
                                     class="rounded-lg shadow-xl">
                            </div>
                            @endif
                        </div>
                    
                    @elseif($section->type === 'products' && $products->count())
                        <!-- Products Section -->
                        <div class="text-center mb-12">
                            <h2 class="text-4xl font-bold mb-4">{{ $content['heading'] ?? 'Our Products' }}</h2>
                            @if(isset($content['subheading']))
                            <p class="text-xl text-gray-600">{{ $content['subheading'] }}</p>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-8">
                            @foreach($products->take($content['max_products'] ?? 6) as $product)
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                                @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-48 object-cover">
                                @endif
                                <div class="p-4">
                                    <h3 class="font-bold text-lg mb-2">{{ $product->name }}</h3>
                                    @if($content['show_price'] ?? true)
                                    <p class="text-2xl font-bold" style="color: var(--color-primary)">
                                        KSh {{ number_format($product->price, 2) }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    
                    @elseif($section->type === 'contact')
                        <!-- Contact Section -->
                        <div class="max-w-2xl mx-auto">
                            <div class="text-center mb-12">
                                <h2 class="text-4xl font-bold mb-4">{{ $content['heading'] ?? 'Contact Us' }}</h2>
                                @if(isset($content['subheading']))
                                <p class="text-xl text-gray-600">{{ $content['subheading'] }}</p>
                                @endif
                            </div>
                            
                            @if($content['show_form'] ?? true)
                            <form action="{{ route('public.website.contact', $website->subdomain) }}" method="POST" class="space-y-6">
                                @csrf
                                <div>
                                    <input type="text" name="name" placeholder="Your Name" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                </div>
                                <div>
                                    <input type="email" name="email" placeholder="Your Email" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                </div>
                                <div>
                                    <input type="tel" name="phone" placeholder="Phone Number"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                                </div>
                                <div>
                                    <textarea name="message" rows="5" placeholder="Your Message" required
                                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"></textarea>
                                </div>
                                <button type="submit" class="w-full btn-primary">
                                    Send Message
                                </button>
                            </form>
                            @endif
                            
                            <!-- Contact Info -->
                            <div class="mt-12 text-center space-y-4">
                                @if(isset($content['email']))
                                <p class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $content['email'] }}
                                </p>
                                @endif
                                @if(isset($content['phone']))
                                <p class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    {{ $content['phone'] }}
                                </p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endforeach
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">{{ $website->business->name }}</h3>
                    <p class="text-gray-400">{{ $website->business->description }}</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Quick Links</h4>
                    <div class="space-y-2">
                        @foreach($menuPages as $menuPage)
                        <a href="{{ $menuPage->url }}" class="block text-gray-400 hover:text-white">
                            {{ $menuPage->title }}
                        </a>
                        @endforeach
                    </div>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Contact</h4>
                    <div class="space-y-2 text-gray-400">
                        @if($website->business->email)
                        <p>{{ $website->business->email }}</p>
                        @endif
                        @if($website->business->phone)
                        <p>{{ $website->business->phone }}</p>
                        @endif
                        @if($website->business->address)
                        <p>{{ $website->business->address }}, {{ $website->business->city }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>© {{ date('Y') }} {{ $website->business->name }}. All rights reserved.</p>
                <p class="mt-2 text-sm">Powered by <a href="https://shopybook.com" class="text-indigo-400 hover:text-indigo-300">Shopybook</a></p>
            </div>
        </div>
    </footer>

    @if(session('success'))
    <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50">
        {{ session('success') }}
    </div>
    @endif

    <script>
        function toggleMobileMenu() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        }
    </script>
</body>
</html>



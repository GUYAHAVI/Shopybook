<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Preview: {{ $theme->name }} - {{ $website->business->name }}</title>
    
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
            padding-top: 60px; /* Account for preview banner */
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
        
        /* Preview Banner */
        .preview-banner {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #7b2e2e 0%, #ff511a 100%);
            color: white;
            text-align: center;
            padding: 12px 16px;
            z-index: 10000;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .preview-banner .theme-name {
            font-size: 1.1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .preview-banner .close-btn {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.2);
            padding: 6px 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .preview-banner .close-btn:hover {
            background: rgba(255,255,255,0.3);
        }
    </style>
</head>
<body>
    <!-- Preview Banner -->
    <div class="preview-banner">
        <div class="theme-name">
            🎨 Theme Preview: <strong>{{ $theme->name }}</strong> 
            <span style="opacity: 0.9; font-size: 0.9rem; margin-left: 12px;">
                (This is how your website will look with this theme)
            </span>
        </div>
        <div class="close-btn" onclick="window.close()">
            ✕ Close Preview
        </div>
    </div>

    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-[60px] z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    @if($website->business->logo_path)
                    <img src="{{ asset('storage/' . $website->business->logo_path) }}" 
                         alt="{{ $website->business->name }}" class="h-12">
                    @endif
                    <span class="text-2xl font-bold" style="color: var(--color-primary)">
                        {{ $website->business->name }}
                    </span>
                </div>
                
                <!-- Sample Menu -->
                <div class="hidden md:flex items-center gap-6">
                    <a href="#" class="text-gray-700 hover:text-gray-900 font-medium transition-colors border-b-2" 
                       style="border-color: var(--color-primary)">Home</a>
                    <a href="#" class="text-gray-700 hover:text-gray-900 font-medium transition-colors">About</a>
                    <a href="#" class="text-gray-700 hover:text-gray-900 font-medium transition-colors">Services</a>
                    <a href="#" class="text-gray-700 hover:text-gray-900 font-medium transition-colors">Contact</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section (Sample) -->
    <main>
        @foreach($page->sections as $section)
            @php
            $content = $section->content ?? [];
            @endphp
            
            <section class="py-16">
                <div class="container mx-auto px-4">
                    @if($section->type === 'hero')
                        <!-- Hero Section -->
                        <div class="text-center max-w-4xl mx-auto">
                            <h1 class="text-5xl md:text-6xl font-bold mb-6" style="color: var(--color-text)">
                                {{ $content['heading'] ?? 'Welcome to ' . $website->business->name }}
                            </h1>
                            <p class="text-xl md:text-2xl mb-8" style="color: var(--color-text); opacity: 0.8;">
                                {{ $content['subheading'] ?? 'Create stunning websites for your business in minutes' }}
                            </p>
                            <a href="#" class="btn-primary">
                                {{ $content['cta_text'] ?? 'Get Started' }}
                            </a>
                        </div>
                    @endif
                </div>
            </section>
        @endforeach
        
        <!-- Sample Features Section -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <h2 class="text-4xl font-bold text-center mb-12" style="color: var(--color-text)">
                    Why Choose Us
                </h2>
                <div class="grid md:grid-cols-3 gap-8">
                    @for($i = 1; $i <= 3; $i++)
                    <div class="bg-white p-8 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        <div class="text-4xl mb-4">{{ ['💼', '⚡', '🎯'][$i-1] }}</div>
                        <h3 class="text-xl font-bold mb-3" style="color: var(--color-text)">
                            Feature {{ $i }}
                        </h3>
                        <p style="color: var(--color-text); opacity: 0.7;">
                            This is a sample feature description showing how your content will look with this theme.
                        </p>
                    </div>
                    @endfor
                </div>
            </div>
        </section>
        
        <!-- Sample Call to Action -->
        <section class="py-20" style="background-color: var(--color-primary);">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-4xl font-bold mb-6 text-white">
                    Ready to Get Started?
                </h2>
                <p class="text-xl mb-8 text-white opacity-90">
                    Join thousands of businesses already using our platform
                </p>
                <a href="#" class="inline-block bg-white text-gray-900 px-8 py-4 rounded-lg font-semibold hover:shadow-lg transition-all">
                    Contact Us Today
                </a>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">{{ $website->business->name }}</h3>
                    <p class="text-gray-400">
                        {{ $website->business->description ?? 'Your business description goes here.' }}
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Home</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">About</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Services</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-400">
                        @if($website->business->email)
                        <li>📧 {{ $website->business->email }}</li>
                        @endif
                        @if($website->business->phone)
                        <li>📞 {{ $website->business->phone }}</li>
                        @endif
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Follow Us</h4>
                    <div class="flex gap-4 text-2xl">
                        <a href="#" class="hover:text-blue-400 transition-colors">📘</a>
                        <a href="#" class="hover:text-blue-400 transition-colors">🐦</a>
                        <a href="#" class="hover:text-pink-400 transition-colors">📷</a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ $website->business->name }}. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

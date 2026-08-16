<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#7b2e2e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Shopybook">
    <meta name="msapplication-TileColor" content="#7b2e2e">
    <meta name="msapplication-tileimage" content="/icons/icon-144x144.png">

    <!-- PWA Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/icon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/icon-16x16.png">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link rel="manifest" href="/manifest.json">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-QEHQPSK885"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-QEHQPSK885');
</script>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- PWA Installation Banner -->
        <div id="pwa-install-banner" class="hidden fixed top-0 left-0 right-0 bg-blue-600 text-white p-4 z-50 shadow-lg">
            <div class="container mx-auto flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-lg">Install Shopybook App</h3>
                        <p class="text-sm opacity-90">Get quick access to your business dashboard</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <button id="pwa-install-btn" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                        Install Now
                    </button>
                    <button id="pwa-dismiss-btn" class="text-white opacity-70 hover:opacity-100 transition-opacity">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <main class="pt-16">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>
    </div>

    <!-- PWA Installation Script -->
    <script>
        let deferredPrompt;
        const pwaBanner = document.getElementById('pwa-install-banner');
        const pwaInstallBtn = document.getElementById('pwa-install-btn');
        const pwaDismissBtn = document.getElementById('pwa-dismiss-btn');

        // Check if PWA is already installed
        const isPWAInstalled = window.matchMedia('(display-mode: standalone)').matches;
        
        // Check if user has dismissed the banner before
        const hasDismissedBanner = localStorage.getItem('pwa-banner-dismissed');

        // Show banner if PWA is not installed and user hasn't dismissed it
        if (!isPWAInstalled && !hasDismissedBanner) {
            // Listen for beforeinstallprompt event
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                
                // Show banner after a short delay
                setTimeout(() => {
                    pwaBanner.classList.remove('hidden');
                    document.body.style.paddingTop = '80px'; // Add padding for banner
                }, 2000);
            });
        }

        // Handle install button click
        pwaInstallBtn.addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                
                if (outcome === 'accepted') {
                    console.log('PWA installed successfully');
                    pwaBanner.classList.add('hidden');
                    document.body.style.paddingTop = '0';
                }
                
                deferredPrompt = null;
            } else {
                // Fallback for manual installation
                showManualInstallGuide();
            }
        });

        // Handle dismiss button click
        pwaDismissBtn.addEventListener('click', () => {
            pwaBanner.classList.add('hidden');
            document.body.style.paddingTop = '0';
            localStorage.setItem('pwa-banner-dismissed', 'true');
        });

        // Manual installation guide
        function showManualInstallGuide() {
            const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
            const isAndroid = /Android/.test(navigator.userAgent);
            
            let message = '';
            if (isIOS) {
                message = 'To install: Tap the share button (📤) then "Add to Home Screen"';
            } else if (isAndroid) {
                message = 'To install: Tap menu (⋮) then "Add to Home screen"';
            } else {
                message = 'To install: Look for the install icon in your browser\'s address bar';
            }
            
            alert(message);
        }

        // Listen for successful installation
        window.addEventListener('appinstalled', () => {
            pwaBanner.classList.add('hidden');
            document.body.style.paddingTop = '0';
            console.log('PWA was installed');
        });
    </script>
</body>
</html>

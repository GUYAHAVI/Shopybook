@extends('layouts.dash')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                <i class="fas fa-download text-2xl text-blue-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">📱 Install Shopybook App</h1>
            <p class="text-lg text-gray-600">Get quick access to your business dashboard on your phone or tablet</p>
        </div>

        <!-- Benefits -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">✨ Why Install the App?</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-bolt text-blue-600"></i>
                    </div>
                    <h3 class="font-semibold mb-2">Faster Access</h3>
                    <p class="text-sm text-gray-600">Open directly from your home screen</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-wifi text-green-600"></i>
                    </div>
                    <h3 class="font-semibold mb-2">Works Offline</h3>
                    <p class="text-sm text-gray-600">Access key features without internet</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-mobile-alt text-purple-600"></i>
                    </div>
                    <h3 class="font-semibold mb-2">Mobile Optimized</h3>
                    <p class="text-sm text-gray-600">Perfect for on-the-go business</p>
                </div>
            </div>
        </div>

        <!-- Installation Instructions -->
        <div class="grid md:grid-cols-2 gap-8">
            <!-- iOS Instructions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center mb-4">
                    <i class="fab fa-apple text-2xl text-gray-800 mr-3"></i>
                    <h2 class="text-xl font-semibold">iPhone & iPad</h2>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-semibold text-blue-600">1</span>
                        </div>
                        <div>
                            <p class="font-medium">Open Safari browser</p>
                            <p class="text-sm text-gray-600">Make sure you're using Safari, not Chrome</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-semibold text-blue-600">2</span>
                        </div>
                        <div>
                            <p class="font-medium">Tap the Share button</p>
                            <p class="text-sm text-gray-600">Look for the square with arrow (📤) at the bottom</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-semibold text-blue-600">3</span>
                        </div>
                        <div>
                            <p class="font-medium">Scroll and tap "Add to Home Screen"</p>
                            <p class="text-sm text-gray-600">You may need to scroll down to see this option</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-semibold text-blue-600">4</span>
                        </div>
                        <div>
                            <p class="font-medium">Tap "Add" to confirm</p>
                            <p class="text-sm text-gray-600">The app will appear on your home screen</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Android Instructions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center mb-4">
                    <i class="fab fa-android text-2xl text-green-600 mr-3"></i>
                    <h2 class="text-xl font-semibold">Android Phone & Tablet</h2>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-semibold text-green-600">1</span>
                        </div>
                        <div>
                            <p class="font-medium">Open Chrome browser</p>
                            <p class="text-sm text-gray-600">Make sure you're using Chrome, not Samsung Internet</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-semibold text-green-600">2</span>
                        </div>
                        <div>
                            <p class="font-medium">Tap the menu button</p>
                            <p class="text-sm text-gray-600">Look for the three dots (⋮) at the top right</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-semibold text-green-600">3</span>
                        </div>
                        <div>
                            <p class="font-medium">Tap "Add to Home screen"</p>
                            <p class="text-sm text-gray-600">This option is usually near the top of the menu</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-semibold text-green-600">4</span>
                        </div>
                        <div>
                            <p class="font-medium">Tap "Add" to confirm</p>
                            <p class="text-sm text-gray-600">The app will appear on your home screen</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Instructions -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center mb-4">
                <i class="fas fa-desktop text-2xl text-gray-600 mr-3"></i>
                <h2 class="text-xl font-semibold">Desktop & Laptop</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="text-center">
                    <i class="fab fa-chrome text-3xl text-blue-600 mb-3"></i>
                    <h3 class="font-semibold mb-2">Chrome</h3>
                    <p class="text-sm text-gray-600">Look for the install icon (📥) in the address bar</p>
                </div>
                <div class="text-center">
                    <i class="fab fa-edge text-3xl text-blue-500 mb-3"></i>
                    <h3 class="font-semibold mb-2">Edge</h3>
                    <p class="text-sm text-gray-600">Click the install icon in the address bar</p>
                </div>
                <div class="text-center">
                    <i class="fab fa-firefox text-3xl text-orange-500 mb-3"></i>
                    <h3 class="font-semibold mb-2">Firefox</h3>
                    <p class="text-sm text-gray-600">Click the install icon in the address bar</p>
                </div>
            </div>
        </div>

        <!-- Troubleshooting -->
        <div class="mt-8 bg-yellow-50 rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4 text-yellow-800">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Having Trouble?
            </h2>
            <div class="space-y-3 text-sm text-yellow-700">
                <p><strong>Can't find the install option?</strong> Make sure you're using the correct browser (Safari for iOS, Chrome for Android)</p>
                <p><strong>Install button not showing?</strong> Try refreshing the page or clearing your browser cache</p>
                <p><strong>App not appearing on home screen?</strong> Check your app drawer or recent apps</p>
                <p><strong>Still having issues?</strong> Visit <a href="/pwa/debug" class="underline font-medium">our debug page</a> for detailed diagnostics</p>
            </div>
        </div>

        <!-- Quick Install Button -->
        <div class="mt-8 text-center">
            <button onclick="tryAutoInstall()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold text-lg transition-colors">
                <i class="fas fa-download mr-2"></i>
                Try Auto Install
            </button>
            <p class="text-sm text-gray-600 mt-2">This will attempt to install the app automatically if your browser supports it</p>
        </div>
    </div>
</div>

<script>
function tryAutoInstall() {
    if (window.deferredPrompt) {
        window.deferredPrompt.prompt();
        window.deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                alert('🎉 App installed successfully! Check your home screen.');
            } else {
                alert('Installation was cancelled. Please follow the manual steps above.');
            }
            window.deferredPrompt = null;
        });
    } else {
        alert('Auto-install not available. Please follow the manual steps above for your device.');
    }
}

// Listen for beforeinstallprompt
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    window.deferredPrompt = e;
    console.log('PWA install prompt available');
});
</script>
@endsection

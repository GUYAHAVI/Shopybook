<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PWA Debug - Shopybook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Arial', sans-serif; }
        .status-item { margin-bottom: 1rem; padding: 1rem; border-radius: 8px; }
        .status-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .status-error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .status-warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
        .status-info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1><i class="fas fa-bug me-2"></i>PWA Debug Information</h1>
        <p class="text-muted">This page helps diagnose PWA installation issues</p>
        
        <div id="debug-info">
            <div class="status-item status-info">
                <i class="fas fa-spinner fa-spin me-2"></i>
                Loading debug information...
            </div>
        </div>
        
        <div class="mt-4">
            <h3>Manual Installation Steps:</h3>
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fab fa-android me-2"></i>Android (Chrome)</h5>
                    <ol>
                        <li>Tap menu (three dots)</li>
                        <li>Select "Add to Home screen"</li>
                        <li>Tap "Add"</li>
                        <li>Check home screen for icon</li>
                    </ol>
                </div>
                <div class="col-md-6">
                    <h5><i class="fab fa-apple me-2"></i>iPhone (Safari)</h5>
                    <ol>
                        <li>Tap share button (square with arrow)</li>
                        <li>Scroll down to "Add to Home Screen"</li>
                        <li>Tap "Add"</li>
                        <li>Check home screen for icon</li>
                    </ol>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h3>Common Issues:</h3>
            <ul>
                <li><strong>Icon not visible:</strong> Check app drawer, recent apps, or search</li>
                <li><strong>Installation failed:</strong> Try refreshing the page and installing again</li>
                <li><strong>App doesn't open:</strong> Check if you're using the correct browser</li>
                <li><strong>HTTPS required:</strong> Make sure you're accessing via HTTPS</li>
            </ul>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const debugInfo = document.getElementById('debug-info');
            let html = '';
            
            // Check HTTPS
            const isHttps = window.location.protocol === 'https:';
            html += `<div class="status-item ${isHttps ? 'status-success' : 'status-error'}">
                <i class="fas fa-${isHttps ? 'check' : 'times'}-circle me-2"></i>
                <strong>HTTPS:</strong> ${isHttps ? '✅ Secure connection' : '❌ HTTPS required for PWA'}
            </div>`;
            
            // Check Service Worker
            const hasServiceWorker = 'serviceWorker' in navigator;
            html += `<div class="status-item ${hasServiceWorker ? 'status-success' : 'status-error'}">
                <i class="fas fa-${hasServiceWorker ? 'check' : 'times'}-circle me-2"></i>
                <strong>Service Worker:</strong> ${hasServiceWorker ? '✅ Supported' : '❌ Not supported'}
            </div>`;
            
            // Check Manifest
            const hasManifest = document.querySelector('link[rel="manifest"]');
            html += `<div class="status-item ${hasManifest ? 'status-success' : 'status-error'}">
                <i class="fas fa-${hasManifest ? 'check' : 'times'}-circle me-2"></i>
                <strong>Web App Manifest:</strong> ${hasManifest ? '✅ Found' : '❌ Not found'}
            </div>`;
            
            // Check Display Mode
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches;
            html += `<div class="status-item ${isStandalone ? 'status-success' : 'status-info'}">
                <i class="fas fa-${isStandalone ? 'check' : 'info'}-circle me-2"></i>
                <strong>Display Mode:</strong> ${isStandalone ? '✅ Running as PWA' : 'ℹ️ Running in browser'}
            </div>`;
            
            // Check User Agent
            const userAgent = navigator.userAgent;
            const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(userAgent);
            html += `<div class="status-item status-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Device:</strong> ${isMobile ? '📱 Mobile' : '💻 Desktop'} - ${userAgent.split(' ')[0]}
            </div>`;
            
            // Check Browser
            let browser = 'Unknown';
            if (userAgent.includes('Chrome')) browser = 'Chrome';
            else if (userAgent.includes('Safari') && !userAgent.includes('Chrome')) browser = 'Safari';
            else if (userAgent.includes('Firefox')) browser = 'Firefox';
            else if (userAgent.includes('Edge')) browser = 'Edge';
            
            html += `<div class="status-item status-info">
                <i class="fas fa-globe me-2"></i>
                <strong>Browser:</strong> ${browser}
            </div>`;
            
            // Check Installation Status
            const canInstall = window.deferredPrompt !== undefined;
            html += `<div class="status-item ${canInstall ? 'status-success' : 'status-warning'}">
                <i class="fas fa-${canInstall ? 'check' : 'exclamation-triangle'}-circle me-2"></i>
                <strong>Installation:</strong> ${canInstall ? '✅ Ready to install' : '⚠️ Installation not available'}
            </div>`;
            
            // Check Icons
            const icon192 = new Image();
            icon192.onload = function() {
                html += `<div class="status-item status-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Icons:</strong> ✅ 192x192 icon loaded successfully
                </div>`;
                debugInfo.innerHTML = html;
            };
            icon192.onerror = function() {
                html += `<div class="status-item status-error">
                    <i class="fas fa-times-circle me-2"></i>
                    <strong>Icons:</strong> ❌ Failed to load 192x192 icon
                </div>`;
                debugInfo.innerHTML = html;
            };
            icon192.src = '/icons/icon-192x192.png';
            
            // Listen for beforeinstallprompt
            window.addEventListener('beforeinstallprompt', (e) => {
                const installItem = document.querySelector('.status-item:last-child');
                if (installItem) {
                    installItem.className = 'status-item status-success';
                    installItem.innerHTML = '<i class="fas fa-check-circle me-2"></i><strong>Installation:</strong> ✅ Ready to install';
                }
            });
        });
    </script>
</body>
</html>

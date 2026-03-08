<div class="pwa-install-guide" id="pwaInstallGuide" style="display: none;">
    <div class="pwa-install-guide-content">
        <div class="pwa-install-guide-header">
            <h5><i class="fas fa-download me-2"></i>Install Shopybook</h5>
            <button class="pwa-install-guide-close" onclick="closePWAInstallGuide()">×</button>
        </div>
        
        <div class="pwa-install-guide-body">
            <div class="pwa-install-method" id="chromeInstall">
                <h6><i class="fab fa-chrome me-2"></i>Chrome / Edge</h6>
                <ol>
                    <li>Click the <i class="fas fa-plus"></i> icon in the address bar</li>
                    <li>Select "Install Shopybook"</li>
                    <li>Click "Install" to add to your home screen</li>
                </ol>
            </div>
            
            <div class="pwa-install-method" id="safariInstall">
                <h6><i class="fab fa-safari me-2"></i>Safari (iOS)</h6>
                <ol>
                    <li>Tap the <i class="fas fa-share"></i> share button</li>
                    <li>Scroll down and tap "Add to Home Screen"</li>
                    <li>Tap "Add" to install the app</li>
                </ol>
            </div>
            
            <div class="pwa-install-method" id="androidInstall">
                <h6><i class="fab fa-android me-2"></i>Android Chrome</h6>
                <ol>
                    <li>Tap the <i class="fas fa-ellipsis-v"></i> menu button</li>
                    <li>Select "Add to Home screen"</li>
                    <li>Tap "Add" to install the app</li>
                </ol>
            </div>
            
            <div class="pwa-install-method" id="firefoxInstall">
                <h6><i class="fab fa-firefox me-2"></i>Firefox</h6>
                <ol>
                    <li>Click the <i class="fas fa-ellipsis-h"></i> menu button</li>
                    <li>Select "Install App"</li>
                    <li>Click "Install" to add to your home screen</li>
                </ol>
            </div>
        </div>
        
        <div class="pwa-install-guide-footer">
            <button class="btn btn-primary" onclick="window.pwaManager.addToHomeScreen()">
                <i class="fas fa-download me-2"></i>Install Now
            </button>
            <button class="btn btn-outline-secondary" onclick="closePWAInstallGuide()">
                Maybe Later
            </button>
        </div>
    </div>
</div>

<style>
.pwa-install-guide {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.pwa-install-guide-content {
    background: white;
    border-radius: 15px;
    max-width: 500px;
    width: 100%;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.pwa-install-guide-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.5rem;
    border-bottom: 1px solid #e9ecef;
}

.pwa-install-guide-header h5 {
    margin: 0;
    color: var(--primary-color);
    font-weight: 600;
}

.pwa-install-guide-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #666;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.pwa-install-guide-close:hover {
    background: #f8f9fa;
    color: #333;
}

.pwa-install-guide-body {
    padding: 1.5rem;
}

.pwa-install-method {
    margin-bottom: 1.5rem;
    padding: 1rem;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    background: #f8f9fa;
}

.pwa-install-method h6 {
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.pwa-install-method ol {
    margin: 0;
    padding-left: 1.25rem;
}

.pwa-install-method li {
    margin-bottom: 0.5rem;
    color: #555;
    line-height: 1.4;
}

.pwa-install-method i {
    color: var(--primary-light);
}

.pwa-install-guide-footer {
    padding: 1.5rem;
    border-top: 1px solid #e9ecef;
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
}

@media (max-width: 768px) {
    .pwa-install-guide {
        padding: 10px;
    }
    
    .pwa-install-guide-content {
        max-height: 90vh;
    }
    
    .pwa-install-guide-footer {
        flex-direction: column;
    }
    
    .pwa-install-guide-footer .btn {
        width: 100%;
    }
}
</style>

<script>
function showPWAInstallGuide() {
    const guide = document.getElementById('pwaInstallGuide');
    guide.style.display = 'flex';
    
    // Show relevant installation method based on browser
    const userAgent = navigator.userAgent;
    const isIOS = /iPad|iPhone|iPod/.test(userAgent);
    const isAndroid = /Android/.test(userAgent);
    const isChrome = /Chrome/.test(userAgent) && !/Edge/.test(userAgent);
    const isFirefox = /Firefox/.test(userAgent);
    const isSafari = /Safari/.test(userAgent) && !/Chrome/.test(userAgent);
    
    // Hide all methods first
    document.querySelectorAll('.pwa-install-method').forEach(method => {
        method.style.display = 'none';
    });
    
    // Show relevant method
    if (isIOS && isSafari) {
        document.getElementById('safariInstall').style.display = 'block';
    } else if (isAndroid && isChrome) {
        document.getElementById('androidInstall').style.display = 'block';
    } else if (isFirefox) {
        document.getElementById('firefoxInstall').style.display = 'block';
    } else {
        document.getElementById('chromeInstall').style.display = 'block';
    }
}

function closePWAInstallGuide() {
    const guide = document.getElementById('pwaInstallGuide');
    guide.style.display = 'none';

    // Mark as dismissed so it doesn't re-appear this session
    sessionStorage.setItem('pwaGuideShown', 'true');

    // Nullify the deferred prompt so no browser install dialog can fire
    if (window.pwaManager) {
        window.pwaManager.deferredPrompt = null;
        window.pwaManager.hideInstallPrompt();
    }
}

// Auto-show guide for new users (once per session)
if (!sessionStorage.getItem('pwaGuideShown')) {
    setTimeout(() => {
        if (window.pwaManager && !window.pwaManager.isInstalled) {
            showPWAInstallGuide();
            sessionStorage.setItem('pwaGuideShown', 'true');
        }
    }, 3000);
}
</script>

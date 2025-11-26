// PWA Management Class
class PWAManager {
    constructor() {
        this.deferredPrompt = null;
        this.installButton = null;
        this.updateButton = null;
        this.isInstalled = false;
        this.isOnline = navigator.onLine;
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.checkInstallationStatus();
        this.registerServiceWorker();
        this.setupUpdateDetection();
        this.setupConnectionMonitoring();
    }
    
    setupEventListeners() {
        // Listen for beforeinstallprompt event
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            
            // Only show install prompt if app is not already installed
            if (!this.isInstalled && !window.matchMedia('(display-mode: standalone)').matches && 
                window.navigator.standalone !== true) {
                this.showInstallPrompt();
            }
        });
        
        // Listen for appinstalled event
        window.addEventListener('appinstalled', (e) => {
            this.isInstalled = true;
            this.hideInstallPrompt();
            this.showInstallationSuccess();
        });
        
        // Listen for online/offline events
        window.addEventListener('online', () => {
            this.isOnline = true;
            this.updateConnectionStatus();
            this.syncOfflineData();
        });
        
        window.addEventListener('offline', () => {
            this.isOnline = false;
            this.updateConnectionStatus();
        });
    }
    
    registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then((registration) => {
                    console.log('Service Worker registered successfully:', registration);
                    this.setupServiceWorkerEvents(registration);
                })
                .catch((error) => {
                    console.error('Service Worker registration failed:', error);
                });
        }
    }
    
    setupServiceWorkerEvents(registration) {
        // Handle service worker updates
        registration.addEventListener('updatefound', () => {
            const newWorker = registration.installing;
            newWorker.addEventListener('statechange', () => {
                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                    this.showUpdatePrompt();
                }
            });
        });
        
        // Handle service worker messages
        navigator.serviceWorker.addEventListener('message', (event) => {
            if (event.data && event.data.type === 'SKIP_WAITING') {
                window.location.reload();
            }
        });
    }
    
    setupUpdateDetection() {
        // Check for updates every hour
        setInterval(() => {
            if (navigator.serviceWorker.controller) {
                navigator.serviceWorker.controller.postMessage({ type: 'CHECK_FOR_UPDATES' });
            }
        }, 3600000); // 1 hour
    }
    
    setupConnectionMonitoring() {
        // Update connection status periodically
        setInterval(() => {
            this.updateConnectionStatus();
        }, 5000);
    }
    
    showInstallPrompt() {
        // Don't show install prompt if app is already installed
        if (this.isInstalled || window.matchMedia('(display-mode: standalone)').matches || 
            window.navigator.standalone === true) {
            this.hideAllInstallElements();
            return;
        }
        
        // Create install button if it doesn't exist
        if (!this.installButton) {
            this.createInstallButton();
        }
        
        this.installButton.style.display = 'block';
        this.installButton.addEventListener('click', () => {
            this.installApp();
        });
    }
    
    hideInstallPrompt() {
        if (this.installButton) {
            this.installButton.style.display = 'none';
        }
    }
    
    hideAllInstallElements() {
        // Hide all installation-related elements
        this.hideInstallPrompt();
        
        // Hide PWA banner
        const pwaBanner = document.getElementById('pwa-install-banner');
        if (pwaBanner) {
            pwaBanner.classList.add('hidden');
        }
        
        // Hide sidebar install button
        const sidebarInstallBtn = document.getElementById('sidebar-install-app');
        if (sidebarInstallBtn) {
            sidebarInstallBtn.style.display = 'none';
        }
        
        // Hide PWA install guide modal trigger
        const pwaInstallGuideTriggers = document.querySelectorAll('[onclick*="showPWAInstallGuide"]');
        pwaInstallGuideTriggers.forEach(trigger => {
            trigger.style.display = 'none';
        });
        
        // Hide any other PWA installation elements
        const pwaElements = document.querySelectorAll('[id*="pwa"], [class*="pwa-install"]');
        pwaElements.forEach(element => {
            if (element.id !== 'pwa-install-banner' && !element.classList.contains('pwa-notification')) {
                element.style.display = 'none';
            }
        });
        
        // Update global installation status
        window.pwaInstalled = true;
    }
    
    createInstallButton() {
        this.installButton = document.createElement('div');
        this.installButton.className = 'pwa-install-button';
        this.installButton.innerHTML = `
            <div class="pwa-install-content">
                <i class="fas fa-download"></i>
                <span>Install Shopybook</span>
                <button class="pwa-install-btn">Install</button>
                <button class="pwa-dismiss-btn">×</button>
            </div>
        `;
        
        // Add dismiss functionality
        this.installButton.querySelector('.pwa-dismiss-btn').addEventListener('click', () => {
            this.hideInstallPrompt();
        });
        
        document.body.appendChild(this.installButton);
    }
    
    async installApp() {
        if (!this.deferredPrompt) {
            return;
        }
        
        this.deferredPrompt.prompt();
        const { outcome } = await this.deferredPrompt.userChoice;
        
        if (outcome === 'accepted') {
            console.log('User accepted the install prompt');
        } else {
            console.log('User dismissed the install prompt');
        }
        
        this.deferredPrompt = null;
        this.hideInstallPrompt();
    }
    
    showInstallationSuccess() {
        this.showNotification('Shopybook installed successfully!', 'success');
    }
    
    showUpdatePrompt() {
        if (!this.updateButton) {
            this.createUpdateButton();
        }
        
        this.updateButton.style.display = 'block';
    }
    
    createUpdateButton() {
        this.updateButton = document.createElement('div');
        this.updateButton.className = 'pwa-update-button';
        this.updateButton.innerHTML = `
            <div class="pwa-update-content">
                <i class="fas fa-sync-alt"></i>
                <span>New version available</span>
                <button class="pwa-update-btn">Update</button>
                <button class="pwa-dismiss-btn">×</button>
            </div>
        `;
        
        this.updateButton.querySelector('.pwa-update-btn').addEventListener('click', () => {
            this.updateApp();
        });
        
        this.updateButton.querySelector('.pwa-dismiss-btn').addEventListener('click', () => {
            this.updateButton.style.display = 'none';
        });
        
        document.body.appendChild(this.updateButton);
    }
    
    updateApp() {
        if (navigator.serviceWorker.controller) {
            navigator.serviceWorker.controller.postMessage({ type: 'SKIP_WAITING' });
        }
    }
    
    checkInstallationStatus() {
        // Check if app is installed
        if (window.matchMedia('(display-mode: standalone)').matches || 
            window.navigator.standalone === true) {
            this.isInstalled = true;
            this.hideInstallPrompt();
            this.hideAllInstallElements();
        }
        
        // Expose installation status globally
        window.pwaInstalled = this.isInstalled;
    }
    
    updateConnectionStatus() {
        const statusElement = document.getElementById('connection-status');
        if (statusElement) {
            if (this.isOnline) {
                statusElement.className = 'connection-status online';
                statusElement.innerHTML = '<i class="fas fa-wifi"></i><span>Online</span>';
            } else {
                statusElement.className = 'connection-status offline';
                statusElement.innerHTML = '<i class="fas fa-wifi-slash"></i><span>Offline</span>';
            }
        }
    }
    
    async syncOfflineData() {
        if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
            try {
                // Trigger background sync
                const registration = await navigator.serviceWorker.ready;
                await registration.sync.register('background-sync');
                console.log('Background sync registered');
            } catch (error) {
                console.error('Background sync failed:', error);
            }
        }
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `pwa-notification ${type}`;
        notification.innerHTML = `
            <div class="pwa-notification-content">
                <i class="fas fa-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
                <button class="pwa-notification-close">×</button>
            </div>
        `;
        
        notification.querySelector('.pwa-notification-close').addEventListener('click', () => {
            notification.remove();
        });
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
    
    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            error: 'exclamation-circle',
            warning: 'exclamation-triangle',
            info: 'info-circle'
        };
        return icons[type] || 'info-circle';
    }
    
    // Add to home screen functionality
    addToHomeScreen() {
        if (this.deferredPrompt) {
            this.installApp();
        } else {
            this.showNotification('Installation not available. Please use your browser\'s menu to install.', 'info');
        }
    }
    
    // Share functionality
    async shareData(data) {
        if (navigator.share) {
            try {
                await navigator.share(data);
            } catch (error) {
                console.error('Share failed:', error);
            }
        } else {
            // Fallback for browsers that don't support Web Share API
            this.copyToClipboard(data.url || data.text);
        }
    }
    
    copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            this.showNotification('Copied to clipboard!', 'success');
        }).catch(() => {
            this.showNotification('Failed to copy to clipboard', 'error');
        });
    }
    
    // Get app info
    getAppInfo() {
        return {
            isInstalled: this.isInstalled,
            isOnline: this.isOnline,
            isStandalone: window.matchMedia('(display-mode: standalone)').matches,
            userAgent: navigator.userAgent,
            platform: navigator.platform
        };
    }
}

// Initialize PWA Manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.pwaManager = new PWAManager();
});

// Add PWA styles
const pwaStyles = `
    .pwa-install-button,
    .pwa-update-button {
        position: fixed;
        bottom: 20px;
        left: 20px;
        z-index: 9999;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        padding: 1rem;
        max-width: 300px;
        display: none;
    }
    
    .pwa-install-content,
    .pwa-update-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.9rem;
        color: #333;
    }
    
    .pwa-install-btn,
    .pwa-update-btn {
        background: linear-gradient(135deg, #020258, #13e8e9);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 5px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .pwa-install-btn:hover,
    .pwa-update-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 10px rgba(2, 2, 88, 0.3);
    }
    
    .pwa-dismiss-btn {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: #666;
        cursor: pointer;
        padding: 0;
        margin-left: auto;
    }
    
    .pwa-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        padding: 1rem;
        max-width: 300px;
        animation: slideIn 0.3s ease;
    }
    
    .pwa-notification.success {
        border-left: 4px solid #28a745;
    }
    
    .pwa-notification.error {
        border-left: 4px solid #dc3545;
    }
    
    .pwa-notification.warning {
        border-left: 4px solid #ffc107;
    }
    
    .pwa-notification.info {
        border-left: 4px solid #17a2b8;
    }
    
    .pwa-notification-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.9rem;
    }
    
    .pwa-notification-close {
        background: none;
        border: none;
        font-size: 1.2rem;
        color: #666;
        cursor: pointer;
        padding: 0;
        margin-left: auto;
    }
    
    .connection-status {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9998;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: white;
    }
    
    .connection-status.online {
        background: #28a745;
    }
    
    .connection-status.offline {
        background: #dc3545;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @media (max-width: 768px) {
        .pwa-install-button,
        .pwa-update-button {
            bottom: 10px;
            left: 10px;
            right: 10px;
            max-width: none;
        }
        
        .pwa-notification {
            top: 10px;
            right: 10px;
            left: 10px;
            max-width: none;
        }
    }
`;

// Inject PWA styles
const styleSheet = document.createElement('style');
styleSheet.textContent = pwaStyles;
document.head.appendChild(styleSheet);

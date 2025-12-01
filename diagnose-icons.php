<?php
/**
 * Font Awesome Icons Diagnostic Tool
 * 
 * This script checks for common issues that prevent Font Awesome icons
 * from displaying properly.
 * 
 * Usage: Upload to project root and access via browser
 * https://yoursite.com/diagnose-icons.php
 * 
 * DELETE THIS FILE AFTER USE!
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Font Awesome Diagnostic Tool</title>
    
    <!-- Test Font Awesome loading -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
          integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #020258;
            border-bottom: 3px solid #13e8e9;
            padding-bottom: 10px;
        }
        .test-section {
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #13e8e9;
        }
        .icon-test {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin: 20px 0;
        }
        .icon-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            min-width: 120px;
        }
        .icon-item i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #020258;
        }
        .icon-item code {
            font-size: 0.8rem;
            color: #666;
            text-align: center;
        }
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            display: inline-block;
            margin: 5px 0;
        }
        .status.ok {
            background: #d4edda;
            color: #155724;
        }
        .status.error {
            background: #f8d7da;
            color: #721c24;
        }
        .status.warning {
            background: #fff3cd;
            color: #856404;
        }
        .check-item {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .check-item:last-child {
            border-bottom: none;
        }
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .btn {
            background: #13e8e9;
            color: #020258;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
        }
        .btn:hover {
            background: #020258;
            color: #13e8e9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Font Awesome Icons Diagnostic</h1>
        <p>This tool checks if Font Awesome icons are loading properly in your application.</p>

        <div class="test-section">
            <h2>1. Icon Rendering Test</h2>
            <p>If you can see the icons below, Font Awesome is loading correctly:</p>
            
            <div class="icon-test">
                <div class="icon-item">
                    <i class="fas fa-building"></i>
                    <code>fa-building</code>
                </div>
                <div class="icon-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <code>fa-map-marker-alt</code>
                </div>
                <div class="icon-item">
                    <i class="fas fa-eye"></i>
                    <code>fa-eye</code>
                </div>
                <div class="icon-item">
                    <i class="fas fa-search"></i>
                    <code>fa-search</code>
                </div>
                <div class="icon-item">
                    <i class="fas fa-sort"></i>
                    <code>fa-sort</code>
                </div>
                <div class="icon-item">
                    <i class="fas fa-store"></i>
                    <code>fa-store</code>
                </div>
            </div>
        </div>

        <div class="test-section">
            <h2>2. CSS Loading Check</h2>
            <div id="css-check">
                <p>Checking Font Awesome CSS...</p>
            </div>
        </div>

        <div class="test-section">
            <h2>3. Common Issues</h2>
            
            <div class="check-item">
                <strong>Issue:</strong> Icons show as squares or missing<br>
                <strong>Cause:</strong> Font Awesome CSS not loaded or blocked<br>
                <strong>Fix:</strong> Check browser console for 404 or CORS errors
            </div>
            
            <div class="check-item">
                <strong>Issue:</strong> Icons work on other pages but not on businesses page<br>
                <strong>Cause:</strong> CSS specificity or z-index conflicts<br>
                <strong>Fix:</strong> Check for CSS overrides in partials/businesses.blade.php
            </div>
            
            <div class="check-item">
                <strong>Issue:</strong> Icons work locally but not on production<br>
                <strong>Cause:</strong> CDN blocked by firewall or Content Security Policy<br>
                <strong>Fix:</strong> Check CSP headers or use local Font Awesome files
            </div>
            
            <div class="check-item">
                <strong>Issue:</strong> Only some icons missing<br>
                <strong>Cause:</strong> Wrong icon name or Font Awesome version mismatch<br>
                <strong>Fix:</strong> Verify icon names at <a href="https://fontawesome.com/search" target="_blank">fontawesome.com/search</a>
            </div>
        </div>

        <div class="test-section">
            <h2>4. Browser Console Check</h2>
            <p>Open your browser's Developer Tools (F12) and check the Console tab for errors.</p>
            <p>Common errors to look for:</p>
            <ul>
                <li><code>Failed to load resource: net::ERR_BLOCKED_BY_CLIENT</code> - Ad blocker</li>
                <li><code>Failed to load resource: 404</code> - Wrong CDN URL</li>
                <li><code>Refused to load stylesheet... CSP</code> - Content Security Policy issue</li>
                <li><code>font-family: "Font Awesome 6 Free" not found</code> - Font files not loading</li>
            </ul>
            
            <button class="btn" onclick="checkConsole()">Check Console Now</button>
        </div>

        <div class="test-section">
            <h2>5. Network Check</h2>
            <p>Check if Font Awesome CSS is loading in the Network tab:</p>
            <ol>
                <li>Open DevTools (F12)</li>
                <li>Go to Network tab</li>
                <li>Reload page</li>
                <li>Filter by "all.min.css"</li>
                <li>Check if status is 200 (OK)</li>
            </ol>
        </div>

        <div class="test-section">
            <h2>6. Recommended Fixes</h2>
            
            <h3>Option 1: Use Different CDN (Fastest)</h3>
            <pre>&lt;!-- Replace in layouts/master.blade.php --&gt;
&lt;link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
      integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" 
      crossorigin="anonymous" referrerpolicy="no-referrer" /&gt;</pre>

            <h3>Option 2: Use jsDelivr CDN (Backup)</h3>
            <pre>&lt;link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css"&gt;</pre>

            <h3>Option 3: Self-Host Font Awesome (Most Reliable)</h3>
            <pre># Install via Composer
composer require components/font-awesome

# Or download from: https://fontawesome.com/download
# Extract to: public/vendor/fontawesome-free/

# Then in blade:
&lt;link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}"&gt;</pre>

            <h3>Option 4: Check for CSS Conflicts</h3>
            <p>Add this to your <code>partials/businesses.blade.php</code> at the top of the styles:</p>
            <pre>/* Force Font Awesome icons to display */
.fas, .fa-solid, .far, .fa-regular, .fab, .fa-brands {
    font-family: "Font Awesome 6 Free" !important;
    font-weight: 900 !important;
    font-style: normal !important;
    font-variant: normal !important;
    text-rendering: auto !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
    display: inline-block !important;
}

.far, .fa-regular {
    font-weight: 400 !important;
}

.fab, .fa-brands {
    font-weight: 400 !important;
}</pre>
        </div>

        <div class="test-section">
            <h2>7. Quick Test Command</h2>
            <p>Run this in your browser console to test if Font Awesome is loaded:</p>
            <pre>// Check if Font Awesome CSS is present
const faCSS = Array.from(document.styleSheets).find(s => s.href && s.href.includes('font-awesome'));
console.log('Font Awesome CSS:', faCSS ? '✅ Loaded' : '❌ Not Found');

// Check if Font Awesome font is loaded
document.fonts.ready.then(() => {
    const fonts = Array.from(document.fonts.values());
    const faFont = fonts.find(f => f.family.includes('Font Awesome'));
    console.log('Font Awesome Font:', faFont ? '✅ Loaded' : '❌ Not Found');
});</pre>
        </div>

        <p style="margin-top: 30px; padding: 20px; background: #fff3cd; border-radius: 8px;">
            <strong>⚠️ Security Note:</strong> Delete this file after diagnosing the issue!
        </p>
    </div>

    <script>
        // Check if Font Awesome CSS is loaded
        window.addEventListener('load', function() {
            const cssCheck = document.getElementById('css-check');
            const faCSS = Array.from(document.styleSheets).find(s => s.href && s.href.includes('font-awesome'));
            
            if (faCSS) {
                cssCheck.innerHTML = '<span class="status ok">✅ Font Awesome CSS is loaded</span><br>' +
                                    '<small>URL: ' + faCSS.href + '</small>';
            } else {
                cssCheck.innerHTML = '<span class="status error">❌ Font Awesome CSS is NOT loaded</span><br>' +
                                    '<p>This is the problem! The Font Awesome CSS file is not being loaded.</p>';
            }

            // Check if fonts are loaded
            if (document.fonts) {
                document.fonts.ready.then(() => {
                    const fonts = Array.from(document.fonts.values());
                    const faFont = fonts.find(f => f.family.includes('Font Awesome'));
                    
                    const fontCheck = document.createElement('div');
                    if (faFont) {
                        fontCheck.innerHTML = '<span class="status ok">✅ Font Awesome font files are loaded</span>';
                    } else {
                        fontCheck.innerHTML = '<span class="status error">❌ Font Awesome font files are NOT loaded</span>';
                    }
                    cssCheck.appendChild(fontCheck);
                });
            }
        });

        function checkConsole() {
            console.clear();
            console.log('%c🔍 Font Awesome Diagnostic', 'font-size: 20px; font-weight: bold; color: #020258;');
            console.log('');
            
            // Check CSS
            const faCSS = Array.from(document.styleSheets).find(s => s.href && s.href.includes('font-awesome'));
            console.log('Font Awesome CSS:', faCSS ? '✅ Loaded' : '❌ Not Found');
            if (faCSS) {
                console.log('  URL:', faCSS.href);
                console.log('  Rules:', faCSS.cssRules ? faCSS.cssRules.length : 'N/A');
            }
            
            // Check fonts
            if (document.fonts) {
                document.fonts.ready.then(() => {
                    const fonts = Array.from(document.fonts.values());
                    const faFont = fonts.find(f => f.family.includes('Font Awesome'));
                    console.log('Font Awesome Font:', faFont ? '✅ Loaded' : '❌ Not Found');
                    if (faFont) {
                        console.log('  Family:', faFont.family);
                        console.log('  Status:', faFont.status);
                    }
                });
            }
            
            // Check icon elements
            const icons = document.querySelectorAll('.fas, .far, .fab, .fa');
            console.log('');
            console.log('Icon elements found:', icons.length);
            if (icons.length > 0) {
                console.log('Sample icon:', icons[0]);
                console.log('Computed style:', window.getComputedStyle(icons[0]).fontFamily);
            }
            
            alert('Check the Console tab in Developer Tools (F12) for diagnostic results!');
        }
    </script>
</body>
</html>


@extends('layouts.dash')

@section('title', 'App Store - Customize Your Dashboard')

@section('content')
<style>
    .app-store-header {
        background: linear-gradient(135deg, #7b2e2e, #ff511a);
        color: white;
        padding: 3rem 0;
        margin: -2rem -2rem 2rem -2rem;
        border-radius: 0 0 20px 20px;
    }
    
    .app-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        border: 2px solid #e5e7eb;
        cursor: pointer;
        position: relative;
        height: 100%;
    }
    
    .app-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        border-color: #7b2e2e;
    }
    
    .app-card.enabled {
        border-color: #10b981;
        background: linear-gradient(to bottom right, #f0fdf4, white);
    }
    
    .app-card.enabled::before {
        content: '✓';
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: #10b981;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
    }
    
    .app-icon {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #7b2e2e, #ff511a);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: white;
        margin-bottom: 1rem;
    }
    
    .app-card.enabled .app-icon {
        background: linear-gradient(135deg, #10b981, #34d399);
    }
    
    .app-category-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .category-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .badge-core { background: #dbeafe; color: #1e40af; }
    .badge-operations { background: #fef3c7; color: #92400e; }
    .badge-sales { background: #ddd6fe; color: #5b21b6; }
    .badge-finance { background: #d1fae5; color: #065f46; }
    .badge-growth { background: #fee2e2; color: #991b1b; }
    .badge-tools { background: #e0e7ff; color: #3730a3; }
    .badge-analytics { background: #fbcfe8; color: #831843; }
    
    .toggle-switch {
        position: relative;
        width: 56px;
        height: 28px;
        background: #cbd5e1;
        border-radius: 14px;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .toggle-switch.active {
        background: #10b981;
    }
    
    .toggle-switch::after {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        width: 24px;
        height: 24px;
        background: white;
        border-radius: 50%;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    .toggle-switch.active::after {
        transform: translateX(28px);
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        border: 2px solid #e5e7eb;
    }
</style>

<div class="app-store-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="fas fa-th me-2"></i>
                    App Store
                </h1>
                <p class="lead mb-0">
                    Customize your dashboard by enabling only the apps you need.
                    <br>Make Shopybook work the way you work.
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="stat-card text-dark">
                    <div class="fs-2 fw-bold text-success" id="enabledCount">{{ count(array_filter($enabledApps)) }}</div>
                    <div class="text-muted">Apps Enabled</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Business Type Info -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>{{ ucfirst($business->business_category ?? 'Your') }} Business:</strong>
        Apps marked with a star (⭐) are recommended for your business type.
    </div>

    @foreach($groupedApps as $category => $apps)
        <div class="mb-5">
            <h3 class="app-category-title">
                @if($category === 'core')
                    <i class="fas fa-star text-warning"></i> Core Apps
                @elseif($category === 'operations')
                    <i class="fas fa-cog"></i> Operations
                @elseif($category === 'sales')
                    <i class="fas fa-chart-line"></i> Sales & CRM
                @elseif($category === 'finance')
                    <i class="fas fa-dollar-sign"></i> Financial
                @elseif($category === 'growth')
                    <i class="fas fa-rocket"></i> Growth & Marketing
                @elseif($category === 'tools')
                    <i class="fas fa-robot"></i> AI & Tools
                @elseif($category === 'analytics')
                    <i class="fas fa-chart-bar"></i> Analytics
                @endif
            </h3>
            
            <div class="row g-4">
                @foreach($apps as $slug => $app)
                    @php
                        $isEnabled = $enabledApps[$slug] ?? false;
                        $isRecommended = in_array($business->business_category, $app['required_for'] ?? []);
                    @endphp
                    
                    <div class="col-md-4 col-lg-3">
                        <div class="app-card {{ $isEnabled ? 'enabled' : '' }}" 
                             data-app-slug="{{ $slug }}"
                             onclick="toggleApp('{{ $slug }}', this, event)">
                            
                            @if($isRecommended && !$isEnabled)
                                <span class="position-absolute top-0 start-0 m-2" title="Recommended for your business">
                                    ⭐
                                </span>
                            @endif
                            
                            <div class="app-icon">
                                <i class="{{ $app['icon'] }}"></i>
                            </div>
                            
                            <h5 class="fw-bold mb-2">{{ $app['name'] }}</h5>
                            
                            <p class="text-muted small mb-3">
                                {{ $app['description'] }}
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="category-badge badge-{{ $category }}">
                                    {{ ucfirst($category) }}
                                </span>
                                
                                <div class="toggle-switch {{ $isEnabled ? 'active' : '' }}" 
                                     data-app-slug="{{ $slug }}">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
    
    <div class="alert alert-light border mt-5">
        <h5><i class="fas fa-lightbulb text-warning me-2"></i>Pro Tip</h5>
        <p class="mb-0">
            Start with the core apps, then add more as your business grows. 
            You can always change this later from <a href="{{ route('apps.index') }}">Settings → Apps</a>.
        </p>
    </div>
</div>

<script>
let enabledCount = {{ count(array_filter($enabledApps)) }};

async function toggleApp(appSlug, cardElement, event) {
    // Log what we received
    console.log('toggleApp called with:', {
        appSlug: appSlug,
        appSlugType: typeof appSlug,
        cardElement: cardElement,
        cardDataSlug: cardElement ? cardElement.getAttribute('data-app-slug') : null
    });
    
    // Prevent event bubbling
    if (event) {
        event.stopPropagation();
    }
    
    // Validate appSlug
    if (!appSlug || typeof appSlug !== 'string') {
        console.error('Invalid appSlug:', appSlug);
        alert('Error: Invalid app identifier');
        return;
    }
    
    const toggleSwitch = cardElement.querySelector('.toggle-switch');
    const currentState = toggleSwitch.classList.contains('active');
    
    // Optimistic UI update
    toggleSwitch.classList.toggle('active');
    cardElement.classList.toggle('enabled');
    
    // Update counter
    enabledCount += currentState ? -1 : 1;
    document.getElementById('enabledCount').textContent = enabledCount;
    
    // Log what we're about to send
    const requestBody = { app_slug: appSlug };
    console.log('Sending request with body:', requestBody);
    
    try {
        const response = await fetch('{{ route('apps.toggle') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(requestBody)
        });
        
        const data = await response.json();
        
        // Log the response for debugging
        console.log('Toggle response:', data);
        
        if (!response.ok || !data.success) {
            // Revert on failure
            toggleSwitch.classList.toggle('active');
            cardElement.classList.toggle('enabled');
            enabledCount += currentState ? 1 : -1;
            document.getElementById('enabledCount').textContent = enabledCount;
            
            // Show detailed error
            const errorMsg = data.message || 'Unknown error';
            const errorDetails = data.errors ? JSON.stringify(data.errors) : '';
            alert('Failed to toggle app: ' + errorMsg + (errorDetails ? '\nDetails: ' + errorDetails : ''));
            console.error('Toggle failed:', data);
        } else {
            // Show toast notification
            showToast(data.message);
        }
    } catch (error) {
        console.error('Error toggling app:', error);
        // Revert on error
        toggleSwitch.classList.toggle('active');
        cardElement.classList.toggle('enabled');
        enabledCount += currentState ? 1 : -1;
        document.getElementById('enabledCount').textContent = enabledCount;
        alert('Failed to connect to server: ' + error.message);
    }
}

function showToast(message) {
    // Simple toast notification
    const toast = document.createElement('div');
    toast.className = 'position-fixed bottom-0 end-0 m-4 alert alert-success';
    toast.style.zIndex = '9999';
    toast.innerHTML = `<i class="fas fa-check-circle me-2"></i>${message}`;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}
</script>
@endsection

@php
    // Resolve the tour for the current route (if any) and whether this user has seen it.
    $advisorTour = null;
    $advisorTourId = null;
    foreach (config('tours', []) as $tourId => $tour) {
        if (request()->routeIs(...$tour['routes'])) {
            $advisorTourId = $tourId;
            $advisorTour = $tour;
            break;
        }
    }
    $advisorSeen = $advisorTour ? auth()->user()->hasCompletedTour($advisorTourId) : true;
    $advisorCanChat = isset($__can) && $__can('ai');
    $advisorFirstName = explode(' ', trim(auth()->user()->name))[0] ?? 'there';
@endphp

@if($advisorTour)
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.css">

<style>
    .advisor-widget {
        position: fixed;
        bottom: 20px;
        left: 20px;
        z-index: 1049;
        font-family: 'Poppins', sans-serif;
    }
    .advisor-fab {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #7b2e2e;
        color: #fff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.18);
        font-size: 1.15rem;
        transition: transform 0.2s ease;
    }
    .advisor-fab:hover { transform: scale(1.08); }
    .advisor-card {
        position: absolute;
        bottom: 60px;
        left: 0;
        width: 320px;
        max-width: calc(100vw - 40px);
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 14px;
        box-shadow: 0 12px 32px rgba(0,0,0,0.16);
        padding: 16px 18px;
        display: none;
        animation: advisorIn 0.25s ease;
    }
    .advisor-card.open { display: block; }
    .advisor-card-head {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
    }
    .advisor-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: linear-gradient(135deg, #7b2e2e, #ff511a);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .advisor-card-head strong {
        font-size: 0.9rem;
        color: var(--text-primary, #1f2937);
    }
    .advisor-card-head small {
        display: block;
        font-size: 0.72rem;
        color: var(--text-muted, #6b7280);
    }
    .advisor-card p {
        font-size: 0.86rem;
        color: var(--text-primary, #1f2937);
        margin: 0 0 12px;
        line-height: 1.5;
    }
    .advisor-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    .advisor-actions .btn { font-size: 0.8rem; }
    .advisor-chat-link {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--border-color, #e5e7eb);
        font-size: 0.78rem;
    }
    .advisor-chat-link a {
        color: #ff511a;
        text-decoration: none;
        font-weight: 600;
    }
    .advisor-close {
        position: absolute;
        top: 8px;
        right: 10px;
        border: none;
        background: none;
        color: var(--text-muted, #6b7280);
        cursor: pointer;
        font-size: 1rem;
    }
    @keyframes advisorIn {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Driver.js theme */
    .driver-popover.shopybook-tour {
        font-family: 'Poppins', sans-serif;
        border-radius: 14px;
        max-width: 340px;
        padding: 18px 20px 14px;
    }
    .driver-popover.shopybook-tour .driver-popover-title {
        font-family: 'Playfair Display', serif;
        font-size: 1.05rem;
        color: #7b2e2e;
    }
    .driver-popover.shopybook-tour .driver-popover-description {
        font-size: 0.88rem;
        line-height: 1.55;
        color: #374151;
    }
    .driver-popover.shopybook-tour .driver-popover-progress-text {
        font-size: 0.75rem;
        color: #9ca3af;
    }
    .driver-popover.shopybook-tour button {
        font-family: 'Poppins', sans-serif;
        border-radius: 8px;
        text-shadow: none;
        font-size: 0.8rem;
        padding: 6px 14px;
    }
    .driver-popover.shopybook-tour .driver-popover-next-btn {
        background: #ff511a;
        color: #fff;
        border: none;
    }
    .driver-popover.shopybook-tour .driver-popover-prev-btn {
        background: #fff;
        color: #7b2e2e;
        border: 1px solid #e5e7eb;
    }
    @media (max-width: 768px) {
        .advisor-widget { bottom: 84px; left: 12px; }
    }
</style>

<div class="advisor-widget" id="advisorWidget">
    <div class="advisor-card" id="advisorCard" role="dialog" aria-labelledby="advisorTitle">
        <button class="advisor-close" onclick="advisorClose()" aria-label="Close">&times;</button>
        <div class="advisor-card-head">
            <div class="advisor-avatar"><i class="fas fa-compass"></i></div>
            <div>
                <strong id="advisorTitle">{{ $advisorTour['title'] }}</strong>
                <small>Your Shopybook guide</small>
            </div>
        </div>
        <p>Hi {{ $advisorFirstName }} — {{ $advisorTour['intro'] }}</p>
        <div class="advisor-actions">
            <button class="btn btn-sm btn-primary" onclick="advisorStartTour()">Show me</button>
            <button class="btn btn-sm btn-outline-secondary" onclick="advisorDismiss()">Not now</button>
        </div>
        @if($advisorCanChat)
        <div class="advisor-chat-link">
            Have a question instead? <a href="#" onclick="advisorOpenChat(event)">Ask the advisor</a>
        </div>
        @endif
    </div>
    <button class="advisor-fab" id="advisorFab" onclick="advisorToggle()" title="Show me around this page" aria-label="Page guide">
        <i class="fas fa-compass"></i>
    </button>
</div>

<script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.js.iife.js"></script>
<script>
(function () {
    const tourId    = @json($advisorTourId);
    const steps     = @json($advisorTour['steps']);
    const seen      = @json($advisorSeen);
    const completeUrl = @json(route('tours.complete'));
    const csrf      = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const card = document.getElementById('advisorCard');
    let driverInstance = null;

    function markComplete() {
        fetch(completeUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ tour: tourId })
        }).catch(() => {});
    }

    window.advisorToggle = function () { card.classList.toggle('open'); };
    window.advisorClose  = function () { card.classList.remove('open'); };

    window.advisorDismiss = function () {
        card.classList.remove('open');
        if (!seen) markComplete();
    };

    window.advisorOpenChat = function (e) {
        e.preventDefault();
        card.classList.remove('open');
        if (typeof toggleAIChat === 'function') {
            const chat = document.getElementById('ai-chat-interface');
            if (chat && chat.style.display === 'none') toggleAIChat();
        }
    };

    window.advisorStartTour = function () {
        card.classList.remove('open');

        const available = steps.filter(s => document.querySelector(s.element));
        if (!available.length) { markComplete(); return; }

        driverInstance = window.driver.js.driver({
            showProgress: true,
            animate: true,
            allowClose: true,
            overlayOpacity: 0.55,
            popoverClass: 'shopybook-tour',
            nextBtnText: 'Next',
            prevBtnText: 'Back',
            doneBtnText: 'Got it',
            progressText: '@{{current}} of @{{total}}',
            steps: available.map(s => ({
                element: s.element,
                popover: {
                    title: s.title,
                    description: s.text,
                    side: s.side || 'bottom',
                    align: 'start'
                }
            })),
            onDestroyed: () => { if (!seen) markComplete(); }
        });
        driverInstance.drive();
    };

    if (!seen) {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => card.classList.add('open'), 1200);
        });
    }
})();
</script>
@endif

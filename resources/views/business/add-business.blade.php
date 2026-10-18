@extends('layouts.dash')

@section('content')
<div class="container-fluid" style="max-width: 640px; margin: 0 auto;">
    <div class="card">
        <div class="card-body p-4 text-center">
            <i class="fas fa-building fa-3x text-primary mb-3"></i>
            <h4 class="mb-2">Business Limit Reached</h4>
            <p class="text-muted">
                You currently manage <strong>{{ $ownedCount }}</strong>
                business{{ $ownedCount === 1 ? '' : 'es' }} (limit: {{ $limit }}).
            </p>

            @if($isEnterprise)
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Add another business for <strong>KES {{ number_format($addonPrice) }}/month</strong>
                    (half the Enterprise rate). Pay once via M-Pesa and the slot unlocks immediately.
                </div>

                <div class="mx-auto" style="max-width: 360px;">
                    <div class="mb-3 text-start">
                        <label for="addonPhone" class="form-label">M-Pesa Phone Number</label>
                        <input type="tel" class="form-control" id="addonPhone"
                               placeholder="e.g. 0712345678" maxlength="12">
                    </div>
                    <button type="button" class="btn btn-primary w-100" id="addonPayBtn" onclick="payForSlot()">
                        <i class="fas fa-mobile-alt me-2"></i>Pay KES {{ number_format($addonPrice) }} via M-Pesa
                    </button>
                    <div id="addonStatus" class="mt-3 small text-muted" style="display:none;"></div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Managing multiple businesses requires the <strong>Enterprise plan</strong>
                    — it includes 2 businesses, and additional ones at half rate.
                </div>
                <a href="{{ route('billing.upgrade') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-up me-2"></i>Upgrade to Enterprise
                </a>
            @endif

            <div class="mt-3">
                <a href="{{ route('dashboard') }}" class="btn btn-link text-muted">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

@if($isEnterprise)
<script>
let addonReference = null;
let addonPollTimer = null;

async function payForSlot() {
    const phone = document.getElementById('addonPhone').value.trim();
    const btn = document.getElementById('addonPayBtn');
    const status = document.getElementById('addonStatus');

    if (!phone) {
        alert('Please enter your M-Pesa phone number.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending STK Push…';
    status.style.display = 'block';
    status.textContent = 'Requesting M-Pesa prompt…';

    try {
        const res = await fetch('{{ route('subscription.addon-business') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ phone_number: phone })
        });

        const result = await res.json();

        if (result.success) {
            addonReference = result.reference;
            status.innerHTML = '<i class="fas fa-mobile-alt me-1"></i> Check your phone — enter your M-Pesa PIN. Waiting for confirmation…';
            pollAddonStatus();
        } else {
            status.textContent = result.message || 'Failed to initiate payment.';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-mobile-alt me-2"></i>Pay KES {{ number_format($addonPrice) }} via M-Pesa';
        }
    } catch (e) {
        status.textContent = 'Error: ' + e.message;
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-mobile-alt me-2"></i>Pay KES {{ number_format($addonPrice) }} via M-Pesa';
    }
}

function pollAddonStatus() {
    let attempts = 0;
    addonPollTimer = setInterval(async () => {
        attempts++;
        if (attempts > 24 || !addonReference) {
            clearInterval(addonPollTimer);
            document.getElementById('addonStatus').textContent = 'Payment is taking longer than expected — check your subscriptions page.';
            document.getElementById('addonPayBtn').disabled = false;
            return;
        }

        try {
            const res = await fetch('{{ route('subscription.check.status') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ reference: addonReference })
            });
            const result = await res.json();

            if (result.status === 'completed') {
                clearInterval(addonPollTimer);
                document.getElementById('addonStatus').innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i>Slot unlocked! Redirecting…</span>';
                setTimeout(() => { window.location.href = '{{ route('business.create') }}'; }, 1200);
            } else if (result.status === 'failed') {
                clearInterval(addonPollTimer);
                document.getElementById('addonStatus').textContent = result.result_desc || 'Payment failed.';
                document.getElementById('addonPayBtn').disabled = false;
            }
        } catch (e) { /* keep polling */ }
    }, 5000);
}
</script>
@endif

@endsection

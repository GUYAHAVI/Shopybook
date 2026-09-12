@extends('layouts.dash')

@section('title', 'Complete Purchase — AI Credits')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-coins me-2"></i>Buy {{ $pack['credits'] }} AI Credits — ${{ $pack['price_usd'] }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">You're about to purchase <strong>{{ $pack['credits'] }} AI credits</strong> for <strong>${{ $pack['price_usd'] }}</strong>. These credits never expire and will be added to your account immediately after payment.</p>

                    @if($paystackKey)
                    <div id="paystack-embed-container"></div>
                    <script src="https://js.paystack.co/v1/inline.js"></script>
                    <script>
                    const handler = PaystackPop.setup({
                        key: '{{ $paystackKey }}',
                        email: '{{ $email }}',
                        amount: {{ $amount }},
                        ref: '{{ $reference }}',
                        currency: 'USD',
                        callback: function(response) {
                            window.location.href = '{{ $callbackUrl }}?reference=' + response.reference;
                        },
                        onClose: function() {
                            window.location.href = '{{ route('ai-credits.index') }}';
                        }
                    });
                    handler.openIframe();
                    </script>
                    @else
                    <form method="POST" action="{{ route('ai-credits.payment.callback') }}">
                        @csrf
                        <input type="hidden" name="reference" value="{{ $reference }}">
                        <p class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Payment gateway not configured. In development mode, click below to add credits instantly.
                        </p>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check me-1"></i>Add {{ $pack['credits'] }} credits (dev mode)
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

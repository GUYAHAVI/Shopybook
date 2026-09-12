@extends('layouts.dash')

@section('title', 'AI Credits — Shopybook')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <h2 style="font-family:'Playfair Display',serif;color:var(--primary-color);">
                    <i class="fas fa-coins me-2"></i>AI Credits
                </h2>
                <p class="text-muted">Premium AI generation uses credits. You get monthly credits with your plan, and can buy more anytime.</p>
            </div>

            {{-- Current credits --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div style="font-family:'Playfair Display',serif;font-size:2rem;color:var(--primary-color);font-weight:700;">{{ $summary['total'] }}</div>
                            <div class="text-muted small text-uppercase">Total credits</div>
                        </div>
                        <div class="col-4">
                            <div style="font-family:'Playfair Display',serif;font-size:2rem;color:var(--success-color);font-weight:700;">{{ $summary['monthly'] }}</div>
                            <div class="text-muted small text-uppercase">Monthly left</div>
                        </div>
                        <div class="col-4">
                            <div style="font-family:'Playfair Display',serif;font-size:2rem;color:var(--primary-light);font-weight:700;">{{ $summary['purchased'] }}</div>
                            <div class="text-muted small text-uppercase">Purchased</div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge" style="background:var(--primary-color);color:#fff;text-transform:uppercase;">{{ $summary['plan'] }}</span>
                            <span class="text-muted small ms-2">{{ $summary['monthly_allowance'] }} credits/month</span>
                        </div>
                        @if(!$summary['is_premium'])
                            <a href="{{ route('pricing') }}" class="btn btn-sm btn-primary">Upgrade plan</a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- What credits are used for --}}
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-info-circle me-2"></i>What credits are used for</div>
                <div class="card-body">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td><i class="fas fa-image me-2 text-primary"></i>Logo generation (high quality)</td>
                            <td class="text-end"><strong>2 credits</strong></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-photo-film me-2 text-primary"></i>Marketing image (high quality)</td>
                            <td class="text-end"><strong>1 credit</strong></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-globe me-2 text-primary"></i>Website section image</td>
                            <td class="text-end"><strong>1 credit</strong></td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-comments me-2 text-muted"></i>AI chat (text only)</td>
                            <td class="text-end text-muted">Free</td>
                        </tr>
                        <tr>
                            <td><i class="fas fa-magic me-2 text-muted"></i>Basic image (Pollinations)</td>
                            <td class="text-end text-muted">Free</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Top-up packs --}}
            <div class="card">
                <div class="card-header"><i class="fas fa-plus-circle me-2"></i>Buy more credits</div>
                <div class="card-body">
                    @if(!$summary['replicate_configured'] && $summary['is_premium'])
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Premium AI is being set up. Your credits will work once the provider is configured. Basic (free) generation is still available.
                        </div>
                    @endif

                    <div class="row g-3">
                        @foreach($packs as $id => $pack)
                        <div class="col-md-3">
                            <div class="card h-100 text-center {{ $id == 50 ? 'border-primary' : '' }}">
                                @if($id == 50)
                                <div class="badge bg-primary position-absolute top-0 start-50 translate-middle-x" style="margin-top:-8px;">Best value</div>
                                @endif
                                <div class="card-body">
                                    <div style="font-family:'Playfair Display',serif;font-size:1.8rem;color:var(--primary-color);font-weight:700;">{{ $pack['credits'] }}</div>
                                    <div class="text-muted small mb-2">credits</div>
                                    <div style="font-size:1.3rem;font-weight:600;">${{ $pack['price_usd'] }}</div>
                                    <div class="text-muted small mb-3">${{ number_format($pack['price_usd'] / $pack['credits'], 2) }} per credit</div>
                                    <form method="POST" action="{{ route('ai-credits.purchase') }}">
                                        @csrf
                                        <x-honeypot />
                                        <input type="hidden" name="pack_id" value="{{ $id }}">
                                        <button type="submit" class="btn btn-sm {{ $id == 50 ? 'btn-primary' : 'btn-outline-primary' }} w-100">
                                            Buy
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('dashboard') }}" class="text-muted text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i>Back to dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

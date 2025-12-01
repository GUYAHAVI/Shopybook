@extends('layouts.dash')

@section('title', 'Social Media Marketing')

@section('content')
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<!-- Sub-navigation for Marketing -->
<div class="sub-navigation mb-4">
    <div class="nav-tabs">
        <a href="{{ route('marketing.social-media') }}" class="nav-tab active">
            <i class="fas fa-share-alt me-1"></i> Social Media
        </a>
        <a href="{{ route('marketing.promotions') }}" class="nav-tab">
            <i class="fas fa-bullhorn me-1"></i> Promotions
        </a>
        <a href="{{ route('marketing.advertising') }}" class="nav-tab">
            <i class="fas fa-ad me-1"></i> Advertising
        </a>
        <a href="{{ route('marketing.bulk-sms') }}" class="nav-tab">
            <i class="fas fa-sms me-1"></i> Bulk SMS
        </a>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0" style="color: var(--text-primary);">Social Media Marketing</h1>
    <div>
        <button class="btn btn-secondary me-2" onclick="testFunction()">
            <i class="fas fa-bug me-2"></i>Test JS
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPostModal">
            <i class="fas fa-plus me-2"></i>Create Post
        </button>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h6 class="m-0 font-weight-bold" style="color: var(--text-primary);">Connected Platforms</h6>
            </div>
            <div class="card-body">
                @php
                    $connectedAccounts = Auth::user()->business->socialMediaAccounts()->where('is_active', true)->get();
                    $connectedPlatforms = $connectedAccounts->pluck('platform')->toArray();
                    
                    // Debug information
                    $debugInfo = [
                        'facebook_client_id' => config('services.facebook.client_id') ? 'Set' : 'Not set',
                        'linkedin_client_id' => config('services.linkedin.client_id') ? 'Set' : 'Not set',
                        'instagram_client_id' => config('services.instagram.client_id') ? 'Set' : 'Not set',
                        'twitter_client_id' => config('services.twitter.client_id') ? 'Set' : 'Not set',
                    ];
                @endphp
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="platform-card card h-100" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                            <div class="card-body text-center">
                                <i class="fab fa-facebook fa-3x mb-3" style="color: #1877f2;"></i>
                                <h5 style="color: var(--text-primary);">Facebook</h5>
                                @if(in_array('facebook', $connectedPlatforms))
                                    @php $account = $connectedAccounts->where('platform', 'facebook')->first(); @endphp
                                    <p style="color: var(--text-secondary);">Connected as: {{ $account->username }}</p>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-success btn-sm" disabled>
                                            <i class="fas fa-check me-1"></i>Connected
                                        </button>
                                        <form action="{{ route('social.disconnect', $account) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to disconnect this account?')">
                                                <i class="fas fa-unlink me-1"></i>Disconnect
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <p style="color: var(--text-secondary);">Connect your Facebook page</p>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="connectSocialMedia('facebook')">
                                        <i class="fab fa-facebook me-1"></i>Connect
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="platform-card card h-100" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                            <div class="card-body text-center">
                                <i class="fab fa-instagram fa-3x mb-3" style="color: #e4405f;"></i>
                                <h5 style="color: var(--text-primary);">Instagram</h5>
                                @if(in_array('instagram', $connectedPlatforms))
                                    @php $account = $connectedAccounts->where('platform', 'instagram')->first(); @endphp
                                    <p style="color: var(--text-secondary);">Connected as: {{ $account->username }}</p>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-success btn-sm" disabled>
                                            <i class="fas fa-check me-1"></i>Connected
                                        </button>
                                        <form action="{{ route('social.disconnect', $account) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to disconnect this account?')">
                                                <i class="fas fa-unlink me-1"></i>Disconnect
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <p style="color: var(--text-secondary);">Connect your Instagram account</p>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="connectSocialMedia('instagram')">
                                        <i class="fab fa-instagram me-1"></i>Connect
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="platform-card card h-100" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                            <div class="card-body text-center">
                                <i class="fab fa-x-twitter fa-3x mb-3" style="color: #000000;"></i>
                                <h5 style="color: var(--text-primary);">X (Twitter)</h5>
                                @if(in_array('twitter', $connectedPlatforms))
                                    @php $account = $connectedAccounts->where('platform', 'twitter')->first(); @endphp
                                    <p style="color: var(--text-secondary);">Connected as: {{ $account->username }}</p>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-success btn-sm" disabled>
                                            <i class="fas fa-check me-1"></i>Connected
                                        </button>
                                        <form action="{{ route('social.disconnect', $account) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to disconnect this account?')">
                                                <i class="fas fa-unlink me-1"></i>Disconnect
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <p style="color: var(--text-secondary);">Connect your X account</p>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="connectSocialMedia('twitter')">
                                        <i class="fab fa-x-twitter me-1"></i>Connect
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="platform-card card h-100" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                            <div class="card-body text-center">
                                <i class="fab fa-linkedin fa-3x mb-3" style="color: #0077b5;"></i>
                                <h5 style="color: var(--text-primary);">LinkedIn</h5>
                                @if(in_array('linkedin', $connectedPlatforms))
                                    @php $account = $connectedAccounts->where('platform', 'linkedin')->first(); @endphp
                                    <p style="color: var(--text-secondary);">Connected as: {{ $account->username }}</p>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-success btn-sm" disabled>
                                            <i class="fas fa-check me-1"></i>Connected
                                        </button>
                                        <form action="{{ route('social.disconnect', $account) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to disconnect this account?')">
                                                <i class="fas fa-unlink me-1"></i>Disconnect
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <p style="color: var(--text-secondary);">Connect your LinkedIn page</p>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="connectSocialMedia('linkedin')">
                                        <i class="fab fa-linkedin me-1"></i>Connect
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="platform-card card h-100" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                            <div class="card-body text-center">
                                <i class="fab fa-telegram fa-3x mb-3" style="color: #0088cc;"></i>
                                <h5 style="color: var(--text-primary);">Telegram</h5>
                                @if(in_array('telegram', $connectedPlatforms))
                                    @php $account = $connectedAccounts->where('platform', 'telegram')->first(); @endphp
                                    <p style="color: var(--text-secondary);">Connected as: {{ $account->username }}</p>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-success btn-sm" disabled>
                                            <i class="fas fa-check me-1"></i>Connected
                                        </button>
                                        <form action="{{ route('social.disconnect', $account) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to disconnect this account?')">
                                                <i class="fas fa-unlink me-1"></i>Disconnect
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <p style="color: var(--text-secondary);">Connect your Telegram channel</p>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="connectSocialMedia('telegram')">
                                        <i class="fab fa-telegram me-1"></i>Connect
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="platform-card card h-100" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                            <div class="card-body text-center">
                                <i class="fab fa-discord fa-3x mb-3" style="color: #7289da;"></i>
                                <h5 style="color: var(--text-primary);">Discord</h5>
                                @if(in_array('discord', $connectedPlatforms))
                                    @php $account = $connectedAccounts->where('platform', 'discord')->first(); @endphp
                                    <p style="color: var(--text-secondary);">Connected as: {{ $account->username }}</p>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-success btn-sm" disabled>
                                            <i class="fas fa-check me-1"></i>Connected
                                        </button>
                                        <form action="{{ route('social.disconnect', $account) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to disconnect this account?')">
                                                <i class="fas fa-unlink me-1"></i>Disconnect
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <p style="color: var(--text-secondary);">Connect your Discord server</p>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="connectSocialMedia('discord')">
                                        <i class="fab fa-discord me-1"></i>Connect
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h6 class="m-0 font-weight-bold" style="color: var(--text-primary);">Recent Posts</h6>
            </div>
            <div class="card-body">
                @php
                    $recentPosts = Auth::user()->business->marketingPosts()->latest()->take(5)->get();
                @endphp
                
                @if($recentPosts->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentPosts as $post)
                            <div class="list-group-item d-flex justify-content-between align-items-start" style="background: transparent; border: none; padding: 0.5rem 0;">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold" style="color: var(--text-primary);">{{ Str::limit($post->content, 50) }}</div>
                                    <small style="color: var(--text-muted);">{{ $post->created_at->diffForHumans() }}</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">{{ $post->publications->count() }} platforms</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('marketing.posts.index') }}" class="btn btn-outline-primary btn-sm">View All Posts</a>
                    </div>
                @else
                    <div class="text-center py-4" style="color: var(--text-muted);">
                        <i class="fas fa-share-alt fa-2x mb-3" style="color: var(--text-muted);"></i>
                        <p style="color: var(--text-muted);">No posts yet</p>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createPostModal">
                            Create Your First Post
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Debug Information (remove in production) -->
@if(config('app.debug'))
    <div class="card shadow mb-4" style="background: var(--card-bg); border: 1px solid var(--border-color);">
        <div class="card-header py-3" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
            <h6 class="m-0 font-weight-bold" style="color: var(--text-primary);">Debug Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 style="color: var(--text-primary);">OAuth Configuration Status:</h6>
                    <ul class="list-unstyled">
                        @foreach($debugInfo as $key => $status)
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> 
                                <span class="badge {{ $status === 'Set' ? 'bg-success' : 'bg-danger' }}">{{ $status }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 style="color: var(--text-primary);">Connected Accounts:</h6>
                    <ul class="list-unstyled">
                        @foreach($connectedAccounts as $account)
                            <li><strong>{{ ucfirst($account->platform) }}:</strong> {{ $account->username }}</li>
                        @endforeach
                        @if($connectedAccounts->count() === 0)
                            <li><em style="color: var(--text-muted);">No accounts connected</em></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Upgrade Plan Modal -->
<div class="modal fade" id="upgradeModal" tabindex="-1" aria-labelledby="upgradeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title" id="upgradeModalLabel" style="color: var(--text-primary);">
                    <i class="fas fa-crown me-2"></i>Upgrade to Premium
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card h-100" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                            <div class="card-body text-center">
                                <h4 style="color: var(--text-primary);">Free Plan</h4>
                                <div class="mb-3">
                                    <span class="h2" style="color: var(--text-primary);">KSh 0</span>
                                    <span style="color: var(--text-muted);">/month</span>
                                </div>
                                <ul class="list-unstyled" style="color: var(--text-secondary);">
                                    <li><i class="fas fa-check text-success me-2"></i>1 Social Media Connection</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Basic Analytics</li>
                                    <li><i class="fas fa-check text-success me-2"></i>5 Posts per Month</li>
                                    <li class="text-muted"><i class="fas fa-times text-danger me-2"></i>Advanced Features</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-primary" style="background: var(--bg-tertiary); border: 2px solid var(--primary-color) !important;">
                            <div class="card-body text-center">
                                <h4 style="color: var(--text-primary);">Premium Plan</h4>
                                <div class="mb-3">
                                    <span class="h2" style="color: var(--primary-color);">KSh 1</span>
                                    <span style="color: var(--text-muted);">/month</span>
                                    <br><small class="text-muted">(Testing Price)</small>
                                </div>
                                <ul class="list-unstyled" style="color: var(--text-secondary);">
                                    <li><i class="fas fa-check text-success me-2"></i>Unlimited Social Media Connections</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Advanced Analytics</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Unlimited Posts</li>
                                    <li><i class="fas fa-check text-success me-2"></i>AI Content Generation</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Priority Support</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6 style="color: var(--text-primary);">Payment Method</h6>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="mpesa_payment" value="mpesa" checked>
                        <label class="form-check-label" for="mpesa_payment" style="color: var(--text-primary);">
                            <i class="fas fa-mobile-alt me-2"></i>M-Pesa
                        </label>
                    </div>
                </div>
                
                <div class="mt-3">
                    <label for="phone_number" class="form-label" style="color: var(--text-primary);">M-Pesa Phone Number</label>
                    <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                           placeholder="07XXXXXXXX or 254XXXXXXXXX" style="background: var(--card-bg); border: 1px solid var(--border-color); color: var(--text-primary);">
                    <small class="text-muted">Enter your M-Pesa registered phone number (07XXXXXXXX or 254XXXXXXXXX)</small>
                </div>
            </div>
            <div class="modal-footer" style="background: var(--bg-tertiary); border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="upgradeBtn" onclick="initiateUpgrade()">
                    <i class="fas fa-crown me-1"></i>Upgrade Now
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Create Post Modal -->
<div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="background: var(--card-bg); border: 1px solid var(--border-color);">
            <div class="modal-header" style="background: var(--bg-tertiary); border-bottom: 1px solid var(--border-color);">
                <h5 class="modal-title" id="createPostModalLabel" style="color: var(--text-primary);">Create New Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createPostForm">
                    <div class="mb-3">
                        <label for="postTitle" class="form-label" style="color: var(--text-primary);">Post Title</label>
                        <input type="text" class="form-control" id="postTitle" name="title" placeholder="Enter a catchy title for your post" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                    
                    <div class="mb-3">
                        <label for="postContent" class="form-label" style="color: var(--text-primary);">Post Content</label>
                        <div class="position-relative">
                            <textarea class="form-control" id="postContent" name="content" rows="4" placeholder="What's on your mind?" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);" required></textarea>
                            <div class="position-absolute top-0 end-0 mt-2 me-2" style="z-index: 10;">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary" onclick="generateMarketingContent()" title="Generate content with AI">
                                        <i class="fas fa-magic"></i> Generate
                                    </button>
                                    <button type="button" class="btn btn-outline-success" onclick="enhanceMarketingContent()" title="Enhance existing content">
                                        <i class="fas fa-wand-magic-sparkles"></i> Enhance
                                    </button>
                                    <button type="button" class="btn btn-outline-info" onclick="generateImagePrompts()" title="Generate AI image prompts">
                                        <i class="fas fa-image"></i> Image Ideas
                                    </button>
                                </div>
                            </div>
                        </div>
                        <small class="form-text text-muted">Write your post content here or use AI to generate/enhance it</small>
                        <div id="aiContentStatus" class="mt-2" style="display: none;">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <small class="text-muted">AI is processing...</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="postHashtags" class="form-label" style="color: var(--text-primary);">Hashtags</label>
                        <input type="text" class="form-control" id="postHashtags" name="hashtags" placeholder="#marketing #business #socialmedia" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                        <small class="form-text text-muted">Separate hashtags with spaces</small>
                    </div>

                    <!-- AI Image Prompts Section -->
                    <div id="aiImagePromptsSection" class="mb-3" style="display: none;">
                        <div class="card" style="background: var(--bg-tertiary); border: 1px solid #13e8e9;">
                            <div class="card-header py-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <h6 class="mb-0 text-white">
                                    <i class="fas fa-palette me-2"></i>AI Image Suggestions
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="small mb-2" style="color: var(--text-secondary);">Use these prompts with AI image generators like DALL-E, Midjourney, or Stable Diffusion:</p>
                                <div id="aiImagePromptsList" class="list-group">
                                    <!-- AI generated prompts will appear here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Media Section -->
                    <div class="mb-3">
                        <label class="form-label" style="color: var(--text-primary);">Media Content</label>
                        
                        <!-- Media Type Selection -->
                        <div class="btn-group w-100 mb-3" role="group">
                            <input type="radio" class="btn-check" name="mediaType" id="noMedia" value="none" checked>
                            <label class="btn btn-outline-secondary" for="noMedia">
                                <i class="fas fa-text-width me-1"></i>Text Only
                            </label>
                            
                            <input type="radio" class="btn-check" name="mediaType" id="uploadMedia" value="upload">
                            <label class="btn btn-outline-secondary" for="uploadMedia">
                                <i class="fas fa-upload me-1"></i>Upload Media
                            </label>
                            
                            <input type="radio" class="btn-check" name="mediaType" id="generateImage" value="generate-image">
                            <label class="btn btn-outline-secondary" for="generateImage">
                                <i class="fas fa-image me-1"></i>Generate Image
                            </label>
                            
                            <input type="radio" class="btn-check" name="mediaType" id="generateVideo" value="generate">
                            <label class="btn btn-outline-secondary" for="generateVideo">
                                <i class="fas fa-magic me-1"></i>Generate Video
                            </label>
                        </div>
                        
                        <!-- Upload Media Section -->
                        <div id="uploadMediaSection" class="media-section" style="display: none;">
                            <div class="mb-3">
                                <label for="postMedia" class="form-label" style="color: var(--text-primary);">Upload Image/Video</label>
                                <input type="file" class="form-control" id="postMedia" name="media" accept="image/*,video/*" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                                <small class="form-text text-muted">Supported: JPG, PNG, GIF, MP4, MOV (Max 10MB)</small>
                            </div>
                        </div>
                        
                        <!-- Generate Image Section -->
                        <div id="generateImageSection" class="media-section" style="display: none;">
                            <div class="card" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                                <div class="card-body">
                                    <h6 class="card-title" style="color: var(--text-primary);">
                                        <i class="fas fa-image me-2"></i>AI Image Generation
                                    </h6>
                                    
                                    <div class="mb-3">
                                        <label for="imagePrompt" class="form-label" style="color: var(--text-primary);">Image Description</label>
                                        <textarea class="form-control" id="imagePrompt" name="image_prompt" rows="3" placeholder="Describe the image you want to generate... (or leave empty to auto-generate from post content)" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);"></textarea>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="useImagePromptForGeneration()" title="Use AI suggested prompts">
                                                <i class="fas fa-lightbulb me-1"></i>Use AI Prompt Suggestion
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="autoGenerateFromPost()" title="Auto-generate prompt from post content">
                                                <i class="fas fa-wand-magic-sparkles me-1"></i>Auto from Post
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="enhanceImagePrompt()" title="Enhance your prompt with AI">
                                                <i class="fas fa-sparkles me-1"></i>Enhance Prompt
                                            </button>
                                        </div>
                                        <small class="form-text text-muted">Describe the image style, colors, mood, and composition - or use auto-generation</small>
                                        <div id="promptEnhanceStatus" class="mt-2" style="display: none;">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-spinner fa-spin me-2"></i>Enhancing your prompt with AI...
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="imageStyle" class="form-label" style="color: var(--text-primary);">Image Style</label>
                                            <select class="form-select" id="imageStyle" name="image_style" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                                                <option value="realistic">Realistic Photo</option>
                                                <option value="digital-art">Digital Art</option>
                                                <option value="illustration">Illustration</option>
                                                <option value="3d-render">3D Render</option>
                                                <option value="minimalist">Minimalist</option>
                                                <option value="vibrant">Vibrant & Colorful</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="imageSize" class="form-label" style="color: var(--text-primary);">Image Size</label>
                                            <select class="form-select" id="imageSize" name="image_size" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                                                <option value="1024x1024">Square (1024x1024)</option>
                                                <option value="1792x1024">Landscape (1792x1024)</option>
                                                <option value="1024x1792">Portrait (1024x1792)</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-primary" id="generateImageBtn">
                                            <i class="fas fa-magic me-2"></i>Generate Image
                                        </button>
                                    </div>
                                    
                                    <!-- Image Generation Progress -->
                                    <div id="imageGenerationProgress" class="mt-3" style="display: none;">
                                        <div class="progress mb-2">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                                        </div>
                                        <small class="text-muted">Generating your image... This may take 10-30 seconds.</small>
                                    </div>
                                    
                                    <!-- Generated Image Preview -->
                                    <div id="generatedImagePreview" class="mt-3" style="display: none;">
                                        <div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                                            <div class="card-body">
                                                <h6 class="card-title" style="color: var(--text-primary);">Generated Image</h6>
                                                <img id="previewImage" src="" class="w-100" style="max-height: 400px; object-fit: contain;" alt="Generated image">
                                                <input type="hidden" id="generatedImageUrl" name="generated_image_url">
                                                <input type="hidden" id="generatedImageLocalPath" name="generated_image_local_path">
                                                <input type="hidden" id="generatedImageRelativePath" name="generated_image_relative_path">
                                                <div class="mt-2">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="regenerateImageBtn">
                                                        <i class="fas fa-redo me-1"></i>Regenerate
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-success" id="useImageBtn">
                                                        <i class="fas fa-check me-1"></i>Use This Image
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-info" id="downloadImageBtn">
                                                        <i class="fas fa-download me-1"></i>Download
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Generate Video Section -->
                        <div id="generateVideoSection" class="media-section" style="display: none;">
                            <div class="card" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                                <div class="card-body">
                                    <h6 class="card-title" style="color: var(--text-primary);">
                                        <i class="fas fa-magic me-2"></i>AI Video Generation
                                    </h6>
                                    
                                    <div class="mb-3">
                                        <label for="videoPrompt" class="form-label" style="color: var(--text-primary);">Video Description</label>
                                        <textarea class="form-control" id="videoPrompt" name="video_prompt" rows="3" placeholder="Describe the video you want to generate... (or leave empty to auto-generate from post content)" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);"></textarea>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="useVideoPromptForGeneration()" title="Use AI suggested video prompts">
                                                <i class="fas fa-lightbulb me-1"></i>Use AI Prompt Suggestion
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="autoGenerateVideoFromPost()" title="Auto-generate video prompt from post content">
                                                <i class="fas fa-wand-magic-sparkles me-1"></i>Auto from Post
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="enhanceVideoPrompt()" title="Enhance your video prompt with AI">
                                                <i class="fas fa-sparkles me-1"></i>Enhance Prompt
                                            </button>
                                        </div>
                                        <small class="form-text text-muted">Describe the scene, camera movements, style, and mood - or use auto-generation</small>
                                        <div id="videoPromptEnhanceStatus" class="mt-2" style="display: none;">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-spinner fa-spin me-2"></i>Enhancing your video prompt with AI...
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="videoStyle" class="form-label" style="color: var(--text-primary);">Video Style</label>
                                            <select class="form-select" id="videoStyle" name="video_style" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                                                <option value="professional">Professional</option>
                                                <option value="dynamic">Dynamic</option>
                                                <option value="minimal">Minimal</option>
                                                <option value="creative">Creative</option>
                                                <option value="social">Social</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="videoDuration" class="form-label" style="color: var(--text-primary);">Duration (seconds)</label>
                                            <select class="form-select" id="videoDuration" name="video_duration" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                                                <option value="4">4 seconds</option>
                                                <option value="8">8 seconds</option>
                                                <option value="12">12 seconds</option>
                                                <option value="16">16 seconds</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="productImage" class="form-label" style="color: var(--text-primary);">Product Image (Optional)</label>
                                        <input type="file" class="form-control" id="productImage" name="product_image" accept="image/*" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                                        <small class="form-text text-muted">Upload a product image to generate image-to-video</small>
                                    </div>
                                    
                                    <button type="button" class="btn btn-primary" id="generateVideoBtn">
                                        <i class="fas fa-play me-2"></i>Generate Video
                                    </button>
                                    
                                    <!-- Video Generation Progress -->
                                    <div id="videoGenerationProgress" class="mt-3" style="display: none;">
                                        <div class="progress mb-2">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <small class="text-muted">Generating your video... This may take 1-2 minutes.</small>
                                    </div>
                                    
                                    <!-- Generated Video Preview -->
                                    <div id="generatedVideoPreview" class="mt-3" style="display: none;">
                                        <div class="card" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                                            <div class="card-body">
                                                <h6 class="card-title" style="color: var(--text-primary);">Generated Video</h6>
                                                <video id="previewVideo" controls class="w-100" style="max-height: 200px;">
                                                    <source src="" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                                <div class="mt-2">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="regenerateVideoBtn">
                                                        <i class="fas fa-redo me-1"></i>Regenerate
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-success" id="useVideoBtn">
                                                        <i class="fas fa-check me-1"></i>Use This Video
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="color: var(--text-primary);">Select Platforms</label>
                        <div class="row">
                            <div class="col-md-6">
                                @if(in_array('facebook', $connectedPlatforms))
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="facebook" id="facebookCheck">
                                        <label class="form-check-label" for="facebookCheck" style="color: var(--text-primary);">
                                            <i class="fab fa-facebook me-1" style="color: #1877f2;"></i>Facebook
                                        </label>
                                    </div>
                                @else
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="facebook" id="facebookCheck" disabled>
                                        <label class="form-check-label" for="facebookCheck" style="color: var(--text-muted);">
                                            <i class="fab fa-facebook me-1"></i>Facebook <small class="text-muted">(Not connected)</small>
                                        </label>
                                    </div>
                                @endif
                                
                                @if(in_array('instagram', $connectedPlatforms))
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="instagram" id="instagramCheck">
                                        <label class="form-check-label" for="instagramCheck" style="color: var(--text-primary);">
                                            <i class="fab fa-instagram me-1" style="color: #e4405f;"></i>Instagram
                                        </label>
                                    </div>
                                @else
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="instagram" id="instagramCheck" disabled>
                                        <label class="form-check-label" for="instagramCheck" style="color: var(--text-muted);">
                                            <i class="fab fa-instagram me-1"></i>Instagram <small class="text-muted">(Not connected)</small>
                                        </label>
                                    </div>
                                @endif
                                
                                @if(in_array('twitter', $connectedPlatforms))
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="twitter" id="twitterCheck">
                                        <label class="form-check-label" for="twitterCheck" style="color: var(--text-primary);">
                                            <i class="fab fa-x-twitter me-1" style="color: #000000;"></i>X (Twitter)
                                        </label>
                                    </div>
                                @else
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="twitter" id="twitterCheck" disabled>
                                        <label class="form-check-label" for="twitterCheck" style="color: var(--text-muted);">
                                            <i class="fab fa-x-twitter me-1"></i>X (Twitter) <small class="text-muted">(Not connected)</small>
                                        </label>
                                    </div>
                                @endif
                                
                                @if(in_array('linkedin', $connectedPlatforms))
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="linkedin" id="linkedinCheck">
                                        <label class="form-check-label" for="linkedinCheck" style="color: var(--text-primary);">
                                            <i class="fab fa-linkedin me-1" style="color: #0077b5;"></i>LinkedIn
                                        </label>
                                    </div>
                                @else
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="linkedin" id="linkedinCheck" disabled>
                                        <label class="form-check-label" for="linkedinCheck" style="color: var(--text-muted);">
                                            <i class="fab fa-linkedin me-1"></i>LinkedIn <small class="text-muted">(Not connected)</small>
                                        </label>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                @if(in_array('telegram', $connectedPlatforms))
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="telegram" id="telegramCheck">
                                        <label class="form-check-label" for="telegramCheck" style="color: var(--text-primary);">
                                            <i class="fab fa-telegram me-1" style="color: #0088cc;"></i>Telegram
                                        </label>
                                    </div>
                                @else
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="telegram" id="telegramCheck" disabled>
                                        <label class="form-check-label" for="telegramCheck" style="color: var(--text-muted);">
                                            <i class="fab fa-telegram me-1"></i>Telegram <small class="text-muted">(Not connected)</small>
                                        </label>
                                    </div>
                                @endif
                                
                                @if(in_array('discord', $connectedPlatforms))
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="discord" id="discordCheck">
                                        <label class="form-check-label" for="discordCheck" style="color: var(--text-primary);">
                                            <i class="fab fa-discord me-1" style="color: #7289da;"></i>Discord
                                        </label>
                                    </div>
                                @else
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="discord" id="discordCheck" disabled>
                                        <label class="form-check-label" for="discordCheck" style="color: var(--text-muted);">
                                            <i class="fab fa-discord me-1"></i>Discord <small class="text-muted">(Not connected)</small>
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        @if(empty($connectedPlatforms))
                            <div class="alert alert-warning mt-2" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No social media accounts connected. <a href="#" onclick="$('.sub-navigation').scrollTop()">Connect accounts above</a> to start posting.
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="scheduleTime" class="form-label" style="color: var(--text-primary);">Schedule Post (Optional)</label>
                        <input type="datetime-local" class="form-control" id="scheduleTime" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="background: var(--bg-tertiary); border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitPostBtn">Create Post</button>
            </div>
        </div>
    </div>
</div>

<style>
.sub-navigation {
    background: var(--card-bg);
    border-radius: 0.75rem;
    padding: 1rem;
    border: 1px solid var(--border-color);
    margin-bottom: 2rem;
}

.sub-navigation .nav-tabs {
    display: flex;
    gap: 1rem;
    list-style: none;
    margin: 0;
    padding: 0;
    flex-wrap: wrap;
}

.sub-navigation .nav-tab {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-muted);
    text-decoration: none;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.sub-navigation .nav-tab:hover {
    color: var(--text-primary);
    background: var(--bg-tertiary);
}

.sub-navigation .nav-tab.active {
    color: var(--primary-color);
    background: var(--primary-color);
    color: var(--white);
}

.platform-card {
    transition: all 0.2s ease;
}

.platform-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px var(--shadow-color);
}

.platform-hint {
    display: block;
    margin-top: 0.25rem;
    font-style: italic;
}

.alert-warning {
    background-color: var(--warning-bg, #fff3cd);
    border-color: var(--warning-border, #ffeeba);
    color: var(--warning-text, #856404);
}

#createPostModal .alert {
    margin-bottom: 1rem;
    border-radius: 0.375rem;
}

#createPostModal .alert:last-child {
    margin-bottom: 0;
}

@media (max-width: 768px) {
    .sub-navigation .nav-tabs {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .sub-navigation .nav-tab {
        text-align: center;
        padding: 0.75rem 1rem;
    }
}

</style>

<script>
// Test function to check if JavaScript is working - GLOBAL SCOPE
window.testFunction = function() {
    console.log('=== TEST FUNCTION CALLED ===');
    showAlert('JavaScript is working! All systems operational.', 'success');
}

// Social Media Connection Functions - Global scope
window.connectSocialMedia = function(platform) {
    console.log('=== CONNECT SOCIAL MEDIA FUNCTION CALLED ===');
    console.log('Connecting to platform:', platform);
    console.log('Event target:', event.target);
    console.log('Current URL:', window.location.href);
    
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Connecting...';
    button.disabled = true;

    // Make AJAX request to check if upgrade is needed
    fetch(`{{ route('social.connect', ':platform') }}`.replace(':platform', platform), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        if (response.redirected) {
            console.log('Redirecting to:', response.url);
            window.location.href = response.url;
            return;
        }
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data && data.error === 'upgrade_required') {
            // Show upgrade modal
            showUpgradeModal(platform);
        } else if (data && data.redirect_url) {
            // Redirect to OAuth
            window.location.href = data.redirect_url;
        } else {
            // Fallback to direct redirect
            window.location.href = `{{ route('social.connect', ':platform') }}`.replace(':platform', platform);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Fallback to direct redirect
        window.location.href = `{{ route('social.connect', ':platform') }}`.replace(':platform', platform);
    })
    .finally(() => {
        // Reset button
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

window.showUpgradeModal = function(platform) {
    // Store the platform for later use
    window.pendingPlatform = platform;
    
    // Show the upgrade modal
    const upgradeModal = new bootstrap.Modal(document.getElementById('upgradeModal'));
    upgradeModal.show();
}

window.initiateUpgrade = function() {
    const phoneNumber = document.getElementById('phone_number').value;
    const upgradeBtn = document.getElementById('upgradeBtn');
    
    if (!phoneNumber) {
        showAlert('Please enter your M-Pesa phone number', 'warning');
        return;
    }

    // Show loading state
    const originalText = upgradeBtn.innerHTML;
    upgradeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Processing...';
    upgradeBtn.disabled = true;

    // Make payment request
    fetch('{{ route("billing.process-upgrade") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            phone_number: phoneNumber,
            platform: window.pendingPlatform
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message and redirect to OAuth
            alert('Payment successful! Redirecting to connect your account...');
            window.location.href = `{{ route('social.connect', ':platform') }}`.replace(':platform', window.pendingPlatform);
        } else {
            alert('Payment failed: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Payment failed. Please try again.');
    })
    .finally(() => {
        // Reset button
        upgradeBtn.innerHTML = originalText;
        upgradeBtn.disabled = false;
    });
}

// ========================================
// AI Content Generation Functions
// These MUST be in global scope to work with onclick attributes
// ========================================

window.generateMarketingContent = function() {
    const title = document.getElementById('postTitle').value;
    const hashtags = document.getElementById('postHashtags').value;
    
    const keywords = prompt('Enter keywords or topic for your post (e.g., "new product launch", "summer sale"):');
    if (!keywords || !keywords.trim()) {
        return;
    }
    
    const statusDiv = document.getElementById('aiContentStatus');
    if (statusDiv) statusDiv.style.display = 'block';
    
    fetch('/marketing/posts/ai/generate-content', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            keywords: keywords,
            title: title,
            hashtags: hashtags
        })
    })
    .then(response => response.json())
    .then(data => {
        if (statusDiv) statusDiv.style.display = 'none';
        if (data.success) {
            document.getElementById('postContent').value = data.content;
            if (data.suggested_hashtags) {
                document.getElementById('postHashtags').value = data.suggested_hashtags;
            }
            showAlert('Content generated successfully!', 'success');
        } else {
            showAlert(data.message || 'Failed to generate content', 'error');
        }
    })
    .catch(error => {
        if (statusDiv) statusDiv.style.display = 'none';
        console.error('AI Generation Error:', error);
        showAlert('Error generating content. Please try again.', 'error');
    });
};

window.enhanceMarketingContent = function() {
    const content = document.getElementById('postContent').value;
    
    if (!content || !content.trim()) {
        showAlert('Please enter some content first before enhancing.', 'warning');
        return;
    }
    
    const statusDiv = document.getElementById('aiContentStatus');
    if (statusDiv) statusDiv.style.display = 'block';
    
    fetch('/marketing/posts/ai/enhance-content', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            content: content
        })
    })
    .then(response => response.json())
    .then(data => {
        if (statusDiv) statusDiv.style.display = 'none';
        if (data.success) {
            document.getElementById('postContent').value = data.enhanced_content;
            showAlert('Content enhanced successfully!', 'success');
        } else {
            showAlert(data.message || 'Failed to enhance content', 'error');
        }
    })
    .catch(error => {
        if (statusDiv) statusDiv.style.display = 'none';
        console.error('AI Enhancement Error:', error);
        showAlert('Error enhancing content. Please try again.', 'error');
    });
};

window.generateImagePrompts = function() {
    const content = document.getElementById('postContent').value;
    const title = document.getElementById('postTitle').value;
    
    if (!content || !content.trim()) {
        showAlert('Please enter post content first to generate image suggestions.', 'warning');
        return;
    }
    
    const statusDiv = document.getElementById('aiContentStatus');
    if (statusDiv) statusDiv.style.display = 'block';
    
    fetch('/marketing/posts/ai/generate-image-prompts', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            content: content,
            title: title
        })
    })
    .then(response => response.json())
    .then(data => {
        if (statusDiv) statusDiv.style.display = 'none';
        if (data.success && data.prompts && data.prompts.length > 0) {
            displayImagePrompts(data.prompts);
            showAlert(`Generated ${data.prompts.length} image prompt suggestions!`, 'success');
        } else {
            showAlert(data.message || 'Failed to generate image prompts', 'error');
        }
    })
    .catch(error => {
        if (statusDiv) statusDiv.style.display = 'none';
        console.error('AI Image Prompts Error:', error);
        showAlert('Error generating image prompts. Please try again.', 'error');
    });
};

function displayImagePrompts(prompts) {
    const section = document.getElementById('aiImagePromptsSection');
    const list = document.getElementById('aiImagePromptsList');
    
    if (!list) return;
    list.innerHTML = '';
    
    prompts.forEach((prompt, index) => {
        const item = document.createElement('div');
        item.className = 'list-group-item d-flex justify-content-between align-items-start';
        item.style.background = 'var(--bg-secondary)';
        item.style.border = '1px solid var(--border-color)';
        item.style.marginBottom = '8px';
        item.innerHTML = `
            <div class="flex-grow-1">
                <h6 class="mb-1" style="color: var(--text-primary);">
                    <i class="fas fa-sparkles me-1" style="color: #667eea;"></i>
                    Prompt ${index + 1}
                </h6>
                <p class="mb-1 small" style="color: var(--text-secondary);">${prompt}</p>
            </div>
            <button class="btn btn-sm btn-outline-primary" onclick="copyToClipboard('${prompt.replace(/'/g, "\\'")}')">
                <i class="fas fa-copy"></i>
            </button>
        `;
        list.appendChild(item);
    });
    
    if (section) section.style.display = 'block';
}

window.copyToClipboard = function(text) {
    navigator.clipboard.writeText(text).then(() => {
        showAlert('Prompt copied to clipboard!', 'info');
    }).catch(err => {
        console.error('Failed to copy:', err);
        alert('Failed to copy to clipboard');
    });
};

window.useImagePromptForGeneration = function() {
    const imagePromptsList = document.getElementById('aiImagePromptsList');
    if (!imagePromptsList || imagePromptsList.children.length === 0) {
        showAlert('Please generate image prompts first using the "Image Ideas" button.', 'info');
        return;
    }
    
    const firstPromptText = imagePromptsList.querySelector('p')?.textContent;
    if (firstPromptText) {
        document.getElementById('imagePrompt').value = firstPromptText;
        showAlert('Prompt added! You can edit it before generating.', 'info');
    }
};

window.autoGenerateFromPost = function() {
    const postContent = document.getElementById('postContent').value;
    const postTitle = document.getElementById('postTitle').value;
    
    if (!postContent || !postContent.trim()) {
        showAlert('Please write or generate post content first.', 'warning');
        return;
    }
    
    let prompt = '';
    if (postTitle && postTitle.trim()) {
        prompt = `Image for: ${postTitle}. `;
    }
    
    const contentSummary = postContent.substring(0, 150).trim();
    prompt += `Visual representation of: ${contentSummary}`;
    
    document.getElementById('imagePrompt').value = prompt;
    showAlert('Image prompt auto-generated from your post!', 'success');
};

window.enhanceImagePrompt = function() {
    const prompt = document.getElementById('imagePrompt').value;
    
    if (!prompt || !prompt.trim()) {
        showAlert('Please enter a prompt first to enhance.', 'warning');
        return;
    }
    
    const statusDiv = document.getElementById('promptEnhanceStatus');
    const textarea = document.getElementById('imagePrompt');
    const originalPrompt = prompt;
    
    if (statusDiv) statusDiv.style.display = 'block';
    textarea.disabled = true;
    
    const postContent = document.getElementById('postContent').value;
    const style = document.getElementById('imageStyle').value;
    
    fetch('/marketing/posts/ai/enhance-image-prompt', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            prompt: originalPrompt,
            post_content: postContent || '',
            style: style || ''
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error('Server error: ' + text);
            });
        }
        return response.json();
    })
    .then(data => {
        if (statusDiv) statusDiv.style.display = 'none';
        textarea.disabled = false;
        
        if (data.success && data.enhanced_prompt) {
            textarea.value = data.enhanced_prompt;
            showAlert('Prompt enhanced! Review and generate image.', 'success');
        } else {
            textarea.value = originalPrompt;
            showAlert(data.message || 'Failed to enhance prompt', 'error');
        }
    })
    .catch(error => {
        if (statusDiv) statusDiv.style.display = 'none';
        textarea.disabled = false;
        textarea.value = originalPrompt;
        console.error('Prompt Enhancement Error:', error);
        showAlert('Error enhancing prompt. Please try again.', 'error');
    });
};

window.autoGenerateVideoFromPost = function() {
    const postContent = document.getElementById('postContent').value;
    const postTitle = document.getElementById('postTitle').value;
    
    if (!postContent || !postContent.trim()) {
        showAlert('Please write or generate post content first.', 'warning');
        return;
    }
    
    let prompt = '';
    if (postTitle && postTitle.trim()) {
        prompt = `Video scene for: ${postTitle}. `;
    }
    
    const contentSummary = postContent.substring(0, 150).trim();
    prompt += `Cinematic video showcasing: ${contentSummary}`;
    
    document.getElementById('videoPrompt').value = prompt;
    showAlert('Video prompt auto-generated from your post!', 'success');
};

window.enhanceVideoPrompt = function() {
    const prompt = document.getElementById('videoPrompt').value;
    
    if (!prompt || !prompt.trim()) {
        showAlert('Please enter a video prompt first to enhance.', 'warning');
        return;
    }
    
    const statusDiv = document.getElementById('videoPromptEnhanceStatus');
    const textarea = document.getElementById('videoPrompt');
    const originalPrompt = prompt;
    
    if (statusDiv) statusDiv.style.display = 'block';
    textarea.disabled = true;
    
    const postContent = document.getElementById('postContent').value;
    const style = document.getElementById('videoStyle').value;
    const duration = document.getElementById('videoDuration').value;
    
    fetch('/marketing/posts/ai/enhance-video-prompt', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            prompt: originalPrompt,
            post_content: postContent || '',
            style: style || '',
            duration: duration || ''
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error('Server error: ' + text);
            });
        }
        return response.json();
    })
    .then(data => {
        if (statusDiv) statusDiv.style.display = 'none';
        textarea.disabled = false;
        
        if (data.success && data.enhanced_prompt) {
            textarea.value = data.enhanced_prompt;
            showAlert('Video prompt enhanced! Review and generate video.', 'success');
        } else {
            textarea.value = originalPrompt;
            showAlert(data.message || 'Failed to enhance video prompt', 'error');
        }
    })
    .catch(error => {
        if (statusDiv) statusDiv.style.display = 'none';
        textarea.disabled = false;
        textarea.value = originalPrompt;
        console.error('Video Prompt Enhancement Error:', error);
        showAlert('Error enhancing video prompt. Please try again.', 'error');
    });
};

window.useVideoPromptForGeneration = function() {
    showAlert('Generate video prompts first using the "Get Image Ideas" button in the AI Features section.', 'info');
};

window.generateAIImage = function() {
    let prompt = document.getElementById('imagePrompt').value;
    const style = document.getElementById('imageStyle').value;
    const size = document.getElementById('imageSize').value;
    const postContent = document.getElementById('postContent').value;
    const postTitle = document.getElementById('postTitle').value;
    
    if ((!prompt || !prompt.trim()) && postContent && postContent.trim()) {
        const contentSummary = postContent.substring(0, 200);
        prompt = `Image for social media post: ${contentSummary}`;
        document.getElementById('imagePrompt').value = prompt;
    }
    
    if (!prompt || !prompt.trim()) {
        showAlert('Please enter an image description or write post content first.', 'warning');
        return;
    }
    
    const progressDiv = document.getElementById('imageGenerationProgress');
    const previewDiv = document.getElementById('generatedImagePreview');
    const generateBtn = document.getElementById('generateImageBtn');
    
    if (progressDiv) progressDiv.style.display = 'block';
    if (previewDiv) previewDiv.style.display = 'none';
    if (generateBtn) generateBtn.disabled = true;
    
    fetch('/marketing/posts/ai/generate-image', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            prompt: prompt,
            post_content: postContent,
            post_title: postTitle,
            style: style,
            size: size
        })
    })
    .then(response => response.json())
    .then(data => {
        if (progressDiv) progressDiv.style.display = 'none';
        if (generateBtn) generateBtn.disabled = false;
        
        if (data.success && data.image_url) {
            const previewImg = document.getElementById('previewImage');
            
            // Add error handler for image loading
            previewImg.onerror = function() {
                if (previewDiv) previewDiv.style.display = 'none';
                showAlert('Failed to load the generated image. The file may be corrupted.', 'error');
            };
            
            // Add success handler for image loading
            previewImg.onload = function() {
                console.log('Image loaded successfully:', data.image_url);
                if (previewDiv) previewDiv.style.display = 'block';
                showAlert('Image generated successfully!', 'success');
            };
            
            // Set image source and store paths
            previewImg.src = data.image_url + '?t=' + Date.now(); // Add cache buster
            document.getElementById('generatedImageUrl').value = data.image_url;
            
            // Store local paths for submission
            if (data.local_path) {
                document.getElementById('generatedImageLocalPath').value = data.local_path;
            }
            if (data.relative_path) {
                document.getElementById('generatedImageRelativePath').value = data.relative_path;
            }
        } else {
            showAlert(data.message || 'Failed to generate image. Please try again with a different prompt.', 'error');
        }
    })
    .catch(error => {
        if (progressDiv) progressDiv.style.display = 'none';
        if (generateBtn) generateBtn.disabled = false;
        console.error('Image Generation Error:', error);
        showAlert('Error generating image. Please try again.', 'error');
    });
};

window.downloadGeneratedImage = function() {
    const imageUrl = document.getElementById('generatedImageUrl').value;
    if (!imageUrl) {
        showAlert('No image to download', 'warning');
        return;
    }
    
    // Show loading message
    showAlert('Preparing image download...', 'info');
    
    // Fetch the image and download it
    fetch(imageUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch image');
            }
            return response.blob();
        })
        .then(blob => {
            // Create download link
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'ai-generated-image-' + Date.now() + '.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Clean up
            window.URL.revokeObjectURL(url);
            
            showAlert('Image downloaded successfully!', 'success');
        })
        .catch(error => {
            console.error('Download error:', error);
            showAlert('Failed to download image. Please try right-clicking the image and selecting "Save Image As..."', 'error');
        });
};

window.showAlert = function(message, type = 'info') {
    // Remove any existing custom alerts
    const existingAlert = document.getElementById('customAlertModal');
    if (existingAlert) {
        existingAlert.remove();
    }
    
    // Determine icon and colors based on type
    let icon, bgColor, textColor, iconColor;
    switch(type) {
        case 'success':
            icon = 'fa-check-circle';
            bgColor = 'var(--success-bg, #d4edda)';
            textColor = 'var(--success-text, #155724)';
            iconColor = '#28a745';
            break;
        case 'error':
        case 'danger':
            icon = 'fa-exclamation-circle';
            bgColor = 'var(--danger-bg, #f8d7da)';
            textColor = 'var(--danger-text, #721c24)';
            iconColor = '#dc3545';
            break;
        case 'warning':
            icon = 'fa-exclamation-triangle';
            bgColor = 'var(--warning-bg, #fff3cd)';
            textColor = 'var(--warning-text, #856404)';
            iconColor = '#ffc107';
            break;
        default: // info
            icon = 'fa-info-circle';
            bgColor = 'var(--info-bg, #d1ecf1)';
            textColor = 'var(--info-text, #0c5460)';
            iconColor = '#17a2b8';
    }
    
    // Create modal HTML
    const modalHTML = `
        <div id="customAlertModal" class="custom-alert-modal" style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.2s ease-in;
        ">
            <div class="custom-alert-content" style="
                background: var(--card-bg, #fff);
                border-radius: 12px;
                padding: 0;
                max-width: 450px;
                width: 90%;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
                animation: slideDown 0.3s ease-out;
                border: 1px solid var(--border-color, #dee2e6);
            ">
                <div style="
                    background: ${bgColor};
                    padding: 20px;
                    border-radius: 12px 12px 0 0;
                    text-align: center;
                    border-bottom: 1px solid ${iconColor};
                ">
                    <i class="fas ${icon}" style="
                        font-size: 48px;
                        color: ${iconColor};
                        margin-bottom: 10px;
                    "></i>
                </div>
                <div style="
                    padding: 24px;
                    text-align: center;
                ">
                    <p style="
                        color: var(--text-primary, #333);
                        font-size: 16px;
                        margin: 0 0 20px 0;
                        line-height: 1.5;
                    ">${message}</p>
                    <button onclick="document.getElementById('customAlertModal').remove()" style="
                        background: ${iconColor};
                        color: white;
                        border: none;
                        padding: 10px 30px;
                        border-radius: 6px;
                        font-size: 14px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.2s ease;
                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.15)';" 
                       onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(0, 0, 0, 0.1)';">
                        OK
                    </button>
                </div>
            </div>
        </div>
        <style>
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            @keyframes slideDown {
                from {
                    transform: translateY(-50px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
        </style>
    `;
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Auto close after delay based on type
    const timeout = type === 'info' ? 8000 : type === 'success' ? 5000 : 7000;
    setTimeout(() => {
        const modal = document.getElementById('customAlertModal');
        if (modal) {
            modal.style.animation = 'fadeOut 0.2s ease-out';
            setTimeout(() => modal.remove(), 200);
        }
    }, timeout);
    
    // Close on background click
    const modal = document.getElementById('customAlertModal');
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
};

// ========================================
// DOM Content Loaded - Event Listeners
// ========================================

// Enhanced Social Media Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Media type switching
    const mediaTypeRadios = document.querySelectorAll('input[name="mediaType"]');
    const mediaSections = document.querySelectorAll('.media-section');
    
    if (mediaTypeRadios.length > 0) {
        mediaTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Hide all media sections
                mediaSections.forEach(section => {
                    section.style.display = 'none';
                });
                
                // Show selected section
                if (this.value === 'upload') {
                    const uploadSection = document.getElementById('uploadMediaSection');
                    if (uploadSection) uploadSection.style.display = 'block';
                } else if (this.value === 'generate-image') {
                    const generateImageSection = document.getElementById('generateImageSection');
                    if (generateImageSection) generateImageSection.style.display = 'block';
                } else if (this.value === 'generate') {
                    const generateSection = document.getElementById('generateVideoSection');
                    if (generateSection) generateSection.style.display = 'block';
                }
            });
        });
    }
    
    // Platform-specific requirement hints
    const platformCheckboxes = document.querySelectorAll('input[type="checkbox"][id$="Check"]');
    platformCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                showPlatformRequirements(this.value);
            }
        });
    });
    
    function showPlatformRequirements(platform) {
        const requirements = {
            'instagram': 'Instagram requires at least one image or video',
            'linkedin': 'LinkedIn works best with professional content and may take longer to process videos',
            'twitter': 'Twitter has a 280 character limit - longer content will be truncated',
            'facebook': 'Facebook supports all content types including links',
            'telegram': 'Telegram supports rich text formatting and media',
            'discord': 'Discord posts will be sent to your configured webhook channel'
        };
        
        if (requirements[platform]) {
            // Show a small hint near the platform checkbox
            const hint = document.createElement('small');
            hint.className = 'text-muted platform-hint';
            hint.innerHTML = `<br><i class="fas fa-info-circle me-1"></i>${requirements[platform]}`;
            
            const label = document.querySelector(`label[for="${platform}Check"]`);
            if (label && !label.querySelector('.platform-hint')) {
                label.appendChild(hint);
            }
        }
    }
    
    // Remove platform hints when unchecked
    platformCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (!this.checked) {
                const label = document.querySelector(`label[for="${this.value}Check"]`);
                if (label) {
                    const hint = label.querySelector('.platform-hint');
                    if (hint) hint.remove();
                }
            }
        });
    });
    
    // Video generation functionality
    const generateVideoBtn = document.getElementById('generateVideoBtn');
    const regenerateVideoBtn = document.getElementById('regenerateVideoBtn');
    const useVideoBtn = document.getElementById('useVideoBtn');
    const videoGenerationProgress = document.getElementById('videoGenerationProgress');
    const generatedVideoPreview = document.getElementById('generatedVideoPreview');
    const previewVideo = document.getElementById('previewVideo');
    const progressBar = document.querySelector('#videoGenerationProgress .progress-bar');
    
    let generatedVideoUrl = null;
    let generatedVideoId = null;
    
    // Generate video function
    function generateVideo() {
        const videoPrompt = document.getElementById('videoPrompt')?.value || '';
        const videoStyle = document.getElementById('videoStyle')?.value || 'professional';
        const videoDuration = document.getElementById('videoDuration')?.value || '8';
        const productImage = document.getElementById('productImage')?.files[0];
        const postContent = document.getElementById('postContent')?.value || '';
        
        if (!videoPrompt && !postContent) {
            showAlert('Please provide either a video description or post content to generate a video.', 'warning');
            return;
        }
        
        // Show progress
        if (videoGenerationProgress) videoGenerationProgress.style.display = 'block';
        if (generatedVideoPreview) generatedVideoPreview.style.display = 'none';
        if (generateVideoBtn) {
            generateVideoBtn.disabled = true;
            generateVideoBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
        }
        
        // Simulate progress
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            if (progressBar) progressBar.style.width = progress + '%';
        }, 1000);
        
        // Prepare form data
        const formData = new FormData();
        formData.append('content', videoPrompt || postContent);
        formData.append('title', document.getElementById('postTitle').value || 'Social Media Post');
        formData.append('style', videoStyle);
        formData.append('duration', videoDuration);
        
        if (productImage) {
            formData.append('product_image', productImage);
        }
        
        // Make API request
        fetch('{{ route("marketing.video.generate") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            clearInterval(progressInterval);
            progressBar.style.width = '100%';
            
            if (data.success) {
                generatedVideoUrl = data.video_url;
                generatedVideoId = data.video_id;
                
                // Update video preview
                if (previewVideo) {
                    previewVideo.src = data.video_url;
                    previewVideo.load();
                }
                
                // Show preview
                if (videoGenerationProgress) videoGenerationProgress.style.display = 'none';
                if (generatedVideoPreview) generatedVideoPreview.style.display = 'block';
                
                // Reset button
                if (generateVideoBtn) {
                    generateVideoBtn.disabled = false;
                    generateVideoBtn.innerHTML = '<i class="fas fa-play me-2"></i>Generate Video';
                }
                
                // Show success message
                showAlert('Video generated successfully!', 'success');
            } else {
                throw new Error(data.message || 'Video generation failed');
            }
        })
        .catch(error => {
            clearInterval(progressInterval);
            if (progressBar) progressBar.style.width = '0%';
            if (videoGenerationProgress) videoGenerationProgress.style.display = 'none';
            
            // Reset button
            if (generateVideoBtn) {
                generateVideoBtn.disabled = false;
                generateVideoBtn.innerHTML = '<i class="fas fa-play me-2"></i>Generate Video';
            }
            
            // Show error
            showAlert('Video generation failed: ' + error.message, 'error');
        });
    }
    
    // Event listeners
    if (generateVideoBtn) {
        generateVideoBtn.addEventListener('click', generateVideo);
    }
    
    if (regenerateVideoBtn) {
        regenerateVideoBtn.addEventListener('click', generateVideo);
    }
    
    // Image generation event listeners
    const generateImageBtn = document.getElementById('generateImageBtn');
    const regenerateImageBtn = document.getElementById('regenerateImageBtn');
    const useImageBtn = document.getElementById('useImageBtn');
    const downloadImageBtn = document.getElementById('downloadImageBtn');
    
    if (generateImageBtn) {
        generateImageBtn.addEventListener('click', generateAIImage);
    }
    
    if (regenerateImageBtn) {
        regenerateImageBtn.addEventListener('click', generateAIImage);
    }
    
    if (downloadImageBtn) {
        downloadImageBtn.addEventListener('click', downloadGeneratedImage);
    }
    
    if (useImageBtn) {
        useImageBtn.addEventListener('click', function() {
            const imageUrl = document.getElementById('generatedImageUrl').value;
            if (!imageUrl) {
                showAlert('No image to use', 'warning');
                return;
            }
            
            // Set media type to generate-image so form knows to include it
            const generateImageRadio = document.getElementById('generateImage');
            if (generateImageRadio) {
                generateImageRadio.checked = true;
            }
            
            // Create a hidden input for the generated image
            let imageInput = document.getElementById('generatedImageInput');
            if (!imageInput) {
                imageInput = document.createElement('input');
                imageInput.type = 'hidden';
                imageInput.id = 'generatedImageInput';
                imageInput.name = 'generated_image_url';
                document.getElementById('createPostForm').appendChild(imageInput);
            }
            imageInput.value = imageUrl;
            
            // Show visual confirmation with preview thumbnail
            showAlert(`
                <strong>Image selected!</strong><br>
                <img src="${imageUrl}" style="max-width: 200px; max-height: 150px; margin-top: 10px; border-radius: 4px;" class="img-thumbnail">
                <br><small class="text-muted">This image will be posted with your content.</small>
            `, 'success');
            
            // Collapse the preview to save space
            const previewDiv = document.getElementById('generatedImagePreview');
            if (previewDiv) {
                const collapseBtn = document.createElement('div');
                collapseBtn.className = 'mt-2 text-center';
                collapseBtn.innerHTML = '<small class="text-success"><i class="fas fa-check-circle"></i> Image ready to post</small>';
                previewDiv.appendChild(collapseBtn);
            }
        });
    }
    
    if (useVideoBtn) {
        useVideoBtn.addEventListener('click', function() {
            if (generatedVideoUrl) {
                // Create a hidden input for the generated video
                let videoInput = document.getElementById('generatedVideoInput');
                if (!videoInput) {
                    videoInput = document.createElement('input');
                    videoInput.type = 'hidden';
                    videoInput.id = 'generatedVideoInput';
                    videoInput.name = 'generated_video_url';
                    document.getElementById('createPostForm').appendChild(videoInput);
                }
                videoInput.value = generatedVideoUrl;
                
                // Switch to upload media type and show the video
                const uploadMediaRadio = document.getElementById('uploadMedia');
                const uploadMediaSection = document.getElementById('uploadMediaSection');
                const generateVideoSection = document.getElementById('generateVideoSection');
                
                if (uploadMediaRadio) uploadMediaRadio.checked = true;
                if (uploadMediaSection) uploadMediaSection.style.display = 'block';
                if (generateVideoSection) generateVideoSection.style.display = 'none';
                
                // Create a preview of the generated video in the upload section
                const uploadSection = document.getElementById('uploadMediaSection');
                let previewDiv = uploadSection.querySelector('.generated-video-preview');
                if (!previewDiv) {
                    previewDiv = document.createElement('div');
                    previewDiv.className = 'generated-video-preview mt-3';
                    uploadSection.appendChild(previewDiv);
                }
                
                previewDiv.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>Generated video ready for posting
                        <video controls class="w-100 mt-2" style="max-height: 150px;">
                            <source src="${generatedVideoUrl}" type="video/mp4">
                        </video>
                    </div>
                `;
                
                showAlert('Video added to your post!', 'success');
            }
        });
    }
    
    // Form submission
    const submitBtn = document.getElementById('submitPostBtn');
    if (submitBtn) {
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            submitPost();
        });
    }
    
    // Guard to prevent multiple submissions
    let isSubmitting = false;
    
    window.submitPost = function() {
        console.log('=== SUBMIT POST FUNCTION CALLED ===');
        
        // Prevent multiple simultaneous submissions
        if (isSubmitting) {
            console.log('Already submitting, ignoring duplicate call');
            return;
        }
        
        const form = document.getElementById('createPostForm');
        if (!form) {
            console.error('Form not found!');
            showAlert('Form not found!', 'error');
            return;
        }
        
        // Get form values
        const title = document.getElementById('postTitle').value;
        const content = document.getElementById('postContent').value;
        const hashtags = document.getElementById('postHashtags').value;
        const scheduleTime = document.getElementById('scheduleTime').value;
        
        // Validate required fields
        if (!content.trim()) {
            showAlert('Please enter post content.', 'error');
            return;
        }
        
        // Get selected platforms
        const selectedPlatforms = [];
        document.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => {
            selectedPlatforms.push(checkbox.value);
        });
        
        if (selectedPlatforms.length === 0) {
            showAlert('Please select at least one platform to post to.', 'error');
            return;
        }
        
        // Validate post for selected platforms
        const mediaTypeElement = document.querySelector('input[name="mediaType"]:checked');
        const mediaType = mediaTypeElement ? mediaTypeElement.value : 'none';
        const warnings = validatePostForPlatforms(content, selectedPlatforms, mediaType);
        
        // Show warnings if any (but don't prevent submission)
        if (warnings.length > 0) {
            const warningMessage = '<strong>Platform Recommendations:</strong><br>' + warnings.join('<br>');
            showAlert(warningMessage, 'warning');
        }
        
        // Prepare form data
        const formData = new FormData();
        formData.append('title', title);
        formData.append('content', content);
        formData.append('platforms', JSON.stringify(selectedPlatforms));
        formData.append('media_type', mediaType);
        
        // Add hashtags - convert space-separated to array
        if (hashtags.trim()) {
            const hashtagArray = hashtags.trim().split(/\s+/).map(tag => {
                // Ensure hashtag starts with #
                return tag.startsWith('#') ? tag : '#' + tag;
            });
            formData.append('hashtags', JSON.stringify(hashtagArray));
        }
        
        // Add schedule time if provided
        if (scheduleTime) {
            formData.append('scheduled_at', scheduleTime);
        } else {
            formData.append('status', 'published'); // Immediate posting
        }
        
        // Add media type (reuse the variable already declared above)
        
        if (mediaType === 'upload') {
            const mediaFile = document.getElementById('postMedia').files[0];
            if (mediaFile) {
                formData.append('media', mediaFile);
            }
        } else if (mediaType === 'generate-image') {
            // Check for generated image
            const generatedImageUrl = document.getElementById('generatedImageUrl')?.value;
            const generatedImageLocalPath = document.getElementById('generatedImageLocalPath')?.value;
            const generatedImageRelativePath = document.getElementById('generatedImageRelativePath')?.value;
            
            if (generatedImageUrl) {
                formData.append('generated_image_url', generatedImageUrl);
                
                // Prefer local path for posting to social media
                if (generatedImageLocalPath) {
                    formData.append('generated_image_local_path', generatedImageLocalPath);
                    console.log('Adding generated image with local path:', generatedImageLocalPath);
                } else if (generatedImageRelativePath) {
                    formData.append('generated_image_relative_path', generatedImageRelativePath);
                    console.log('Adding generated image with relative path:', generatedImageRelativePath);
                } else {
                    console.log('Adding generated image with URL only:', generatedImageUrl);
                }
            }
        } else if (mediaType === 'generate' && generatedVideoUrl) {
            formData.append('generated_video_url', generatedVideoUrl);
            formData.append('generated_video_id', generatedVideoId);
        }
        
        // Set submitting flag
        isSubmitting = true;
        
        // Show loading
        const createPostBtn = document.getElementById('submitPostBtn');
        if (!createPostBtn) {
            isSubmitting = false;
            showAlert('Submit button not found!', 'error');
            return;
        }
        const originalText = createPostBtn.innerHTML;
        createPostBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Post...';
        createPostBtn.disabled = true;
        
        // Submit to backend
        console.log('Submitting to:', '{{ route("marketing.posts.store") }}');
        console.log('Form data:', formData);
        
        // Log form data contents
        for (let [key, value] of formData.entries()) {
            console.log(`${key}:`, value);
        }
        fetch('{{ route("marketing.posts.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            console.log('Response OK:', response.ok);
            
            if (!response.ok) {
                // Log the response for debugging
                return response.text().then(text => {
                    console.error('Server error response:', text);
                    throw new Error(`Server error: ${response.status} - ${text.substring(0, 200)}`);
                });
            }
            
            return response.json().catch(err => {
                console.error('Failed to parse JSON response:', err);
                throw new Error('Server returned invalid response. Check browser console for details.');
            });
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                showAlert('Post created and published successfully!', 'success');
                
                // Show publishing results if available
                if (data.publishing_results) {
                    let resultsMessage = '<br><strong>Publishing Results:</strong><br>';
                    for (const [platform, result] of Object.entries(data.publishing_results)) {
                        const status = result.success ? '✓' : '✗';
                        const statusClass = result.success ? 'text-success' : 'text-danger';
                        resultsMessage += `<span class="${statusClass}">${status} ${platform.charAt(0).toUpperCase() + platform.slice(1)}</span><br>`;
                        if (!result.success) {
                            resultsMessage += `<small class="text-muted">Error: ${result.message}</small><br>`;
                        }
                    }
                    
                    // Update the alert with results
                    setTimeout(() => {
                        showAlert('Post created! ' + resultsMessage, 'info');
                    }, 1000);
                }
                
                // Close modal and refresh page
                const modal = bootstrap.Modal.getInstance(document.getElementById('createPostModal'));
                if (modal) {
                    modal.hide();
                }
                setTimeout(() => window.location.reload(), 2000);
            } else {
                // Handle validation errors
                if (data.errors) {
                    let errorMessage = 'Validation errors:\n';
                    for (const [field, messages] of Object.entries(data.errors)) {
                        errorMessage += `${field}: ${messages.join(', ')}\n`;
                    }
                    showAlert(errorMessage, 'error');
                } else {
                    throw new Error(data.message || 'Failed to create post');
                }
            }
        })
        .catch(error => {
            console.error('Error creating post:', error);
            showAlert('Failed to create post: ' + error.message, 'error');
        })
        .finally(() => {
            // Reset button and flag
            isSubmitting = false;
            if (createPostBtn) {
                createPostBtn.innerHTML = originalText;
                createPostBtn.disabled = false;
            }
        });
    }
    
    // Helper function to validate post content based on selected platforms
    window.validatePostForPlatforms = function(content, selectedPlatforms, mediaType) {
        const warnings = [];
        
        selectedPlatforms.forEach(platform => {
            switch (platform) {
                case 'twitter':
                    if (content.length > 280) {
                        warnings.push(`Twitter: Content will be truncated to 280 characters (currently ${content.length})`);
                    }
                    break;
                case 'instagram':
                    if (mediaType === 'none') {
                        warnings.push('Instagram: Consider adding an image or video for better engagement');
                    }
                    break;
                case 'linkedin':
                    if (content.length < 50) {
                        warnings.push('LinkedIn: Consider adding more detailed content for better professional engagement');
                    }
                    break;
            }
        });
        
        return warnings;
    }
});
</script>
@endsection





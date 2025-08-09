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
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPostModal">
        <i class="fas fa-plus me-2"></i>Create Post
    </button>
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
                        <label for="postContent" class="form-label">Post Content</label>
                        <x-ai-enhanced-textarea name="postContent" 
                                                   content-type="social-media" 
                                                   tone="engaging" 
                                                   rows="4" 
                                                   placeholder="What's on your mind?">
                        </x-ai-enhanced-textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="postHashtags" class="form-label" style="color: var(--text-primary);">Hashtags</label>
                        <input type="text" class="form-control" id="postHashtags" name="hashtags" placeholder="#marketing #business #socialmedia" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                        <small class="form-text text-muted">Separate hashtags with spaces</small>
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
                        
                        <!-- Generate Video Section -->
                        <div id="generateVideoSection" class="media-section" style="display: none;">
                            <div class="card" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                                <div class="card-body">
                                    <h6 class="card-title" style="color: var(--text-primary);">
                                        <i class="fas fa-magic me-2"></i>AI Video Generation
                                    </h6>
                                    
                                    <div class="mb-3">
                                        <label for="videoPrompt" class="form-label" style="color: var(--text-primary);">Video Description</label>
                                        <textarea class="form-control" id="videoPrompt" name="video_prompt" rows="3" placeholder="Describe the video you want to generate..." style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);"></textarea>
                                        <small class="form-text text-muted">Describe the scene, style, and mood you want</small>
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
                        @if(in_array('facebook', $connectedPlatforms))
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="facebook" id="facebookCheck">
                                <label class="form-check-label" for="facebookCheck" style="color: var(--text-primary);">
                                    <i class="fab fa-facebook me-1"></i>Facebook
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
                                    <i class="fab fa-instagram me-1"></i>Instagram
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
                                    <i class="fab fa-x-twitter me-1"></i>X (Twitter)
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
                                    <i class="fab fa-linkedin me-1"></i>LinkedIn
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
                    <div class="mb-3">
                        <label for="scheduleTime" class="form-label" style="color: var(--text-primary);">Schedule Post (Optional)</label>
                        <input type="datetime-local" class="form-control" id="scheduleTime" style="background: var(--bg-secondary); border: 1px solid var(--border-color); color: var(--text-primary);">
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="background: var(--bg-tertiary); border-top: 1px solid var(--border-color);">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Create Post</button>
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
// Social Media Connection Functions - Global scope
window.connectSocialMedia = function(platform) {
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
    .then(response => response.json())
    .then(data => {
        if (data.error === 'upgrade_required') {
            // Show upgrade modal
            showUpgradeModal(platform);
        } else {
            // Redirect to OAuth
            window.location.href = data.redirect_url || `{{ route('social.connect', ':platform') }}`.replace(':platform', platform);
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
        alert('Please enter your M-Pesa phone number');
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
                } else if (this.value === 'generate') {
                    const generateSection = document.getElementById('generateVideoSection');
                    if (generateSection) generateSection.style.display = 'block';
                }
            });
        });
    }
    
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
            alert('Please provide either a video description or post content to generate a video.');
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
    const createPostBtn = document.querySelector('#createPostModal .btn-primary');
    if (createPostBtn) {
        createPostBtn.addEventListener('click', function() {
            submitPost();
        });
    }
    
    function submitPost() {
        const form = document.getElementById('createPostForm');
        if (!form) {
            showAlert('Form not found!', 'error');
            return;
        }
        
        const formData = new FormData(form);
        
        // Get selected platforms
        const selectedPlatforms = [];
        document.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => {
            selectedPlatforms.push(checkbox.value);
        });
        
        if (selectedPlatforms.length === 0) {
            showAlert('Please select at least one platform to post to.', 'error');
            return;
        }
        
        // Add platforms to form data
        formData.append('platforms', JSON.stringify(selectedPlatforms));
        
        // Add media type
        const mediaTypeElement = document.querySelector('input[name="mediaType"]:checked');
        const mediaType = mediaTypeElement ? mediaTypeElement.value : 'none';
        formData.append('media_type', mediaType);
        
        // Add generated video if exists
        if (generatedVideoUrl) {
            formData.append('generated_video_url', generatedVideoUrl);
            formData.append('generated_video_id', generatedVideoId);
        }
        
        // Show loading
        if (!createPostBtn) {
            showAlert('Submit button not found!', 'error');
            return;
        }
        
        const originalText = createPostBtn.innerHTML;
        createPostBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Post...';
        createPostBtn.disabled = true;
        
        // Submit to backend
        fetch('{{ route("marketing.posts.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Post created successfully!', 'success');
                // Close modal and refresh page
                const modal = bootstrap.Modal.getInstance(document.getElementById('createPostModal'));
                modal.hide();
                setTimeout(() => window.location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Failed to create post');
            }
        })
        .catch(error => {
            showAlert('Failed to create post: ' + error.message, 'error');
        })
        .finally(() => {
            // Reset button
            createPostBtn.innerHTML = originalText;
            createPostBtn.disabled = false;
        });
    }
    
    // Helper function to show alerts
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'check-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insert at the top of the modal body
        const modalBody = document.querySelector('#createPostModal .modal-body');
        modalBody.insertBefore(alertDiv, modalBody.firstChild);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});
</script>
@endsection





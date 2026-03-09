<?php

namespace App\Http\Controllers;

use App\Models\MarketingPost;
use App\Models\SocialMediaAccount;
use App\Services\SocialMediaService;
use App\Services\ClaudeAPIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MarketingPostController extends Controller
{
    protected $socialMediaService;
    protected $claudeService;

    public function __construct(SocialMediaService $socialMediaService, ClaudeAPIService $claudeService)
    {
        $this->socialMediaService = $socialMediaService;
        $this->claudeService = $claudeService;
    }
    public function index()
    {
        $business = Auth::user()->business;
        
        $connectedAccounts = SocialMediaAccount::where('business_id', $business->getKey())
            ->where('is_active', true)
            ->get();

        $recentPosts = MarketingPost::where('business_id', $business->getKey())
            ->with(['publications.socialMediaAccount'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('marketing.social-media', compact('connectedAccounts', 'recentPosts'));
    }

    /**
     * Show the posts index page with all marketing posts
     */
    public function postsIndex()
    {
        $business = Auth::user()->business;
        
        $posts = MarketingPost::where('business_id', $business->getKey())
            ->with(['publications.socialMediaAccount'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('marketing.posts.index', compact('posts'));
    }

     public function store(Request $request)
    {
        try {
            Log::channel('social_media')->info('Starting post creation', ['user_id' => Auth::id()]);
            
            // Check if user has business
            $user = Auth::user();
            if (!$user->business) {
                Log::channel('social_media')->error('User has no business', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'You must have a business profile to create posts.'
                ], 400);
            }
            
            Log::channel('social_media')->debug('Validating request data', ['request_data' => $request->all()]);
            
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string|max:2200',
                'hashtags' => 'nullable|string',
                'platforms' => 'required|string', // JSON string from frontend
                'media_type' => 'required|in:none,upload,generate,generate-image',
                'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov|max:10240',
                'generated_video_url' => 'nullable|string|url',
                'generated_video_id' => 'nullable|string',
                'generated_image_url' => 'nullable|string',
                'generated_image_local_path' => 'nullable|string',
                'generated_image_relative_path' => 'nullable|string',
                'scheduled_at' => 'nullable|date|after:now',
            ]);
            
            Log::channel('social_media')->info('Validation passed', ['validated_data' => $validatedData]);

            $business = $user->business;
            Log::channel('social_media')->debug('Business loaded', ['business_id' => $business->getKey(), 'is_premium' => $business->isPremium()]);
            
            // Parse platforms from JSON
            $targetPlatforms = json_decode($validatedData['platforms'], true);
            if (!is_array($targetPlatforms) || empty($targetPlatforms)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select at least one platform to post to.'
                ], 400);
            }
            
            // Validate platforms
            $validPlatforms = ['facebook', 'instagram', 'twitter', 'linkedin'];
            foreach ($targetPlatforms as $platform) {
                if (!in_array($platform, $validPlatforms)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Invalid platform: {$platform}"
                    ], 400);
                }
            }
            
            // Check if user has connected accounts for selected platforms
            $connectedAccounts = $business->socialMediaAccounts()
                ->whereIn('platform', $targetPlatforms)
                ->where('is_active', true)
                ->get();
                
            if ($connectedAccounts->count() !== count($targetPlatforms)) {
                $missingPlatforms = array_diff($targetPlatforms, $connectedAccounts->pluck('platform')->toArray());
                return response()->json([
                    'success' => false,
                    'message' => 'Please connect your accounts for: ' . implode(', ', $missingPlatforms)
                ], 400);
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::channel('social_media')->error('Validation failed', [
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::channel('social_media')->error('Unexpected error in store method start', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }

        // Handle media files based on media type
        $mediaFiles = [];
        
        if ($validatedData['media_type'] === 'upload' && $request->hasFile('media')) {
            Log::channel('social_media')->debug('Processing uploaded media files', ['count' => 1]);
            try {
                $file = $request->file('media');
                $path = $file->store('marketing-posts/' . $business->getKey(), 'public');
                $fullUrl = Storage::url($path);
                $mediaFiles[] = $fullUrl;
                
                Log::channel('social_media')->debug('Media file stored with details', [
                    'original_name' => $file->getClientOriginalName(),
                    'stored_path' => $path,
                    'full_url' => $fullUrl,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ]);
            } catch (\Exception $e) {
                Log::channel('social_media')->error('Failed to store media file', [
                    'error' => $e->getMessage(),
                    'file_name' => $file->getClientOriginalName() ?? 'unknown'
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload media file: ' . $e->getMessage()
                ], 500);
            }
        } elseif ($validatedData['media_type'] === 'generate' && $validatedData['generated_video_url']) {
            // Use generated video URL
            $mediaFiles[] = $validatedData['generated_video_url'];
            Log::channel('social_media')->debug('Using generated video', [
                'video_url' => $validatedData['generated_video_url'],
                'video_id' => $validatedData['generated_video_id'] ?? 'unknown'
            ]);
        } elseif ($validatedData['media_type'] === 'generate-image') {
            // Use generated image - prioritize local path for social media posting
            if (isset($validatedData['generated_image_local_path']) && file_exists($validatedData['generated_image_local_path'])) {
                $mediaFiles[] = $validatedData['generated_image_local_path'];
                Log::channel('social_media')->debug('Using generated AI image (local path)', [
                    'local_path' => $validatedData['generated_image_local_path']
                ]);
            } elseif (isset($validatedData['generated_image_relative_path'])) {
                $localPath = storage_path('app/public/' . $validatedData['generated_image_relative_path']);
                if (file_exists($localPath)) {
                    $mediaFiles[] = $localPath;
                    Log::channel('social_media')->debug('Using generated AI image (relative path)', [
                        'local_path' => $localPath
                    ]);
                } else {
                    Log::channel('social_media')->warning('Generated image file not found at relative path', [
                        'relative_path' => $validatedData['generated_image_relative_path'],
                        'expected_path' => $localPath
                    ]);
                }
            } elseif (isset($validatedData['generated_image_url'])) {
                // Fallback: extract filename from URL and try to build local path
                $imageUrl = $validatedData['generated_image_url'];
                if (strpos($imageUrl, 'storage/marketing/generated-images/') !== false) {
                    $filename = basename(parse_url($imageUrl, PHP_URL_PATH));
                    $localPath = storage_path('app/public/marketing/generated-images/' . $filename);
                    
                    if (file_exists($localPath)) {
                        $mediaFiles[] = $localPath;
                        Log::channel('social_media')->debug('Using generated AI image (extracted from URL)', [
                            'local_path' => $localPath
                        ]);
                    } else {
                        Log::channel('social_media')->warning('Generated image file not found', [
                            'expected_path' => $localPath,
                            'public_url' => $imageUrl
                        ]);
                    }
                } else {
                    Log::channel('social_media')->warning('Cannot determine local path for generated image', [
                        'url' => $imageUrl
                    ]);
                }
            }
        }
        
        // Create the marketing post
        try {
            $post = MarketingPost::create([
                'user_id' => $user->id,
                'business_id' => $business->getKey(),
                'title' => $validatedData['title'],
                'content' => $validatedData['content'],
                'hashtags' => $validatedData['hashtags'] ? json_decode($validatedData['hashtags'], true) : null,
                'media_files' => $mediaFiles,
                'target_platforms' => $targetPlatforms,
                'status' => (isset($validatedData['scheduled_at']) && $validatedData['scheduled_at']) ? 'scheduled' : 'draft',
                'post_type' => isset($validatedData['scheduled_at']) && $validatedData['scheduled_at'] ? 'scheduled' : 'immediate',
                'scheduled_at' => $validatedData['scheduled_at'] ?? null,
                'metadata' => [
                    'media_type' => $validatedData['media_type'],
                    'generated_video_id' => $validatedData['generated_video_id'] ?? null,
                    'generated_image_url' => $validatedData['generated_image_url'] ?? null,
                    'created_via' => 'enhanced_modal'
                ]
            ]);
            
            Log::channel('social_media')->info('Marketing post created successfully', [
                'post_id' => $post->getKey(),
                'business_id' => $business->getKey(),
                'platforms' => $targetPlatforms,
                'has_media' => !empty($mediaFiles)
            ]);
            
            // Publish immediately if not scheduled
            if (!isset($validatedData['scheduled_at']) || !$validatedData['scheduled_at']) {
                $publishResult = $this->publishPost($post);
                
                if ($publishResult['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Post created and published successfully!',
                        'post_id' => $post->getKey(),
                        'publications' => $publishResult['publications']
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Post created but publishing failed: ' . $publishResult['message'],
                        'post_id' => $post->getKey()
                    ], 500);
                }
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'Post scheduled successfully!',
                    'post_id' => $post->getKey(),
                    'scheduled_at' => $validatedData['scheduled_at'] ?? null
                ]);
            }
            
        } catch (\Exception $e) {
            Log::channel('social_media')->error('Failed to create marketing post', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'business_id' => $business->getKey()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create post: ' . $e->getMessage()
            ], 500);
        }
    }


    public function show(MarketingPost $post)
    {
        $this->authorize('view', $post);
        
        $post->load(['publications.socialMediaAccount', 'user']);
        
        return view('marketing.posts.show', compact('post'));
    }

    public function edit(MarketingPost $post)
    {
        $this->authorize('update', $post);
        
        $connectedAccounts = SocialMediaAccount::where('business_id', $post->business->getKey())
            ->where('is_active', true)
            ->get();

        return view('marketing.posts.edit', compact('post', 'connectedAccounts'));
    }

    public function update(Request $request, MarketingPost $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2200',
            'hashtags' => 'nullable|string',
            'target_platforms' => 'required|array|min:1',
            'target_platforms.*' => 'string|in:facebook,instagram,twitter,linkedin',
            'post_type' => 'required|in:immediate,scheduled',
            'scheduled_at' => 'nullable|date|after:now',
            'generated_image_url' => 'nullable|string',
            'generated_image_local_path' => 'nullable|string',
            'generated_image_relative_path' => 'nullable|string',
        ]);

        // Process hashtags
        $hashtags = [];
        if ($request->input('hashtags')) {
            $hashtags = array_filter(array_map(function($tag) {
                return '#' . ltrim(trim($tag), '#');
            }, explode(' ', $request->input('hashtags'))));
        }

        // Resolve new generated image if provided
        $mediaFiles = $post->media_files ?? [];
        $localPath     = $request->input('generated_image_local_path');
        $relativePath  = $request->input('generated_image_relative_path');

        if ($localPath && file_exists($localPath)) {
            $mediaFiles = [$localPath];
        } elseif ($relativePath) {
            $absPath = storage_path('app/public/' . $relativePath);
            if (file_exists($absPath)) {
                $mediaFiles = [$absPath];
            }
        }

        $post->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'hashtags' => $hashtags,
            'target_platforms' => $request->input('target_platforms'),
            'post_type' => $request->input('post_type'),
            'scheduled_at' => $request->input('post_type') === 'scheduled' ? $request->input('scheduled_at') : null,
            'status' => $request->input('post_type') === 'immediate' ? 'pending' : 'scheduled',
            'media_files' => $mediaFiles,
        ]);

        return redirect()->route('marketing.posts.show', $post)
            ->with('success', 'Post updated successfully!');
    }

    public function destroy(MarketingPost $post)
    {
        $this->authorize('delete', $post);
        
        // Delete associated media files
        if ($post->media_files) {
            foreach ($post->media_files as $file) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $file));
            }
        }

        $post->delete();

        return redirect()->route('marketing.social-media')
            ->with('success', 'Post deleted successfully!');
    }

   public function publish(MarketingPost $post)
    {
        $this->authorize('update', $post);
        
        if (!Auth::user()->business->isPremium()) {
            Log::channel('social_media')->warning('Premium required for publishing', [
                'user_id' => Auth::id(),
                'post_id' => $post->id
            ]);
            return response()->json(['error' => 'Premium subscription required for auto-posting'], 403);
        }

        Log::channel('social_media')->info('Manual publish initiated', [
            'post_id' => $post->getKey(),
            'user_id' => Auth::id()
        ]);

        $result = $this->publishPost($post);

        if ($result['success']) {
            Log::channel('social_media')->info('Manual publish successful', [
                'post_id' => $post->getKey(),
                'result' => $result
            ]);
            return response()->json(['message' => 'Post published successfully!']);
        } else {
            Log::channel('social_media')->error('Manual publish failed', [
                'post_id' => $post->getKey(),
                'error' => $result['message']
            ]);
            return response()->json(['error' => $result['message']], 500);
        }
    }


  protected function publishPost(MarketingPost $post)
    {
        $business = $post->business;
        $successCount = 0;
        $failureCount = 0;
        $errors = [];

        Log::channel('social_media')->info('Starting post publication', [
            'post_id' => $post->getKey(),
            'business_id' => $business->getKey(),
            'target_platforms' => $post->target_platforms
        ]);

        // Get connected accounts for target platforms
        $accounts = SocialMediaAccount::where('business_id', $business->getKey())
            ->whereIn('platform', $post->target_platforms)
            ->where('is_active', true)
            ->get();

        Log::channel('social_media')->debug('Found accounts for publishing', [
            'count' => $accounts->count(),
            'platforms' => $accounts->pluck('platform')->toArray()
        ]);

        foreach ($accounts as $account) {
            try {
                Log::channel('social_media')->info('Attempting to publish to account', [
                    'platform' => $account->platform,
                    'account_id' => $account->getKey(),
                    'post_id' => $post->getKey(),
                    'post_title' => $post->getAttribute('title'),
                    'media_count' => count($post->media_files)
                ]);

                // Special logging for LinkedIn
                if ($account->platform === 'linkedin') {
                    Log::channel('social_media')->debug('LinkedIn specific pre-check', [
                        'access_token_expired' => $account->isTokenExpired(),
                        'api_config' => config('services.linkedin')
                    ]);
                }

                $result = $this->socialMediaService->publishToAccount($post, $account);
                
                Log::channel('social_media')->info('Publish result', [
                    'platform' => $account->platform,
                    'account_id' => $account->getKey(),
                    'result' => $result
                ]);

                if ($result['success']) {
                    $successCount++;
                    Log::channel('social_media')->info('Successfully published', [
                        'platform' => $account->platform,
                        'external_id' => $result['external_id'] ?? null
                    ]);
                } else {
                    $failureCount++;
                    $errors[] = $account->platform . ': ' . $result['message'];
                    Log::channel('social_media')->error('Publish failed', [
                        'platform' => $account->platform,
                        'account_id' => $account->getKey(),
                        'error' => $result['message'],
                        'response' => $result['response'] ?? null
                    ]);
                }
            } catch (\Exception $e) {
                $failureCount++;
                $errors[] = $account->platform . ': ' . $e->getMessage();
                Log::channel('social_media')->error('Exception during publish', [
                    'platform' => $account->platform,
                    'account_id' => $account->getKey(),
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        // Update post status
        $status = 'failed';
        if ($successCount > 0 && $failureCount === 0) {
            $status = 'published';
        } elseif ($successCount > 0) {
            $status = 'partially_published';
        }

        $post->update(['status' => $status]);

        Log::channel('social_media')->info('Publication summary', [
            'post_id' => $post->getKey(),
            'status' => $status,
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'errors' => $errors
        ]);

        return [
            'success' => $successCount > 0,
            'message' => $successCount > 0 
                ? "Published to {$successCount} platform(s)" . ($failureCount > 0 ? " with {$failureCount} failure(s)" : '')
                : 'Failed to publish to any platform: ' . implode(', ', $errors),
            'publications' => [
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'errors' => $errors,
                'status' => $status
            ]
        ];
    }


    public function analytics(MarketingPost $post)
    {
        $this->authorize('view', $post);
        
        $post->load(['publications.socialMediaAccount']);
        
        // Aggregate engagement data from all publications
        $totalEngagement = [
            'likes' => 0,
            'shares' => 0,
            'comments' => 0,
            'reach' => 0
        ];

        foreach ($post->publications as $publication) {
            if ($publication->engagement_metrics) {
                foreach ($totalEngagement as $metric => $value) {
                    $totalEngagement[$metric] += $publication->engagement_metrics[$metric] ?? 0;
                }
            }
        }

        return view('marketing.posts.analytics', compact('post', 'totalEngagement'));
    }

    public function duplicate(MarketingPost $post)
    {
        $this->authorize('view', $post);
        
        $newPost = $post->replicate();
        $newPost->setAttribute('title', 'Copy of ' . $post->getAttribute('title'));
        $newPost->status = 'draft';
        $newPost->scheduled_at = null;
        $newPost->created_at = now();
        $newPost->updated_at = now();
        $newPost->save();

        return redirect()->route('marketing.posts.edit', $newPost)
            ->with('success', 'Post duplicated successfully!');
    }

    /**
     * Generate marketing content using AI
     */
    public function generateContent(Request $request)
    {
        $request->validate([
            'keywords' => 'required|string|max:500',
            'title' => 'nullable|string|max:255',
            'hashtags' => 'nullable|string|max:500',
        ]);

        try {
            $business = Auth::user()->business;
            
            $generated = $this->claudeService->generateMarketingContent(
                $request->keywords,
                $business->name,
                $business->business_type,
                $request->title,
                $request->hashtags
            );

            return response()->json([
                'success' => true,
                'content' => $generated['content'],
                'suggested_hashtags' => $generated['hashtags'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('Marketing Content Generation Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate content. Please try again.',
            ], 500);
        }
    }

    /**
     * Enhance existing marketing content using AI
     */
    public function enhanceContent(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2200',
        ]);

        try {
            $business = Auth::user()->business;
            
            $enhanced = $this->claudeService->enhanceMarketingContent(
                $request->input('content'),
                $business->name,
                $business->business_type
            );

            return response()->json([
                'success' => true,
                'enhanced_content' => $enhanced,
            ]);

        } catch (\Exception $e) {
            Log::error('Marketing Content Enhancement Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to enhance content. Please try again.',
            ], 500);
        }
    }

    /**
     * Generate AI image prompts based on post content
     */
    public function generateImagePrompts(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2200',
            'title' => 'nullable|string|max:255',
        ]);

        try {
            $business = Auth::user()->business;
            
            $prompts = $this->claudeService->generateMarketingImagePrompts(
                $request->input('content'),
                $request->input('title'),
                $business->name,
                $business->business_type
            );

            return response()->json([
                'success' => true,
                'prompts' => $prompts,
            ]);

        } catch (\Exception $e) {
            Log::error('Image Prompts Generation Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate image prompts. Please try again.',
            ], 500);
        }
    }

    /**
     * Enhance user's image prompt with AI
     */
    public function enhanceImagePrompt(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:500',
            'post_content' => 'nullable|string|max:2200',
            'style' => 'nullable|string|max:50',
        ]);

        try {
            $business = Auth::user()->business;
            
            $enhanced = $this->claudeService->enhanceImagePromptForUser(
                $request->prompt,
                $business->name,
                $business->business_type,
                $request->post_content,
                $request->style
            );

            return response()->json([
                'success' => true,
                'enhanced_prompt' => $enhanced,
            ]);

        } catch (\Exception $e) {
            Log::error('Image Prompt Enhancement Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to enhance prompt. Please try again.',
            ], 500);
        }
    }

    /**
     * Generate video prompts for marketing post
     */
    public function generateVideoPrompts(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2200',
            'title' => 'nullable|string|max:255',
        ]);

        try {
            $business = Auth::user()->business;
            
            $prompts = $this->claudeService->generateMarketingVideoPrompts(
                $request->input('content'),
                $request->input('title'),
                $business->name,
                $business->business_type
            );

            return response()->json([
                'success' => true,
                'prompts' => $prompts,
            ]);

        } catch (\Exception $e) {
            Log::error('Video Prompts Generation Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate video prompts. Please try again.',
            ], 500);
        }
    }

    /**
     * Enhance user's video prompt with AI
     */
    public function enhanceVideoPrompt(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:500',
            'post_content' => 'nullable|string|max:2200',
            'style' => 'nullable|string|max:50',
            'duration' => 'nullable|string|max:10',
        ]);

        try {
            $business = Auth::user()->business;
            
            $enhanced = $this->claudeService->enhanceVideoPromptForUser(
                $request->prompt,
                $business->name,
                $business->business_type,
                $request->post_content,
                $request->style,
                $request->duration
            );

            return response()->json([
                'success' => true,
                'enhanced_prompt' => $enhanced,
            ]);

        } catch (\Exception $e) {
            Log::error('Video Prompt Enhancement Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to enhance video prompt. Please try again.',
            ], 500);
        }
    }

    /**
     * Generate AI image for marketing post
     */
    public function generateImage(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'style' => 'nullable|string|max:50',
            'size' => 'nullable|string|max:20',
            'post_content' => 'nullable|string|max:5000',
            'post_title' => 'nullable|string|max:255',
        ]);

        try {
            $business = Auth::user()->business;
            
            Log::info('Image generation request', [
                'prompt' => substr($request->prompt, 0, 100),
                'style' => $request->style ?? 'realistic',
                'size' => $request->size ?? '1024x1024',
                'business' => $business->name
            ]);
            
            $imageData = $this->claudeService->generateMarketingImage(
                $request->prompt,
                $request->style ?? 'realistic',
                $request->size ?? '1024x1024',
                $business->name,
                $business->business_type,
                $request->post_content,
                $request->post_title
            );

            if ($imageData && isset($imageData['public_url'])) {
                // Verify the file actually exists
                $storagePath = storage_path('app/public/' . $imageData['relative_path']);
                
                if (file_exists($storagePath)) {
                    $fileSize = filesize($storagePath);
                    
                    Log::info('Image generation successful', [
                        'public_url' => $imageData['public_url'],
                        'file_path' => $storagePath,
                        'file_size' => $fileSize
                    ]);
                    
                    return response()->json([
                        'success' => true,
                        'image_url' => $imageData['public_url'],
                        'local_path' => $imageData['local_path'],
                        'relative_path' => $imageData['relative_path'],
                        'file_size' => $fileSize,
                    ]);
                } else {
                    Log::error('Generated image file not found', [
                        'expected_path' => $storagePath,
                        'relative_path' => $imageData['relative_path']
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Image was generated but could not be saved properly. Please try again.',
                    ], 500);
                }
            } else {
                Log::error('Image data incomplete', ['imageData' => $imageData]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate and download image. Please try again with a different prompt.',
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Image Generation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'prompt' => substr($request->prompt ?? '', 0, 100)
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate image: ' . $e->getMessage(),
            ], 500);
        }
    }
}

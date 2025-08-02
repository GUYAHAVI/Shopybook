<?php

namespace App\Http\Controllers;

use App\Models\MarketingPost;
use App\Models\SocialMediaAccount;
use App\Services\SocialMediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MarketingPostController extends Controller
{
    protected $socialMediaService;

    public function __construct(SocialMediaService $socialMediaService)
    {
        $this->socialMediaService = $socialMediaService;
    }
    public function index()
    {
        $business = Auth::user()->business;
        
        $connectedAccounts = SocialMediaAccount::where('business_id', $business->id)
            ->where('is_active', true)
            ->get();

        $recentPosts = MarketingPost::where('business_id', $business->id)
            ->with(['publications.socialMediaAccount'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('marketing.social-media', compact('connectedAccounts', 'recentPosts'));
    }

     public function store(Request $request)
    {
        try {
            Log::channel('social_media')->info('Starting post creation', ['user_id' => Auth::id()]);
            
            // Check if user has business
            $user = Auth::user();
            if (!$user->business) {
                Log::channel('social_media')->error('User has no business', ['user_id' => $user->id]);
                return redirect()->route('marketing.social-media')
                    ->with('error', 'You must have a business profile to create posts.');
            }
            
            Log::channel('social_media')->debug('Validating request data', ['request_data' => $request->all()]);
            
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string|max:2200',
                'hashtags' => 'nullable|string',
                'target_platforms' => 'required|array|min:1',
                'target_platforms.*' => 'string|in:facebook,instagram,twitter,linkedin',
                'post_type' => 'required|in:immediate,scheduled',
                'scheduled_at' => 'nullable|date|after:now',
                'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov|max:10240'
            ]);
            
            Log::channel('social_media')->info('Validation passed', ['validated_data' => $validatedData]);

            $business = $user->business;
            Log::channel('social_media')->debug('Business loaded', ['business_id' => $business->id, 'is_premium' => $business->isPremium()]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::channel('social_media')->error('Validation failed', [
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::channel('social_media')->error('Unexpected error in store method start', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);
            return redirect()->route('marketing.social-media')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }

        // Handle media uploads
        $mediaFiles = [];
        if ($request->hasFile('media')) {
            Log::channel('social_media')->debug('Processing media files', ['count' => count($request->file('media'))]);
            foreach ($request->file('media') as $file) {
                try {
                    $path = $file->store('marketing-posts/' . $business->id, 'public');
                    $fullUrl = Storage::url($path);
                    $mediaFiles[] = $fullUrl;
                    
                    // Enhanced logging for media file storage
                    Log::channel('social_media')->debug('Media file stored with details', [
                        'original_name' => $file->getClientOriginalName(),
                        'stored_path' => $path,
                        'full_url' => $fullUrl,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'storage_disk_path' => storage_path('app/public/' . $path)
                    ]);
                } catch (\Exception $e) {
                    Log::channel('social_media')->error('Media upload failed', [
                        'error' => $e->getMessage(),
                        'file' => $file->getClientOriginalName(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        }

        // Process hashtags
        $hashtags = [];
        if ($request->input('hashtags')) {
            $hashtags = array_filter(array_map(function($tag) {
                return '#' . ltrim(trim($tag), '#');
            }, explode(' ', $request->input('hashtags'))));
            Log::channel('social_media')->debug('Processed hashtags', ['hashtags' => $hashtags]);
        }

        try {
            $post = MarketingPost::create([
                'business_id' => $business->id,
                'user_id' => Auth::id(),
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'media_files' => $mediaFiles,
                'hashtags' => $hashtags,
                'target_platforms' => $request->input('target_platforms'),
                'post_type' => $request->input('post_type'),
                'scheduled_at' => $request->input('post_type') === 'scheduled' ? $request->input('scheduled_at') : null,
                'status' => $request->input('post_type') === 'immediate' ? 'pending' : 'scheduled',
            ]);

            Log::channel('social_media')->info('Post created successfully', [
                'post_id' => $post->id,
                'target_platforms' => $post->target_platforms,
                'post_type' => $post->post_type
            ]);

            // If immediate posting and business is premium, publish now
            if ($request->input('post_type') === 'immediate' && $business->isPremium()) {
                Log::channel('social_media')->info('Attempting immediate publication');
                $this->publishPost($post);
            }

            return redirect()->route('marketing.social-media')
                ->with('success', 'Marketing post created successfully!');

        } catch (\Exception $e) {
            Log::channel('social_media')->error('Post creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to create post: ' . $e->getMessage());
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
        
        $connectedAccounts = SocialMediaAccount::where('business_id', $post->business->id)
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
        ]);

        // Process hashtags
        $hashtags = [];
        if ($request->input('hashtags')) {
            $hashtags = array_filter(array_map(function($tag) {
                return '#' . ltrim(trim($tag), '#');
            }, explode(' ', $request->input('hashtags'))));
        }

        $post->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'hashtags' => $hashtags,
            'target_platforms' => $request->input('target_platforms'),
            'post_type' => $request->input('post_type'),
            'scheduled_at' => $request->input('post_type') === 'scheduled' ? $request->input('scheduled_at') : null,
            'status' => $request->input('post_type') === 'immediate' ? 'pending' : 'scheduled',
        ]);

        return redirect()->route('marketing.social-media')
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
            'post_id' => $post->id,
            'user_id' => Auth::id()
        ]);

        $result = $this->publishPost($post);

        if ($result['success']) {
            Log::channel('social_media')->info('Manual publish successful', [
                'post_id' => $post->id,
                'result' => $result
            ]);
            return response()->json(['message' => 'Post published successfully!']);
        } else {
            Log::channel('social_media')->error('Manual publish failed', [
                'post_id' => $post->id,
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
            'post_id' => $post->id,
            'business_id' => $business->id,
            'target_platforms' => $post->target_platforms
        ]);

        // Get connected accounts for target platforms
        $accounts = SocialMediaAccount::where('business_id', $business->id)
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
                    'account_id' => $account->id,
                    'post_id' => $post->id,
                    'post_title' => $post->title,
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
                    'account_id' => $account->id,
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
                        'account_id' => $account->id,
                        'error' => $result['message'],
                        'response' => $result['response'] ?? null
                    ]);
                }
            } catch (\Exception $e) {
                $failureCount++;
                $errors[] = $account->platform . ': ' . $e->getMessage();
                Log::channel('social_media')->error('Exception during publish', [
                    'platform' => $account->platform,
                    'account_id' => $account->id,
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
            'post_id' => $post->id,
            'status' => $status,
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'errors' => $errors
        ]);

        return [
            'success' => $successCount > 0,
            'message' => $successCount > 0 
                ? "Published to {$successCount} platform(s)" . ($failureCount > 0 ? " with {$failureCount} failure(s)" : '')
                : 'Failed to publish to any platform: ' . implode(', ', $errors)
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
        $newPost->title = 'Copy of ' . $post->getAttribute('title');
        $newPost->status = 'draft';
        $newPost->scheduled_at = null;
        $newPost->created_at = now();
        $newPost->updated_at = now();
        $newPost->save();

        return redirect()->route('marketing.posts.edit', $newPost)
            ->with('success', 'Post duplicated successfully!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\MarketingPost;
use App\Models\SocialMediaAccount;
use App\Services\SocialMediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2200',
            'hashtags' => 'nullable|string',
            'target_platforms' => 'required|array|min:1',
            'target_platforms.*' => 'string|in:facebook,instagram,twitter,linkedin',
            'post_type' => 'required|in:immediate,scheduled',
            'scheduled_at' => 'nullable|date|after:now',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov|max:10240' // 10MB max
        ]);

        $business = Auth::user()->business;

        // Handle media uploads
        $mediaFiles = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('marketing-posts/' . $business->id, 'public');
                $mediaFiles[] = Storage::url($path);
            }
        }

        // Process hashtags
        $hashtags = [];
        if ($request->input('hashtags')) {
            $hashtags = array_filter(array_map(function($tag) {
                return '#' . ltrim(trim($tag), '#');
            }, explode(' ', $request->input('hashtags'))));
        }

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

        // If immediate posting and business is premium, publish now
        if ($request->input('post_type') === 'immediate' && $business->isPremium()) {
            $this->publishPost($post);
        }

        return redirect()->route('marketing.social-media')
            ->with('success', 'Marketing post created successfully!');
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
            return response()->json(['error' => 'Premium subscription required for auto-posting'], 403);
        }

        $result = $this->publishPost($post);

        if ($result['success']) {
            return response()->json(['message' => 'Post published successfully!']);
        } else {
            return response()->json(['error' => $result['message']], 500);
        }
    }

    protected function publishPost(MarketingPost $post)
    {
        $business = $post->business;
        $successCount = 0;
        $failureCount = 0;
        $errors = [];

        // Get connected accounts for target platforms
        $accounts = SocialMediaAccount::where('business_id', $business->id)
            ->whereIn('platform', $post->target_platforms)
            ->where('is_active', true)
            ->get();

        foreach ($accounts as $account) {
            try {
                $result = $this->socialMediaService->publishToAccount($post, $account);
                
                if ($result['success']) {
                    $successCount++;
                } else {
                    $failureCount++;
                    $errors[] = $account->platform . ': ' . $result['message'];
                }
            } catch (\Exception $e) {
                $failureCount++;
                $errors[] = $account->platform . ': ' . $e->getMessage();
            }
        }

        // Update post status
        if ($successCount > 0 && $failureCount === 0) {
            $post->update(['status' => 'published']);
        } elseif ($successCount > 0) {
            $post->update(['status' => 'partially_published']);
        } else {
            $post->update(['status' => 'failed']);
        }

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

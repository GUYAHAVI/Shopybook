<?php

namespace App\Console\Commands;

use App\Models\MarketingPost;
use App\Models\SocialMediaAccount;
use App\Services\SocialMediaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishScheduledPosts extends Command
{
    protected $signature = 'marketing:publish-scheduled';

    protected $description = 'Publish scheduled marketing posts that are due';

    public function handle(SocialMediaService $socialMediaService): int
    {
        $posts = MarketingPost::where('post_type', 'scheduled')
            ->whereIn('status', ['scheduled', 'pending'])
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($posts->isEmpty()) {
            $this->info('No scheduled posts due for publishing.');
            return Command::SUCCESS;
        }

        $this->info("Found {$posts->count()} scheduled post(s) to publish.");

        foreach ($posts as $post) {
            try {
                Log::info('Publishing scheduled post', [
                    'post_id' => $post->getKey(),
                    'scheduled_at' => $post->scheduled_at,
                    'title' => $post->title,
                ]);

                $business = $post->business;

                $accounts = SocialMediaAccount::where('business_id', $business->getKey())
                    ->whereIn('platform', $post->target_platforms)
                    ->where('is_active', true)
                    ->get();

                if ($accounts->isEmpty()) {
                    $this->warn("Post #{$post->getKey()}: no connected accounts found, skipping.");
                    $post->update(['status' => 'failed']);
                    continue;
                }

                $successCount = 0;
                $failCount = 0;

                foreach ($accounts as $account) {
                    $result = $socialMediaService->publishToAccount($post, $account);
                    if ($result['success']) {
                        $successCount++;
                    } else {
                        $failCount++;
                        Log::error('Scheduled post publication failed', [
                            'post_id' => $post->getKey(),
                            'platform' => $account->platform,
                            'error' => $result['message'],
                        ]);
                    }
                }

                if ($successCount > 0 && $failCount === 0) {
                    $post->update(['status' => 'published']);
                } elseif ($successCount > 0) {
                    $post->update(['status' => 'partially_published']);
                } else {
                    $post->update(['status' => 'failed']);
                }

                $this->info("Post #{$post->getKey()}: {$successCount} succeeded, {$failCount} failed.");

            } catch (\Exception $e) {
                Log::error('Exception while publishing scheduled post', [
                    'post_id' => $post->getKey(),
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                $post->update(['status' => 'failed']);
                $this->error("Post #{$post->getKey()} threw an exception: " . $e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}

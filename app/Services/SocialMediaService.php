<?php

namespace App\Services;

use App\Models\SocialMediaAccount;
use App\Models\MarketingPost;
use App\Models\PostPublication;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialMediaService
{
    /**
     * Publish a marketing post to all connected platforms
     */
    public function publishPost(MarketingPost $post): array
    {
        $results = [];
        $socialAccounts = $post->business->socialMediaAccounts()
            ->whereIn('platform', $post->getAttribute('target_platforms'))
            ->where('is_active', true)
            ->get();

        foreach ($socialAccounts as $account) {
            $result = $this->publishToAccount($post, $account);
            $results[$account->getAttribute('platform')] = $result;
        }

        // Update post status
        $allSuccessful = collect($results)->every(fn($result) => $result['success']);
        $post->update([
            'status' => $allSuccessful ? 'published' : 'partial'
        ]);

        return $results;
    }

    /**
     * Publish to specific social media account
     */
    public function publishToAccount(MarketingPost $post, SocialMediaAccount $account): array
    {
        try {
            $publication = PostPublication::create([
                'marketing_post_id' => $post->getAttribute('id'),
                'social_media_account_id' => $account->getAttribute('id'),
                'status' => 'pending',
            ]);

            $result = match ($account->getAttribute('platform')) {
                'facebook' => $this->publishToFacebook($post, $account),
                'instagram' => $this->publishToInstagram($post, $account),
                'twitter' => $this->publishToTwitter($post, $account),
                'linkedin' => $this->publishToLinkedIn($post, $account),
                'tiktok' => $this->publishToTikTok($post, $account),
                'youtube' => $this->publishToYouTube($post, $account),
                'pinterest' => $this->publishToPinterest($post, $account),
                'snapchat' => $this->publishToSnapchat($post, $account),
                'whatsapp' => $this->publishToWhatsApp($post, $account),
                'telegram' => $this->publishToTelegram($post, $account),
                'discord' => $this->publishToDiscord($post, $account),
                'reddit' => $this->publishToReddit($post, $account),
                default => ['success' => false, 'message' => 'Platform not supported'],
            };

            $publication->update([
                'status' => $result['success'] ? 'published' : 'failed',
                'platform_post_id' => $result['post_id'] ?? null,
                'platform_response' => json_encode($result),
                'error_message' => $result['success'] ? null : $result['message'],
                'published_at' => $result['success'] ? now() : null,
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error("Failed to publish post to {$account->getAttribute('platform')}", [
                'post_id' => $post->getAttribute('id'),
                'account_id' => $account->getAttribute('id'),
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Publishing failed: ' . $e->getMessage()
            ];
        }
    }

    // Platform-specific publishing methods
    private function publishToFacebook(MarketingPost $post, SocialMediaAccount $account): array
    {
        try {
            $accessToken = decrypt($account->getAttribute('access_token'));
            $content = $post->getAttribute('content');
            
            if ($post->getAttribute('hashtags')) {
                $content .= $account->formatHashtags($post->getAttribute('hashtags'));
            }

            $data = ['message' => $content];
            if ($post->getAttribute('media_files') && count($post->getAttribute('media_files')) > 0) {
                $data['link'] = $post->getAttribute('media_files')[0];
            }

            $response = Http::post("https://graph.facebook.com/v18.0/me/feed", array_merge($data, [
                'access_token' => $accessToken
            ]));

            if ($response->successful()) {
                return ['success' => true, 'post_id' => $response->json()['id'], 'message' => 'Posted to Facebook successfully'];
            }

            return ['success' => false, 'message' => 'Facebook API error: ' . $response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Facebook publishing failed: ' . $e->getMessage()];
        }
    }

    private function publishToInstagram(MarketingPost $post, SocialMediaAccount $account): array
    {
        try {
            $accessToken = decrypt($account->getAttribute('access_token'));
            $content = $post->getAttribute('content');
            
            if ($post->getAttribute('hashtags')) {
                $content .= $account->formatHashtags($post->getAttribute('hashtags'));
            }

            if (!$post->getAttribute('media_files') || count($post->getAttribute('media_files')) === 0) {
                return ['success' => false, 'message' => 'Instagram requires at least one image or video'];
            }

            $mediaUrl = $post->getAttribute('media_files')[0];
            
            // Create media container
            $containerResponse = Http::post("https://graph.facebook.com/v18.0/me/media", [
                'image_url' => $mediaUrl,
                'caption' => $content,
                'access_token' => $accessToken
            ]);

            if (!$containerResponse->successful()) {
                return ['success' => false, 'message' => 'Instagram container creation failed: ' . $containerResponse->body()];
            }

            // Publish the media
            $publishResponse = Http::post("https://graph.facebook.com/v18.0/me/media_publish", [
                'creation_id' => $containerResponse->json()['id'],
                'access_token' => $accessToken
            ]);

            if ($publishResponse->successful()) {
                return ['success' => true, 'post_id' => $publishResponse->json()['id'], 'message' => 'Posted to Instagram successfully'];
            }

            return ['success' => false, 'message' => 'Instagram publishing failed: ' . $publishResponse->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Instagram publishing failed: ' . $e->getMessage()];
        }
    }

    private function publishToTwitter(MarketingPost $post, SocialMediaAccount $account): array
    {
        try {
            $accessToken = decrypt($account->getAttribute('access_token'));
            $content = $post->getAttribute('content');
            
            if ($post->getAttribute('hashtags')) {
                $content .= $account->formatHashtags($post->getAttribute('hashtags'));
            }

            if (strlen($content) > 280) {
                $content = substr($content, 0, 277) . '...';
            }

            $tweetData = ['text' => $content];
            $response = Http::withToken($accessToken)->post('https://api.twitter.com/2/tweets', $tweetData);

            if ($response->successful()) {
                return ['success' => true, 'post_id' => $response->json()['data']['id'], 'message' => 'Posted to Twitter successfully'];
            }

            return ['success' => false, 'message' => 'Twitter API error: ' . $response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Twitter publishing failed: ' . $e->getMessage()];
        }
    }

    private function publishToLinkedIn(MarketingPost $post, SocialMediaAccount $account): array
    {
        try {
            $accessToken = decrypt($account->getAttribute('access_token'));
            $content = $post->getAttribute('content');
            
            if ($post->getAttribute('hashtags')) {
                $content .= $account->formatHashtags($post->getAttribute('hashtags'));
            }

            $shareData = [
                'author' => 'urn:li:person:' . $account->getAttribute('platform_user_id'),
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary' => ['text' => $content],
                        'shareMediaCategory' => 'NONE'
                    ]
                ],
                'visibility' => ['com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC']
            ];

            $response = Http::withToken($accessToken)
                ->withHeaders(['X-Restli-Protocol-Version' => '2.0.0'])
                ->post('https://api.linkedin.com/v2/ugcPosts', $shareData);

            if ($response->successful()) {
                return ['success' => true, 'post_id' => $response->json()['id'], 'message' => 'Posted to LinkedIn successfully'];
            }

            return ['success' => false, 'message' => 'LinkedIn API error: ' . $response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'LinkedIn publishing failed: ' . $e->getMessage()];
        }
    }

    private function publishToTikTok(MarketingPost $post, SocialMediaAccount $account): array
    {
        return ['success' => false, 'message' => 'TikTok API requires video content and business approval'];
    }

    private function publishToYouTube(MarketingPost $post, SocialMediaAccount $account): array
    {
        return ['success' => false, 'message' => 'YouTube API requires video content'];
    }

    private function publishToPinterest(MarketingPost $post, SocialMediaAccount $account): array
    {
        return ['success' => false, 'message' => 'Pinterest API requires image content'];
    }

    private function publishToSnapchat(MarketingPost $post, SocialMediaAccount $account): array
    {
        return ['success' => false, 'message' => 'Snapchat organic posting not available via API'];
    }

    private function publishToWhatsApp(MarketingPost $post, SocialMediaAccount $account): array
    {
        return ['success' => false, 'message' => 'WhatsApp Business API requires approval'];
    }

    private function publishToTelegram(MarketingPost $post, SocialMediaAccount $account): array
    {
        try {
            $botToken = config('services.telegram.bot_token');
            $channelId = $account->getAttribute('platform_data')['channel_id'] ?? null;
            
            if (!$channelId) {
                return ['success' => false, 'message' => 'Telegram channel ID not configured'];
            }

            $content = $post->getAttribute('content');
            if ($post->getAttribute('hashtags')) {
                $content .= $account->formatHashtags($post->getAttribute('hashtags'));
            }

            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $channelId,
                'text' => $content,
                'parse_mode' => 'HTML'
            ]);

            if ($response->successful()) {
                return ['success' => true, 'post_id' => $response->json()['result']['message_id'], 'message' => 'Posted to Telegram successfully'];
            }

            return ['success' => false, 'message' => 'Telegram API error: ' . $response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Telegram publishing failed: ' . $e->getMessage()];
        }
    }

    private function publishToDiscord(MarketingPost $post, SocialMediaAccount $account): array
    {
        try {
            $webhookUrl = $account->getAttribute('platform_data')['webhook_url'] ?? null;
            
            if (!$webhookUrl) {
                return ['success' => false, 'message' => 'Discord webhook URL not configured'];
            }

            $content = $post->getAttribute('content');
            if ($post->getAttribute('hashtags')) {
                $content .= $account->formatHashtags($post->getAttribute('hashtags'));
            }

            $response = Http::post($webhookUrl, [
                'content' => $content,
                'username' => $account->business->name
            ]);

            if ($response->successful()) {
                return ['success' => true, 'post_id' => 'discord_' . time(), 'message' => 'Posted to Discord successfully'];
            }

            return ['success' => false, 'message' => 'Discord webhook error: ' . $response->body()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Discord publishing failed: ' . $e->getMessage()];
        }
    }

    private function publishToReddit(MarketingPost $post, SocialMediaAccount $account): array
    {
        return ['success' => false, 'message' => 'Reddit API requires application approval'];
    }

    /**
     * Refresh OAuth token for a social media account
     */
    public function refreshToken(SocialMediaAccount $account): array
    {
        try {
            $refreshToken = decrypt($account->getAttribute('refresh_token'));
            
            if (!$refreshToken) {
                return ['success' => false, 'message' => 'No refresh token available. Please reconnect your account.'];
            }

            $response = match ($account->getAttribute('platform')) {
                'facebook' => Http::post('https://graph.facebook.com/v18.0/oauth/access_token', [
                    'grant_type' => 'fb_exchange_token',
                    'client_id' => config('services.facebook.client_id'),
                    'client_secret' => config('services.facebook.client_secret'),
                    'fb_exchange_token' => decrypt($account->getAttribute('access_token')),
                ]),
                'twitter' => Http::withHeaders([
                    'Authorization' => 'Basic ' . base64_encode(config('services.twitter.client_id') . ':' . config('services.twitter.client_secret')),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])->asForm()->post('https://api.twitter.com/2/oauth2/token', [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                ]),
                'linkedin' => Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                    'client_id' => config('services.linkedin.client_id'),
                    'client_secret' => config('services.linkedin.client_secret'),
                ]),
                default => null,
            };

            if (!$response || !$response->successful()) {
                return ['success' => false, 'message' => 'Failed to refresh token: ' . ($response?->body() ?? 'Platform not supported')];
            }

            $tokenData = $response->json();
            $account->update([
                'access_token' => encrypt($tokenData['access_token']),
                'refresh_token' => isset($tokenData['refresh_token']) ? encrypt($tokenData['refresh_token']) : $account->getAttribute('refresh_token'),
                'token_expires_at' => isset($tokenData['expires_in']) ? now()->addSeconds($tokenData['expires_in']) : null,
                'last_connected_at' => now(),
            ]);

            return ['success' => true, 'message' => 'Token refreshed successfully'];
        } catch (\Exception $e) {
            Log::error("Failed to refresh token for {$account->getAttribute('platform')}", [
                'account_id' => $account->getAttribute('id'),
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'message' => 'Token refresh failed: ' . $e->getMessage()];
        }
    }

    /**
     * Generate AI-powered content suggestions
     */
    public function generateContentSuggestions(string $businessType): array
    {
        $templates = [
            'retail' => [
                "🛍️ New arrivals are here! Check out our latest collection. #NewArrivals #Shopping #Fashion",
                "💫 Weekend special! Get 20% off on all items. Limited time offer! #WeekendDeal #Sale",
                "🌟 Customer love! Thanks to everyone supporting our business. #CustomerLove #Grateful"
            ],
            'service' => [
                "💼 Professional service you can trust. Book your appointment today! #ProfessionalService #BookNow",
                "🏆 Quality service, every time. Here's what our customers are saying... #Quality #Reviews",
                "📞 Need help? Our expert team is ready to assist you. Contact us today! #ExpertHelp"
            ],
            'salon' => [
                "💇‍♀️ Transform your look with our expert stylists! Book today. #HairTransformation #Beauty",
                "✨ Pamper yourself with our premium beauty treatments. You deserve it! #SelfCare #Beauty",
                "🌺 Fresh new styles for the new season! Come see what we can do. #NewSeason #Makeover"
            ]
        ];

        return $templates[$businessType] ?? $templates['retail'];
    }
}

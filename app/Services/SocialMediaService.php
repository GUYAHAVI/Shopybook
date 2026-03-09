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
            $platform = $account->getAttribute('platform');
            // Log the platform being published to
            try {
                Log::channel('social_media')->debug('publishToAccount platform', [
                    'platform' => $platform,
                    'account_id' => $account->getAttribute('id'),
                    'post_id' => $post->getAttribute('id')
                ]);
            } catch (\Exception $logEx) {
                Log::debug('publishToAccount platform (default channel fallback)', [
                    'platform' => $platform,
                    'account_id' => $account->getAttribute('id'),
                    'post_id' => $post->getAttribute('id')
                ]);
            }

            // Auto-refresh the token if it is already known to be expired
            if ($account->isTokenExpired()) {
                Log::info('Token expired before publishing, attempting auto-refresh', [
                    'platform' => $platform,
                    'account_id' => $account->getAttribute('id'),
                ]);
                $refreshResult = $this->refreshToken($account);
                if (!$refreshResult['success']) {
                    return [
                        'success' => false,
                        'message' => ucfirst($platform) . ' token expired and could not be refreshed. Please reconnect your ' . ucfirst($platform) . ' account.',
                    ];
                }
                // Reload account to get the fresh token
                $account->refresh();
            }

            $publication = PostPublication::create([
                'marketing_post_id' => $post->getAttribute('id'),
                'social_media_account_id' => $account->getAttribute('id'),
                'status' => 'pending',
            ]);

            $result = match ($platform) {
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
            
            $content .= $this->processHashtags($post, $account);

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
            
            $content .= $this->processHashtags($post, $account);

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
            
            $content .= $this->processHashtags($post, $account);

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
        Log::info('=== LinkedIn Function Called ===', [
            'post_id' => $post->getAttribute('id'),
            'account_id' => $account->getAttribute('id'),
        ]);

        $accessToken = decrypt($account->getAttribute('access_token'));
        $content     = $post->getAttribute('content');

        // Get author URN via v2/me — no LinkedIn-Version header needed
        $authorUrn = $this->getCorrectLinkedInURN($accessToken, $account);
        Log::debug('Determined LinkedIn URN', ['urn' => $authorUrn]);

        // Process hashtags
        $hashtags = $post->getAttribute('hashtags');
        if ($hashtags) {
            if (is_string($hashtags)) {
                $hashtags = json_decode($hashtags, true);
            }
            if (is_array($hashtags)) {
                $content .= $account->formatHashtags($hashtags);
            }
        }

        $mediaFiles       = $post->getAttribute('media_files') ?? [];
        $ugcMedia         = [];   // items for the ugcPost media array
        $shareMediaCategory = 'NONE';

        Log::debug('LinkedIn media files processing', [
            'count' => count($mediaFiles),
            'files' => $mediaFiles,
        ]);

        foreach ($mediaFiles as $mediaFile) {
            try {
                // Resolve to an absolute local path
                $filePath = file_exists($mediaFile)
                    ? $mediaFile
                    : storage_path('app/public/' . ltrim(str_replace('/storage/', '', $mediaFile), '/'));

                if (!file_exists($filePath)) {
                    Log::error('LinkedIn media file not found', ['media_file' => $mediaFile, 'resolved' => $filePath]);
                    continue;
                }

                $mimeType = mime_content_type($filePath);
                $isVideo  = str_starts_with($mimeType, 'video/');

                $assetUrn = $isVideo
                    ? $this->uploadLinkedInVideo($accessToken, $authorUrn, $filePath)
                    : $this->uploadLinkedInImage($accessToken, $authorUrn, $filePath);

                if (!$assetUrn) {
                    Log::error('LinkedIn asset upload returned null', ['file' => $mediaFile]);
                    continue;
                }

                $ugcMedia[] = [
                    'status' => 'READY',
                    'media'  => $assetUrn,
                    'title'  => ['text' => $isVideo ? 'Video' : 'Image'],
                ];
                $shareMediaCategory = $isVideo ? 'VIDEO' : 'IMAGE';

                Log::debug('LinkedIn asset uploaded', ['urn' => $assetUrn, 'type' => $isVideo ? 'video' : 'image']);
                sleep(1);

            } catch (\Exception $e) {
                Log::error('Exception during LinkedIn media processing', [
                    'error' => $e->getMessage(),
                    'file'  => $mediaFile,
                ]);
            }
        }

        // Build UGC post payload (v2/ugcPosts — no LinkedIn-Version header)
        $ugcPost = [
            'author'          => $authorUrn,
            'lifecycleState'  => 'PUBLISHED',
            'specificContent' => [
                'com.linkedin.ugc.ShareContent' => [
                    'shareCommentary'    => ['text' => $content],
                    'shareMediaCategory' => empty($ugcMedia) ? 'NONE' : $shareMediaCategory,
                ],
            ],
            'visibility' => [
                'com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC',
            ],
        ];

        if (!empty($ugcMedia)) {
            $ugcPost['specificContent']['com.linkedin.ugc.ShareContent']['media'] = $ugcMedia;
        }

        Log::debug('Final LinkedIn ugcPost payload', ['payload' => $ugcPost]);

        $response     = $this->postToLinkedIn($accessToken, $ugcPost);
        $responseData = $response->json();
        $statusCode   = $response->status();

        if ($response->successful()) {
            Log::info('LinkedIn post successful', ['post_id' => $responseData['id'] ?? null]);
            return [
                'success'  => true,
                'post_id'  => $responseData['id'] ?? null,
                'message'  => 'Posted to LinkedIn successfully',
            ];
        }

        // 401 → try token refresh once, then retry
        if ($statusCode === 401) {
            Log::warning('LinkedIn 401 — attempting token refresh', ['response' => $responseData]);
            $refreshResult = $this->refreshToken($account);
            if ($refreshResult['success']) {
                $account->refresh();
                $accessToken   = decrypt($account->getAttribute('access_token'));
                $retryResponse = $this->postToLinkedIn($accessToken, $ugcPost);

                if ($retryResponse->successful()) {
                    return [
                        'success' => true,
                        'post_id' => $retryResponse->json()['id'] ?? null,
                        'message' => 'Posted to LinkedIn successfully (after token refresh)',
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'LinkedIn token refreshed but posting still failed. Please reconnect your LinkedIn account.',
                ];
            }

            return [
                'success' => false,
                'message' => 'LinkedIn token expired and could not be refreshed. Please reconnect your LinkedIn account.',
            ];
        }

        // 400/422 with media → try text-only fallback
        if (in_array($statusCode, [400, 422]) && !empty($ugcMedia)) {
            Log::warning('LinkedIn media error — retrying text-only', ['status' => $statusCode, 'response' => $responseData]);

            $fallback = [
                'author'         => $authorUrn,
                'lifecycleState' => 'PUBLISHED',
                'specificContent' => [
                    'com.linkedin.ugc.ShareContent' => [
                        'shareCommentary'    => ['text' => $content . ' (Images could not be attached due to API limitations)'],
                        'shareMediaCategory' => 'NONE',
                    ],
                ],
                'visibility' => ['com.linkedin.ugc.MemberNetworkVisibility' => 'PUBLIC'],
            ];

            $fallbackResponse = $this->postToLinkedIn($accessToken, $fallback);
            if ($fallbackResponse->successful()) {
                return [
                    'success' => true,
                    'post_id' => $fallbackResponse->json()['id'] ?? null,
                    'message' => 'Posted to LinkedIn (text-only due to media upload issues)',
                ];
            }
        }

        Log::error('LinkedIn API error', ['status' => $statusCode, 'response' => $responseData]);
        return [
            'success'  => false,
            'message'  => 'LinkedIn API error: ' . ($responseData['message'] ?? $responseData['serviceErrorCode'] ?? 'Unknown error'),
            'response' => $responseData,
        ];

    } catch (\Exception $e) {
        Log::error('LinkedIn publishing failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return ['success' => false, 'message' => 'LinkedIn publishing failed: ' . $e->getMessage()];
    }
}

/** POST to v2/ugcPosts — no LinkedIn-Version header required */
private function postToLinkedIn(string $accessToken, array $payload): \Illuminate\Http\Client\Response
{
    return Http::withToken($accessToken)
        ->withHeaders([
            'X-Restli-Protocol-Version' => '2.0.0',
            'Content-Type'              => 'application/json',
        ])
        ->post('https://api.linkedin.com/v2/ugcPosts', $payload);
}

/** Upload image via v2/assets — no LinkedIn-Version header required */
private function uploadLinkedInImage(string $accessToken, string $authorUrn, string $filePath): ?string
{
    try {
        $registerResponse = Http::withToken($accessToken)
            ->withHeaders([
                'X-Restli-Protocol-Version' => '2.0.0',
                'Content-Type'              => 'application/json',
            ])
            ->post('https://api.linkedin.com/v2/assets?action=registerUpload', [
                'registerUploadRequest' => [
                    'recipes'              => ['urn:li:digitalmediaRecipe:feedshare-image'],
                    'owner'                => $authorUrn,
                    'serviceRelationships' => [[
                        'relationshipType' => 'OWNER',
                        'identifier'       => 'urn:li:userGeneratedContent',
                    ]],
                ],
            ]);

        if (!$registerResponse->successful()) {
            Log::error('LinkedIn image registration failed', [
                'status'   => $registerResponse->status(),
                'response' => $registerResponse->body(),
            ]);
            return null;
        }

        $data      = $registerResponse->json();
        $uploadUrl = $data['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'] ?? null;
        $assetUrn  = $data['value']['asset'] ?? null;

        if (!$uploadUrl || !$assetUrn) {
            Log::error('LinkedIn image registration missing fields', ['response' => $data]);
            return null;
        }

        $upload = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type'  => 'application/octet-stream',
        ])->withBody(file_get_contents($filePath), 'application/octet-stream')->put($uploadUrl);

        if (!$upload->successful()) {
            Log::error('LinkedIn image upload failed', ['status' => $upload->status(), 'response' => $upload->body()]);
            return null;
        }

        return $assetUrn;

    } catch (\Exception $e) {
        Log::error('Exception during LinkedIn image upload', ['error' => $e->getMessage()]);
        return null;
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
            $content .= $this->processHashtags($post, $account);

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
            $content .= $this->processHashtags($post, $account);

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

    /**
     * Resolve the author URN using v2/me — no LinkedIn-Version header needed
     */
    private function getCorrectLinkedInURN(string $accessToken, SocialMediaAccount $account): string
    {
        try {
            $response = Http::withToken($accessToken)
                ->withHeaders(['X-Restli-Protocol-Version' => '2.0.0'])
                ->get('https://api.linkedin.com/v2/me');

            if ($response->successful()) {
                $userInfo = $response->json();
                $userId   = $userInfo['id'] ?? null;

                Log::debug('LinkedIn v2/me response', ['user_info' => $userInfo]);

                if ($userId) {
                    if ($account->getAttribute('platform_user_id') !== $userId) {
                        $account->update(['platform_user_id' => $userId]);
                    }
                    return 'urn:li:person:' . $userId;
                }
            }

            Log::warning('LinkedIn v2/me failed, using stored ID', [
                'status'   => $response->status(),
                'response' => $response->body(),
            ]);

        } catch (\Exception $e) {
            Log::error('Exception while determining LinkedIn URN', ['error' => $e->getMessage()]);
        }

        return 'urn:li:person:' . $account->getAttribute('platform_user_id');
    }

    /**
     * Upload video via v2/assets — no LinkedIn-Version header needed
     */
    private function uploadLinkedInVideo(string $accessToken, string $authorUrn, string $filePath): ?string
    {
        try {
            $registerResponse = Http::withToken($accessToken)
                ->withHeaders([
                    'X-Restli-Protocol-Version' => '2.0.0',
                    'Content-Type'              => 'application/json',
                ])
                ->post('https://api.linkedin.com/v2/assets?action=registerUpload', [
                    'registerUploadRequest' => [
                        'recipes'                   => ['urn:li:digitalmediaRecipe:feedshare-video'],
                        'owner'                     => $authorUrn,
                        'serviceRelationships'      => [[
                            'relationshipType' => 'OWNER',
                            'identifier'       => 'urn:li:userGeneratedContent',
                        ]],
                        'supportedUploadMechanism'  => ['SYNCHRONOUS_UPLOAD'],
                    ],
                ]);

            if (!$registerResponse->successful()) {
                Log::error('LinkedIn video registration failed', [
                    'status'   => $registerResponse->status(),
                    'response' => $registerResponse->body(),
                ]);
                return null;
            }

            $data      = $registerResponse->json();
            $uploadUrl = $data['value']['uploadMechanism']['com.linkedin.digitalmedia.uploading.MediaUploadHttpRequest']['uploadUrl'] ?? null;
            $assetUrn  = $data['value']['asset'] ?? null;

            if (!$uploadUrl || !$assetUrn) {
                Log::error('LinkedIn video registration missing fields', ['response' => $data]);
                return null;
            }

            $upload = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/octet-stream',
            ])->withBody(file_get_contents($filePath), 'application/octet-stream')->put($uploadUrl);

            if (!$upload->successful()) {
                Log::error('LinkedIn video upload failed', ['status' => $upload->status(), 'response' => $upload->body()]);
                return null;
            }

            Log::debug('LinkedIn video uploaded', ['asset_urn' => $assetUrn]);
            return $assetUrn;

        } catch (\Exception $e) {
            Log::error('Exception during LinkedIn video upload', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Helper method to safely process hashtags
     */
    private function processHashtags($post, $account): string
    {
        $hashtags = $post->getAttribute('hashtags');
        
        if (!$hashtags) {
            return '';
        }
        
        // Ensure hashtags is an array
        if (is_string($hashtags)) {
            $hashtags = json_decode($hashtags, true);
        }
        
        if (is_array($hashtags)) {
            return $account->formatHashtags($hashtags);
        }
        
        return '';
    }
}

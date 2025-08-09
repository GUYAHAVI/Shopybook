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
        // Force logging to default channel to ensure we see it
        Log::info('=== LinkedIn Function Called ===', [
            'post_id' => $post->getAttribute('id'),
            'account_id' => $account->getAttribute('id')
        ]);
        $accessToken = decrypt($account->getAttribute('access_token'));
        $content = $post->getAttribute('content');
        
        // Determine the correct URN by querying LinkedIn API
        $authorUrn = $this->getCorrectLinkedInURN($accessToken, $account);
        
        Log::debug('Determined LinkedIn URN', [
            'urn' => $authorUrn,
            'platform_user_id' => $account->getAttribute('platform_user_id')
        ]);
        
        // Process hashtags
        if ($post->getAttribute('hashtags')) {
            $content .= $account->formatHashtags($post->getAttribute('hashtags'));
        }

        $mediaFiles = $post->getAttribute('media_files') ?? [];
        $mediaItems = [];
        $shareMediaCategory = 'NONE';

        // Enhanced logging for media files debugging
        Log::debug('LinkedIn media files processing', [
            'media_files_count' => count($mediaFiles),
            'media_files' => $mediaFiles,
            'post_id' => $post->getAttribute('id')
        ]);

        if (count($mediaFiles) > 0) {
            foreach ($mediaFiles as $mediaFile) {
                try {
                    $filePath = storage_path('app/public/' . str_replace('/storage/', '', $mediaFile));
                    
                    if (!file_exists($filePath)) {
                        Log::error('LinkedIn media file not found', [
                            'media_file' => $mediaFile,
                            'file_path' => $filePath
                        ]);
                        continue;
                    }
                    
                    // Determine if this is a video or image
                    $mimeType = mime_content_type($filePath);
                    $isVideo = strpos($mimeType, 'video/') === 0;
                    
                    Log::debug('LinkedIn media file detected', [
                        'file' => $mediaFile,
                        'mime_type' => $mimeType,
                        'is_video' => $isVideo,
                        'file_size' => filesize($filePath)
                    ]);
                    
                    if ($isVideo) {
                        // Process video file
                        $videoUrn = $this->uploadLinkedInVideo($accessToken, $authorUrn, $filePath);
                        if ($videoUrn) {
                            $mediaItems[] = [
                                'status' => 'READY',
                                'media' => $videoUrn,
                                'type' => 'video'
                            ];
                            $shareMediaCategory = 'VIDEO';
                            
                            Log::debug('LinkedIn video uploaded successfully', [
                                'urn' => $videoUrn
                            ]);
                        }
                    } else {
                        // Process image file (existing logic)
                        Log::debug('LinkedIn registering image with owner', [
                            'author_urn' => $authorUrn,
                            'platform_user_id' => $account->getAttribute('platform_user_id')
                        ]);
                        
                        $registerResponse = Http::withToken($accessToken)
                            ->withHeaders([
                                'X-Restli-Protocol-Version' => '2.0.0',
                                'LinkedIn-Version' => '202307',
                                'Content-Type' => 'application/json'
                            ])
                            ->post('https://api.linkedin.com/rest/images?action=initializeUpload', [
                                'initializeUploadRequest' => [
                                    'owner' => $authorUrn,
                                ]
                            ]);

                        if (!$registerResponse->successful()) {
                            Log::error('LinkedIn image registration failed', [
                                'status' => $registerResponse->status(),
                                'response' => $registerResponse->body()
                            ]);
                            continue;
                        }

                        $responseData = $registerResponse->json();
                        $uploadUrl = $responseData['value']['uploadUrl'] ?? null;
                        $imageUrn = $responseData['value']['image'] ?? null;

                        if (!$uploadUrl || !$imageUrn) {
                            Log::error('LinkedIn image registration missing required fields', [
                                'response' => $responseData
                            ]);
                            continue;
                        }

                        $imageData = file_get_contents($filePath);
                        
                        $uploadResponse = Http::withHeaders([
                            'Content-Type' => 'application/octet-stream',
                            'Authorization' => 'Bearer ' . $accessToken
                        ])->withBody($imageData, 'application/octet-stream')
                          ->put($uploadUrl);

                        if (!$uploadResponse->successful()) {
                            Log::error('LinkedIn image upload failed', [
                                'status' => $uploadResponse->status(),
                                'response' => $uploadResponse->body()
                            ]);
                            continue;
                        }

                        $mediaItems[] = [
                            'status' => 'READY',
                            'media' => $imageUrn,
                            'type' => 'image',
                            'title' => ['text' => 'Post Image']
                        ];
                        $shareMediaCategory = 'IMAGE';
                        
                        Log::debug('LinkedIn image uploaded successfully', [
                            'urn' => $imageUrn
                        ]);
                    }
                    
                    // Add delay to ensure processing
                    sleep(2);
                    
                } catch (\Exception $e) {
                    Log::error('Exception during LinkedIn media processing', [
                        'error' => $e->getMessage(),
                        'file' => $mediaFile,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }
        }

        // Build the post content using REST API format
        $postData = [
            'author' => $authorUrn,
            'commentary' => $content,
            'visibility' => 'PUBLIC',
            'lifecycleState' => 'PUBLISHED'
        ];

        // Add media if we have content
        if (!empty($mediaItems)) {
            // Check if we have videos
            $hasVideo = collect($mediaItems)->contains('type', 'video');
            $hasImages = collect($mediaItems)->contains('type', 'image');
            
            if ($hasVideo && !$hasImages) {
                // Video post (LinkedIn only supports one video per post)
                $videoItem = collect($mediaItems)->where('type', 'video')->first();
                $postData['content'] = [
                    'media' => [
                        'title' => 'Video Post',
                        'id' => $videoItem['media']
                    ]
                ];
                Log::debug('LinkedIn video post structure', [
                    'video_urn' => $videoItem['media']
                ]);
            } elseif ($hasImages && !$hasVideo) {
                // Image post(s)
                $imageItems = collect($mediaItems)->where('type', 'image')->values()->toArray();
                
                if (count($imageItems) === 1) {
                    // Single image post
                    $postData['content'] = [
                        'media' => [
                            'title' => 'Post with Image',
                            'id' => $imageItems[0]['media']
                        ]
                    ];
                } else {
                    // Multiple images - create carousel
                    $carouselItems = [];
                    foreach ($imageItems as $index => $imageItem) {
                        $carouselItems[] = [
                            'id' => $imageItem['media'],
                            'title' => 'Image ' . ($index + 1)
                        ];
                    }
                    
                    $postData['content'] = [
                        'multiImage' => [
                            'images' => $carouselItems
                        ]
                    ];
                }
            } else {
                // Mixed content - LinkedIn doesn't support this, use first video or first image
                Log::warning('LinkedIn mixed media detected, using first item only');
                $firstItem = $mediaItems[0];
                $postData['content'] = [
                    'media' => [
                        'title' => $firstItem['type'] === 'video' ? 'Video Post' : 'Post with Image',
                        'id' => $firstItem['media']
                    ]
                ];
            }
            
            Log::debug('LinkedIn media content structure', [
                'media_count' => count($mediaItems),
                'has_video' => $hasVideo,
                'has_images' => $hasImages,
                'content_structure' => $postData['content']
            ]);
        }
        
        // Add required distribution field
        $postData['distribution'] = [
            'feedDistribution' => 'MAIN_FEED',
            'targetEntities' => [],
            'thirdPartyDistributionChannels' => []
        ];

        Log::debug('Final LinkedIn post payload', ['payload' => $postData]);

        // Make the API request using REST API
        $response = Http::withToken($accessToken)
            ->withHeaders([
                'LinkedIn-Version' => '202307',
                'X-Restli-Protocol-Version' => '2.0.0',
                'Content-Type' => 'application/json'
            ])
            ->post('https://api.linkedin.com/rest/posts', $postData);

        $responseData = $response->json();
        $statusCode = $response->status();

        if ($response->successful()) {
            Log::info('LinkedIn post successful', [
                'post_id' => $responseData['id'] ?? null,
                'has_media' => !empty($mediaItems)
            ]);
            return [
                'success' => true,
                'post_id' => $responseData['id'] ?? null,
                'message' => 'Posted to LinkedIn successfully'
            ];
        }

        // If we get an error and have media, try text-only as fallback
        if (($statusCode === 400 || $statusCode === 422) && !empty($mediaItems)) {
            
            Log::warning('LinkedIn error with images, trying text-only fallback', [
                'status' => $statusCode,
                'response' => $responseData
            ]);
            
            // Retry with text-only post using REST API
            $fallbackPostData = [
                'author' => $authorUrn,
                'commentary' => $content . ' (Images could not be attached due to API limitations)',
                'visibility' => 'PUBLIC',
                'lifecycleState' => 'PUBLISHED'
            ];
            
            $fallbackResponse = Http::withToken($accessToken)
                ->withHeaders([
                    'LinkedIn-Version' => '202307',
                    'X-Restli-Protocol-Version' => '2.0.0',
                    'Content-Type' => 'application/json'
                ])
                ->post('https://api.linkedin.com/rest/posts', $fallbackPostData);
                
            if ($fallbackResponse->successful()) {
                $fallbackData = $fallbackResponse->json();
                Log::info('LinkedIn fallback text-only post successful', [
                    'post_id' => $fallbackData['id'] ?? null
                ]);
                return [
                    'success' => true,
                    'post_id' => $fallbackData['id'] ?? null,
                    'message' => 'Posted to LinkedIn successfully (text-only due to image ownership issues)'
                ];
            }
        }

        Log::error('LinkedIn API error', [
            'status' => $statusCode,
            'response' => $responseData
        ]);
        
        return [
            'success' => false,
            'message' => 'LinkedIn API error: ' . ($responseData['message'] ?? 'Unknown error'),
            'response' => $responseData
        ];
    } catch (\Exception $e) {
        Log::error('LinkedIn publishing failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return [
            'success' => false,
            'message' => 'LinkedIn publishing failed: ' . $e->getMessage()
        ];
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

    /**
     * Determine the correct LinkedIn URN by querying the API
     */
    private function getCorrectLinkedInURN(string $accessToken, SocialMediaAccount $account): string
    {
        try {
            // Query LinkedIn API to get user profile info using REST API
            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'LinkedIn-Version' => '202307',
                    'X-Restli-Protocol-Version' => '2.0.0'
                ])
                ->get('https://api.linkedin.com/rest/people?q=viewer&projection=(id)');

            if ($response->successful()) {
                $userInfo = $response->json();
                $userId = $userInfo['elements'][0]['id'] ?? null;
                
                Log::debug('LinkedIn people API response', [
                    'user_info' => $userInfo,
                    'extracted_user_id' => $userId
                ]);
                
                if ($userId) {
                    // Update the stored platform_user_id if it's different
                    if ($account->getAttribute('platform_user_id') !== $userId) {
                        $account->update(['platform_user_id' => $userId]);
                        Log::debug('Updated LinkedIn platform_user_id', [
                            'old_id' => $account->getAttribute('platform_user_id'),
                            'new_id' => $userId
                        ]);
                    }
                    
                    return 'urn:li:person:' . $userId;
                }
            }
            
            Log::warning('LinkedIn people API call failed, using stored ID', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Exception while determining LinkedIn URN', [
                'error' => $e->getMessage()
            ]);
        }
        
        // Fallback to stored platform_user_id
        return 'urn:li:person:' . $account->getAttribute('platform_user_id');
    }
    
    /**
     * Upload video to LinkedIn
     */
    private function uploadLinkedInVideo(string $accessToken, string $authorUrn, string $filePath): ?string
    {
        try {
            $fileSize = filesize($filePath);
            
            // Step 1: Initialize video upload
            Log::debug('LinkedIn initializing video upload', [
                'author_urn' => $authorUrn,
                'file_size' => $fileSize
            ]);
            
            $initResponse = Http::withToken($accessToken)
                ->withHeaders([
                    'X-Restli-Protocol-Version' => '2.0.0',
                    'LinkedIn-Version' => '202307',
                    'Content-Type' => 'application/json'
                ])
                ->post('https://api.linkedin.com/rest/videos?action=initializeUpload', [
                    'initializeUploadRequest' => [
                        'owner' => $authorUrn,
                        'fileSizeBytes' => $fileSize,
                        'uploadCaptions' => false,
                        'uploadThumbnail' => false
                    ]
                ]);

            if (!$initResponse->successful()) {
                Log::error('LinkedIn video initialization failed', [
                    'status' => $initResponse->status(),
                    'response' => $initResponse->body()
                ]);
                return null;
            }

            $initData = $initResponse->json();
            $uploadUrl = $initData['value']['uploadInstructions'][0]['uploadUrl'] ?? null;
            $videoUrn = $initData['value']['video'] ?? null;

            if (!$uploadUrl || !$videoUrn) {
                Log::error('LinkedIn video initialization missing data', [
                    'response' => $initData
                ]);
                return null;
            }

            // Step 2: Upload the video file
            Log::debug('LinkedIn uploading video file', [
                'video_urn' => $videoUrn,
                'upload_url' => $uploadUrl
            ]);
            
            $videoData = file_get_contents($filePath);
            
            $uploadResponse = Http::withHeaders([
                'Content-Type' => 'application/octet-stream',
                'Authorization' => 'Bearer ' . $accessToken
            ])->withBody($videoData, 'application/octet-stream')
              ->put($uploadUrl);

            if (!$uploadResponse->successful()) {
                Log::error('LinkedIn video upload failed', [
                    'status' => $uploadResponse->status(),
                    'response' => $uploadResponse->body()
                ]);
                return null;
            }

            // Step 3: Finalize upload
            $finalizeResponse = Http::withToken($accessToken)
                ->withHeaders([
                    'X-Restli-Protocol-Version' => '2.0.0',
                    'LinkedIn-Version' => '202307',
                    'Content-Type' => 'application/json'
                ])
                ->post('https://api.linkedin.com/rest/videos?action=finalizeUpload', [
                    'finalizeUploadRequest' => [
                        'video' => $videoUrn,
                        'uploadToken' => $initData['value']['uploadInstructions'][0]['uploadToken'] ?? ''
                    ]
                ]);

            if (!$finalizeResponse->successful()) {
                Log::error('LinkedIn video finalization failed', [
                    'status' => $finalizeResponse->status(),
                    'response' => $finalizeResponse->body()
                ]);
                return null;
            }

            Log::debug('LinkedIn video upload completed', [
                'video_urn' => $videoUrn
            ]);
            
            return $videoUrn;
            
        } catch (\Exception $e) {
            Log::error('Exception during LinkedIn video upload', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}

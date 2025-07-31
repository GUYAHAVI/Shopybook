<?php

namespace App\Http\Controllers;

use App\Models\SocialMediaAccount;
use App\Models\Business;
use App\Services\SocialMediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SocialMediaController extends Controller
{
    protected $socialMediaService;

    public function __construct(SocialMediaService $socialMediaService)
    {
        $this->socialMediaService = $socialMediaService;
    }

    /**
     * Initiate OAuth connection for a platform
     */
    public function connect(Request $request, $platform)
    {
        $business = Auth::user()->business;

        if (!$business->isPremium() && $business->socialMediaAccounts()->count() >= 1) {
            return redirect()->route('marketing.social-media')
                ->with('error', 'Free plan allows only 1 social media connection. Upgrade to Premium for unlimited connections.');
        }

        $redirectUrl = $this->getOAuthUrl($platform);

        if (!$redirectUrl) {
            return redirect()->route('marketing.social-media')
                ->with('error', "Social media platform '{$platform}' is not supported yet.");
        }

        // Generate and store state
        $state = Str::random(40);
        session([
            'oauth_state' => $state,
            'oauth_platform' => $platform,
            'oauth_business_id' => $business->id // Additional security
        ]);

        // Special handling for LinkedIn
        if ($platform === 'linkedin') {
            $redirectUrl .= (parse_url($redirectUrl, PHP_URL_QUERY) ? '&' : '?') . 'state=' . urlencode($state);
        }

        \Log::debug("Initiating OAuth connection", [
            'platform' => $platform,
            'state' => $state,
            'redirect_url' => $redirectUrl
        ]);

        return redirect($redirectUrl);
    }
    /**
     * Handle OAuth callback
     */
    public function callback(Request $request, $platform)
    {
        \Log::info("Social media callback initiated", [
            'platform' => $platform,
            'params' => $request->all(),
            'ip' => $request->ip()
        ]);

        // Validate user session
        if (!Auth::check()) {
            \Log::error("No authenticated user in callback");
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        // Get business from session rather than fresh query for security
        $businessId = session('oauth_business_id');
        $business = Business::find($businessId);

        if (!$business || $business->user_id !== Auth::id()) {
            \Log::error("Business validation failed", [
                'session_business_id' => $businessId,
                'auth_user_id' => Auth::id()
            ]);
            return redirect()->route('dashboard')->with('error', 'Business account validation failed.');
        }

        // Modified state validation for LinkedIn
        if ($platform !== 'linkedin') {
            if ($request->missing('state') || $request->get('state') !== session('oauth_state')) {
                \Log::error("OAuth state validation failed", [
                    'session_state' => session('oauth_state'),
                    'request_state' => $request->get('state')
                ]);
                return redirect()->route('marketing.social-media')
                    ->with('error', 'Security validation failed. Please try connecting again.');
            }
        } elseif (session('oauth_platform') !== $platform) {
            \Log::error("LinkedIn platform validation failed");
            return redirect()->route('marketing.social-media')
                ->with('error', 'Session validation failed. Please try connecting again.');
        }

        // Handle OAuth errors
        if ($request->has('error')) {
            $errorDescription = $request->get('error_description', 'No description provided');
            \Log::error("OAuth provider returned error", [
                'error' => $request->get('error'),
                'description' => $errorDescription
            ]);
            return redirect()->route('marketing.social-media')
                ->with('error', "Connection failed: {$errorDescription}");
        }

        // Validate authorization code
        if (!$request->has('code')) {
            \Log::error("Missing authorization code in callback");
            return redirect()->route('marketing.social-media')
                ->with('error', 'Authorization failed: No code received.');
        }

        try {
            // Exchange code for token
            \Log::debug("Exchanging code for token");
            $tokenData = $this->exchangeCodeForToken($platform, $request->get('code'));

            if (!isset($tokenData['access_token'])) {
                \Log::error("Token exchange failed", ['response' => $tokenData]);
                throw new \Exception('Failed to obtain access token');
            }

            // Get user info (with special LinkedIn handling)
            $userInfo = $platform === 'linkedin'
                ? $this->getLinkedInUserInfo($tokenData['access_token'])
                : $this->getUserInfo($platform, $tokenData['access_token']);

            if (empty($userInfo['id'])) {
                throw new \Exception('Invalid user information received');
            }

            // Create/update account
            $account = SocialMediaAccount::updateOrCreate(
                [
                    'business_id' => $business->id,
                    'platform' => $platform,
                    'platform_user_id' => $userInfo['id'],
                ],
                [
                    'username' => $userInfo['username'] ?? $userInfo['name'] ?? 'Unknown',
                    'access_token' => encrypt($tokenData['access_token']),
                    'refresh_token' => isset($tokenData['refresh_token']) ? encrypt($tokenData['refresh_token']) : null,
                    'token_expires_at' => isset($tokenData['expires_in']) ? now()->addSeconds($tokenData['expires_in']) : null,
                    'platform_data' => $userInfo,
                    'is_active' => true,
                    'last_connected_at' => now(),
                ]
            );

            // Clean up session
            session()->forget(['oauth_state', 'oauth_platform', 'oauth_business_id']);

            \Log::info("Social account connected successfully", [
                'platform' => $platform,
                'account_id' => $account->id
            ]);

            return redirect()->route('marketing.social-media')
                ->with('success', ucfirst($platform) . ' account connected successfully!')
                ->with('connected_account', $account->id);

        } catch (\Exception $e) {
            \Log::error("Social media connection failed", [
                'platform' => $platform,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('marketing.social-media')
                ->with('error', "Failed to connect {$platform} account: " . $e->getMessage());
        }
    }

    protected function getLinkedInUserInfo($accessToken)
    {
        try {
            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'X-RestLi-Protocol-Version' => '2.0.0',
                    'LinkedIn-Version' => '202304'
                ])
                ->get('https://api.linkedin.com/v2/userinfo');

            \Log::debug('LinkedIn userinfo response', ['body' => $response->body()]);

            if (!$response->successful()) {
                return [
                    'id' => null,
                    'name' => null,
                    'email' => null,
                    'error' => 'Userinfo API error: ' . $response->body()
                ];
            }

            $data = $response->json();
            // Build username: prefer name, then given_name + family_name, then sub
            $username = $data['name'] ?? null;
            if (!$username && isset($data['given_name'], $data['family_name'])) {
                $username = $data['given_name'] . ' ' . $data['family_name'];
            }
            if (!$username && isset($data['sub'])) {
                $username = $data['sub'];
            }
            if (!$username) {
                $username = 'LinkedIn User';
            }
            return [
                'id' => $data['sub'] ?? null,
                'name' => $data['name'] ?? null,
                'username' => $username,
                'email' => $data['email'] ?? null,
                'picture' => $data['picture'] ?? null,
                'given_name' => $data['given_name'] ?? null,
                'family_name' => $data['family_name'] ?? null,
                'locale' => $data['locale'] ?? null
            ];
        } catch (\Exception $e) {
            \Log::error('LinkedIn userinfo failed', ['error' => $e->getMessage()]);
            return [
                'id' => null,
                'name' => null,
                'email' => null,
                'error' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Updated getUserInfo method
     */
    protected function getUserInfo($platform, $accessToken)
    {
        try {
            // Special handling for LinkedIn
            if ($platform === 'linkedin') {
                return $this->getLinkedInUserInfo($accessToken);
            }

            // ... rest of your existing platform handlers ...

        } catch (\Exception $e) {
            \Log::error("Failed to get user info from {$platform}", ['error' => $e->getMessage()]);
            throw new \Exception("Failed to retrieve user information from {$platform}");
        }
    }

    /**
     * Disconnect a social media account
     */
    public function disconnect(SocialMediaAccount $account)
    {
        $this->authorize('delete', $account);

        $platform = $account->getAttribute('platform');
        $account->delete();

        return redirect()->route('marketing.social-media')
            ->with('success', ucfirst($platform) . ' account disconnected successfully.');
    }

    /**
     * Refresh OAuth token
     */
    public function refreshToken(SocialMediaAccount $account)
    {
        $this->authorize('update', $account);

        try {
            $result = $this->socialMediaService->refreshToken($account);

            if ($result['success']) {
                return response()->json(['message' => 'Token refreshed successfully']);
            } else {
                return response()->json(['error' => $result['message']], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to refresh token'], 500);
        }
    }

    /**
     * Get OAuth URL for platform
     */
    protected function getOAuthUrl($platform)
    {
        $state = session('oauth_state');
        $redirectUri = route('social.callback', $platform);

        return match ($platform) {
            'facebook' => 'https://www.facebook.com/v18.0/dialog/oauth?' . http_build_query([
                'client_id' => config('services.facebook.client_id'),
                'redirect_uri' => $redirectUri,
                'scope' => 'pages_manage_posts,pages_read_engagement,pages_show_list',
                'state' => $state,
                'response_type' => 'code',
            ]),
            'instagram' => 'https://api.instagram.com/oauth/authorize?' . http_build_query([
                'client_id' => config('services.instagram.client_id'),
                'redirect_uri' => $redirectUri,
                'scope' => 'user_profile,user_media',
                'state' => $state,
                'response_type' => 'code',
            ]),
            'x' => null, // X (Twitter) uses direct API tokens, no OAuth for posting
            'linkedin' => 'https://www.linkedin.com/oauth/v2/authorization?' . http_build_query([
                'client_id' => config('services.linkedin.client_id'),
                'redirect_uri' => $redirectUri,
                'scope' => 'openid profile w_member_social',
                'state' => $state,
                'response_type' => 'code',
            ]),

            'tiktok' => 'https://www.tiktok.com/auth/authorize/?' . http_build_query([
                'client_key' => config('services.tiktok.client_id'),
                'redirect_uri' => $redirectUri,
                'scope' => 'user.info.basic,video.list,video.upload',
                'state' => $state,
                'response_type' => 'code',
            ]),

            'youtube' => 'https://accounts.google.com/o/oauth2/auth?' . http_build_query([
                'client_id' => config('services.youtube.client_id'),
                'redirect_uri' => $redirectUri,
                'scope' => 'https://www.googleapis.com/auth/youtube.upload',
                'state' => $state,
                'response_type' => 'code',
                'access_type' => 'offline',
            ]),

            'pinterest' => 'https://www.pinterest.com/oauth/?' . http_build_query([
                'client_id' => config('services.pinterest.client_id'),
                'redirect_uri' => $redirectUri,
                'scope' => 'boards:read,pins:read,pins:write',
                'state' => $state,
                'response_type' => 'code',
            ]),

            'telegram' => null, // Telegram uses bot tokens, no OAuth needed

            'discord' => 'https://discord.com/api/oauth2/authorize?' . http_build_query([
                'client_id' => config('services.discord.client_id'),
                'redirect_uri' => $redirectUri,
                'scope' => 'webhook.incoming',
                'state' => $state,
                'response_type' => 'code',
            ]),

            'reddit' => 'https://www.reddit.com/api/v1/authorize?' . http_build_query([
                'client_id' => config('services.reddit.client_id'),
                'redirect_uri' => $redirectUri,
                'scope' => 'submit,identity',
                'state' => $state,
                'response_type' => 'code',
                'duration' => 'permanent',
            ]),

            default => null,
        };
    }

    /**
     * Exchange authorization code for access token
     */
    protected function exchangeCodeForToken($platform, $code)
    {
        $redirectUri = route('social.callback', $platform);

        $response = match ($platform) {
            'facebook' => Http::post('https://graph.facebook.com/v18.0/oauth/access_token', [
                'client_id' => config('services.facebook.client_id'),
                'client_secret' => config('services.facebook.client_secret'),
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ]),
            'instagram' => Http::asForm()->post('https://api.instagram.com/oauth/access_token', [
                'client_id' => config('services.instagram.client_id'),
                'client_secret' => config('services.instagram.client_secret'),
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
                'code' => $code,
            ]),
            'x' => null, // X (Twitter) uses direct API tokens, no OAuth for posting
            'linkedin' => Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
                'client_id' => config('services.linkedin.client_id'),
                'client_secret' => config('services.linkedin.client_secret'),
                'redirect_uri' => $redirectUri,
                'code' => $code,
                'grant_type' => 'authorization_code',
            ]),
            'tiktok' => Http::asForm()->post('https://open-api.tiktok.com/oauth/access_token/', [
                'client_key' => config('services.tiktok.client_id'),
                'client_secret' => config('services.tiktok.client_secret'),
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
            ]),

            'youtube' => Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.youtube.client_id'),
                'client_secret' => config('services.youtube.client_secret'),
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
            ]),

            'pinterest' => Http::asForm()->post('https://api.pinterest.com/v5/oauth/token', [
                'client_id' => config('services.pinterest.client_id'),
                'client_secret' => config('services.pinterest.client_secret'),
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
            ]),

            'discord' => Http::asForm()->post('https://discord.com/api/oauth2/token', [
                'client_id' => config('services.discord.client_id'),
                'client_secret' => config('services.discord.client_secret'),
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
            ]),

            'reddit' => Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode(
                    config('services.reddit.client_id') . ':' . config('services.reddit.client_secret')
                ),
                'User-Agent' => config('services.reddit.user_agent'),
            ])->asForm()->post('https://www.reddit.com/api/v1/access_token', [
                        'grant_type' => 'authorization_code',
                        'code' => $code,
                        'redirect_uri' => $redirectUri,
                    ]),

            default => null,
        };

        if (!$response->successful()) {
            throw new \Exception('Failed to exchange code for token: ' . $response->body());
        }

        return $response->json();
    }



    /**
     * Handle Facebook deauthorize callback
     * This is called when users remove the app from their Facebook account
     */
    public function facebookDeauthorize(Request $request)
    {
        // Facebook sends a signed request when user deauthorizes
        $signedRequest = $request->input('signed_request');

        if (!$signedRequest) {
            return response()->json(['status' => 'error', 'message' => 'No signed request provided'], 400);
        }

        try {
            // Parse the signed request
            list($encodedSig, $payload) = explode('.', $signedRequest, 2);
            $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

            // Get the user ID from the payload
            $facebookUserId = $data['user_id'] ?? null;

            if ($facebookUserId) {
                // Find and delete all Facebook accounts for this user
                $deletedAccounts = SocialMediaAccount::where('platform', 'facebook')
                    ->where('platform_user_id', $facebookUserId)
                    ->delete();

                // Also delete Instagram accounts (they use same Facebook app)
                $deletedInstagramAccounts = SocialMediaAccount::where('platform', 'instagram')
                    ->where('platform_user_id', $facebookUserId)
                    ->delete();

                \Log::info("Facebook deauthorization: Deleted {$deletedAccounts} Facebook and {$deletedInstagramAccounts} Instagram accounts for user {$facebookUserId}");
            }

            // Facebook expects a JSON response
            return response()->json([
                'status' => 'success',
                'message' => 'User data deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Facebook deauthorization error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process deauthorization'
            ], 500);
        }
    }
}

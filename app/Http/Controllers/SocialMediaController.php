<?php

namespace App\Http\Controllers;

use App\Models\SocialMediaAccount;
use App\Services\SocialMediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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

        // Store state for CSRF protection
        session(['oauth_state' => Str::random(40), 'oauth_platform' => $platform]);

        return redirect($redirectUrl);
    }

    /**
     * Handle OAuth callback
     */
    public function callback(Request $request, $platform)
    {
        $business = Auth::user()->business;
        
        // Verify state for CSRF protection
        if ($request->get('state') !== session('oauth_state') || session('oauth_platform') !== $platform) {
            return redirect()->route('marketing.social-media')
                ->with('error', 'Invalid OAuth state. Please try connecting again.');
        }

        if ($request->has('error')) {
            return redirect()->route('marketing.social-media')
                ->with('error', 'Social media connection was cancelled or failed.');
        }

        $code = $request->get('code');
        if (!$code) {
            return redirect()->route('marketing.social-media')
                ->with('error', 'No authorization code received from social platform.');
        }

        try {
            $tokenData = $this->exchangeCodeForToken($platform, $code);
            $userInfo = $this->getUserInfo($platform, $tokenData['access_token']);

            // Create or update social media account
            $account = SocialMediaAccount::updateOrCreate([
                'business_id' => $business->id,
                'platform' => $platform,
                'platform_user_id' => $userInfo['id'],
            ], [
                'username' => $userInfo['username'] ?? $userInfo['name'] ?? null,
                'access_token' => encrypt($tokenData['access_token']),
                'refresh_token' => isset($tokenData['refresh_token']) ? encrypt($tokenData['refresh_token']) : null,
                'token_expires_at' => isset($tokenData['expires_in']) ? now()->addSeconds($tokenData['expires_in']) : null,
                'platform_data' => $userInfo,
                'is_active' => true,
                'last_connected_at' => now(),
            ]);

            session()->forget(['oauth_state', 'oauth_platform']);

            return redirect()->route('marketing.social-media')
                ->with('success', ucfirst($platform) . ' account connected successfully!');

        } catch (\Exception $e) {
            \Log::error("Social media connection failed for {$platform}", [
                'error' => $e->getMessage(),
                'business_id' => $business->id
            ]);

            return redirect()->route('marketing.social-media')
                ->with('error', 'Failed to connect ' . ucfirst($platform) . ' account. Please try again.');
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
            
            'twitter' => 'https://twitter.com/i/oauth2/authorize?' . http_build_query([
                'client_id' => config('services.twitter.client_id'),
                'redirect_uri' => $redirectUri,
                'scope' => 'tweet.read tweet.write users.read',
                'state' => $state,
                'response_type' => 'code',
                'code_challenge' => 'challenge',
                'code_challenge_method' => 'plain',
            ]),
            
            'linkedin' => 'https://www.linkedin.com/oauth/v2/authorization?' . http_build_query([
                'client_id' => config('services.linkedin.client_id'),
                'redirect_uri' => $redirectUri,
                'scope' => 'w_member_social',
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
            
            'twitter' => Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode(
                    config('services.twitter.client_id') . ':' . config('services.twitter.client_secret')
                ),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post('https://api.twitter.com/2/oauth2/token', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirectUri,
                'code_verifier' => 'challenge',
            ]),
            
            'linkedin' => Http::asForm()->post('https://www.linkedin.com/oauth/v2/accessToken', [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $redirectUri,
                'client_id' => config('services.linkedin.client_id'),
                'client_secret' => config('services.linkedin.client_secret'),
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
     * Get user info from platform
     */
    protected function getUserInfo($platform, $accessToken)
    {
        $response = match ($platform) {
            'facebook' => Http::get('https://graph.facebook.com/v18.0/me', [
                'access_token' => $accessToken,
                'fields' => 'id,name,email',
            ]),
            
            'instagram' => Http::get('https://graph.instagram.com/me', [
                'access_token' => $accessToken,
                'fields' => 'id,username',
            ]),
            
            'twitter' => Http::withToken($accessToken)->get('https://api.twitter.com/2/users/me'),
            
            'linkedin' => Http::withToken($accessToken)->get('https://api.linkedin.com/v2/people/~'),

            'tiktok' => Http::post('https://open.tiktokapis.com/v2/user/info/', [
                'access_token' => $accessToken
            ]),

            'youtube' => Http::withToken($accessToken)->get('https://www.googleapis.com/youtube/v3/channels?part=snippet&mine=true'),

            'pinterest' => Http::withToken($accessToken)->get('https://api.pinterest.com/v5/user_account'),

            'discord' => Http::withToken($accessToken)->get('https://discord.com/api/users/@me'),

            'reddit' => Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'User-Agent' => config('services.reddit.user_agent'),
            ])->get('https://oauth.reddit.com/api/v1/me'),

            default => null,
        };

        if (!$response->successful()) {
            throw new \Exception('Failed to get user info: ' . $response->body());
        }

        $data = $response->json();

        // Normalize response format
        return match ($platform) {
            'facebook' => [
                'id' => $data['id'],
                'name' => $data['name'],
                'username' => $data['name'],
                'email' => $data['email'] ?? null,
            ],
            
            'instagram' => [
                'id' => $data['id'],
                'username' => $data['username'],
                'name' => $data['username'],
            ],
            
            'twitter' => [
                'id' => $data['data']['id'],
                'username' => $data['data']['username'],
                'name' => $data['data']['name'],
            ],
            
            'linkedin' => [
                'id' => $data['id'],
                'name' => $data['localizedFirstName'] . ' ' . $data['localizedLastName'],
                'username' => $data['localizedFirstName'] . ' ' . $data['localizedLastName'],
            ],
        };
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

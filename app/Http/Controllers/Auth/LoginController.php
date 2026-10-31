<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Models\BusinessMember;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
        $this->middleware('throttle:5,1')->only('login');
    }

    protected function authenticated(Request $request, $user)
    {
        // Polled JSON endpoints are never a real destination: a stale dashboard
        // tab hitting one while logged out stores it as url.intended and would
        // dump the user on a raw JSON response right after login. Drop those.
        $intendedPath = (string) parse_url((string) $request->session()->get('url.intended'), PHP_URL_PATH);
        if (preg_match('#^/notifications(/|$)#', $intendedPath)
            || preg_match('#/unread-count$#', $intendedPath)
            || preg_match('#^/payment/mpesa/status/#', $intendedPath)
            || preg_match('#^/ai-communication/(status|history)$#', $intendedPath)) {
            $request->session()->forget('url.intended');
        }

        // Check if user owns a business
        if ($user->business) {
            return redirect()->intended($this->redirectPath());
        }

        // Check if user is an active team member of a business
        $isMember = BusinessMember::where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();

        if ($isMember) {
            return redirect()->intended($this->redirectPath());
        }

        // No business association at all — prompt to create one
        return redirect()->route('business.choose-type');
    }
}

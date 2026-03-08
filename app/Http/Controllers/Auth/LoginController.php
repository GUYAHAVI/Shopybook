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
    }

    protected function authenticated(Request $request, $user)
    {
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

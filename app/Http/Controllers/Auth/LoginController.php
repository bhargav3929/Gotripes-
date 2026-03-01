<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME; // Points to /admin/

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Tell Laravel to use 'name' field instead of 'email' for login
     */
    public function username()
    {
        return 'name';
    }

    /**
     * Override the login method to add partner status validation and session setup
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Check for lockouts
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            $user = Auth::user();
            
            // Log::info('🔍 Login attempt successful', [
            //     'user_id' => $user->id,
            //     'user_name' => $user->name,
            //     'email_verified_at' => $user->email_verified_at ?? 'NULL'
            // ]);
            
            // Check if user is allowed to login
            if (!$this->isUserAllowedToLogin($user)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return $this->sendPartnerStatusFailedResponse($request, $user);
            }

            // 🎯 SET SESSION FLAGS BASED ON USER TYPE
            $this->setUserSessionFlags($user);

            // Log::info('🎯 Login successful - session set and redirecting to HOME route', [
            //     'user_id' => $user->id,
            //     'user_name' => $user->name,
            //     'user_type' => session('user_type'),
            //     'is_partner_restricted' => session('is_partner_restricted'),
            //     'home_route' => RouteServiceProvider::HOME
            // ]);

            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Set session flags based on user type
     */
    private function setUserSessionFlags($user)
    {
        // Check for Activities Manager role first
        if ($user->isActivitiesManager() && !$user->isAdmin()) {
            session(['user_type' => 'activities_manager']);
            session(['is_partner_restricted' => false]);
            return;
        }

        if (is_null($user->email_verified_at)) {
            // Admin user
            session(['user_type' => 'admin']);
            session(['is_partner_restricted' => false]);
            session(['admin_name' => $user->name]);
            
        } elseif ($user->email_verified_at && str_contains($user->email_verified_at, 'rseparator1')) {
            // Approved partner
            session(['user_type' => 'approved_partner']);
            session(['is_partner_restricted' => true]);
            session(['partner_name' => $user->name]);
            
            // Extract emirates information for session
            $parts = explode('rseparator', $user->email_verified_at, 3);
            $emirates = isset($parts[0]) && !empty($parts[0]) ? explode(',', $parts[0]) : [];
            session(['partner_emirates' => $emirates]);
            
        } else {
            session(['user_type' => 'restricted']);
            session(['is_partner_restricted' => true]);
            session(['restriction_reason' => 'Account not approved']);
        }
    }

    /**
     * Check if user is allowed to login based on partner status
     */
    protected function isUserAllowedToLogin($user)
    {
        // Allow Activities Manager role users directly
        if ($user->isActivitiesManager()) {
            return true;
        }

        // Allow login for regular users/admins (null email_verified_at)
        if (is_null($user->email_verified_at)) {
            return true;
        }

        // Check partner status
        if (str_contains($user->email_verified_at, 'rseparator')) {
            $parts = explode('rseparator', $user->email_verified_at, 3);
            $status = isset($parts[1]) ? $parts[1] : '0';

            if ($status == '1') {
                return true;
            }
            if ($status == '0') {
                return false;
            }
            if ($status == '2') {
                return false;
            }
        }

        return false;
    }

    /**
     * Send error response for blocked partners
     */
    protected function sendPartnerStatusFailedResponse(Request $request, $user)
    {
        $errorMessage = 'Your account access is currently restricted. Please contact support.';
        
        if (str_contains($user->email_verified_at ?? '', 'rseparator')) {
            $parts = explode('rseparator', $user->email_verified_at, 3);
            $status = isset($parts[1]) ? $parts[1] : '0';
            $adminComments = isset($parts[2]) && !empty(trim($parts[2])) ? trim($parts[2]) : null;
            
            if ($status == '0') {
                $errorMessage = 'Your partner account is pending admin approval. Please wait for confirmation.';
            } elseif ($status == '2') {
                $errorMessage = 'Your partner account has been rejected. Please contact support for assistance.';
                if ($adminComments) {
                    $errorMessage .= ' Admin notes: ' . $adminComments;
                }
            }
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors([
                $this->username() => $errorMessage,
            ]);
    }

    /**
     * Override logout to clear custom sessions and redirect to /admin
     */
    public function logout(Request $request)
    {
        // Log::info('🚪 User logging out', [
        //     'user_id' => Auth::id(),
        //     'user_type' => session('user_type'),
        // ]);

        // Clear custom session variables
        session()->forget(['user_type', 'is_partner_restricted', 'admin_name', 'partner_name', 'partner_emirates', 'restriction_reason', 'activities_manager']);

        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Log::info('✅ User logged out successfully, redirecting to /admin');

        // 🎯 CHANGED: Redirect to /admin instead of default behavior
        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new \Illuminate\Http\JsonResponse([], 204)
            : redirect('/admin')->with([
                'message' => 'You have been logged out successfully.',
                'alert-type' => 'success'
            ]);
    }

    /**
     * 🆕 NEW METHOD: The user has logged out of the application.
     * This method is called by the logout process and allows us to customize the redirect.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        // 🎯 Redirect to /admin with success message after logout
        return redirect('/admin')->with([
            'message' => 'You have been logged out successfully.',
            'alert-type' => 'success'
        ]);
    }
}

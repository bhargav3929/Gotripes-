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

            // Check if user is allowed to login
            if (!$this->isUserAllowedToLogin($user)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return $this->sendPartnerStatusFailedResponse($request, $user);
            }

            // Set session flags based on user type
            $this->setUserSessionFlags($user);

            // Regenerate session for security
            $request->session()->regenerate();

            // Redirect based on user role
            return $this->redirectBasedOnRole($user);
        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Redirect user to the appropriate area based on their role
     */
    protected function redirectBasedOnRole($user)
    {
        // Admin users: full access to admin dashboard
        if ($user->isAdmin()) {
            return redirect()->intended('/admin/users');
        }

        // Activities Manager: redirect to UAE activities section
        if ($user->isActivitiesManager()) {
            return redirect()->intended('/admin/uaeactivities');
        }

        // Approved partners: redirect to UAE activities
        if (!is_null($user->email_verified_at) && str_contains($user->email_verified_at, 'rseparator1')) {
            return redirect()->intended('/admin/uaeactivities');
        }

        // Any other user with roles (specific access employees): redirect to UAE activities
        if ($user->roles()->exists()) {
            return redirect()->intended('/admin/uaeactivities');
        }

        // Users without any role should not access admin
        Auth::logout();
        return redirect('/admin')->withErrors([
            $this->username() => 'Your account does not have admin access.',
        ]);
    }

    /**
     * Set session flags based on user type (role-based)
     */
    private function setUserSessionFlags($user)
    {
        // 1. Admin role gets full access
        if ($user->isAdmin()) {
            session(['user_type' => 'admin']);
            session(['is_partner_restricted' => false]);
            session(['admin_name' => $user->name]);
            return;
        }

        // 2. Activities Manager role gets limited access
        if ($user->isActivitiesManager()) {
            session(['user_type' => 'activities_manager']);
            session(['is_partner_restricted' => false]);
            return;
        }

        // 3. Approved partner (legacy: email_verified_at contains rseparator1)
        if ($user->email_verified_at && str_contains($user->email_verified_at, 'rseparator1')) {
            session(['user_type' => 'approved_partner']);
            session(['is_partner_restricted' => true]);
            session(['partner_name' => $user->name]);

            $parts = explode('rseparator', $user->email_verified_at, 3);
            $emirates = isset($parts[0]) && !empty($parts[0]) ? explode(',', $parts[0]) : [];
            session(['partner_emirates' => $emirates]);
            return;
        }

        // 4. Any other authenticated user with roles - employee with specific access
        if ($user->roles()->exists()) {
            session(['user_type' => 'employee']);
            session(['is_partner_restricted' => false]);
            return;
        }

        // 5. Fallback: restricted
        session(['user_type' => 'restricted']);
        session(['is_partner_restricted' => true]);
        session(['restriction_reason' => 'No role assigned']);
    }

    /**
     * Check if user is allowed to login based on role and partner status
     */
    protected function isUserAllowedToLogin($user)
    {
        // Allow any user with a role (Admin, Activities Manager, or custom roles)
        if ($user->roles()->exists()) {
            return true;
        }

        // Allow login for legacy admin users (null email_verified_at, no roles yet)
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
            return false;
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

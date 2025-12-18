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
        if (is_null($user->email_verified_at)) {
            // Admin user
            session(['user_type' => 'admin']);
            session(['is_partner_restricted' => false]);
            session(['admin_name' => $user->name]);
            
            // Log::info('🎯 Session set for admin user', [
            //     'user_id' => $user->id,
            //     'user_type' => 'admin',
            //     'is_partner_restricted' => false
            // ]);
            
        } elseif ($user->email_verified_at && str_contains($user->email_verified_at, 'rseparator1')) {
            // Approved partner
            session(['user_type' => 'approved_partner']);
            session(['is_partner_restricted' => true]);
            session(['partner_name' => $user->name]);
            
            // Extract emirates information for session
            $parts = explode('rseparator', $user->email_verified_at, 3);
            $emirates = isset($parts[0]) && !empty($parts[0]) ? explode(',', $parts[0]) : [];
            session(['partner_emirates' => $emirates]);
            
            // Log::info('🎯 Session set for approved partner', [
            //     'user_id' => $user->id,
            //     'user_type' => 'approved_partner',
            //     'is_partner_restricted' => true,
            //     'emirates_count' => count($emirates)
            // ]);
            
        } else {
            // Other cases (shouldn't reach here due to login validation, but just in case)
            session(['user_type' => 'restricted']);
            session(['is_partner_restricted' => true]);
            session(['restriction_reason' => 'Account not approved']);
            
            // Log::warning('🎯 Session set for restricted user', [
            //     'user_id' => $user->id,
            //     'user_type' => 'restricted',
            //     'is_partner_restricted' => true
            // ]);
        }
    }

    /**
     * Check if user is allowed to login based on partner status
     */
    protected function isUserAllowedToLogin($user)
    {
        // Allow login for regular users (null email_verified_at)
        if (is_null($user->email_verified_at)) {
            // Log::info('✅ Login allowed: Regular user/Admin', [
            //     'user_id' => $user->id,
            //     'user_name' => $user->name,
            // ]);
            return true;
        }

        // Check partner status
        if (str_contains($user->email_verified_at, 'rseparator')) {
            $parts = explode('rseparator', $user->email_verified_at, 3);
            $status = isset($parts[1]) ? $parts[1] : '0';
            
            // Log::info('🔍 Partner login attempt detected', [
            //     'user_id' => $user->id,
            //     'user_name' => $user->name,
            //     'partner_status' => $status,
            // ]);

            // Allow only approved partners (status = 1)
            if ($status == '1') {
                // Log::info('✅ Login allowed: Approved partner', [
                //     'user_id' => $user->id,
                //     'user_name' => $user->name
                // ]);
                return true;
            }
            
            // Block pending partners (status = 0)
            if ($status == '0') {
                // Log::warning('❌ Login blocked: Pending partner', [
                //     'user_id' => $user->id,
                //     'user_name' => $user->name
                // ]);
                return false;
            }
            
            // Block rejected partners (status = 2)
            if ($status == '2') {
                // Log::warning('❌ Login blocked: Rejected partner', [
                //     'user_id' => $user->id,
                //     'user_name' => $user->name
                // ]);
                return false;
            }
        }

        // Log::warning('❌ Login blocked: Unknown user type', [
        //     'user_id' => $user->id,
        //     'user_name' => $user->name,
        // ]);
        
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
        session()->forget(['user_type', 'is_partner_restricted', 'admin_name', 'partner_name', 'partner_emirates', 'restriction_reason']);

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

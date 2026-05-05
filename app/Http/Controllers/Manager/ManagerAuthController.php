<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && $this->canAccessManager(Auth::user())) {
            return redirect()->route('manager.dashboard');
        }
        return view('manager.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['credentials' => 'Invalid email or password.']);
        }

        $user = Auth::user();

        if (!$this->canAccessManager($user)) {
            $this->logoutAndInvalidate($request);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['credentials' => 'This account does not have manager access.']);
        }

        $tenant = app()->bound('current_company') ? app('current_company') : null;
        $isSuperAdmin = $user->is_super_admin || $user->role === 'super_admin';

        if (!$isSuperAdmin && $tenant instanceof Company && (int) $user->company_id !== (int) $tenant->id) {
            $this->logoutAndInvalidate($request);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['credentials' => 'This account does not belong to ' . $tenant->subdomain . '.gotrips.ai.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('manager.dashboard'));
    }

    public function logout(Request $request)
    {
        $this->logoutAndInvalidate($request);
        return redirect()->route('manager.login');
    }

    private function canAccessManager($user): bool
    {
        if (!$user) {
            return false;
        }

        $isSuperAdmin = $user->is_super_admin || $user->role === 'super_admin';
        $isCompanyManager = in_array($user->role, ['company_owner', 'company_admin'], true);

        return $isSuperAdmin || $isCompanyManager;
    }

    private function logoutAndInvalidate(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}

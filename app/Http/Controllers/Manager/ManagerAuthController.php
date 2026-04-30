<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagerAuthController extends Controller
{
    public function showLogin()
    {
        if (session('manager_authenticated')) {
            return redirect()->route('manager.dashboard');
        }
        return view('manager.login');
    }

    /**
     * Authenticate against the users table. The user must:
     *   - have role company_owner or company_admin (or be super_admin)
     *   - belong to the current tenant company (matched via subdomain by IdentifyTenant middleware),
     *     unless they are super_admin (super admins can manage any tenant).
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // The "username" field is treated as email.
        $email = trim($validated['username']);

        $user = User::where('email', $email)->first();
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return redirect()->back()
                ->withInput($request->only('username'))
                ->withErrors(['credentials' => 'Invalid email or password.']);
        }

        $isSuperAdmin = $user->is_super_admin || $user->role === 'super_admin';
        $isCompanyManager = in_array($user->role, ['company_owner', 'company_admin'], true);

        if (!$isSuperAdmin && !$isCompanyManager) {
            return redirect()->back()
                ->withInput($request->only('username'))
                ->withErrors(['credentials' => 'This account does not have manager access.']);
        }

        // Tenant scoping: the manager must belong to this subdomain's company.
        $tenant = app()->bound('current_company') ? app('current_company') : null;
        if (!$isSuperAdmin && $tenant instanceof Company) {
            if ((int) $user->company_id !== (int) $tenant->id) {
                return redirect()->back()
                    ->withInput($request->only('username'))
                    ->withErrors(['credentials' => 'This account does not belong to ' . $tenant->subdomain . '.gotrips.ai.']);
            }
        }

        session([
            'manager_authenticated' => true,
            'manager_user_id'       => $user->id,
            'manager_name'          => $user->name,
            'manager_email'         => $user->email,
            'manager_company_id'    => $user->company_id,
        ]);

        return redirect()->route('manager.dashboard');
    }

    public function logout()
    {
        session()->forget([
            'manager_authenticated',
            'manager_user_id',
            'manager_name',
            'manager_email',
            'manager_company_id',
        ]);
        return redirect()->route('manager.login');
    }
}

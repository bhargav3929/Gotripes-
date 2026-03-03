<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Smart admin dashboard router - uses role-based routing
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin role: redirect to user management
        if ($user->isAdmin()) {
            return redirect('/admin/users')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Activities Manager role: redirect to activities
        if ($user->isActivitiesManager()) {
            return redirect('/admin/uaeactivities')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Approved partner (legacy): redirect to activities
        if ($user->email_verified_at && str_contains($user->email_verified_at, 'rseparator1')) {
            return redirect('/admin/uaeactivities')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Users with other roles: redirect to activities (their allowed section)
        if ($user->roles()->exists()) {
            return redirect('/admin/uaeactivities')
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // No role, not a partner: deny access
        Auth::logout();
        session()->flush();

        return redirect('/admin')->withErrors([
            'name' => 'Your account does not have admin access. Please contact support.'
        ]);
    }
}

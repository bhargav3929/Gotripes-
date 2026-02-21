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
     * Smart admin dashboard router - FIXED: Don't override existing sessions
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
           // Log::warning('⚠️ Unauthenticated user tried to access admin route');
            return redirect()->route('login');
        }

        // Log::info('🎯 Admin route accessed - checking existing session first', [
        //     'user_id' => $user->id,
        //     'user_name' => $user->name,
        //     'existing_user_type' => session('user_type'),
        //     'existing_is_partner_restricted' => session('is_partner_restricted'),
        //     'email_verified_at' => $user->email_verified_at ?? 'NULL'
        // ]);

        // 🔥 FIX: Check if session already exists (from LoginController)
        $existingUserType = session('user_type');
        $existingRestricted = session('is_partner_restricted');

        if ($existingUserType && $existingRestricted !== null) {
            // Session already set by LoginController - just redirect based on existing session
            // Log::info('🎯 Using existing session values from LoginController', [
            //     'user_id' => $user->id,
            //     'existing_user_type' => $existingUserType,
            //     'existing_is_partner_restricted' => $existingRestricted
            // ]);

            if ($existingUserType === 'approved_partner' && $existingRestricted === true) {
                // Log::info('🚀 Redirecting approved partner to UAE Activities (using existing session)', [
                //     'user_id' => $user->id,
                //     'user_name' => $user->name,
                // ]);
                
                return redirect('/admin/uaeactivities')
                    ->with('success', 'Welcome back, ' . $user->name . '! You can manage UAE Activities here.');
                    
            } elseif ($existingUserType === 'admin' && $existingRestricted === false) {
                // Log::info('🚀 Redirecting admin to users management (using existing session)', [
                //     'user_id' => $user->id,
                //     'user_name' => $user->name,
                // ]);
                
                return redirect('/admin/users')
                    ->with('success', 'Welcome back, ' . $user->name . '! Admin dashboard loaded.');
            }
        }

        // 🔥 FALLBACK: Only set session if not already set by LoginController
        // Log::info('🔄 No existing session found, setting new session values', [
        //     'user_id' => $user->id,
        //     'user_name' => $user->name,
        // ]);

        // Check if user is an approved partner
        if ($user->email_verified_at && str_contains($user->email_verified_at, 'rseparator1')) {
            // Log::info('🚀 Setting NEW partner session and redirecting to UAE Activities', [
            //     'user_id' => $user->id,
            //     'user_name' => $user->name,
            // ]);
            
            // Set session flags for approved partner
            session(['user_type' => 'approved_partner']);
            session(['is_partner_restricted' => true]);
            session(['partner_name' => $user->name]);
            
            return redirect('/admin/uaeactivities')
                ->with('success', 'Welcome back, ' . $user->name . '! You can manage UAE Activities here.');
        }

        // Check if user is a regular user/admin (null email_verified_at)
        if (is_null($user->email_verified_at)) {
            // Log::info('🚀 Setting NEW admin session and redirecting to users management', [
            //     'user_id' => $user->id,
            //     'user_name' => $user->name,
            // ]);
            
            // Set session flags for admin
            session(['user_type' => 'admin']);
            session(['is_partner_restricted' => false]);
            session(['admin_name' => $user->name]);
            
            return redirect('/admin/users')
                ->with('success', 'Welcome back, ' . $user->name . '! Admin dashboard loaded.');
        }

        // Handle restricted users
        // Log::warning('⚠️ User has restricted access', [
        //     'user_id' => $user->id,
        //     'user_name' => $user->name,
        //     'email_verified_at' => $user->email_verified_at
        // ]);

        session(['user_type' => 'restricted']);
        session(['is_partner_restricted' => true]);
        session(['restriction_reason' => 'Account not approved']);

        Auth::logout();
        session()->flush();
        
        return redirect()->route('login')->withErrors([
            'name' => 'Your account access is restricted. Please contact support.'
        ]);
    }
}

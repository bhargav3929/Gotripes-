<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManagerAuthController extends Controller
{
    public function showLogin()
    {
        if (session('manager_authenticated')) {
            return redirect()->route('manager.dashboard');
        }
        return view('manager.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($request->username === 'admin' && $request->password === 'admin123') {
            session([
                'manager_authenticated' => true,
                'manager_name' => 'Admin Manager',
            ]);
            return redirect()->route('manager.dashboard');
        }

        return redirect()->back()->withErrors(['credentials' => 'Invalid username or password.']);
    }

    public function logout()
    {
        session()->forget(['manager_authenticated', 'manager_name']);
        return redirect()->route('manager.login');
    }
}

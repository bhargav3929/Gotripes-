<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $stats = [
            'total_users' => 0,
            'total_activities' => \App\Models\UAEActivity::where('isActive', 1)->count(),
            'monthly_revenue' => 0,
            'pending_approvals' => 0,
        ];

        if ($user->isAdmin()) {
            $stats['total_users'] = \App\Models\User::count();
            // Add other admin stats here if needed
        }

        return view('admin.dashboard', compact('stats'));
    }
}

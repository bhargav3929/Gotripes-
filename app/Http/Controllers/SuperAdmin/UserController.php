<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('company')->withCount('esimOrders');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        $companies = Company::orderBy('name')->get();

        return view('superadmin.users.index', compact('users', 'companies'));
    }

    public function show(User $user)
    {
        $user->load('company', 'esimOrders');

        $stats = [
            'total_orders' => $user->esimOrders()->count(),
            'total_spent' => $user->esimOrders()->where('status', 'completed')->sum('total_amount'),
        ];

        return view('superadmin.users.show', compact('user', 'stats'));
    }

    public function edit(User $user)
    {
        $companies = Company::orderBy('name')->get();
        return view('superadmin.users.edit', compact('user', 'companies'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:super_admin,company_owner,company_admin,company_staff,customer',
            'company_id' => 'nullable|exists:companies,id',
            'password' => 'nullable|min:8',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->company_id = $validated['company_id'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'super_admin') {
            return back()->with('error', 'Cannot delete a super admin.');
        }

        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}

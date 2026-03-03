<?php

namespace App\Http\Controllers\Admin;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('roles')->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::pluck('title', 'id');

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        $accessType = $request->input('access_type', 'full');

        $data = $request->validated();
        $data['password'] = bcrypt($request->password);
        $data['access_type'] = $accessType;

        $user = User::create($data);

        // Assign roles based on access type
        $roleIds = $this->resolveRoleIds($accessType, $request->input('modules', []));
        $user->roles()->sync($roleIds);

        return redirect()->route('admin.users.index')->with([
            'message' => 'User created successfully!',
            'alert-type' => 'success'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::pluck('title', 'id');

        return view('admin.users.edit', compact('user','roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $accessType = $request->input('access_type', 'full');

        $updateData = $request->validated();
        $updateData['access_type'] = $accessType;

        // Only update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password);
        } else {
            unset($updateData['password']);
        }

        $user->update($updateData);

        // Assign roles based on access type
        $roleIds = $this->resolveRoleIds($accessType, $request->input('modules', []));
        $user->roles()->sync($roleIds);

        return redirect()->route('admin.users.index')->with([
            'message' => 'User updated successfully!',
            'alert-type' => 'info'
        ]);
    }

    /**
     * Map access_type + modules to role IDs
     */
    private function resolveRoleIds(string $accessType, array $modules = []): array
    {
        if ($accessType === 'full') {
            $adminRole = Role::where('title', 'Admin')->first();
            return $adminRole ? [$adminRole->id] : [];
        }

        // Specific access: map module names to roles and their required permissions
        $roleIds = [];
        $moduleConfig = [
            'uaeactivities' => [
                'role' => 'Activities Manager',
                'permissions' => ['manage_uae_activities', 'view_dashboard'],
            ],
            'announcements' => [
                'role' => 'Announcements Manager',
                'permissions' => ['manage_announcements', 'view_dashboard'],
            ],
            'homepageads' => [
                'role' => 'Carousel Manager',
                'permissions' => ['manage_carousel', 'view_dashboard'],
            ],
        ];

        foreach ($modules as $module) {
            if (isset($moduleConfig[$module])) {
                $config = $moduleConfig[$module];
                $role = Role::firstOrCreate(['title' => $config['role']]);

                // Ensure the role has the required permissions attached
                $permissionIds = [];
                foreach ($config['permissions'] as $permTitle) {
                    $perm = Permission::firstOrCreate(['title' => $permTitle]);
                    $permissionIds[] = $perm->id;
                }
                $role->permissions()->syncWithoutDetaching($permissionIds);

                $roleIds[] = $role->id;
            }
        }

        return $roleIds;
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with([
            'message' => 'successfully deleted !',
            'alert-type' => 'danger'
        ]);
    }

     /**
     * Delete all selected Permission at once.
     *
     * @param Request $request
     */
    public function massDestroy(Request $request)
    {
        User::whereIn('id', request('ids'))->delete();

        return response()->noContent();
    }
}

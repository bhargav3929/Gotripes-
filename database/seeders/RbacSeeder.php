<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'manage_users',
            'manage_roles',
            'manage_announcements',
            'manage_carousel',
            'manage_uae_activities',
            'view_dashboard',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['title' => $perm]);
        }

        // Create Admin role with all permissions
        $adminRole = Role::firstOrCreate(['title' => 'Admin']);
        $adminRole->permissions()->sync(Permission::all()->pluck('id'));

        // Create Activities Manager role with limited permissions
        $activitiesRole = Role::firstOrCreate(['title' => 'Activities Manager']);
        $activitiesRole->permissions()->sync(
            Permission::whereIn('title', ['manage_uae_activities', 'view_dashboard'])->pluck('id')
        );

        // Create Announcements Manager role
        $announcementsRole = Role::firstOrCreate(['title' => 'Announcements Manager']);
        $announcementsRole->permissions()->sync(
            Permission::whereIn('title', ['manage_announcements', 'view_dashboard'])->pluck('id')
        );

        // Create Carousel Manager role
        $carouselRole = Role::firstOrCreate(['title' => 'Carousel Manager']);
        $carouselRole->permissions()->sync(
            Permission::whereIn('title', ['manage_carousel', 'view_dashboard'])->pluck('id')
        );
    }
}

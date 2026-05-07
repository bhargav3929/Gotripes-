<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeLegacyAdmin extends Command
{
    protected $signature = 'make:admin-user
                            {name : Login username (matches users.name column)}
                            {password : Plain-text password (will be hashed)}
                            {--email= : Email for the user (defaults to <name>@gotrips.ai)}
                            {--role=Admin : Role title to attach (must exist in roles table)}';

    protected $description = 'Create or update a legacy admin user for the /login form (name-based auth + role)';

    public function handle(): int
    {
        $name      = $this->argument('name');
        $password  = $this->argument('password');
        $emailOpt  = $this->option('email');
        $roleTitle = $this->option('role');

        $role = Role::where('title', $roleTitle)->first();
        if (!$role) {
            $this->error("Role '{$roleTitle}' not found. Run db:seed --class=RbacSeeder first.");
            return Command::FAILURE;
        }

        $user = User::where('name', $name)->first();

        if ($user) {
            $user->password = Hash::make($password);
            if ($emailOpt) {
                $user->email = $emailOpt;
            }
            $user->save();
        } else {
            $user = User::create([
                'name'     => $name,
                'email'    => $emailOpt ?: $name . '@gotrips.ai',
                'password' => Hash::make($password),
            ]);
        }

        $user->roles()->syncWithoutDetaching([$role->id]);

        $this->info("Legacy admin ready: name={$user->name}");
        $this->line("  Email:  {$user->email}");
        $this->line("  Role:   {$role->title}");
        $this->line("  Login:  https://gotrips.ai/login  (Username: {$user->name})");

        return Command::SUCCESS;
    }
}

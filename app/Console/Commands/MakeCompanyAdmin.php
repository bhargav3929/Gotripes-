<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeCompanyAdmin extends Command
{
    protected $signature = 'tenant:make-admin
                            {email : Login email for the admin user}
                            {password : Plain-text password (will be hashed)}
                            {--company= : Company name, slug, or subdomain (defaults to GoTrips)}
                            {--name= : Display name for the user (defaults to "Admin")}
                            {--role=company_owner : Role: company_owner or company_admin}';

    protected $description = 'Create or update a tenant admin user with manager-panel access';

    public function handle(): int
    {
        $email    = strtolower(trim($this->argument('email')));
        $password = $this->argument('password');
        $name     = $this->option('name') ?: 'Admin';
        $role     = $this->option('role');

        if (!in_array($role, ['company_owner', 'company_admin'], true)) {
            $this->error("Role must be 'company_owner' or 'company_admin'.");
            return Command::FAILURE;
        }

        $company = $this->resolveCompany($this->option('company'));
        if (!$company) {
            $this->error('Company not found. Use --company=<name|slug|subdomain>.');
            return Command::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'           => $name,
                'password'       => Hash::make($password),
                'role'           => $role,
                'company_id'     => $company->id,
                'is_super_admin' => false,
            ]
        );

        $this->info("Admin user ready: {$user->email}");
        $this->line("  Name:       {$user->name}");
        $this->line("  Role:       {$user->role}");
        $this->line("  Company:    {$company->name} (id={$company->id}, subdomain={$company->subdomain})");
        $this->line("  Login URL:  https://{$company->subdomain}." . config('app.domain', 'gotrips.ai') . '/manager/login');

        return Command::SUCCESS;
    }

    protected function resolveCompany(?string $needle): ?Company
    {
        if (!$needle) {
            return Company::where('slug', 'gotrips')
                ->orWhere('subdomain', 'gotrips')
                ->orWhere('name', 'GoTrips')
                ->first();
        }

        return Company::where('name', $needle)
            ->orWhere('slug', $needle)
            ->orWhere('subdomain', $needle)
            ->first();
    }
}

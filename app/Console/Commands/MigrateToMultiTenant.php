<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\EsimOrder;
use App\Models\ReferralAgent;
use App\Models\ReferralClick;
use App\Models\ReferralTracking;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MigrateToMultiTenant extends Command
{
    protected $signature = 'tenant:migrate
                            {--company-name=GoTrips : The default company name}
                            {--admin-email= : Super admin email}
                            {--admin-password= : Super admin password}';

    protected $description = 'Migrate existing data to multi-tenant structure';

    public function handle()
    {
        $this->info('Starting multi-tenant migration...');

        DB::beginTransaction();

        try {
            // Step 1: Create default company
            $company = $this->createDefaultCompany();
            $this->info("Created company: {$company->name} (ID: {$company->id})");

            // Step 2: Create or update super admin
            $this->createSuperAdmin();

            // Step 3: Assign all existing users to the default company
            $usersUpdated = User::whereNull('company_id')
                ->where('role', '!=', 'super_admin')
                ->update(['company_id' => $company->id]);
            $this->info("Assigned {$usersUpdated} users to default company");

            // Step 4: Assign all existing orders to the default company
            $ordersUpdated = EsimOrder::whereNull('company_id')
                ->update(['company_id' => $company->id]);
            $this->info("Assigned {$ordersUpdated} eSIM orders to default company");

            // Step 5: Assign all referral agents to the default company
            $agentsUpdated = ReferralAgent::whereNull('company_id')
                ->update(['company_id' => $company->id]);
            $this->info("Assigned {$agentsUpdated} referral agents to default company");

            // Step 6: Assign all referral tracking to the default company
            $trackingUpdated = ReferralTracking::whereNull('company_id')
                ->update(['company_id' => $company->id]);
            $this->info("Assigned {$trackingUpdated} referral tracking records to default company");

            // Step 7: Assign all referral clicks to the default company
            $clicksUpdated = ReferralClick::whereNull('company_id')
                ->update(['company_id' => $company->id]);
            $this->info("Assigned {$clicksUpdated} referral clicks to default company");

            // Step 8: Set first admin as company owner
            $firstAdmin = User::where('company_id', $company->id)
                ->whereHas('roles', function ($q) {
                    $q->where('title', 'Admin');
                })
                ->first();

            if ($firstAdmin) {
                $firstAdmin->role = 'company_owner';
                $firstAdmin->save();
                $this->info("Set {$firstAdmin->email} as company owner");
            }

            DB::commit();

            $this->newLine();
            $this->info('Multi-tenant migration completed successfully!');
            $this->table(
                ['Entity', 'Count'],
                [
                    ['Companies', Company::count()],
                    ['Users', User::whereNotNull('company_id')->count()],
                    ['eSIM Orders', EsimOrder::whereNotNull('company_id')->count()],
                    ['Referral Agents', ReferralAgent::whereNotNull('company_id')->count()],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Migration failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    protected function createDefaultCompany(): Company
    {
        $companyName = $this->option('company-name');

        return Company::firstOrCreate(
            ['slug' => Str::slug($companyName)],
            [
                'name' => $companyName,
                'subdomain' => Str::slug($companyName),
                'email' => 'admin@gotrips.ai',
                'primary_color' => '#FFD700',
                'secondary_color' => '#FFA500',
                'currency' => 'AED',
                'timezone' => 'Asia/Dubai',
                'plan' => 'enterprise',
                'markup_percentage' => 20,
                'is_active' => true,
            ]
        );
    }

    protected function createSuperAdmin(): User
    {
        $email = $this->option('admin-email') ?: 'superadmin@gotrips.ai';
        $password = $this->option('admin-password') ?: 'SuperAdmin@123';

        $superAdmin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($password),
                'role' => 'super_admin',
                'company_id' => null,
            ]
        );

        $this->info("Super Admin created/updated: {$email}");
        if ($this->option('admin-password')) {
            $this->warn("Password set as provided");
        } else {
            $this->warn("Default password: SuperAdmin@123");
        }

        return $superAdmin;
    }
}

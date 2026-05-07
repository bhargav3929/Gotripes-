<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Production drift: several tenant-scoped tables on prod MySQL never
     * received the company_id column (it was added manually to local SQLite
     * during development and never captured in a migration).
     *
     * This migration is fully idempotent: for each known tenant-owned table,
     * it adds company_id ONLY if missing, then backfills NULL → gotrips id.
     */
    public function up(): void
    {
        $tables = [
            'tbl_UAEActivities',
            'activitybookings',
            'activityBookings',          // some setups have mixed case
            'tbl_announcements',
            'tbl_homepageads',
            'banners',
            'tbl_travel_packages',
            'tbl_umrah_packages',
            'esim_orders',
            'nomod_transactions',
            'UAEV_application',
            'uae_visa_master',
            'referral_agents',
            'referral_clicks',
            'referral_tracking',
        ];

        $defaultCompanyId = DB::table('companies')->where('slug', 'gotrips')->value('id') ?? 1;

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            // Add column if missing
            if (!Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->unsignedBigInteger('company_id')->nullable();
                    $t->index('company_id');
                });
                echo "  added company_id to {$table}\n";
            }

            // Backfill any remaining NULL rows
            $updated = DB::table($table)->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
            if ($updated > 0) {
                echo "  backfilled {$updated} NULL rows in {$table}\n";
            }
        }
    }

    public function down(): void
    {
        // No-op. Removing the column on rollback would orphan tenant data.
    }
};

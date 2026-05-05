<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Backfill company_id = 1 (GoTrips main company) on every tenant-scoped
     * table that has historical NULL rows. These rows pre-date multi-tenancy
     * and all belong to the original GoTrips business.
     *
     * This migration is idempotent: re-running only touches rows that are
     * still NULL.
     */
    public function up(): void
    {
        $defaultCompanyId = DB::table('companies')->where('slug', 'gotrips')->value('id') ?? 1;

        $tables = [
            'tbl_UAEActivities',
            'tbl_announcements',
            'nomod_transactions',
            'esim_orders',
            'referral_agents',
            'referral_clicks',
            'referral_tracking',
            'tbl_homepageads',
            'banners',
            'tbl_travel_packages',
            'tbl_umrah_packages',
            'activityBookings',
            'UAEV_application',
            'uae_visa_master',
        ];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) continue;
            if (!Schema::hasColumn($table, 'company_id')) continue;

            $updated = DB::table($table)->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
            if ($updated > 0) {
                echo "  backfilled {$updated} rows in {$table}\n";
            }
        }
    }

    public function down(): void
    {
        // No-op: we cannot reliably tell which rows were backfilled vs originally
        // assigned. Safer to leave the data as-is on rollback.
    }
};

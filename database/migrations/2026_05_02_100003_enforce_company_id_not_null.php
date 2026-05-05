<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Enforce NOT NULL on company_id for every tenant-scoped table.
     *
     * Skipped on SQLite — SQLite cannot ALTER a column in place. Local dev
     * keeps the column nullable; the BelongsToCompany trait still auto-fills
     * company_id on every insert, and CompanyScope still filters reads, so
     * the constraint is enforced in app code even without DB enforcement.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            echo "  SQLite detected — skipping NOT NULL enforcement (column stays nullable in dev).\n";
            return;
        }

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

        $defaultCompanyId = DB::table('companies')->where('slug', 'gotrips')->value('id') ?? 1;

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) continue;
            if (!Schema::hasColumn($table, 'company_id')) continue;

            $stillNull = DB::table($table)->whereNull('company_id')->count();
            if ($stillNull > 0) {
                echo "  {$table}: {$stillNull} rows still NULL — backfilling before NOT NULL.\n";
                DB::table($table)->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
            }

            DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `company_id` BIGINT UNSIGNED NOT NULL");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') return;

        $tables = [
            'tbl_UAEActivities', 'tbl_announcements', 'nomod_transactions', 'esim_orders',
            'referral_agents', 'referral_clicks', 'referral_tracking', 'tbl_homepageads',
            'banners', 'tbl_travel_packages', 'tbl_umrah_packages', 'activityBookings',
            'UAEV_application', 'uae_visa_master',
        ];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) continue;
            if (!Schema::hasColumn($table, 'company_id')) continue;
            DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `company_id` BIGINT UNSIGNED NULL");
        }
    }
};

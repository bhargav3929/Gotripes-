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

        // Resolve real table names case-correctly. On Hostinger MySQL with
        // lower_case_table_names=1, Schema::hasTable() may answer true for
        // a wrong-cased name and then ALTER TABLE fails. We query the
        // information_schema for the exact stored name.
        $driver = DB::getDriverName();

        foreach ($tables as $table) {
            $realTable = $this->resolveRealTableName($table, $driver);
            if ($realTable === null) {
                continue; // table doesn't exist at all
            }

            // Add column if missing
            if (!Schema::hasColumn($realTable, 'company_id')) {
                try {
                    Schema::table($realTable, function (Blueprint $t) {
                        $t->unsignedBigInteger('company_id')->nullable();
                        $t->index('company_id');
                    });
                    echo "  added company_id to {$realTable}\n";
                } catch (\Throwable $e) {
                    echo "  SKIP {$realTable}: " . $e->getMessage() . "\n";
                    continue;
                }
            }

            // Backfill any remaining NULL rows
            $updated = DB::table($realTable)->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
            if ($updated > 0) {
                echo "  backfilled {$updated} NULL rows in {$realTable}\n";
            }
        }
    }

    /**
     * Find the real (case-correct) table name on the current connection.
     * Returns null if the table truly doesn't exist.
     */
    private function resolveRealTableName(string $candidate, string $driver): ?string
    {
        if (!Schema::hasTable($candidate)) {
            return null;
        }

        // SQLite + most setups: the candidate name is already correct.
        if ($driver !== 'mysql') {
            return $candidate;
        }

        // MySQL: look up the exact case stored in information_schema.
        $row = DB::selectOne(
            "SELECT TABLE_NAME FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE() AND LOWER(TABLE_NAME) = LOWER(?)
             LIMIT 1",
            [$candidate]
        );
        return $row?->TABLE_NAME ?? null;
    }

    public function down(): void
    {
        // No-op. Removing the column on rollback would orphan tenant data.
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Production MySQL never received a company_id column on UAEV_application.
     * Local SQLite has it (probably added manually during development).
     *
     * This migration is idempotent — runs only if the column doesn't exist.
     * Backfills any NULL rows to the gotrips main company.
     */
    public function up(): void
    {
        if (!Schema::hasTable('UAEV_application')) {
            return;
        }

        if (!Schema::hasColumn('UAEV_application', 'company_id')) {
            Schema::table('UAEV_application', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('UAEV_status');
                $table->index('company_id');
            });
        }

        // Backfill any NULL rows to gotrips (id = 1, or whatever has slug 'gotrips')
        $defaultCompanyId = DB::table('companies')->where('slug', 'gotrips')->value('id') ?? 1;
        DB::table('UAEV_application')->whereNull('company_id')->update(['company_id' => $defaultCompanyId]);
    }

    public function down(): void
    {
        if (Schema::hasTable('UAEV_application') && Schema::hasColumn('UAEV_application', 'company_id')) {
            Schema::table('UAEV_application', function (Blueprint $table) {
                $table->dropIndex(['company_id']);
                $table->dropColumn('company_id');
            });
        }
    }
};

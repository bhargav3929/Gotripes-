<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Adds company_id to tbl_UAEActivityDetails so a tenant manager can edit details
 * (overview, highlights, image list) without leaking changes to other tenants.
 * Backfills existing detail rows from their parent activity's company_id.
 */
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('tbl_UAEActivityDetails', 'company_id')) {
            Schema::table('tbl_UAEActivityDetails', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('activityID');
                $table->index('company_id');
            });
        }

        // Backfill from parent UAEActivity row, using a correlated subquery
        // (portable across MySQL on production and SQLite locally).
        DB::statement('
            UPDATE tbl_UAEActivityDetails
            SET company_id = (
                SELECT a.company_id
                FROM tbl_UAEActivities a
                WHERE a.activityID = tbl_UAEActivityDetails.activityID
            )
            WHERE company_id IS NULL
              AND EXISTS (
                  SELECT 1 FROM tbl_UAEActivities a
                  WHERE a.activityID = tbl_UAEActivityDetails.activityID
                    AND a.company_id IS NOT NULL
              )
        ');
    }

    public function down(): void
    {
        if (Schema::hasColumn('tbl_UAEActivityDetails', 'company_id')) {
            Schema::table('tbl_UAEActivityDetails', function (Blueprint $table) {
                $table->dropIndex(['company_id']);
                $table->dropColumn('company_id');
            });
        }
    }
};

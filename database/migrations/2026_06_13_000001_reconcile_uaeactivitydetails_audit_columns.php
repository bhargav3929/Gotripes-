<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reconciles tbl_UAEActivityDetails across environments.
 *
 * Production was built from the original migration and uses the project's
 * manual audit columns (createdBy/createdDate/modifiedBy/modifiedDate) with no
 * Laravel created_at/updated_at. The local sqlite copy had drifted (it carried
 * created_at/updated_at but was missing the audit columns), so the manager
 * "create activity" flow failed differently in each environment.
 *
 * The UAEActivityDetail model now has $timestamps = false, matching production.
 * This migration guarantees the isActive flag and the four manual audit
 * columns (createdBy/createdDate/modifiedBy/modifiedDate) exist everywhere:
 *   - production: no-op (columns already present)
 *   - local sqlite: adds the missing columns so create/update works
 *
 * Each add is guarded by Schema::hasColumn so it is safe to re-run.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_UAEActivityDetails', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_UAEActivityDetails', 'isActive')) {
                $table->boolean('isActive')->default(1);
            }
            if (!Schema::hasColumn('tbl_UAEActivityDetails', 'createdBy')) {
                $table->string('createdBy')->nullable();
            }
            if (!Schema::hasColumn('tbl_UAEActivityDetails', 'createdDate')) {
                $table->timestamp('createdDate')->nullable();
            }
            if (!Schema::hasColumn('tbl_UAEActivityDetails', 'modifiedBy')) {
                $table->string('modifiedBy')->nullable();
            }
            if (!Schema::hasColumn('tbl_UAEActivityDetails', 'modifiedDate')) {
                $table->timestamp('modifiedDate')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Intentionally left as a no-op: these are pre-existing audit columns on
        // production and dropping them would lose data. The reconciliation is
        // additive and safe to keep.
    }
};

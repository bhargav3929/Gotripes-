<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Multi-country activities (non-UAE) have no Emirate, so emiratesID must be
     * nullable. An earlier migration declared nullable() but targeted the
     * lowercase name `tbl_uaeactivities`; on case-sensitive production MySQL the
     * real table is `tbl_UAEActivities`, so the column stayed NOT NULL there.
     * Use raw SQL against the correct mixed-case name (no doctrine/dbal needed).
     */
    public function up(): void
    {
        // MySQL-only: production's tbl_UAEActivities had emiratesID NOT NULL. On
        // SQLite (local) the column was already created nullable, so skip.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `tbl_UAEActivities` MODIFY `emiratesID` INT(11) NULL DEFAULT NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `tbl_UAEActivities` MODIFY `emiratesID` INT(11) NOT NULL');
        }
    }
};

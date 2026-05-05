<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Add 'reserved' to the tenant_commissions.status enum.
     *
     * 'reserved' means the row is locked to a specific TenantWithdrawal:
     *   - it does NOT count toward the tenant's available balance
     *   - it CANNOT be reserved again by another withdrawal
     *   - it flips to 'paid' when the withdrawal is marked paid
     *   - it flips back to 'available' when the withdrawal is rejected
     *
     * This eliminates the double-withdrawal race condition.
     *
     * SQLite does not enforce enum constraints at the DB level (the column
     * is just a varchar), so no schema change is needed there. MySQL/Postgres
     * need an ALTER.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return; // SQLite stores enums as text — new value 'reserved' just works.
        }

        DB::statement("
            ALTER TABLE `tenant_commissions`
            MODIFY COLUMN `status`
            ENUM('pending', 'available', 'reserved', 'paid', 'reversed')
            NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') return;

        // Demoting reserved → available before shrinking the enum keeps data sane.
        DB::table('tenant_commissions')
            ->where('status', 'reserved')
            ->update(['status' => 'available', 'withdrawal_id' => null]);

        DB::statement("
            ALTER TABLE `tenant_commissions`
            MODIFY COLUMN `status`
            ENUM('pending', 'available', 'paid', 'reversed')
            NOT NULL DEFAULT 'pending'
        ");
    }
};

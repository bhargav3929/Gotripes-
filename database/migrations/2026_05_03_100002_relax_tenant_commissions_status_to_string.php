<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * The original tenant_commissions migration declared status as an ENUM,
     * which SQLite enforces with a CHECK constraint that blocks new values
     * like 'reserved'. The previous migration intended to expand the enum
     * but couldn't on SQLite without a table rebuild.
     *
     * Fix: relax the column to a plain string with an app-level constraint.
     * The CommissionService is the single writer and only ever sets known
     * values, so DB-level enforcement is redundant.
     *
     * SQLite path: rebuild the table so the CHECK constraint is dropped.
     * MySQL path:  ALTER COLUMN to VARCHAR.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite cannot drop a CHECK constraint in place. Rebuild the table.
            $this->rebuildSqliteTable();
            return;
        }

        DB::statement("
            ALTER TABLE `tenant_commissions`
            MODIFY COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // No-op. Re-imposing the strict enum would risk breaking rows that
        // were validly set to 'reserved' between the up and down.
    }

    private function rebuildSqliteTable(): void
    {
        Schema::create('tenant_commissions_new', function ($table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('source_type', 50);
            $table->unsignedBigInteger('source_id');
            $table->decimal('gross_amount', 12, 2);
            $table->string('commission_type', 20);
            $table->decimal('commission_rate', 10, 2);
            $table->decimal('commission_amount', 12, 2);
            $table->string('currency', 3)->default('AED');
            $table->string('status', 20)->default('pending');   // ← no CHECK constraint
            $table->timestamp('available_at')->nullable();
            $table->unsignedBigInteger('withdrawal_id')->nullable()->index();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index(['source_type', 'source_id']);
        });

        DB::statement('
            INSERT INTO tenant_commissions_new
            (id, company_id, source_type, source_id, gross_amount, commission_type,
             commission_rate, commission_amount, currency, status, available_at,
             withdrawal_id, created_at, updated_at)
            SELECT
             id, company_id, source_type, source_id, gross_amount, commission_type,
             commission_rate, commission_amount, currency, status, available_at,
             withdrawal_id, created_at, updated_at
            FROM tenant_commissions
        ');

        Schema::drop('tenant_commissions');
        Schema::rename('tenant_commissions_new', 'tenant_commissions');
    }
};

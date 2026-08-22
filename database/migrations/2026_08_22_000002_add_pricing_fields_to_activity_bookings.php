<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The live database already carries 8 columns on activitybookings — address,
 * nationality, remarks, amount, currency, transportCharges, actionType,
 * status — that ActivityBookingController and the Manager Finance "Bookings"
 * page both depend on, but no tracked migration ever created them (they were
 * added out-of-band). A fresh `migrate` on a new environment would produce a
 * table missing all 8, breaking booking creation and this Finance page. This
 * migration is idempotent (Schema::hasColumn guards) so it is a no-op on a
 * database that was already patched manually, and only backfills a database
 * that was migrated from a clean slate.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activitybookings', function (Blueprint $table) {
            if (!Schema::hasColumn('activitybookings', 'address')) {
                $table->string('address')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('activitybookings', 'nationality')) {
                $table->string('nationality', 100)->nullable()->after('address');
            }
            if (!Schema::hasColumn('activitybookings', 'remarks')) {
                $table->string('remarks', 500)->nullable()->after('childrens');
            }
            if (!Schema::hasColumn('activitybookings', 'amount')) {
                $table->decimal('amount', 12, 2)->nullable()->after('remarks');
            }
            if (!Schema::hasColumn('activitybookings', 'currency')) {
                $table->string('currency', 10)->nullable()->after('amount');
            }
            if (!Schema::hasColumn('activitybookings', 'transportCharges')) {
                $table->decimal('transportCharges', 12, 2)->nullable()->after('transfer');
            }
            if (!Schema::hasColumn('activitybookings', 'actionType')) {
                $table->string('actionType', 50)->nullable()->after('transportCharges');
            }
            if (!Schema::hasColumn('activitybookings', 'status')) {
                $table->string('status', 50)->nullable()->after('actionType');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activitybookings', function (Blueprint $table) {
            foreach (['address', 'nationality', 'remarks', 'amount', 'currency', 'transportCharges', 'actionType', 'status'] as $col) {
                if (Schema::hasColumn('activitybookings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

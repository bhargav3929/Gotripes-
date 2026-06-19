<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fifa_ticket_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('fifa_ticket_requests', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id')->index();
            }
            if (!Schema::hasColumn('fifa_ticket_requests', 'order_id')) {
                // ORDFIFA{id} — links the paid booking to its Nomod transaction.
                $table->string('order_id')->nullable()->after('status')->index();
            }
            if (!Schema::hasColumn('fifa_ticket_requests', 'unit_price')) {
                $table->decimal('unit_price', 10, 2)->nullable()->after('order_id');
            }
            if (!Schema::hasColumn('fifa_ticket_requests', 'amount')) {
                $table->decimal('amount', 10, 2)->nullable()->after('unit_price');
            }
            if (!Schema::hasColumn('fifa_ticket_requests', 'currency')) {
                $table->string('currency', 8)->nullable()->after('amount');
            }
            if (!Schema::hasColumn('fifa_ticket_requests', 'payment_status')) {
                // null = enquiry only; else awaiting_payment | paid | failed
                $table->string('payment_status')->nullable()->after('currency');
            }
            if (!Schema::hasColumn('fifa_ticket_requests', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('fifa_ticket_requests', 'notified_at')) {
                // Guards against re-notifying on a duplicate payment callback.
                $table->timestamp('notified_at')->nullable()->after('paid_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fifa_ticket_requests', function (Blueprint $table) {
            foreach (['company_id', 'order_id', 'unit_price', 'amount', 'currency', 'payment_status', 'paid_at', 'notified_at'] as $col) {
                if (Schema::hasColumn('fifa_ticket_requests', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ledger of every commission generated for a tenant company.
 * One row per qualifying booking/order.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('source_type', 50);   // e.g. activity_booking, esim_order
            $table->unsignedBigInteger('source_id'); // id of the underlying booking/order row
            $table->decimal('gross_amount', 12, 2);
            $table->string('commission_type', 20); // percentage | flat (snapshot at creation)
            $table->decimal('commission_rate', 10, 2); // snapshot
            $table->decimal('commission_amount', 12, 2);
            $table->string('currency', 3)->default('AED');
            $table->enum('status', ['pending', 'available', 'paid', 'reversed'])->default('pending');
            $table->timestamp('available_at')->nullable(); // when it becomes withdrawable
            $table->unsignedBigInteger('withdrawal_id')->nullable()->index(); // FK once paid
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_commissions');
    }
};

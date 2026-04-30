<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('AED');
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->string('payment_reference')->nullable(); // bank ref / tx id
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable(); // super admin user id
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('bank_account_id')->references('id')->on('tenant_bank_accounts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_withdrawals');
    }
};

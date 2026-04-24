<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referral_agent_id');
            $table->unsignedBigInteger('referral_bank_account_id')->nullable();
            $table->json('bank_snapshot')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('AED');
            $table->enum('status', ['pending', 'processing', 'completed', 'rejected'])->default('pending');
            $table->text('agent_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('referral_agent_id')
                  ->references('id')
                  ->on('referral_agents')
                  ->onDelete('cascade');

            $table->foreign('referral_bank_account_id')
                  ->references('id')
                  ->on('referral_bank_accounts')
                  ->onDelete('set null');

            $table->index(['referral_agent_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_withdrawal_requests');
    }
};

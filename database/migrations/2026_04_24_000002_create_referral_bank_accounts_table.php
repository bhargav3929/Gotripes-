<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referral_agent_id');
            $table->string('bank_name');
            $table->string('account_holder_name');
            $table->string('account_number');
            $table->string('iban')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('country')->default('UAE');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('referral_agent_id')
                  ->references('id')
                  ->on('referral_agents')
                  ->onDelete('cascade');

            $table->index('referral_agent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_bank_accounts');
    }
};

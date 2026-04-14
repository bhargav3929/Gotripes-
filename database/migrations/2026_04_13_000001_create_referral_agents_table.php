<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('referral_agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('referral_code', 50)->unique();
            $table->enum('commission_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('commission_value', 10, 2)->default(10.00);
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->decimal('total_earnings', 12, 2)->default(0.00);
            $table->decimal('pending_earnings', 12, 2)->default(0.00);
            $table->decimal('paid_earnings', 12, 2)->default(0.00);
            $table->integer('total_sales')->default(0);
            $table->integer('total_clicks')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'referral_code']);
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_agents');
    }
};

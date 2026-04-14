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
        Schema::create('referral_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_agent_id')->constrained('referral_agents')->onDelete('cascade');
            $table->string('order_id')->index();
            $table->string('order_type')->default('esim'); // esim, activity, umrah, travel
            $table->string('customer_email')->nullable();
            $table->string('customer_name')->nullable();
            $table->decimal('order_amount', 12, 2);
            $table->string('currency', 10)->default('AED');
            $table->decimal('commission_amount', 12, 2);
            $table->enum('commission_type', ['percentage', 'fixed']);
            $table->decimal('commission_value', 10, 2);
            $table->enum('status', ['pending', 'approved', 'paid', 'rejected', 'cancelled'])->default('pending');
            $table->enum('payment_status', ['pending', 'completed', 'refunded', 'partially_refunded'])->default('pending');
            $table->boolean('is_refunded')->default(false);
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->json('order_details')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['referral_agent_id', 'status']);
            $table->index(['status', 'payment_status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_tracking');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('esim_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_reference')->index();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->string('country_code', 5);
            $table->string('country_name');
            $table->string('bundle_code');
            $table->string('bundle_name');
            $table->string('data_amount');
            $table->integer('validity_days');
            $table->decimal('monty_cost_price', 12, 2);
            $table->decimal('selling_price', 12, 2);
            $table->string('currency', 10)->default('AED');
            $table->string('monty_order_id')->nullable();
            $table->string('monty_iccid')->nullable();
            $table->string('reservation_status')->default('pending');
            $table->string('payment_status')->default('unpaid');
            $table->json('monty_response')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('esim_orders');
    }
};

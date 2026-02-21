<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nomod_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('checkout_id')->unique()->nullable();
            $table->string('order_id')->index();
            $table->string('status')->default('created');
            $table->decimal('amount', 12, 2)->nullable();
            $table->decimal('discount', 12, 2)->nullable();
            $table->string('currency', 10)->default('AED');
            $table->string('booking_type')->nullable();
            $table->text('checkout_url')->nullable();
            $table->json('items')->nullable();
            $table->json('customer')->nullable();
            $table->json('charges')->nullable();
            $table->json('metadata')->nullable();
            $table->json('response_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nomod_transactions');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_umrah_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreignId('umrah_package_id')->constrained('tbl_umrah_packages');
            $table->date('departure_date');
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->integer('infants')->default(0);
            $table->string('payment_gateway')->nullable(); // Card, Tabby, Tamara
            $table->integer('installment_months')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->text('passenger_details')->nullable(); // JSON list of passenger details
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            $table->string('order_id')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_umrah_bookings');
    }
};

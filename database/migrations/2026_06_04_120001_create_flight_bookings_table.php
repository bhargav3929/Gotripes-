<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();

            $table->string('order_id')->index();
            $table->string('offer_id')->nullable();
            $table->string('pnr')->nullable()->index();
            $table->string('booking_reference')->nullable()->index();
            $table->string('status')->default('searched')->index();

            $table->string('trip_type')->nullable();
            $table->string('gds_provider')->nullable();
            $table->string('branch')->nullable();

            $table->string('origin', 10)->nullable();
            $table->string('destination', 10)->nullable();
            $table->date('departure_date')->nullable();
            $table->date('return_date')->nullable();
            $table->string('cabin')->nullable();

            $table->unsignedTinyInteger('adults')->default(1);
            $table->unsignedTinyInteger('children')->default(0);
            $table->unsignedTinyInteger('infants')->default(0);

            $table->string('currency', 10)->default('AED');
            $table->decimal('net_amount', 12, 2)->nullable();
            $table->decimal('amount', 12, 2)->nullable();

            $table->json('ticket_numbers')->nullable();
            $table->dateTime('ticket_time_limit')->nullable();

            $table->json('passengers')->nullable();
            $table->json('contact')->nullable();
            $table->json('search_request')->nullable();
            $table->json('offer_data')->nullable();
            $table->json('booking_response')->nullable();
            $table->json('ticket_response')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_bookings');
    }
};

<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentResponsesTable extends Migration
{
    public function up()
    {
        Schema::create('payment_responses', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            // Foreign key to booking
            $table->unsignedBigInteger('booking_id');
            $table->foreign('booking_id')->references('id')->on('activity_bookings')->onDelete('cascade');
            
            // Store order id and payment status separately for quick lookups
            $table->string('order_id')->index();  // e.g. CCAvenue order id like 'ORD1756902693401'
            $table->string('order_status')->nullable();
            
            // Store full raw JSON response from payment gateway
            $table->json('response_data')->nullable();
            
            // Track timestamps for record keeping
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_responses');
    }
}


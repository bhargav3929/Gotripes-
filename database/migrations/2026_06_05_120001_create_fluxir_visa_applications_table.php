<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fluxir_visa_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();

            $table->string('order_id')->unique();
            $table->string('fluxir_person_id')->nullable()->index();
            $table->string('fluxir_trip_id')->nullable()->index();
            $table->string('fluxir_service_application_id')->nullable()->index();

            $table->string('state')->nullable();              // Fluxir state machine
            $table->string('status')->default('draft')->index(); // local lifecycle
            $table->boolean('is_paid')->default(false);
            $table->string('checkout_session_id')->nullable();
            $table->text('checkout_url')->nullable();

            // Traveller snapshot
            $table->string('title')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('country_of_issuance', 10)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality', 10)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('destination_code', 10)->nullable();
            $table->string('origination_code', 10)->nullable();
            $table->date('arrival_date')->nullable();
            $table->date('departure_date')->nullable();

            $table->string('currency', 10)->nullable();
            $table->decimal('amount', 12, 2)->nullable();

            $table->json('attachments')->nullable();
            $table->json('items')->nullable();
            $table->json('last_response')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fluxir_visa_applications');
    }
};

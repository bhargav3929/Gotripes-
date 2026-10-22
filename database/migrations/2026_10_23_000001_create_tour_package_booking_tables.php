<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Online booking wizard for tour packages (package_type = 'wizard').
 *
 * Supplier rate sheets are NET prices; the package's margin_percent is added
 * on top of every net line when the customer is quoted. Durations, hotel
 * tiers, meal plans and add-ons all vary by destination, so each lives in its
 * own table per package rather than being fixed in code.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_travel_packages', function (Blueprint $table) {
            $table->decimal('margin_percent', 6, 2)->default(0);
            $table->boolean('insurance_required')->default(false);
            $table->unsignedTinyInteger('child_max_age')->default(11);
            $table->unsignedTinyInteger('infant_max_age')->default(1);
        });

        // Net price per person for the whole stay, by duration and hotel tier.
        Schema::create('tour_package_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id')->index();
            $table->unsignedTinyInteger('days');
            $table->unsignedTinyInteger('hotel_stars');
            $table->decimal('adult_net', 10, 2);
            $table->decimal('child_net', 10, 2)->default(0);
            $table->decimal('infant_net', 10, 2)->default(0);
            $table->decimal('single_supplement_net', 10, 2)->default(0);
            $table->timestamps();
            $table->unique(['package_id', 'days', 'hotel_stars']);
        });

        // The hotel used for each tier, with its policies and real meal plans.
        Schema::create('tour_package_hotels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id')->index();
            $table->unsignedTinyInteger('hotel_stars');
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('check_in_time', 5)->default('14:00');
            $table->string('check_out_time', 5)->default('12:00');
            $table->text('late_check_in_policy')->nullable();
            $table->text('late_check_out_policy')->nullable();
            $table->json('meal_plans')->nullable();   // subset of room_only, breakfast, half_board, full_board
            $table->timestamps();
            $table->unique(['package_id', 'hotel_stars']);
        });

        // Options: guide language, transport, meal plan supplements, airport transfer, insurance.
        Schema::create('tour_package_addons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id')->index();
            $table->string('category', 30);
            $table->string('code', 40);
            $table->string('label');
            $table->string('description')->nullable();
            $table->decimal('net_price', 10, 2)->default(0);
            $table->string('charge_basis', 30)->default('per_booking'); // per_person | per_person_per_day | per_booking | per_day
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['package_id', 'category', 'code']);
        });

        // Day-by-day itinerary. duration_days null = used for any duration.
        Schema::create('tour_package_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id')->index();
            $table->unsignedTinyInteger('duration_days')->nullable();
            $table->unsignedTinyInteger('day_number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        // Peak periods: a percentage added to the package (rate) price.
        Schema::create('tour_package_seasons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id')->index();
            $table->string('name');
            $table->date('starts_on');
            $table->date('ends_on');
            $table->decimal('surcharge_percent', 6, 2);
            $table->timestamps();
        });

        // What a passport holder needs to enter a destination. nationality '*' = everyone else.
        Schema::create('destination_visa_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('destination', 100);
            $table->string('nationality', 100);
            $table->string('requirement', 30);  // visa_free | visa_on_arrival | evisa | visa_required
            $table->unsignedSmallInteger('max_stay_days')->nullable();
            $table->unsignedSmallInteger('processing_days')->nullable();
            $table->decimal('fee', 10, 2)->nullable();
            $table->text('documents')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'destination', 'nationality']);
        });

        Schema::create('tour_package_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->unsignedBigInteger('package_id')->index();
            $table->string('order_id', 40)->nullable()->unique();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 40);
            $table->string('nationality', 100);
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedTinyInteger('days');
            $table->unsignedTinyInteger('hotel_stars');
            $table->string('traveler_type', 20);
            $table->unsignedSmallInteger('adults');
            $table->unsignedSmallInteger('children')->default(0);
            $table->unsignedSmallInteger('infants')->default(0);
            $table->json('child_ages')->nullable();
            $table->json('selections');           // guide, transport, meal plan, transfer, insurance codes
            $table->json('price_breakdown');      // the quote shown to the customer, frozen
            $table->decimal('net_total', 12, 2);
            $table->decimal('total', 12, 2);
            $table->string('currency', 3)->default('AED');
            $table->string('visa_requirement', 30)->nullable();
            $table->string('payment_status', 20)->default('pending'); // pending | paid | failed | cancelled
            $table->string('status', 20)->default('new');             // new | confirmed | cancelled
            $table->text('special_requests')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_package_bookings');
        Schema::dropIfExists('destination_visa_rules');
        Schema::dropIfExists('tour_package_seasons');
        Schema::dropIfExists('tour_package_days');
        Schema::dropIfExists('tour_package_addons');
        Schema::dropIfExists('tour_package_hotels');
        Schema::dropIfExists('tour_package_rates');
        Schema::table('tbl_travel_packages', function (Blueprint $table) {
            $table->dropColumn(['margin_percent', 'insurance_required', 'child_max_age', 'infant_max_age']);
        });
    }
};

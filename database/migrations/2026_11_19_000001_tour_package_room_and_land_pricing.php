<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tour package pricing the way supplier sheets are written:
 *   - hotel: a NET price per ROOM per NIGHT (single, double, triple, extra bed)
 *     for each hotel level, so the wizard can work out the rooms a party needs;
 *   - land (tours, transfers, guide, vehicle): a NET price per PERSON for the
 *     whole trip, by trip length and group size (bigger groups share the car).
 * The old per-person "twin sharing" rates (tour_package_rates) still price any
 * package that has not been moved over yet.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tour_package_hotels', function (Blueprint $table) {
            $table->decimal('single_net', 10, 2)->nullable()->after('location');
            $table->decimal('double_net', 10, 2)->nullable()->after('single_net');
            $table->decimal('triple_net', 10, 2)->nullable()->after('double_net');
            $table->decimal('extra_bed_net', 10, 2)->nullable()->after('triple_net');
        });

        Schema::create('tour_package_land_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('package_id')->index();
            $table->unsignedSmallInteger('days');
            $table->unsignedSmallInteger('min_group'); // smallest group size this price is for
            $table->decimal('adult_net', 10, 2);
            $table->timestamps();
            $table->unique(['package_id', 'days', 'min_group']);
        });

        Schema::table('tbl_travel_packages', function (Blueprint $table) {
            $table->decimal('child_land_percent', 5, 2)->default(50);
            $table->unsignedTinyInteger('child_sharing_max_age')->default(5);
        });
    }

    public function down(): void
    {
        Schema::table('tour_package_hotels', function (Blueprint $table) {
            $table->dropColumn(['single_net', 'double_net', 'triple_net', 'extra_bed_net']);
        });
        Schema::dropIfExists('tour_package_land_rates');
        Schema::table('tbl_travel_packages', function (Blueprint $table) {
            $table->dropColumn(['child_land_percent', 'child_sharing_max_age']);
        });
    }
};

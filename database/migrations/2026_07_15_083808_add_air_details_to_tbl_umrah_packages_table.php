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
        Schema::table('tbl_umrah_packages', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('sub_category');
            $table->string('airline')->nullable()->after('category_id');
            $table->string('flight_number')->nullable()->after('airline');
            $table->string('departure_airport')->nullable()->after('flight_number');
            $table->string('arrival_airport')->nullable()->after('departure_airport');
            $table->string('cabin_class')->nullable()->after('arrival_airport');
            $table->string('baggage')->nullable()->after('cabin_class');
            $table->text('transit_details')->nullable()->after('baggage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_umrah_packages', function (Blueprint $table) {
            $table->dropColumn([
                'category_id', 'airline', 'flight_number', 'departure_airport', 
                'arrival_airport', 'cabin_class', 'baggage', 'transit_details'
            ]);
        });
    }
};

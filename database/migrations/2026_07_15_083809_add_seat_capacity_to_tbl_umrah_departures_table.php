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
        Schema::table('tbl_umrah_departures', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_umrah_departures', 'seats_total')) {
                $table->integer('seats_total')->default(0)->after('price');
            }
            if (!Schema::hasColumn('tbl_umrah_departures', 'booking_cutoff')) {
                $table->date('booking_cutoff')->nullable()->after('seats_booked');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_umrah_departures', function (Blueprint $table) {
            $table->dropColumn(['seats_total', 'booking_cutoff']);
        });
    }
};

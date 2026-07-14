<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_umrah_departures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umrah_package_id')->constrained('tbl_umrah_packages')->onDelete('cascade');
            $table->date('departure_date');
            $table->integer('seats_available')->default(0);
            $table->integer('seats_booked')->default(0);
            $table->string('status')->default('available'); // available, sold_out, inactive
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_umrah_departures');
    }
};

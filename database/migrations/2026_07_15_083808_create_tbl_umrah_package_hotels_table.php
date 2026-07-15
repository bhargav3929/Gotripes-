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
        Schema::create('tbl_umrah_package_hotels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('umrah_package_id');
            $table->unsignedBigInteger('umrah_hotel_id');
            $table->timestamps();
            
            $table->index('umrah_package_id');
            $table->index('umrah_hotel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_umrah_package_hotels');
    }
};

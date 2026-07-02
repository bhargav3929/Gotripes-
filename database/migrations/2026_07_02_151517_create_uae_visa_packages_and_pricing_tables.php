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
        Schema::create('uae_visa_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('emirates_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('isActive')->default(1);
            $table->unsignedBigInteger('company_id')->nullable();
            $table->timestamps();

            $table->foreign('emirates_id')->references('emiratesID')->on('tbl_emirates')->onDelete('cascade');
        });

        Schema::create('uae_visa_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visa_package_id');
            $table->string('entry_type');
            $table->string('duration');
            $table->string('traveller_type');
            $table->decimal('price', 10, 2);
            $table->boolean('isActive')->default(1);
            $table->unsignedBigInteger('company_id')->nullable();
            $table->timestamps();

            $table->foreign('visa_package_id')->references('id')->on('uae_visa_packages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uae_visa_prices');
        Schema::dropIfExists('uae_visa_packages');
    }
};

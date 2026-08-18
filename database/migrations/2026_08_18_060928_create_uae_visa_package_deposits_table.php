<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets a package's refundable security deposit vary by nationality (e.g. an
 * India/Pakistan/Nepal applicant pays a different deposit than everyone
 * else), mirroring the nationality-override pattern already used for
 * uae_visa_prices: a row with nationality = null is the default/fallback,
 * a row with a specific nationality overrides it for that nationality only.
 *
 * Kept as its own table rather than folded into uae_visa_prices, because
 * deposit only varies by package + nationality, not by entry type, duration
 * or traveller type — reusing the price matrix would force meaningless
 * duplication across those dimensions.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uae_visa_package_deposits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visa_package_id');
            $table->string('nationality')->nullable();
            $table->decimal('security_deposit', 10, 2);
            $table->decimal('deposit_admin_fee', 10, 2)->default(0);
            $table->boolean('isActive')->default(1);
            $table->unsignedBigInteger('company_id')->nullable();
            $table->timestamps();

            $table->foreign('visa_package_id')->references('id')->on('uae_visa_packages')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uae_visa_package_deposits');
    }
};

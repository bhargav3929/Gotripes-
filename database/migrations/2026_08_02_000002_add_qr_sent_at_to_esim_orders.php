<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Records when GoTrips emailed the customer their eSIM QR code.
 *
 * Until now the only QR email came from the provider, so "did the customer
 * actually receive their eSIM?" was unanswerable from our side. This makes it
 * visible in the manager portal and lets the reconciler spot a provisioned
 * order whose customer was never sent anything.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('esim_orders', function (Blueprint $table) {
            $table->timestamp('qr_sent_at')->nullable()->after('monty_response');
        });
    }

    public function down(): void
    {
        Schema::table('esim_orders', function (Blueprint $table) {
            $table->dropColumn('qr_sent_at');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Closes the flight-booking IDOR: show()/checkout()/cancel()/refund()/ticket()
 * identified a booking purely by its order_id (a uniqid()-based, guessable
 * string), so anyone who could reach the endpoint at all — now gated behind
 * auth:sanctum — could still act on ANY booking, not just their own. This
 * records who created each booking so the controller can check ownership.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flight_bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('company_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('flight_bookings', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};

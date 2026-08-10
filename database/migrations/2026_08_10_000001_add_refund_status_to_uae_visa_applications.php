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
        Schema::table('UAEV_application', function (Blueprint $table) {
            // The deposit/refund amount columns record what is owed, but nothing
            // tracked whether it was actually paid out. 'pending' is set only for
            // Sharjah applications with a refund amount due; stays null otherwise.
            $table->string('UAEV_refund_status')->nullable()->after('UAEV_refund_amount');
            $table->timestamp('UAEV_refunded_at')->nullable()->after('UAEV_refund_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('UAEV_application', function (Blueprint $table) {
            $table->dropColumn(['UAEV_refund_status', 'UAEV_refunded_at']);
        });
    }
};

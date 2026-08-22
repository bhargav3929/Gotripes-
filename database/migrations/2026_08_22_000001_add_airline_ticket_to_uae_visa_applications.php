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
            // The form has always required an airline ticket upload per
            // applicant, but nothing stored, validated, or displayed it — the
            // file was silently dropped on every submission.
            $table->string('UAEV_airline_ticket')->nullable()->after('UAEV_passport_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('UAEV_application', function (Blueprint $table) {
            $table->dropColumn('UAEV_airline_ticket');
        });
    }
};

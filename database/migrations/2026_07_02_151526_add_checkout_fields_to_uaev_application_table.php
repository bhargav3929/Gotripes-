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
            $table->string('UAEV_emirate')->nullable()->after('UAEV_residence');
            $table->string('UAEV_package_name')->nullable()->after('UAEV_emirate');
            $table->string('UAEV_visa_type')->nullable()->after('UAEV_package_name');
            $table->string('UAEV_traveller_type')->nullable()->after('UAEV_visa_type');
            $table->text('UAEV_addons')->nullable()->after('UAEV_passport_photo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('UAEV_application', function (Blueprint $table) {
            $table->dropColumn(['UAEV_emirate', 'UAEV_package_name', 'UAEV_visa_type', 'UAEV_traveller_type', 'UAEV_addons']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'uaev_application' => 'id',
            'uae_visa_master' => 'vID',
            'tbl_uaeactivities' => 'activityID',
            'activity_bookings' => 'id',
            'activitybookings' => 'id',
            'tbl_travel_packages' => 'id',
            'tbl_umrah_packages' => 'id',
            'nomod_transactions' => 'id',
            'banners' => 'id',
            'tbl_homepageads' => 'id',
            'payment_responses' => 'id',
        ];

        foreach ($tables as $tableName => $afterColumn) {
            try {
                if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'company_id')) {
                    Schema::table($tableName, function (Blueprint $table) use ($afterColumn) {
                        $table->unsignedBigInteger('company_id')->nullable()->after($afterColumn);
                        $table->index('company_id');
                    });
                }
            } catch (\Exception $e) {
                // Table doesn't exist or column already exists, skip
                continue;
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'uaev_application',
            'uae_visa_master',
            'tbl_uaeactivities',
            'activity_bookings',
            'tbl_travel_packages',
            'tbl_umrah_packages',
            'nomod_transactions',
            'banners',
            'tbl_homepageads',
            'payment_responses'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'company_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['company_id']);
                    $table->dropColumn('company_id');
                });
            }
        }
    }
};

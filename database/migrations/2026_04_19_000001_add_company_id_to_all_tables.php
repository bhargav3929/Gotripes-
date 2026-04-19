<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // UAE Visa Applications
        if (Schema::hasTable('uaev_application') && !Schema::hasColumn('uaev_application', 'company_id')) {
            Schema::table('uaev_application', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->index('company_id');
            });
        }

        // UAE Visa Master (pricing per company)
        if (Schema::hasTable('uae_visa_master') && !Schema::hasColumn('uae_visa_master', 'company_id')) {
            Schema::table('uae_visa_master', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('vID');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->index('company_id');
            });
        }

        // UAE Activities
        if (Schema::hasTable('tbl_uaeactivities') && !Schema::hasColumn('tbl_uaeactivities', 'company_id')) {
            Schema::table('tbl_uaeactivities', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('activityID');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->index('company_id');
            });
        }

        // Activity Bookings
        if (Schema::hasTable('activity_bookings') && !Schema::hasColumn('activity_bookings', 'company_id')) {
            Schema::table('activity_bookings', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->index('company_id');
            });
        }

        // Travel Packages
        if (Schema::hasTable('tbl_travel_packages') && !Schema::hasColumn('tbl_travel_packages', 'company_id')) {
            Schema::table('tbl_travel_packages', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->index('company_id');
            });
        }

        // Umrah Packages
        if (Schema::hasTable('tbl_umrah_packages') && !Schema::hasColumn('tbl_umrah_packages', 'company_id')) {
            Schema::table('tbl_umrah_packages', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->index('company_id');
            });
        }

        // Nomod Transactions (Flights/Hotels)
        if (Schema::hasTable('nomod_transactions') && !Schema::hasColumn('nomod_transactions', 'company_id')) {
            Schema::table('nomod_transactions', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->index('company_id');
            });
        }

        // Banners
        if (Schema::hasTable('banners') && !Schema::hasColumn('banners', 'company_id')) {
            Schema::table('banners', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->index('company_id');
            });
        }

        // Homepage Ads
        if (Schema::hasTable('tbl_homepageads') && !Schema::hasColumn('tbl_homepageads', 'company_id')) {
            Schema::table('tbl_homepageads', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->index('company_id');
            });
        }

        // Payment Responses
        if (Schema::hasTable('payment_responses') && !Schema::hasColumn('payment_responses', 'company_id')) {
            Schema::table('payment_responses', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->index('company_id');
            });
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

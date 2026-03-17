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
        Schema::table('tbl_UAEActivities', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_UAEActivities', 'activityCategory')) {
                $table->string('activityCategory')->nullable()->after('activityName');
            }
            if (!Schema::hasColumn('tbl_UAEActivities', 'supplierName')) {
                $table->string('supplierName')->nullable();
            }
            if (!Schema::hasColumn('tbl_UAEActivities', 'supplierEmail')) {
                $table->string('supplierEmail')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_UAEActivities', function (Blueprint $table) {
            $table->dropColumn(['activityCategory', 'supplierName', 'supplierEmail']);
        });
    }
};

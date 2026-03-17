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
            if (!Schema::hasColumn('tbl_UAEActivities', 'dubaiPrice')) {
                $table->decimal('dubaiPrice', 10, 2)->nullable()->after('activityPrice');
            }
            if (!Schema::hasColumn('tbl_UAEActivities', 'abuDhabiPrice')) {
                $table->decimal('abuDhabiPrice', 10, 2)->nullable()->after('activityPrice');
            }
            if (!Schema::hasColumn('tbl_UAEActivities', 'fromAbuDhabiToDubai')) {
                $table->decimal('fromAbuDhabiToDubai', 10, 2)->nullable()->after('activityPrice');
            }
            if (!Schema::hasColumn('tbl_UAEActivities', 'emirates')) {
                $table->decimal('emirates', 10, 2)->nullable()->after('activityPrice');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_UAEActivities', function (Blueprint $table) {
            $table->dropColumn(['dubaiPrice', 'abuDhabiPrice', 'fromAbuDhabiToDubai', 'emirates']);
        });
    }
};

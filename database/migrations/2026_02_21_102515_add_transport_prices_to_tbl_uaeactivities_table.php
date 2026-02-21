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
            $table->decimal('dubaiPrice', 10, 2)->nullable()->after('activityPrice');
            $table->decimal('abuDhabiPrice', 10, 2)->nullable()->after('dubaiPrice');
            $table->decimal('fromAbuDhabiToDubai', 10, 2)->nullable()->after('abuDhabiPrice');
            $table->decimal('emirates', 10, 2)->nullable()->after('fromAbuDhabiToDubai');
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

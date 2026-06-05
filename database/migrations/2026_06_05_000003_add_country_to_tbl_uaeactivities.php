<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_UAEActivities', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_UAEActivities', 'country')) {
                // Existing activities belong to the UAE. New activities created by a
                // partner are tagged with that partner's company country, so the
                // public Activities page can group them by country.
                $table->string('country', 100)->default('United Arab Emirates')->after('emiratesID');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_UAEActivities', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_UAEActivities', 'country')) {
                $table->dropColumn('country');
            }
        });
    }
};

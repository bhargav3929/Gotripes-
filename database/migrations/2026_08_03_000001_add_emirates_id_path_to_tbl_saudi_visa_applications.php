<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Saudi visa types can require an Emirates ID / GCC residence copy (the 1-Year
 * Multiple Entry visa does). The requirement was listed as text on the visa
 * type with nowhere for the applicant to upload it, so the document never
 * reached the supplier.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_saudi_visa_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_saudi_visa_applications', 'emirates_id_path')) {
                $table->string('emirates_id_path')->nullable()->after('photo_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_saudi_visa_applications', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_saudi_visa_applications', 'emirates_id_path')) {
                $table->dropColumn('emirates_id_path');
            }
        });
    }
};

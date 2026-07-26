<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-visa-type notification inboxes. `supplier_email` receives the full
     * application (with the uploaded documents attached) once payment clears;
     * `company_email` receives a copy of the same. Both nullable — an empty
     * value simply means "don't send to that recipient". Mirrors the UAE visa
     * supplier-email behaviour, kept per visa type as requested.
     */
    public function up(): void
    {
        Schema::table('tbl_saudi_visa_types', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_saudi_visa_types', 'company_email')) {
                $table->string('company_email')->nullable()->after('processing_days');
            }
            if (!Schema::hasColumn('tbl_saudi_visa_types', 'supplier_email')) {
                $table->string('supplier_email')->nullable()->after('company_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_saudi_visa_types', function (Blueprint $table) {
            foreach (['company_email', 'supplier_email'] as $col) {
                if (Schema::hasColumn('tbl_saudi_visa_types', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

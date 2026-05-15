<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_travel_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_travel_packages', 'country')) {
                $table->string('country', 100)->nullable()->after('title');
                $table->index(['company_id', 'country']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_travel_packages', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_travel_packages', 'country')) {
                $table->dropIndex(['company_id', 'country']);
                $table->dropColumn('country');
            }
        });
    }
};

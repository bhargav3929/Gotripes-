<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_emirates', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_emirates', 'country')) {
                // Existing emirates (Dubai, Abu Dhabi, …) belong to the UAE.
                $table->string('country', 100)->default('United Arab Emirates')->after('emiratesName');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_emirates', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_emirates', 'country')) {
                $table->dropColumn('country');
            }
        });
    }
};

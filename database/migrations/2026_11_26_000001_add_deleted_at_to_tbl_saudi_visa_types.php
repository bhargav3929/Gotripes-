<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Deleting a visa type hides it everywhere; past applications still point at it.
    public function up(): void
    {
        Schema::table('tbl_saudi_visa_types', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('tbl_saudi_visa_types', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};

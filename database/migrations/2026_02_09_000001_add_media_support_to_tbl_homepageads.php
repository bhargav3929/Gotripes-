<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_homepageads', function (Blueprint $table) {
            $table->string('mediaType')->default('image')->after('imgPath');
            $table->integer('slotOrder')->default(0)->after('mediaType');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_homepageads', function (Blueprint $table) {
            $table->dropColumn(['mediaType', 'slotOrder']);
        });
    }
};

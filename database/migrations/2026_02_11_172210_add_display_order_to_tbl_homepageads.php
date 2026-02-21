<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_homepageads', function (Blueprint $table) {
            $table->integer('displayOrder')->default(1)->after('slotOrder');
            $table->integer('duration')->default(5)->after('displayOrder'); // seconds for images; ignored for videos
        });
    }

    public function down(): void
    {
        Schema::table('tbl_homepageads', function (Blueprint $table) {
            $table->dropColumn(['displayOrder', 'duration']);
        });
    }
};

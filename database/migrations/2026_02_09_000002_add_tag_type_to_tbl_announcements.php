<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_announcements', function (Blueprint $table) {
            $table->string('tagType')->default('none')->after('AnnouncementImportance');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_announcements', function (Blueprint $table) {
            $table->dropColumn('tagType');
        });
    }
};

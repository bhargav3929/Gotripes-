<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tblLFJprofiles', function (Blueprint $table) {
            $table->string('LFJLocationStatus')->nullable()->after('LFJStatus');
        });
    }

    public function down(): void
    {
        Schema::table('tblLFJprofiles', function (Blueprint $table) {
            $table->dropColumn('LFJLocationStatus');
        });
    }
};

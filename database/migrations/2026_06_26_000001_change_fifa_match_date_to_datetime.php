<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Widen fifa_matches.match_date from a date-only column to a datetime so an
     * optional kickoff time can be stored alongside the date. All existing
     * values are NULL, so dropping and re-adding the column loses no data.
     */
    public function up(): void
    {
        Schema::table('fifa_matches', function (Blueprint $table) {
            $table->dropColumn('match_date');
        });
        Schema::table('fifa_matches', function (Blueprint $table) {
            $table->dateTime('match_date')->nullable()->after('stage');
        });
    }

    public function down(): void
    {
        Schema::table('fifa_matches', function (Blueprint $table) {
            $table->dropColumn('match_date');
        });
        Schema::table('fifa_matches', function (Blueprint $table) {
            $table->date('match_date')->nullable()->after('stage');
        });
    }
};

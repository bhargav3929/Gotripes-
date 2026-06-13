<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fifa_matches', function (Blueprint $table) {
            $table->id();
            $table->string('match_code', 20)->index();   // e.g. M6
            $table->string('team_a', 120);
            $table->string('team_b', 120);
            $table->string('stage', 60)->default('Group Stage'); // Group Stage / Round of 16 / Semi-final ...
            $table->date('match_date')->nullable();
            $table->string('venue', 160)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fifa_matches');
    }
};

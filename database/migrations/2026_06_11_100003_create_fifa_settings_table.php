<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fifa_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('markup_percent', 6, 2)->default(5);   // global profit margin %
            $table->string('display_currency', 3)->default('USD'); // currency tickets are priced in
            $table->timestamps();
        });

        // Single config row.
        DB::table('fifa_settings')->insert([
            'markup_percent'   => 5,
            'display_currency' => 'USD',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('fifa_settings');
    }
};

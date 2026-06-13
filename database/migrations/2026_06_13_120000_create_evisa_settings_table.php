<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Global e-Visa storefront settings (markup % applied over Fluxir's net fee).
 * Mirrors fifa_settings — a single config row, seeded with a sensible default.
 */
return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('evisa_settings')) {
            Schema::create('evisa_settings', function (Blueprint $table) {
                $table->id();
                $table->decimal('markup_percent', 5, 2)->default(15);
                $table->timestamps();
            });
        }

        if (DB::table('evisa_settings')->count() === 0) {
            DB::table('evisa_settings')->insert([
                'markup_percent' => 15,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('evisa_settings');
    }
};

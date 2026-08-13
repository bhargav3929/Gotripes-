<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-agent e-Visa commission, set by a manager on the new Global e-Visa tab.
 * Nullable so "no commission set" stays distinct from an explicit 0%.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'evisa_commission_percent')) {
                $table->decimal('evisa_commission_percent', 5, 2)->nullable()->after('agent_services');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'evisa_commission_percent')) {
                $table->dropColumn('evisa_commission_percent');
            }
        });
    }
};

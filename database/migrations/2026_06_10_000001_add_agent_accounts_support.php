<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Agent accounts: tenant managers can create `company_agent` users who get a
 * dedicated /agent portal. `agent_services` is a JSON array of service keys
 * (subset of Company::AVAILABLE_FEATURES — tours / activities / esim) chosen
 * by the manager at creation time. Content tables get an `agent_id` so each
 * agent only manages their own listings.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'agent_services')) {
                $table->text('agent_services')->nullable()->after('role');
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('agent_services');
            }
        });

        Schema::table('tbl_travel_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_travel_packages', 'agent_id')) {
                $table->unsignedBigInteger('agent_id')->nullable()->index()->after('company_id');
            }
        });

        Schema::table('tbl_uaeactivities', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_uaeactivities', 'agent_id')) {
                $table->unsignedBigInteger('agent_id')->nullable()->index()->after('company_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'agent_services')) {
                $table->dropColumn('agent_services');
            }
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });

        Schema::table('tbl_travel_packages', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_travel_packages', 'agent_id')) {
                $table->dropColumn('agent_id');
            }
        });

        Schema::table('tbl_uaeactivities', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_uaeactivities', 'agent_id')) {
                $table->dropColumn('agent_id');
            }
        });
    }
};

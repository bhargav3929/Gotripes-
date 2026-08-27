<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Meeting requirement (2026-08-25): agent registration must capture the
 * business name, address, and — for UAE applicants — a specific Emirate
 * (not just a free-text country) so the manager side can filter by location.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agent_applications', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('name');
            $table->string('address', 500)->nullable()->after('country');
            $table->string('emirate', 100)->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('agent_applications', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'address', 'emirate']);
        });
    }
};

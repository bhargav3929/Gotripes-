<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `isActive` is already taken: the manager panel's delete button sets it to 0,
 * and every listing filters on isActive = 1 — so an activity switched off that
 * way disappears from the admin panel too and can never be switched back on.
 *
 * `isVisible` is the separate, reversible storefront switch the client asked
 * for: hide an attraction that is closed for maintenance without wiping the
 * record, then put it back when it reopens.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_UAEActivities', function (Blueprint $table) {
            $table->boolean('isVisible')->default(true)->after('isActive');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_UAEActivities', function (Blueprint $table) {
            $table->dropColumn('isVisible');
        });
    }
};

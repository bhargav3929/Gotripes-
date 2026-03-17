<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds supplier contact fields to activities so that
     * when a customer books, the supplier (tour operator) also
     * receives an email notification.
     */
    public function up(): void
    {
        Schema::table('tbl_UAEActivities', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_UAEActivities', 'supplierName')) {
                $table->string('supplierName', 255)->nullable();
            }
            if (!Schema::hasColumn('tbl_UAEActivities', 'supplierEmail')) {
                $table->string('supplierEmail', 255)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_UAEActivities', function (Blueprint $table) {
            $table->dropColumn(['supplierName', 'supplierEmail']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $tables = [
        'esim_orders',
        'referral_agents',
        'referral_tracking',
        'referral_clicks',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'company_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->unsignedBigInteger('company_id')->nullable()->after('id');
                    $table->index('company_id');
                });
            }
        }

        // Add company_id to any orders table if exists
        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'company_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
                $table->index('company_id');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'company_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('company_id');
                });
            }
        }

        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'company_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }
    }
};

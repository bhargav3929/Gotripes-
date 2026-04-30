<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (!Schema::hasColumn('companies', 'commission_type')) {
                $table->enum('commission_type', ['percentage', 'flat'])->default('percentage')->after('markup_percentage');
            }
            if (!Schema::hasColumn('companies', 'commission_value')) {
                $table->decimal('commission_value', 10, 2)->default(15.00)->after('commission_type');
            }
            if (!Schema::hasColumn('companies', 'pending_commission')) {
                $table->decimal('pending_commission', 15, 2)->default(0)->after('commission_value');
            }
            if (!Schema::hasColumn('companies', 'paid_commission')) {
                $table->decimal('paid_commission', 15, 2)->default(0)->after('pending_commission');
            }
            if (!Schema::hasColumn('companies', 'total_commission')) {
                $table->decimal('total_commission', 15, 2)->default(0)->after('paid_commission');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['commission_type', 'commission_value', 'pending_commission', 'paid_commission', 'total_commission']);
        });
    }
};

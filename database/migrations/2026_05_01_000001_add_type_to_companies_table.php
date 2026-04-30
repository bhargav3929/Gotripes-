<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Tenant type — agency = B2B partner, freelancer = individual reseller, corporate = enterprise
            if (!Schema::hasColumn('companies', 'type')) {
                $table->enum('type', ['agency', 'freelancer', 'corporate'])
                    ->default('agency')
                    ->after('subdomain');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};

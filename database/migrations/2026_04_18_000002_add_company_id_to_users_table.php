<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
            $table->enum('role', [
                'super_admin',      // Platform owner - access to everything
                'company_owner',    // Company owner - full company access
                'company_admin',    // Company admin - manage company
                'company_staff',    // Company staff - limited access
                'customer'          // End customer
            ])->default('customer')->after('email');

            $table->boolean('is_super_admin')->default(false)->after('role');
            $table->timestamp('last_login_at')->nullable();

            $table->foreign('company_id')
                  ->references('id')
                  ->on('companies')
                  ->onDelete('cascade');

            $table->index('company_id');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'role', 'is_super_admin', 'last_login_at']);
        });
    }
};

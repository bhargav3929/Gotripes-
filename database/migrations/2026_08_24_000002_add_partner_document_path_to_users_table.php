<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * UserController::register() and User::$fillable have referenced
 * partner_document_path since partner registration was built, but no
 * migration ever actually added the column — every registration submission
 * has been failing with "Unknown column 'partner_document_path'" as a
 * result. Stores a JSON array of uploaded trade-license document paths,
 * same text-column pattern as agent_services (see
 * 2026_06_10_000001_add_agent_accounts_support.php).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'partner_document_path')) {
                $table->text('partner_document_path')->nullable()->after('email_verified_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'partner_document_path')) {
                $table->dropColumn('partner_document_path');
            }
        });
    }
};

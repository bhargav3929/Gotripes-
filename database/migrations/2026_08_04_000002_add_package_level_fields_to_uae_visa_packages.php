<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Moves the deposit and the notification addresses onto the individual visa
 * package.
 *
 * Until now the Sharjah security deposit, the supplier address and the
 * processing fee were single company-wide settings, so every emirate and every
 * package had to share one supplier and one deposit. The client buys different
 * visa types from different suppliers at different rates, and wants any emirate
 * — not just Sharjah — to be able to carry a deposit.
 *
 * All five columns are nullable on purpose: null means "fall back to the
 * company-level setting", so existing packages keep behaving exactly as they do
 * today and nothing has to be backfilled.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uae_visa_packages', function (Blueprint $table) {
            // Urgent vs Standard processing.
            $table->string('package_type', 50)->nullable()->after('name');
            // Refundable deposit per applicant. Null = use the company setting.
            $table->decimal('security_deposit', 10, 2)->nullable()->after('description');
            // Non-refundable amount held back from the deposit at refund time.
            $table->decimal('deposit_admin_fee', 10, 2)->nullable()->after('security_deposit');
            // Who fulfils this package, and which of our addresses owns it.
            // Both accept a comma-separated list.
            $table->string('supplier_email', 255)->nullable()->after('deposit_admin_fee');
            $table->string('company_email', 255)->nullable()->after('supplier_email');
        });
    }

    public function down(): void
    {
        Schema::table('uae_visa_packages', function (Blueprint $table) {
            $table->dropColumn([
                'package_type',
                'security_deposit',
                'deposit_admin_fee',
                'supplier_email',
                'company_email',
            ]);
        });
    }
};

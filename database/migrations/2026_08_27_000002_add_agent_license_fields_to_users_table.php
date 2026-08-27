<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Once an AgentApplication is approved, its registration/trade-license
 * details need a permanent home on the live `users` row so they can be
 * (a) filtered by managers and (b) monitored for expiry going forward —
 * neither of which is possible while they only exist on the application.
 * Mirrors the equivalent columns already proven out on `b2b_partners`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('name');
            $table->string('country', 100)->nullable()->after('company_name');
            $table->string('address', 500)->nullable()->after('country');
            $table->string('emirate', 100)->nullable()->after('address');

            $table->string('trade_license_number')->nullable()->after('agent_services');
            $table->date('trade_license_expiry_date')->nullable()->after('trade_license_number');
            $table->string('trade_license_document_path')->nullable()->after('trade_license_expiry_date');

            // Expiry lifecycle — mirrors b2b_partners' disabled_at /
            // pending_license_review / expiry_warning_sent_at exactly, so the
            // same auto-disable + self-service-renewal + manager-confirm
            // pattern applies to agents.
            $table->timestamp('disabled_at')->nullable()->after('is_active');
            $table->boolean('pending_license_review')->default(false)->after('disabled_at');
            $table->timestamp('expiry_warning_sent_at')->nullable()->after('pending_license_review');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'country',
                'address',
                'emirate',
                'trade_license_number',
                'trade_license_expiry_date',
                'trade_license_document_path',
                'disabled_at',
                'pending_license_review',
                'expiry_warning_sent_at',
            ]);
        });
    }
};

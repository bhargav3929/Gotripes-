<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dedupe guard for the customer-facing confirmation email, separate from
 * notified_at (which guards the internal business notification) so either
 * one failing to send can be retried without re-sending the other.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fluxir_visa_applications', function (Blueprint $table) {
            $table->timestamp('customer_notified_at')->nullable()->after('notified_at');
        });
    }

    public function down(): void
    {
        Schema::table('fluxir_visa_applications', function (Blueprint $table) {
            $table->dropColumn('customer_notified_at');
        });
    }
};

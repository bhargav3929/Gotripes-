<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('fluxir_visa_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('fluxir_visa_applications', 'notified_at')) {
                // Set when the business-side booking notification has been sent,
                // so a browser refresh of the Stripe success page doesn't re-notify.
                $table->timestamp('notified_at')->nullable()->after('is_paid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fluxir_visa_applications', function (Blueprint $table) {
            if (Schema::hasColumn('fluxir_visa_applications', 'notified_at')) {
                $table->dropColumn('notified_at');
            }
        });
    }
};

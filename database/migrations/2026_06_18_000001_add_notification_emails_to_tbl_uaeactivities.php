<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_UAEActivities', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_UAEActivities', 'notification_emails')) {
                // Comma/newline separated list of inboxes to notify when this
                // activity is booked. Empty => falls back to the company email.
                $table->text('notification_emails')->nullable()->after('supplierEmail');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_UAEActivities', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_UAEActivities', 'notification_emails')) {
                $table->dropColumn('notification_emails');
            }
        });
    }
};

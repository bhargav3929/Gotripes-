<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_travel_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_travel_packages', 'notification_emails')) {
                // Comma/newline separated inboxes notified when a customer sends
                // an enquiry for this package. Empty => falls back to company email.
                $table->text('notification_emails')->nullable()->after('partner_whatsapp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_travel_packages', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_travel_packages', 'notification_emails')) {
                $table->dropColumn('notification_emails');
            }
        });
    }
};

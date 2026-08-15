<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A UAE partner auto-disabled for an expired license can submit a renewed
 * license through a public flow that verifies their email+password without
 * granting full login (canLogin() still returns false while is_active is
 * false). The renewal sits here as "pending review" rather than
 * auto-reactivating, so a manager still confirms the new document before
 * access is restored — the same fraud/legal rationale that disabled them
 * in the first place applies to trusting an unverified re-upload.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('b2b_partners', function (Blueprint $table) {
            if (!Schema::hasColumn('b2b_partners', 'pending_license_review')) {
                $table->boolean('pending_license_review')->default(false)->after('disabled_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('b2b_partners', function (Blueprint $table) {
            if (Schema::hasColumn('b2b_partners', 'pending_license_review')) {
                $table->dropColumn('pending_license_review');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * users.email_verified_at is no longer a real verification timestamp — the
 * app repurposes it to store "<emirates_csv>rseparator<statusCode>rseparator"
 * for partner registrations (see LoginController, AdminController,
 * PartnerAccessMiddleware, UAEActivityAdminController and
 * UserController@register/@getPendingPartners, all of which read/write it as
 * plain text via str_contains()/explode()).
 *
 * The column was never migrated off its original `timestamp` type though, so
 * on MySQL (strict mode is on — config/database.php) any INSERT/UPDATE that
 * writes the emirates string throws "Incorrect datetime value" and the whole
 * request fails. SQLite's dynamic typing silently accepts a string in a
 * "datetime"-declared column, which is why this only surfaces on production
 * MySQL and not in local/SQLite testing.
 */
return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            // SQLite already stores this as opaque text without complaint.
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN email_verified_at VARCHAR(255) NULL");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN email_verified_at TIMESTAMP NULL");
    }
};

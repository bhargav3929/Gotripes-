<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * SQLite does not support ALTER COLUMN, so we rebuild the users table to
 * extend the role CHECK constraint with 'company_agent'.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Only rebuild for SQLite; MySQL/PostgreSQL use a different path.
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR NOT NULL DEFAULT 'customer' CHECK (role IN ('super_admin','company_owner','company_admin','company_staff','customer','company_agent'))");
            return;
        }

        DB::statement('PRAGMA foreign_keys = OFF');
        DB::statement('BEGIN EXCLUSIVE TRANSACTION');

        // Create replacement table with the updated CHECK constraint.
        DB::statement('CREATE TABLE "users_new" (
            "id" integer primary key autoincrement not null,
            "name" varchar not null,
            "email" varchar not null,
            "email_verified_at" datetime,
            "password" varchar not null,
            "remember_token" varchar,
            "created_at" datetime,
            "updated_at" datetime,
            "access_type" varchar not null default \'full\',
            "company_id" integer,
            "role" varchar check ("role" in (\'super_admin\',\'company_owner\',\'company_admin\',\'company_staff\',\'customer\',\'company_agent\')) not null default \'customer\',
            "is_super_admin" tinyint(1) not null default \'0\',
            "last_login_at" datetime,
            "agent_services" text,
            "is_active" tinyint(1) not null default \'1\',
            "phone" varchar
        )');

        DB::statement('INSERT INTO "users_new" SELECT
            id, name, email, email_verified_at, password, remember_token,
            created_at, updated_at, access_type, company_id, role,
            is_super_admin, last_login_at, agent_services, is_active, phone
            FROM "users"');

        DB::statement('DROP TABLE "users"');
        DB::statement('ALTER TABLE "users_new" RENAME TO "users"');
        DB::statement('CREATE INDEX "users_role_index" on "users" ("role")');

        DB::statement('COMMIT');
        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        // Intentionally omitted — downgrading the CHECK constraint would
        // fail if any company_agent rows exist.
    }
};

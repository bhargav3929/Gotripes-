<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Adds the restricted `customer_care` role (support-queue-only manager login).
 *
 * MySQL/PostgreSQL: users.role is already a plain VARCHAR(50) (see
 * 2026_06_10_000003), so there is nothing to change.
 *
 * SQLite: the original CREATE TABLE still carries a CHECK constraint on
 * `role`, and SQLite cannot ALTER a constraint, so the table is rebuilt.
 * Unlike the 2026_06_10 rebuild this one derives the column list and the
 * indexes from the live schema instead of hard-coding them, so every column
 * added since (evisa_commission_percent, the agent-license fields, and any
 * later additions) is carried over automatically.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // Column is VARCHAR(50) without a CHECK constraint on MySQL: no-op.
            return;
        }

        $createSql = (string) DB::table('sqlite_master')
            ->where('type', 'table')
            ->where('name', 'users')
            ->value('sql');

        if ($createSql === '' || str_contains($createSql, "'customer_care'")) {
            return;
        }

        $pattern = '/check\s*\(\s*"role"\s+in\s*\(([^)]*)\)\s*\)/i';
        if (!preg_match($pattern, $createSql)) {
            // No CHECK constraint on role: nothing restricts the new value.
            return;
        }

        $newCreateSql = preg_replace_callback($pattern, function (array $match) {
            return 'check ("role" in (' . $match[1] . ",'customer_care'))";
        }, $createSql, 1);

        // CREATE TABLE "users" (...) -> CREATE TABLE "users_new" (...)
        $newCreateSql = preg_replace(
            '/^\s*CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?("?users"?)/i',
            'CREATE TABLE "users_new"',
            $newCreateSql,
            1
        );

        $columns = array_map(
            fn ($column) => '"' . $column->name . '"',
            DB::select('PRAGMA table_info("users")')
        );
        $columnList = implode(', ', $columns);

        $indexes = DB::table('sqlite_master')
            ->where('type', 'index')
            ->where('tbl_name', 'users')
            ->whereNotNull('sql')
            ->pluck('sql')
            ->all();

        DB::statement('PRAGMA foreign_keys = OFF');
        DB::statement('BEGIN EXCLUSIVE TRANSACTION');

        DB::statement($newCreateSql);
        DB::statement("INSERT INTO \"users_new\" ({$columnList}) SELECT {$columnList} FROM \"users\"");
        DB::statement('DROP TABLE "users"');
        DB::statement('ALTER TABLE "users_new" RENAME TO "users"');

        foreach ($indexes as $indexSql) {
            DB::statement($indexSql);
        }

        DB::statement('COMMIT');
        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        // Intentionally omitted: shrinking the CHECK constraint would fail
        // while any customer_care rows exist.
    }
};

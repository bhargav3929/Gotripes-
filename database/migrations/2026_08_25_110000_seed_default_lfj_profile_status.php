<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * tbl_LFJProfileStatus exists (created directly on the DB, no migration on
 * record) with columns id/status_name/created_at/updated_at, but has zero
 * rows. tblLFJprofiles.LFJProfile_status is a NOT-NULL foreign key into it,
 * defaulting to 1 — so every job-application insert has been failing with a
 * foreign key violation, because there is nothing with id=1 to reference.
 * Seed exactly the one default status row the schema already expects.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('tbl_LFJProfileStatus')->count() === 0) {
            DB::table('tbl_LFJProfileStatus')->insert([
                'id' => 1,
                'status_name' => 'New',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tbl_LFJProfileStatus')->where('id', 1)->where('status_name', 'New')->delete();
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $updates = [
            'M94'  => ['match_date' => '2026-07-06'],
            'M95'  => ['match_date' => '2026-07-07'],
            'M96'  => ['match_date' => '2026-07-07'],
            'M102' => ['match_date' => '2026-07-15'],
        ];

        foreach ($updates as $code => $data) {
            DB::table('fifa_matches')->where('match_code', $code)->update($data);
        }
    }

    public function down(): void
    {
        DB::table('fifa_matches')
            ->whereIn('match_code', ['M94', 'M95', 'M96', 'M102'])
            ->update(['match_date' => null]);
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $updates = [
            'M77' => ['team_a' => 'France',    'team_b' => 'Sweden',             'match_date' => '2026-06-30', 'venue' => 'MetLife Stadium, East Rutherford NJ'],
            'M81' => ['team_a' => 'USA',        'team_b' => 'Bosnia-Herzegovina', 'match_date' => '2026-07-01', 'venue' => "Levi's Stadium, Santa Clara CA"],
            'M83' => ['team_a' => 'Portugal',   'team_b' => 'Croatia',            'match_date' => '2026-07-02', 'venue' => 'BMO Field, Toronto'],
            'M84' => ['team_a' => 'Spain',      'team_b' => 'Austria',            'match_date' => '2026-07-02', 'venue' => 'SoFi Stadium, Inglewood CA'],
        ];

        foreach ($updates as $code => $data) {
            DB::table('fifa_matches')->where('match_code', $code)->update($data);
        }
    }

    public function down(): void
    {
        $reverts = [
            'M77' => ['team_a' => 'Winner 1I', 'team_b' => '3rd C/D/F/G/H', 'match_date' => null, 'venue' => null],
            'M81' => ['team_a' => 'Winner 1D', 'team_b' => '3rd B/E/F/I/J', 'match_date' => null, 'venue' => null],
            'M83' => ['team_a' => '2K',        'team_b' => '2L',             'match_date' => null, 'venue' => null],
            'M84' => ['team_a' => '1H',        'team_b' => '2J',             'match_date' => null, 'venue' => null],
        ];

        foreach ($reverts as $code => $data) {
            DB::table('fifa_matches')->where('match_code', $code)->update($data);
        }
    }
};

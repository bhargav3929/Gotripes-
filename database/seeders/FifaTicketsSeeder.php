<?php

namespace Database\Seeders;

use App\Models\FifaMatch;
use App\Models\FifaTicket;
use Illuminate\Database\Seeder;

/**
 * Seeds FIFA World Cup 2026 matches + ticket inventory from the LEVEL9
 * CONCIERGERIE availability list (updated Jun 2026). Idempotent: matches are
 * matched by match_code; each match's tickets are rebuilt on every run.
 *
 * Prices below are the raw SUPPLIER cost in USD (straight off the PDF). The
 * customer-facing price is supplier × (1 + global markup%) — see FifaSetting.
 *
 * Ticket tuple format: [quantity, category, block, row, supplierPriceUSD]
 */
class FifaTicketsSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->data() as $m) {
            $match = FifaMatch::updateOrCreate(
                ['match_code' => $m['code']],
                [
                    'team_a'     => $m['a'],
                    'team_b'     => $m['b'],
                    'stage'      => $m['stage'],
                    'sort_order' => (int) preg_replace('/\D/', '', $m['code']),
                    'is_active'  => true,
                ]
            );

            $match->tickets()->delete();

            foreach ($m['tickets'] as $i => $t) {
                FifaTicket::create([
                    'match_id'       => $match->id,
                    'quantity'       => $t[0],
                    'category'       => $t[1],
                    'block'          => $t[2],
                    'seat_row'       => $t[3],
                    'supplier_price' => $t[4],
                    'is_active'      => true,
                    'sort_order'     => $i,
                ]);
            }
        }
    }

    private function data(): array
    {
        $G   = 'Group Stage';
        $R32 = 'Round of 32';
        $R16 = 'Round of 16';
        $SF  = 'Semi-final';

        return [
            ['code' => 'M6', 'a' => 'Australia', 'b' => 'Turkey', 'stage' => $G, 'tickets' => [
                [1, 'Cat 1', '219', 'BB', 1027], [1, 'Cat 1', '219', 'G', 1087],
                [4, 'Cat 1', '219', 'KK', 966], [4, 'Cat 2 RV', '411', 'E', 544],
            ]],
            ['code' => 'M10', 'a' => 'Germany', 'b' => 'Curacao', 'stage' => $G, 'tickets' => [
                [1, 'Cat 1', '133', 'Y', 846],
            ]],
            ['code' => 'M14', 'a' => 'Spain', 'b' => 'Cape Verde', 'stage' => $G, 'tickets' => [
                [1, 'Cat 2', '219', '9', 544],
            ]],
            ['code' => 'M15', 'a' => 'Iran', 'b' => 'New Zealand', 'stage' => $G, 'tickets' => [
                [1, 'Cat 4 RV', '553', '22', 242], [1, 'Cat 4 RV', '526', '20', 242],
            ]],
            ['code' => 'M17', 'a' => 'France', 'b' => 'Senegal', 'stage' => $G, 'tickets' => [
                [4, 'Cat 1', '126', '24', 1148],
            ]],
            ['code' => 'M18', 'a' => 'Iraq', 'b' => 'Norway', 'stage' => $G, 'tickets' => [
                [2, 'Cat 2', '239', '3', 544],
            ]],
            ['code' => 'M19', 'a' => 'Argentina', 'b' => 'Algeria', 'stage' => $G, 'tickets' => [
                [1, 'Cat 3', '313', '3', 544],
            ]],
            ['code' => 'M20', 'a' => 'Austria', 'b' => 'Jordan', 'stage' => $G, 'tickets' => [
                [1, 'Cat 3', '410', '10', 332], [2, 'Cat 3', '401', '9', 266], [4, 'Cat 3', '201', '10', 332],
            ]],
            ['code' => 'M21', 'a' => 'Ghana', 'b' => 'Panama', 'stage' => $G, 'tickets' => [
                [4, 'Cat 1', '211', '4', 786], [4, 'Cat 1', '211', '5', 786],
            ]],
            ['code' => 'M22', 'a' => 'England', 'b' => 'Croatia', 'stage' => $G, 'tickets' => [
                [2, 'Cat 2', '437', '26', 907], [1, 'Cat 2', '412', '25', 846],
            ]],
            ['code' => 'M23', 'a' => 'Portugal', 'b' => 'DR Congo', 'stage' => $G, 'tickets' => [
                [1, 'Cat 1', '350', 'P', 1087], [1, 'Cat 2', '507', 'H', 725],
            ]],
            ['code' => 'M25', 'a' => 'Czech Republic', 'b' => 'South Africa', 'stage' => $G, 'tickets' => [
                [1, 'Cat 1', '126C', '13', 846], [2, 'Cat 1', '133', '21', 786],
                [1, 'Cat 3', '304', '1', 393], [4, 'Cat 3', '303', '1', 393],
                [4, 'Cat 3', '304', '2', 363], [2, 'Cat 3', '302', '4', 332],
            ]],
            ['code' => 'M26', 'a' => 'Switzerland', 'b' => 'Bosnia', 'stage' => $G, 'tickets' => [
                [3, 'Cat 2', '314', '9', 514], [1, 'Cat 4', '526', '19', 152],
            ]],
            ['code' => 'M29', 'a' => 'Brazil', 'b' => 'Haiti', 'stage' => $G, 'tickets' => [
                [3, 'Cat 1', '128', '28', 1329],
            ]],
            ['code' => 'M31', 'a' => 'Turkey', 'b' => 'Paraguay', 'stage' => $G, 'tickets' => [
                [4, 'Cat 1', '125', '13', 907],
            ]],
            ['code' => 'M33', 'a' => 'Germany', 'b' => 'Ivory Coast', 'stage' => $G, 'tickets' => [
                [4, 'Cat 2', '222', '19', 786], [5, 'Cat 2', '225', '13', 846],
            ]],
            ['code' => 'M34', 'a' => 'Ecuador', 'b' => 'Curacao', 'stage' => $G, 'tickets' => [
                [4, 'Cat 2', '129', '32', 453],
            ]],
            ['code' => 'M35', 'a' => 'Netherlands', 'b' => 'Sweden', 'stage' => $G, 'tickets' => [
                [1, 'Cat 2', '512', 'H', 665],
            ]],
            ['code' => 'M36', 'a' => 'Tunisia', 'b' => 'Japan', 'stage' => $G, 'tickets' => [
                [2, 'Cat 2', '117', 'F', 514],
            ]],
            ['code' => 'M38', 'a' => 'Spain', 'b' => 'Saudi Arabia', 'stage' => $G, 'tickets' => [
                [3, 'Cat 2', '338', '16', 786],
            ]],
            ['code' => 'M39', 'a' => 'Belgium', 'b' => 'Iran', 'stage' => $G, 'tickets' => [
                [1, 'Cat 3', '523', '17', 332], [1, 'Cat 3', '522', '12', 363], [1, 'Cat 3', '522', '1', 424],
            ]],
            ['code' => 'M40', 'a' => 'New Zealand', 'b' => 'Egypt', 'stage' => $G, 'tickets' => [
                [2, 'Cat 2', '201', '2', 544], [2, 'Cat 2', '252', 'F', 514], [4, 'Cat 2', '202', 'G', 574],
                [4, 'Cat 3', '403', 'UU', 272], [2, 'Cat 3', '425', 'EE', 303],
            ]],
            ['code' => 'M44', 'a' => 'Jordan', 'b' => 'Algeria', 'stage' => $G, 'tickets' => [
                [4, 'Cat 3', '408', '18', 303],
            ]],
            ['code' => 'M47', 'a' => 'Portugal', 'b' => 'Uzbekistan', 'stage' => $G, 'tickets' => [
                [4, 'Cat 2', '514', 'L', 1027],
            ]],
            ['code' => 'M50', 'a' => 'Morocco', 'b' => 'Haiti', 'stage' => $G, 'tickets' => [
                [2, 'Cat 3', '308', '13', 332], [4, 'Cat 3', '307', '21', 332],
            ]],
            ['code' => 'M52', 'a' => 'Bosnia', 'b' => 'Qatar', 'stage' => $G, 'tickets' => [
                [4, 'Cat 3', '301', 'BB', 272], [4, 'Cat 3', '301', 'CC', 272], [4, 'Cat 3', '320', 'Z', 272],
                [4, 'Cat 3', '319', 'X', 303], [1, 'Cat 3', '300', 'L', 242], [2, 'Cat 3', '422', 'UU', 272],
            ]],
            ['code' => 'M54', 'a' => 'South Africa', 'b' => 'South Korea', 'stage' => $G, 'tickets' => [
                [2, 'Cat 2', '116', 'J', 514],
            ]],
            ['code' => 'M55', 'a' => 'Curacao', 'b' => 'Ivory Coast', 'stage' => $G, 'tickets' => [
                [1, 'Cat 4', '239', '28', 212], [3, 'Cat 1', '116', '6', 786], [1, 'Cat 3', '220', '3', 272],
                [2, 'Cat 3', '231', '4', 272], [1, 'Cat 3', '220', '4', 272], [1, 'Cat 3', '230', '3', 272],
            ]],
            ['code' => 'M56', 'a' => 'Ecuador', 'b' => 'Germany', 'stage' => $G, 'tickets' => [
                [2, 'Cat 2', '338', '18', 846],
            ]],
            ['code' => 'M57', 'a' => 'Japan', 'b' => 'Sweden', 'stage' => $G, 'tickets' => [
                [3, 'Cat 1', '120', '6', 1027],
            ]],
            ['code' => 'M58', 'a' => 'Tunisia', 'b' => 'Netherlands', 'stage' => $G, 'tickets' => [
                [1, 'Cat 1', '111', '5', 1027],
            ]],
            ['code' => 'M60', 'a' => 'Paraguay', 'b' => 'Australia', 'stage' => $G, 'tickets' => [
                [1, 'Cat 3', '406', '6', 393], [1, 'Cat 3', '304', '5', 393], [1, 'Cat 3', '411', '24', 303],
                [1, 'Cat 3', '420', '26', 303], [1, 'Cat 3', '402', '6', 363],
            ]],
            ['code' => 'M63', 'a' => 'Egypt', 'b' => 'Iran', 'stage' => $G, 'tickets' => [
                [2, 'Cat 2', '203', 'H', 665], [1, 'Cat 3', '328', 'AA', 483],
                [1, 'Cat 3', '317', 'CC', 483], [4, 'Cat 3', '318', 'Y', 514],
            ]],
            ['code' => 'M64', 'a' => 'New Zealand', 'b' => 'Belgium', 'stage' => $G, 'tickets' => [
                [2, 'Cat 2', '441', 'GG', 604],
            ]],
            ['code' => 'M68', 'a' => 'Croatia', 'b' => 'Ghana', 'stage' => $G, 'tickets' => [
                [3, 'Cat 1', '111', '35/38', 846], [2, 'Cat 2', 'C17', '4', 604],
            ]],
            ['code' => 'M72', 'a' => 'DR Congo', 'b' => 'Uzbekistan', 'stage' => $G, 'tickets' => [
                [1, 'Cat 3', '304', '7', 272], [2, 'Cat 3', '303', '2', 303],
            ]],

            // ---- Knockout stage ----
            ['code' => 'M77', 'a' => 'Winner 1I', 'b' => '3rd C/D/F/G/H', 'stage' => $R32, 'tickets' => [
                [1, 'Cat 1', '146', '25', 1691], [1, 'Cat 1', '146', '22', 1812],
                [1, 'Cat 1', '146', '33', 1631], [2, 'Cat 1', '146', '32', 1631],
            ]],
            ['code' => 'M81', 'a' => 'Winner 1D', 'b' => '3rd B/E/F/I/J', 'stage' => $R32, 'tickets' => [
                [1, 'Cat 2', '201', '4', 1148], [4, 'Cat 2', '201', '5', 1148], [4, 'Cat 2', '201', '6', 1208],
                [4, 'Cat 2', '202', '4', 1148], [4, 'Cat 2', '202', '2', 1269],
            ]],
            ['code' => 'M83', 'a' => '2K', 'b' => '2L', 'stage' => $R32, 'tickets' => [
                [1, 'Cat 3', '217', '1', 846],
            ]],
            ['code' => 'M84', 'a' => '1H', 'b' => '2J', 'stage' => $R32, 'tickets' => [
                [4, 'Cat 1', '121', '12', 1812],
            ]],
            ['code' => 'M94', 'a' => 'Winner 81', 'b' => 'Winner 82', 'stage' => $R16, 'tickets' => [
                [4, 'Cat 1', '127', 'S', 2657], [4, 'Cat 1', '126', 'S', 2657], [4, 'Cat 1', '126', 'S', 2657],
                [1, 'Cat 1', '126', 'Y', 2415], [2, 'Cat 1', '239', 'D', 2174],
            ]],
            ['code' => 'M95', 'a' => 'Winner 86', 'b' => 'Winner 88', 'stage' => $R16, 'tickets' => [
                [1, 'Cat 2', '227', '3', 1510], [1, 'Cat 2', '338', '5', 1510],
            ]],
            ['code' => 'M96', 'a' => 'Winner 85', 'b' => 'Winner 87', 'stage' => $R16, 'tickets' => [
                [1, 'Cat 1', '229', 'E', 2415],
            ]],
            ['code' => 'M102', 'a' => 'Winner 99', 'b' => 'Winner 100', 'stage' => $SF, 'tickets' => [
                [1, 'Cat 3', '307', '19', 4227],
            ]],
        ];
    }
}

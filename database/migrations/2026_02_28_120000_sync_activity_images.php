<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $images = [
            1 => 'assets/activities/1772191653_lKKwjAlhwq.jpeg',
            2 => 'assets/activities/1772191578_UtTHLVZLBi.jpeg',
            3 => 'assets/activities/1772191613_i4fxvXOymG.jpeg',
            4 => 'assets/activities/1772191778_fr7TL3s2PK.jpeg',
            5 => 'assets/activities/1772191806_U1e2MW0qLg.jpeg',
            6 => 'assets/activities/1772191833_UZUX9REwqC.jpeg',
            7 => 'assets/activities/1772191851_NwQKAAUT5P.jpeg',
            8 => 'assets/activities/1772191870_XR48cuUi89.jpeg',
            9 => 'assets/activities/1772192055_2LyK1Grqiy.jpeg',
            10 => 'assets/activities/1772192078_Rjselb0Wox.jpeg',
            11 => 'assets/activities/1772192097_BlJRGvo0v0.jpeg',
            12 => 'assets/activities/1772192122_ArRQf6NRjB.jpeg',
            13 => 'assets/activities/1772192142_dhuEKsWyON.jpeg',
            15 => 'assets/activities/1772192191_tvuAAmb85S.jpeg',
            16 => 'assets/activities/1772192209_e4npB0AFzf.jpeg',
            17 => 'assets/activities/1772171271_jLyJBlczo0.jpeg',
            18 => 'assets/activities/1772194793_IAaENJi91m.jpeg',
            19 => 'assets/activities/1772171471_R5AHQHUppd.jpeg',
            20 => 'assets/activities/1772194824_hFPria1JSr.jpeg',
            21 => 'assets/activities/1772194852_73LGe29ltt.jpeg',
            22 => 'assets/activities/1772171599_SywZRbm7Vn.jpeg',
            23 => 'assets/activities/1772171648_zExwyzUzlt.jpeg',
            24 => 'assets/activities/1772171742_Nadid6RtVJ.jpeg',
            25 => 'assets/activities/1772171855_sIpfOzROPH.jpeg',
            26 => 'assets/activities/1772171939_XIxSg8Y60z.jpeg',
            27 => 'assets/activities/1772172035_7q5F5Dj0Hf.jpeg',
            28 => 'assets/activities/1772172088_KomPNrX4An.jpeg',
            29 => 'assets/activities/1772172139_UIGEftHBI7.jpeg',
            30 => 'assets/activities/1772172223_gXnPxCoS41.jpeg',
            31 => 'assets/activities/1772172324_2sDIiVDPFa.jpeg',
            32 => 'assets/activities/1772172421_tXPGy90D8p.jpeg',
            33 => 'assets/activities/1772172471_IMvXqAaTJ3.jpeg',
            34 => 'assets/activities/1772172600_bTpBq2zhLe.jpeg',
            35 => 'assets/activities/1772172637_9vKkhWJ31d.jpeg',
            36 => 'assets/activities/1772172659_cxEHezn4iJ.jpeg',
            37 => 'assets/activities/1772172775_EbIgjeH3Mw.jpeg',
            38 => 'assets/activities/1772172796_ZUwERRxZ1D.jpeg',
            39 => 'assets/activities/1772172821_MDQflhR8cm.jpeg',
            40 => 'assets/activities/1772172971_w3WXjt3ttZ.jpeg',
            41 => 'assets/activities/1772172993_hxbK8mrHRF.jpeg',
            42 => 'assets/activities/1772173037_ST9GXjuX5z.jpeg',
            43 => 'assets/activities/1772173089_E4acGVopCi.jpeg',
            44 => 'assets/activities/1772173227_vnTlrAzHHD.jpeg',
            45 => 'assets/activities/1772173255_0cHu20L9IK.jpeg',
            46 => 'assets/activities/1772173293_ofBWFBlDVS.jpeg',
            47 => 'assets/activities/1772173443_aQsSWmiN2U.jpeg',
            48 => 'assets/activities/1772173470_90U1qCqH8A.jpeg',
            49 => 'assets/activities/1772173501_coqexUKDbR.jpeg',
            50 => 'assets/activities/1772173566_u3rjBr0CPA.jpeg',
            51 => 'assets/activities/1772173734_gs6U1HowI4.jpeg',
            52 => 'assets/activities/1772173757_DnvjL4Ilft.jpeg',
            53 => 'assets/activities/1772173781_4Xz528RSik.jpeg',
            54 => 'assets/activities/1772173928_y44CKpMjYu.jpeg',
            55 => 'assets/activities/1772173962_nxJ8fks3T0.jpeg',
            56 => 'assets/activities/1772174054_PlP3nJCOzj.jpeg',
            57 => 'assets/activities/1772192488_HsjCeoQ8N2.jpeg',
            58 => 'assets/activities/1772174162_rA4vmoX853.jpeg',
            59 => 'assets/activities/1772174298_k1u6OeiFNa.jpeg',
            60 => 'assets/activities/1772192651_CjDPQqAeTk.jpeg',
            61 => 'assets/activities/1772174355_a3Dbjxs45K.jpeg',
            62 => 'assets/activities/1772174383_UfEbM3qipX.jpeg',
            63 => 'assets/activities/1772174413_YtnJG23U71.jpeg',
            64 => 'assets/activities/1772174693_iKXI7sIWbg.jpeg',
            65 => 'assets/activities/1772174724_JYFN7zOy7X.jpeg',
            66 => 'assets/activities/1772174764_PqPhVVbFNb.jpeg',
            67 => 'assets/activities/1772174802_CVjr6ALv5o.jpeg',
            68 => 'assets/activities/1772174839_jgiOGs2rqu.jpeg',
            69 => 'assets/activities/1772174874_AdBKnGJzRy.jpeg',
            70 => 'assets/activities/1772174912_cxAttCwzEu.jpeg',
            71 => 'assets/activities/1772174943_9lQZ6GHYFx.jpeg',
            72 => 'assets/activities/1772174968_Afz2LXlkJJ.jpeg',
            73 => 'assets/activities/1772174998_TpmjWPEyAN.jpeg',
            74 => 'assets/activities/1772180036_HQ8jOgIUMu.jpeg',
            75 => 'assets/activities/1772180062_t2o3TgRQt5.jpeg',
            76 => 'assets/activities/1772180089_UmeQAfMk3K.jpeg',
            77 => 'assets/activities/1772180287_kLFiyaV1lN.jpeg',
            78 => 'assets/activities/1772180216_43Y3DRjEvq.jpeg',
            79 => 'assets/activities/1772190492_GAp4DNAxcm.jpeg',
            80 => 'assets/activities/1772190677_LzOaIoGN8S.jpeg',
            81 => 'assets/activities/1772190700_y65vUF0NR8.jpeg',
            82 => 'assets/activities/1772190552_4SBVOztZEv.jpeg',
            83 => 'assets/activities/1772190603_2TqKtaLg7l.jpeg',
            84 => 'assets/activities/1772190961_0n8W1688ai.jpeg',
            85 => 'assets/activities/1772190983_0TecMCVYHR.jpeg',
            86 => 'assets/activities/1772191008_DsoZFCwGJk.jpeg',
            87 => 'assets/activities/1772191028_Q30MEgxOzq.jpeg',
            88 => 'assets/activities/1772191119_0OHFj2IPvv.jpeg',
            89 => 'assets/activities/1772191200_8mREsGP7ua.jpeg',
            90 => 'assets/activities/1772191253_0l6m7lGPsv.jpeg',
            91 => 'assets/activities/1772191273_KY2zNwHOY3.jpeg',
            92 => 'assets/activities/1772191292_dUi0iczmFi.jpeg',
            93 => 'assets/activities/1772191315_E445ojlZQS.jpeg',
            94 => 'assets/activities/1772191504_VG60tUqXpF.jpeg',
            95 => 'assets/activities/1772191523_LjUVdzD28Y.jpeg',
            96 => 'assets/activities/1772123021_PkGy8m4DJW.jpeg',
            97 => 'assets/activities/1772192365_z2a88ABdhT.jpeg',
            98 => 'assets/activities/1772192365_z2a88ABdhT.jpeg',
            99 => 'assets/activities/1772194603_Pcg867sbV4.jpeg',
            100 => 'assets/activities/1772194683_Ag5ukGxbwP.jpeg',
            101 => 'assets/activities/1772170274_uzAKbCiM7N.jpeg',
            102 => 'assets/activities/1772180106_mw4uszOEs0.jpeg',
            103 => 'assets/activities/1772170983_fDqUesj8eB.jpeg',
            104 => 'assets/activities/1772171111_X2iBBxkKln.jpeg',
            105 => 'assets/activities/1772194174_DbNSMLqBM7.jpeg',
        ];

        // Update main activities table
        foreach ($images as $activityID => $image) {
            DB::table('tbl_UAEActivities')
                ->where('activityID', $activityID)
                ->update(['activityImage' => $image]);
        }

        // Update details table (same image as card thumbnail)
        foreach ($images as $activityID => $image) {
            DB::table('tbl_UAEActivityDetails')
                ->where('activityID', $activityID)
                ->update(['activityImage' => $image]);
        }

        // Activate these activities (they were activated locally)
        DB::table('tbl_UAEActivities')
            ->whereIn('activityID', [18, 20, 21, 98, 99, 100])
            ->update(['isActive' => 1]);

        // Deactivate duplicate Dubai Garden Glow (ID=14, keep ID=85)
        DB::table('tbl_UAEActivities')
            ->where('activityID', 14)
            ->update(['isActive' => 0]);

        // Ensure detail rows exist for all activities
        $existingDetails = DB::table('tbl_UAEActivityDetails')->pluck('activityID')->toArray();
        foreach ($images as $activityID => $image) {
            if (!in_array($activityID, $existingDetails)) {
                DB::table('tbl_UAEActivityDetails')->insert([
                    'activityID' => $activityID,
                    'activityImage' => $image,
                    'detailsOverview' => '',
                    'detailsIminfo' => '',
                    'detailsHighlights' => '',
                    'isActive' => 1,
                    'createdBy' => 'migration',
                    'createdDate' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // No rollback needed — images are just paths
    }
};

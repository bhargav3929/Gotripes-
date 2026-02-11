<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tbl_emirates')->insert([
            'emiratesName' => 'Western Region',
            'emiratesDescription' => 'Explore the stunning Western Region of Abu Dhabi, home to the vast Liwa Desert, the breathtaking Empty Quarter (Rub al Khali), and hidden oases. A paradise for adventure seekers and nature lovers.',
            'emiratesImage' => null,
            'isActive' => 1,
            'createdBy' => 'system',
            'createdDate' => now(),
            'modifiedBy' => null,
            'modifiedDate' => null,
        ]);
    }

    public function down(): void
    {
        DB::table('tbl_emirates')->where('emiratesName', 'Western Region')->delete();
    }
};

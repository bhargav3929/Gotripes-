<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivityimageToTblUaeactivitydetails extends Migration
{
    public function up()
    {
        Schema::table('tbl_UAEActivityDetails', function (Blueprint $table) {
            $table->string('activityImage')->nullable(); // Make nullable if old rows exist
        });
    }

    public function down()
    {
        Schema::table('tbl_UAEActivityDetails', function (Blueprint $table) {
            $table->dropColumn('activityImage');
        });
    }
}

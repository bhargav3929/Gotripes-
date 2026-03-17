<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('tbl_homepageads', function (Blueprint $table) {
        $table->bigIncrements('id')->startingValue(10000); // ID starts from 10000 [web:43][web:48]
        $table->string('createdBy')->nullable();
        $table->timestamp('createdDate')->useCurrent();
        $table->string('modifiedBy')->nullable();
        $table->timestamp('modifiedDate')->nullable();
        $table->boolean('isActive')->default(true);
        $table->string('imgPath'); // Path to the image file
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_homepageads');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tbl_UAEActivityDetails', function (Blueprint $table) {
            $table->bigIncrements('detailsID');
            $table->text('detailsOverview')->nullable();
            $table->text('detailsIminfo')->nullable();
            $table->text('detailsHighlights')->nullable();
            $table->boolean('isActive')->default(1);
            $table->string('createdBy')->nullable();
            $table->timestamp('createdDate')->useCurrent();
            $table->string('modifiedBy')->nullable();
            $table->timestamp('modifiedDate')->nullable()->useCurrentOnUpdate();
            $table->unsignedBigInteger('activityID');

            // Foreign key constraint
            $table->foreign('activityID')
                ->references('activityID')
                ->on('tbl_uaeactivities')
                ->onDelete('cascade');
        });
        // Set the initial AUTO_INCREMENT value to 10000 (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE tbl_UAEActivityDetails AUTO_INCREMENT = 10000;');
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_uaeactivitydetails');
    }
};

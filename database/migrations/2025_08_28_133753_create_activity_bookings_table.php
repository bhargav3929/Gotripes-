<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::create('activityBookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('isActive')->default(1);
            $table->string('createdBy')->nullable();
            $table->timestamp('createDate')->useCurrent();
            $table->string('modifiedBy')->nullable();
            $table->timestamp('modifiedDate')->nullable()->useCurrentOnUpdate();
            $table->unsignedBigInteger('activityId'); // FK

            // Form fields
            $table->string('name');
            $table->date('date')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('adults')->nullable();
            $table->integer('childrens')->nullable();
            $table->string('paymentOption')->nullable();
            $table->string('transfer')->nullable();
        });

        // Set the initial AUTO_INCREMENT value to 10000 (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE activitybookings AUTO_INCREMENT = 10000;');
        }

        // Add foreign key
        Schema::table('activityBookings', function (Blueprint $table) {
            $table->foreign('activityId')->references('activityID')->on('tbl_uaeactivities')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activityBookings');
    }
};



<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::create('UAEV_application', function (Blueprint $table) {
            $table->id('id');
            $table->string('UAEV_nationality')->nullable();
            $table->string('UAEV_residence')->nullable();
            $table->string('UAEV_first_name');
            $table->string('UAEV_last_name');
            $table->boolean('UAEV_passport_valid')->nullable();
            $table->boolean('UAEV_not_stay_long')->nullable();
            $table->string('UAEV_gender')->nullable();
            $table->date('UAEV_dob')->nullable();
            $table->date('UAEV_arrival_date')->nullable();
            $table->date('UAEV_departure_date')->nullable();
            $table->string('UAEV_phone')->nullable();
            $table->string('UAEV_email');
            $table->string('UAEV_profession')->nullable();
            $table->string('UAEV_marital_status')->nullable();
            $table->string('UAEV_passport_copy')->nullable();
            $table->string('UAEV_passport_photo')->nullable();
            $table->string('UAEV_visaDuration')->nullable();
            $table->string('UAEV_price')->nullable();
            $table->string('UAEV_Created_by');
            $table->timestamp('UAEV_created_date')->useCurrent();
            $table->boolean('UAEV_isActive')->default(1);
            $table->unsignedBigInteger('UAEV_status')->default(1);

            // Add the foreign key constraint
            $table->foreign('UAEV_status')->references('id')->on('tbl_UAEVStatus');
        });

        // Set the initial AUTO_INCREMENT value to 10000 (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE UAEV_application AUTO_INCREMENT = 10000;');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('UAEV_application');
    }
};

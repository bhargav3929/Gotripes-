<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tblLFJprofiles', function (Blueprint $table) {
            $table->bigIncrements('LFJid');
            $table->unsignedInteger('LFJProfile_status')->default(1);

            $table->string('LFJCreatedBy')->nullable();
            $table->timestamp('LFJCreatedDate')->nullable();
            $table->boolean('LFJisActive')->default(true);
            $table->string('LFJModifiedBy')->nullable();
            $table->timestamp('LFJModifiedDate')->nullable();

            $table->string('LFJStatus')->nullable();
            $table->string('LFJName');
            $table->integer('LFJAge')->nullable();
            $table->string('LFJNationality')->nullable();
            $table->string('LFJMobile');
            $table->string('LFJEmail');
            $table->string('LFJProfession')->nullable();
            $table->integer('LFJExperience')->nullable();
            $table->string('LFJVisaStatus')->nullable();
            $table->string('LFJExpectedSalary')->nullable();
            $table->string('LFJLastCompany')->nullable();
            $table->string('LFJLastLocation')->nullable();
            $table->string('LFJPreferredLocation')->nullable();
            $table->string('LFJNoticePeriod')->nullable();
            $table->string('LFJReferenceName')->nullable();
            $table->string('LFJReferencePosition')->nullable();
            $table->string('LFJReferenceMobile')->nullable();
            $table->string('LFJResume')->nullable();
            $table->string('LFJPassport')->nullable();

            $table->timestamps();

            $table->foreign('LFJProfile_status')
                ->references('id')
                ->on('tbl_LFJProfileStatus')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });

        // Set the initial AUTO_INCREMENT value to 10000 (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE tblLFJprofiles AUTO_INCREMENT = 10000;');
        }
    }

    public function down(): void
    {
        Schema::table('tblLFJprofiles', function (Blueprint $table) {
            $table->dropForeign(['LFJProfile_status']);
        });
        Schema::dropIfExists('tblLFJprofiles');
    }
};

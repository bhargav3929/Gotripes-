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
    Schema::create('tbl_UAEActivities', function (Blueprint $table) {
        $table->bigIncrements('activityID')->startingValue(10000); // Starts from 10000        
        $table->boolean('isActive')->default(1);
        $table->string('createdBy')->nullable();
        $table->timestamp('createdDate')->useCurrent();
        $table->string('modifiedBy')->nullable();
        $table->timestamp('modifiedDate')->nullable()->default(null);

        $table->string('activityName');
        $table->string('activityLocation');
        $table->string('activityImage');
        $table->string('activityCurrency')->default('$');
        $table->decimal('activityPrice', 10, 2);
        $table->string('activityRoute'); // e.g., /uaeactivities/scuba-diving

        $table->timestamps(); // (optional) Laravel's created_at/updated_at; you can omit if not used
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl__u_a_e_activities');
    }
};

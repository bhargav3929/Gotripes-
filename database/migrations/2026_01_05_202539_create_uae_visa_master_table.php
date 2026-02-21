<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('uae_visa_master', function (Blueprint $table) {
            $table->id('vID');
            $table->string('UAEVisaDuration')->nullable();
            $table->decimal('UAEVPrice', 10, 2)->nullable(); // Assuming price is decimal
            $table->string('createdBy')->nullable();
            $table->timestamp('createdDate')->useCurrent();
            $table->string('modifiedBy')->nullable();
            $table->timestamp('modifiedDate')->useCurrent()->nullable();
            $table->boolean('isActive')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uae_visa_master');
    }
};

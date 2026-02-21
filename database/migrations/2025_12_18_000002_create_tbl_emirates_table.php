<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tbl_emirates')) {
            Schema::create('tbl_emirates', function (Blueprint $table) {
                $table->id('emiratesID');
                $table->string('emiratesName');
                $table->text('emiratesDescription')->nullable();
                $table->string('emiratesImage')->nullable();
                $table->boolean('isActive')->default(true);
                $table->string('createdBy')->nullable();
                $table->timestamp('createdDate')->nullable();
                $table->string('modifiedBy')->nullable();
                $table->timestamp('modifiedDate')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_emirates');
    }
};

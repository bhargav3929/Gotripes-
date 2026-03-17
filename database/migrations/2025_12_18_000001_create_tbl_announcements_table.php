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
        Schema::create('tbl_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('flagImgPath')->nullable();
            $table->text('description')->nullable();
            $table->string('createdBy')->nullable();
            $table->timestamp('createdDate')->nullable();
            $table->string('modifiedBy')->nullable();
            $table->timestamp('modifiedDate')->nullable();
            $table->boolean('isActive')->default(true);
            $table->integer('AnnouncementImportance')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_announcements');
    }
};

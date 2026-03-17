<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_umrah_packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 10)->default('USD');
            $table->text('description')->nullable();
            $table->string('duration')->nullable();
            $table->string('tag')->nullable();            // e.g. Popular, Best Value, Luxury
            $table->text('features')->nullable();          // JSON array of feature strings
            $table->boolean('isFeatured')->default(false);
            $table->integer('sortOrder')->default(0);
            $table->boolean('isActive')->default(true);
            $table->string('createdBy')->nullable();
            $table->timestamp('createdDate')->nullable();
            $table->string('modifiedBy')->nullable();
            $table->timestamp('modifiedDate')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_umrah_packages');
    }
};

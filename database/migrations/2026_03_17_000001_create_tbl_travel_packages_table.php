<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_travel_packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->string('duration')->nullable();
            $table->boolean('isActive')->default(true);
            $table->string('createdBy')->nullable();
            $table->timestamp('createdDate')->nullable();
            $table->string('modifiedBy')->nullable();
            $table->timestamp('modifiedDate')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_travel_packages');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fifa_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('fifa_matches')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('category', 30);                 // Cat 1, Cat 2 RV, Cat 4 ...
            $table->string('block', 30)->nullable();
            $table->string('seat_row', 30)->nullable();
            $table->decimal('supplier_price', 10, 2);       // per-ticket cost in USD (from supplier)
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fifa_tickets');
    }
};

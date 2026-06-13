<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fifa_ticket_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name', 160);
            $table->string('email', 160);
            $table->string('phone', 40)->nullable();
            $table->string('country', 120)->nullable();
            $table->foreignId('match_id')->nullable()->constrained('fifa_matches')->nullOnDelete();
            $table->foreignId('ticket_id')->nullable()->constrained('fifa_tickets')->nullOnDelete();
            $table->string('match_label', 200)->nullable();  // snapshot, survives ticket deletion
            $table->string('category', 60)->nullable();      // snapshot
            $table->decimal('quoted_price', 10, 2)->nullable(); // customer price shown at request time
            $table->unsignedInteger('quantity')->default(1);
            $table->text('message')->nullable();
            $table->string('status', 30)->default('new')->index(); // new / contacted / closed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fifa_ticket_requests');
    }
};

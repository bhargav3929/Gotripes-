<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('package_enquiries')) {
            return;
        }

        Schema::create('package_enquiries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->unsignedBigInteger('package_id')->nullable()->index();
            $table->string('package_title')->nullable();   // snapshot
            $table->string('country')->nullable();          // snapshot
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('travel_date')->nullable();
            $table->unsignedInteger('travellers')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_enquiries');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tbl_saudi_visa_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreignId('saudi_visa_type_id')->constrained('tbl_saudi_visa_types');
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->string('nationality');
            $table->string('passport_path');
            $table->string('additional_doc_path')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('payment_status')->default('pending');
            $table->string('order_id')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_saudi_visa_applications');
    }
};

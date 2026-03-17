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
        // Check if table exists to prevent errors if manually run multiple times
        if (!Schema::hasTable('agent_bookings')) {
            Schema::create('agent_bookings', function (Blueprint $table) {
                $table->id();
                $table->string('agent_name')->nullable();
                $table->string('client_name');
                $table->string('client_email');
                $table->string('client_phone');
                $table->string('service_type'); // 'Visa', 'World Tour Package'
                $table->decimal('amount', 10, 2);
                $table->string('currency')->default('AED');
                $table->string('payment_status')->default('Pending'); // Pending, Paid, Failed - matching other tables style roughly
                $table->string('order_id')->nullable()->unique(); // To store the ORDAG... ID for reference
                $table->text('details')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_bookings');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets one eSIM order cover a whole tour group.
 *
 * A tour operator moving 20–30 people cannot check out 30 separate times, so an
 * order now carries a quantity and provisions that many eSIMs, each of which is
 * a separate assignment at the provider with its own ICCID and QR. Those live in
 * `esim_order_units`, one row per eSIM, and the customer gets a single email
 * containing all of them.
 *
 * `quantity` defaults to 1, and a quantity-1 order keeps writing the provider's
 * ids to the parent columns exactly as before, so every existing order, manager
 * view and retry path is untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('esim_orders', function (Blueprint $table) {
            $table->unsignedInteger('quantity')->default(1)->after('bundle_name');
            // Price of one eSIM. selling_price stays the order total (unit × qty)
            // so payment, reporting and the Nomod charge need no special-casing.
            $table->decimal('unit_selling_price', 10, 2)->nullable()->after('selling_price');
        });

        Schema::create('esim_order_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('esim_order_id')->constrained('esim_orders')->cascadeOnDelete();
            // 1-based position within the order, used to build the per-unit
            // provider reference (ORDESIM12-1, ORDESIM12-2, …).
            $table->unsignedInteger('unit_number');
            $table->string('monty_order_id')->nullable();
            $table->string('monty_iccid')->nullable();
            // pending | completed | assign_failed | error
            $table->string('reservation_status', 30)->default('pending');
            // Activation details, read back from the provider for the QR email.
            $table->string('smdp_address')->nullable();
            $table->string('matching_id')->nullable();
            $table->text('activation_code')->nullable();
            $table->json('monty_response')->nullable();
            $table->timestamps();

            $table->unique(['esim_order_id', 'unit_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('esim_order_units');

        Schema::table('esim_orders', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'unit_selling_price']);
        });
    }
};

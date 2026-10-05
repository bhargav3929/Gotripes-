<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Records which freelancer sold an eSIM order and how the price splits.
 *
 * unit_selling_price = platform_unit_price + freelancer_margin_unit. Stored on
 * the order so the freelancer's earning never depends on a cookie surviving to
 * the payment callback, and never changes if the margin is edited later.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('esim_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('referral_agent_id')->nullable()->index()->after('company_id');
            $table->decimal('platform_unit_price', 10, 2)->nullable()->after('unit_selling_price');
            $table->decimal('freelancer_margin_unit', 10, 2)->default(0)->after('platform_unit_price');
        });
    }

    public function down(): void
    {
        Schema::table('esim_orders', function (Blueprint $table) {
            $table->dropColumn(['referral_agent_id', 'platform_unit_price', 'freelancer_margin_unit']);
        });
    }
};

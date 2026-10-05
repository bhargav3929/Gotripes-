<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The margin a manager grants a freelancer on every eSIM they sell.
 *
 * Added ON TOP of the platform price (supplier cost + the company's eSIM
 * markup), so a customer arriving through a freelancer's link pays that much
 * more and the freelancer earns exactly the difference. Set by a manager at
 * approval (or later in Admin -> Referrals); freelancers cannot change it.
 * Distinct from commission_type/value, which stays the rate for other services.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referral_agents', function (Blueprint $table) {
            if (!Schema::hasColumn('referral_agents', 'esim_margin_type')) {
                $table->string('esim_margin_type', 12)->default('fixed')->after('commission_value'); // fixed (AED per eSIM) | percentage
            }
            if (!Schema::hasColumn('referral_agents', 'esim_margin_value')) {
                $table->decimal('esim_margin_value', 10, 2)->default(0)->after('esim_margin_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('referral_agents', function (Blueprint $table) {
            $table->dropColumn(['esim_margin_type', 'esim_margin_value']);
        });
    }
};

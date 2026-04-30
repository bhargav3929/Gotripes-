<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referral_agents', function (Blueprint $table) {
            if (!Schema::hasColumn('referral_agents', 'is_freelancer')) {
                $table->boolean('is_freelancer')->default(false)->after('country');
            }
            if (!Schema::hasColumn('referral_agents', 'profile_headline')) {
                $table->string('profile_headline', 160)->nullable()->after('is_freelancer');
            }
            if (!Schema::hasColumn('referral_agents', 'services_offered')) {
                $table->text('services_offered')->nullable()->after('profile_headline');
            }
        });
    }

    public function down(): void
    {
        Schema::table('referral_agents', function (Blueprint $table) {
            foreach (['is_freelancer', 'profile_headline', 'services_offered'] as $col) {
                if (Schema::hasColumn('referral_agents', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

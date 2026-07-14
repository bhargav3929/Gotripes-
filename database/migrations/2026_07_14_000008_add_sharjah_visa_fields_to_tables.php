<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('uae_visa_prices', function (Blueprint $table) {
            $table->string('nationality')->nullable()->after('traveller_type');
        });

        Schema::table('UAEV_application', function (Blueprint $table) {
            $table->string('UAEV_passport_number')->nullable()->after('UAEV_last_name');
            $table->decimal('UAEV_deposit_amount', 10, 2)->default(0.00)->after('UAEV_price');
            $table->decimal('UAEV_refund_amount', 10, 2)->default(0.00)->after('UAEV_deposit_amount');
            $table->string('UAEV_bank_account_holder')->nullable()->after('UAEV_refund_amount');
            $table->string('UAEV_bank_name')->nullable()->after('UAEV_bank_account_holder');
            $table->string('UAEV_bank_account_number')->nullable()->after('UAEV_bank_name');
            $table->string('UAEV_bank_swift_code')->nullable()->after('UAEV_bank_account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uae_visa_prices', function (Blueprint $table) {
            $table->dropColumn(['nationality']);
        });

        Schema::table('UAEV_application', function (Blueprint $table) {
            $table->dropColumn([
                'UAEV_passport_number',
                'UAEV_deposit_amount',
                'UAEV_refund_amount',
                'UAEV_bank_account_holder',
                'UAEV_bank_name',
                'UAEV_bank_account_number',
                'UAEV_bank_swift_code',
            ]);
        });
    }
};

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
        Schema::table('tbl_saudi_visa_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_saudi_visa_applications', 'first_name')) {
                $table->string('first_name')->nullable()->after('full_name');
            }
            if (!Schema::hasColumn('tbl_saudi_visa_applications', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('tbl_saudi_visa_applications', 'passport_number')) {
                $table->string('passport_number')->nullable()->after('nationality');
            }
            if (!Schema::hasColumn('tbl_saudi_visa_applications', 'passport_expiry')) {
                $table->date('passport_expiry')->nullable()->after('passport_number');
            }
            if (!Schema::hasColumn('tbl_saudi_visa_applications', 'dob')) {
                $table->date('dob')->nullable()->after('passport_expiry');
            }
            if (!Schema::hasColumn('tbl_saudi_visa_applications', 'gender')) {
                $table->string('gender')->nullable()->after('dob');
            }
            if (!Schema::hasColumn('tbl_saudi_visa_applications', 'photo_path')) {
                $table->string('photo_path')->nullable()->after('passport_path');
            }
            if (!Schema::hasColumn('tbl_saudi_visa_applications', 'status')) {
                $table->string('status')->default('pending')->after('payment_status');
            }
            if (!Schema::hasColumn('tbl_saudi_visa_applications', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_saudi_visa_applications', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'passport_number',
                'passport_expiry',
                'dob',
                'gender',
                'photo_path',
                'status',
                'internal_notes',
            ]);
        });
    }
};

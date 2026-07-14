<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Add pricing + status fields to Umrah packages ──────────────
        Schema::table('tbl_umrah_packages', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_umrah_packages', 'discount_price')) {
                $table->decimal('discount_price', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('tbl_umrah_packages', 'adult_price')) {
                $table->decimal('adult_price', 10, 2)->nullable()->after('discount_price');
            }
            if (!Schema::hasColumn('tbl_umrah_packages', 'child_price')) {
                $table->decimal('child_price', 10, 2)->nullable()->after('adult_price');
            }
            if (!Schema::hasColumn('tbl_umrah_packages', 'infant_price')) {
                $table->decimal('infant_price', 10, 2)->nullable()->after('child_price');
            }
            if (!Schema::hasColumn('tbl_umrah_packages', 'sub_category')) {
                $table->string('sub_category', 30)->nullable()->after('category');
                // e.g. 'economy','standard','premium','vip' (type = bus/air, tier = sub_category)
            }
            if (!Schema::hasColumn('tbl_umrah_packages', 'status')) {
                $table->string('status', 20)->default('active')->after('isActive');
                // active | disabled | archived
            }
        });

        // ── Add description + required_documents to Saudi visa types ───
        Schema::table('tbl_saudi_visa_types', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_saudi_visa_types', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('tbl_saudi_visa_types', 'required_documents')) {
                $table->text('required_documents')->nullable()->after('description');
                // stored as JSON array
            }
            if (!Schema::hasColumn('tbl_saudi_visa_types', 'processing_days')) {
                $table->unsignedSmallInteger('processing_days')->default(3)->after('required_documents');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_umrah_packages', function (Blueprint $table) {
            $table->dropColumnIfExists(['discount_price', 'adult_price', 'child_price', 'infant_price', 'sub_category', 'status']);
        });
        Schema::table('tbl_saudi_visa_types', function (Blueprint $table) {
            $table->dropColumnIfExists(['description', 'required_documents', 'processing_days']);
        });
    }
};

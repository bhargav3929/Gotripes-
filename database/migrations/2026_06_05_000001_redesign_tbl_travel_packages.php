<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_travel_packages', function (Blueprint $table) {
            // Enquire (custom, contact partner) vs Purchase (ready-made, checkout).
            if (!Schema::hasColumn('tbl_travel_packages', 'package_type')) {
                $table->string('package_type', 20)->default('enquire')->after('country');
            }
            // Per-country partner contacts shown / used on the package page.
            if (!Schema::hasColumn('tbl_travel_packages', 'partner_email')) {
                $table->string('partner_email')->nullable()->after('package_type');
            }
            if (!Schema::hasColumn('tbl_travel_packages', 'partner_whatsapp')) {
                $table->string('partner_whatsapp', 30)->nullable()->after('partner_email');
            }
            // Per-person pricing for the dynamic price calculator. `price` stays
            // as the "from" price shown on cards (kept for backward compatibility).
            if (!Schema::hasColumn('tbl_travel_packages', 'price_adult')) {
                $table->decimal('price_adult', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('tbl_travel_packages', 'price_child')) {
                $table->decimal('price_child', 10, 2)->nullable()->after('price_adult');
            }
            if (!Schema::hasColumn('tbl_travel_packages', 'price_infant')) {
                $table->decimal('price_infant', 10, 2)->nullable()->after('price_child');
            }
        });

        // Multiple images per package (5+). Keeps the existing `image` column on
        // tbl_travel_packages as the primary/cover image for backward compatibility.
        if (!Schema::hasTable('tbl_travel_package_images')) {
            Schema::create('tbl_travel_package_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('package_id');
                $table->string('image_path');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index('package_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_travel_package_images');

        Schema::table('tbl_travel_packages', function (Blueprint $table) {
            foreach ([
                'package_type', 'partner_email', 'partner_whatsapp',
                'price_adult', 'price_child', 'price_infant',
            ] as $col) {
                if (Schema::hasColumn('tbl_travel_packages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

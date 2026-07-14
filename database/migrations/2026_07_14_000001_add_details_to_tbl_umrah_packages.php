<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_umrah_packages', function (Blueprint $table) {
            $table->string('category')->default('economy')->after('title');
            $table->text('transport')->nullable()->after('duration');
            $table->text('hotels')->nullable()->after('transport');
            $table->text('inclusions')->nullable()->after('hotels'); // JSON array of inclusion strings
            $table->text('exclusions')->nullable()->after('inclusions'); // JSON array of exclusion strings
            $table->text('itinerary')->nullable()->after('exclusions'); // JSON array of day-by-day itinerary
            $table->text('gallery_images')->nullable()->after('image'); // JSON array of image URLs
        });
    }

    public function down(): void
    {
        Schema::table('tbl_umrah_packages', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'transport',
                'hotels',
                'inclusions',
                'exclusions',
                'itinerary',
                'gallery_images',
            ]);
        });
    }
};

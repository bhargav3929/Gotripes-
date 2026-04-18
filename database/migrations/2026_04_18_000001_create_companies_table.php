<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug', 100)->unique();
            $table->string('domain')->nullable()->unique();
            $table->string('subdomain')->nullable()->unique();

            // Branding
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('primary_color', 7)->default('#FFD700');
            $table->string('secondary_color', 7)->default('#FFA500');
            $table->string('text_color', 7)->default('#FFFFFF');
            $table->string('bg_color', 7)->default('#16161a');

            // Contact Info
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();

            // Business Details
            $table->string('business_name')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('currency', 3)->default('AED');
            $table->string('timezone')->default('Asia/Dubai');

            // API Keys (for eSIM providers)
            $table->text('api_keys')->nullable(); // JSON encrypted
            $table->decimal('markup_percentage', 5, 2)->default(20.00);

            // Subscription
            $table->enum('plan', ['trial', 'basic', 'pro', 'enterprise'])->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('subscription_ends_at')->nullable();
            $table->boolean('is_active')->default(true);

            // Settings (JSON for flexible config)
            $table->json('settings')->nullable();
            $table->json('features')->nullable(); // enabled features

            // Stats
            $table->unsignedBigInteger('total_orders')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};

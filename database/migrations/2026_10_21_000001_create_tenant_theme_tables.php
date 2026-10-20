<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Developer themes: a tenant's own GitHub repo of static files (CSS, JS,
 * images) that is published to their storefront and dashboard on push.
 * Everything is switched off until a super admin turns it on.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('theme_deployments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('source', 20);              // github | upload | rollback | wipe
            $table->string('ref')->nullable();          // branch the commit came from
            $table->string('commit_sha', 64)->nullable();
            $table->string('status', 20);               // success | failed
            $table->text('message')->nullable();
            $table->json('manifest')->nullable();       // validated theme.json
            $table->json('files')->nullable();          // published file list
            $table->unsignedInteger('file_count')->default(0);
            $table->unsignedBigInteger('total_bytes')->default(0);
            $table->string('directory')->nullable();    // relative to the public themes dir; null once pruned
            $table->string('triggered_by')->nullable();
            $table->timestamps();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('theme_enabled')->default(false);
            $table->string('theme_repo')->nullable();
            $table->string('theme_branch', 100)->default('main');
            $table->text('theme_webhook_secret')->nullable();
            $table->unsignedBigInteger('theme_deployment_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['theme_enabled', 'theme_repo', 'theme_branch', 'theme_webhook_secret', 'theme_deployment_id']);
        });
        Schema::dropIfExists('theme_deployments');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('super_admin_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('actor_user_id')->nullable()->index();   // SA who performed the action
            $table->string('action', 80)->index();                              // e.g. 'company.toggle_status'
            $table->string('target_type', 80)->nullable();                      // e.g. 'App\Models\Company'
            $table->unsignedBigInteger('target_id')->nullable();
            $table->string('target_label')->nullable();                         // human-readable: company name, etc.
            $table->json('changes')->nullable();                                // before/after diff or arbitrary payload
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['target_type', 'target_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('super_admin_audit_logs');
    }
};

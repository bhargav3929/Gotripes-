<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Prospective agents self-register with the services they're interested in
 * (Tours, Activities, eSIM, Global e-Visa, UAE Visa) and a trade license, and
 * wait for manager approval — mirroring the b2b_partners flow. Kept as its
 * own table rather than pending-state columns on `users`, so an unapproved
 * applicant never has a live, loginable User row; approval is what
 * provisions the real `company_agent` User account (see
 * ManagerAgentApplicationsController::approve()).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone');
            $table->string('country', 100)->nullable();

            // Which services (Company::AVAILABLE_FEATURES-aligned keys) the
            // applicant is interested in — copied onto the User's
            // agent_services column verbatim on approval.
            $table->json('services');

            $table->string('trade_license_number')->nullable();
            $table->date('trade_license_expiry_date')->nullable();
            $table->string('trade_license_document_path')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            // Set once approve() provisions the real User account, so the
            // application keeps a permanent link to it without ever being
            // itself authenticatable.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_applications');
    }
};

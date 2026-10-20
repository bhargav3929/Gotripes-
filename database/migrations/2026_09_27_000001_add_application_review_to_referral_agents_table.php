<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets the freelancer sign-up (`/freelancer/register`) gate on a signed
 * contract and manager review, the same way `agent_applications` already
 * does (see 2026_09_22_000002). `referral_agents` is shared with the older,
 * ungated Referral Agent sign-up (`ReferralAgentSignupController`), so this
 * deliberately does NOT touch the existing `status` enum (active/inactive/
 * suspended) — that flow keeps creating rows with `status = 'active'`
 * exactly as before.
 *
 * Instead, a separate `application_status` column tracks the freelancer
 * review workflow: null for every pre-existing/general referral agent row,
 * and 'pending_review' / 'approved' / 'rejected' only for rows created via
 * the freelancer flow. `FreelancerSignupController` also parks new
 * applicants on `status = 'inactive'` (an enum value that already exists)
 * until approved, since `ReferralAgentDashboardController::login()` already
 * refuses any non-'active' account — no change needed there.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referral_agents', function (Blueprint $table) {
            $table->string('application_status')->nullable()->after('status');
            $table->foreignId('contract_document_id')->nullable()->after('application_status')
                ->constrained('contract_documents')->nullOnDelete();
            $table->string('signature_full_name')->nullable()->after('contract_document_id');
            $table->boolean('signature_agreed')->default(false)->after('signature_full_name');
            $table->string('signature_ip', 45)->nullable()->after('signature_agreed');
            $table->timestamp('signed_at')->nullable()->after('signature_ip');
            $table->string('signed_pdf_path')->nullable()->after('signed_at');
            $table->text('rejection_reason')->nullable()->after('signed_pdf_path');
            $table->foreignId('reviewed_by')->nullable()->after('rejection_reason')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::table('referral_agents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropConstrainedForeignId('contract_document_id');
            $table->dropColumn([
                'application_status', 'signature_full_name', 'signature_agreed',
                'signature_ip', 'signed_at', 'signed_pdf_path', 'rejection_reason',
                'reviewed_at',
            ]);
        });
    }
};

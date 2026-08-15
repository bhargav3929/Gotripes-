<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * B2B agency partners: self-register on the public site, upload a trade
 * license (UAE only), e-sign an auto-generated contract (national if UAE,
 * international otherwise), and wait for manager approval before they can
 * log in. UAE partners' license expiry is tracked separately (see the
 * partners:check-license-expiry command) so an expired license blocks login
 * without losing the fact that the account was once approved.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('b2b_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            // Core identity
            $table->string('company_name');
            $table->string('contact_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone');
            // A fixed known value from a <select>, not free text — both the
            // contract_type derivation and the UAE-conditional registration
            // JS rely on an exact string match against B2bPartner::UAE_COUNTRY_NAME.
            $table->string('country', 100);

            // UAE-only trade-license fields — nullable, only populated when
            // country === UAE.
            $table->string('trade_license_number')->nullable();
            $table->date('trade_license_expiry_date')->nullable();
            $table->string('trade_license_document_path')->nullable();

            // Stored explicitly at registration time rather than re-derived
            // from `country` on read, so a later admin edit to `country` can
            // never retroactively disagree with which contract was actually
            // generated and signed.
            $table->enum('contract_type', ['national', 'international']);
            $table->string('contract_pdf_path')->nullable();

            // In-house e-signature capture (v1 — no third-party vendor).
            $table->string('signature_full_name')->nullable();
            $table->boolean('signature_agreed')->default(false);
            $table->string('signature_ip', 45)->nullable();
            $table->timestamp('signed_at')->nullable();

            // Single enum, not separate booleans — pending/approved/rejected
            // are inherently mutually exclusive.
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();

            // Kept independent of `status` on purpose: an approved partner
            // disabled for an expired license shouldn't lose their approval
            // history, and re-enabling on renewal shouldn't require
            // re-running the whole approval workflow.
            $table->boolean('is_active')->default(true);
            $table->timestamp('disabled_at')->nullable();

            // Idempotency for the scheduled expiry-check command, mirroring
            // the notified_at pattern already used by evisa:reconcile.
            $table->timestamp('expiry_warning_sent_at')->nullable();

            // Reserved for a future commission model — unused this phase.
            // Distinct from the unrelated users.evisa_commission_percent
            // column shipped separately.
            $table->decimal('commission_percent', 5, 2)->nullable();

            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'status']);
            $table->index(['country', 'trade_license_expiry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('b2b_partners');
    }
};

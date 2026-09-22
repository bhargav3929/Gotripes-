<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Where an agent's signature of the B2B contract is recorded.
 *
 * The signature is taken at registration, on `agent_applications`, and copied
 * onto the `users` row when a manager approves — the application is the
 * evidence, the user row is what the portal checks. Mirrors the columns
 * b2b_partners has carried since 2026_08_14_000001 so both flows read alike.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agent_applications', function (Blueprint $table) {
            $table->foreignId('contract_document_id')->nullable()->after('services')
                ->constrained('contract_documents')->nullOnDelete();
            $table->string('signature_full_name')->nullable()->after('contract_document_id');
            $table->boolean('signature_agreed')->default(false)->after('signature_full_name');
            $table->string('signature_ip', 45)->nullable()->after('signature_agreed');
            $table->timestamp('signed_at')->nullable()->after('signature_ip');
            $table->string('signed_pdf_path')->nullable()->after('signed_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('contract_document_id')->nullable()
                ->constrained('contract_documents')->nullOnDelete();
            $table->timestamp('contract_signed_at')->nullable();
            $table->string('contract_signed_pdf_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('agent_applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contract_document_id');
            $table->dropColumn([
                'signature_full_name', 'signature_agreed', 'signature_ip',
                'signed_at', 'signed_pdf_path',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contract_document_id');
            $table->dropColumn(['contract_signed_at', 'contract_signed_pdf_path']);
        });
    }
};

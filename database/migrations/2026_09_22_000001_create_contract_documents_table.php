<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The contracts a manager loads into the portal for applicants to sign, as
 * asked for on 22 Sep 2026 ("if contract is ready we can load it in portal for
 * auto sign and start giving access to agents internally").
 *
 * Versioned rather than edited in place: an applicant who signed version 2
 * must still be provably bound to the text of version 2 after version 3 is
 * published, so rows are immutable once signed and only `is_current` moves.
 * `body` holds pasted text; `pdf_path` holds an uploaded file. Exactly one of
 * the two is set, which `source` records.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            // Which sign-up the contract belongs to. Only 'agent' is used
            // today; b2b/freelancer can reuse the table without a migration.
            $table->string('audience', 40)->default('agent');

            $table->string('title');
            $table->unsignedInteger('version')->default(1);
            $table->string('source', 20)->default('text');   // text | upload
            $table->longText('body')->nullable();            // source = text
            $table->string('pdf_path')->nullable();          // source = upload
            $table->string('file_name')->nullable();
            // SHA-256 of the exact bytes signed, so a stored signature can be
            // checked against the file it refers to.
            $table->string('content_hash', 64)->nullable();

            $table->boolean('is_current')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'audience', 'is_current']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_documents');
    }
};

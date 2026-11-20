<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Supplier rate forms: staff give a ground supplier an access code, the
 * supplier fills in hotels, room prices, tour prices, extras, itinerary and
 * photos at /suppliers, and staff apply it to a tour package in one click.
 * The same code can be used again later to send updated prices.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_invites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('code', 16)->unique(); // stored without dashes, upper case
            $table->string('supplier_name', 190);
            $table->string('country', 100)->nullable();
            $table->unsignedBigInteger('package_id')->nullable(); // the package their rates go into, when known
            $table->date('expires_on')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->string('created_by', 190)->nullable();
            $table->timestamps();
        });

        Schema::create('supplier_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('supplier_invite_id')->index();
            $table->string('status', 20)->default('new'); // new, applied
            $table->longText('data');
            $table->text('photos')->nullable(); // [{path, name}] under storage/app/supplier-uploads
            $table->unsignedBigInteger('applied_package_id')->nullable();
            $table->timestamp('applied_at')->nullable();
            $table->string('applied_by', 190)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_submissions');
        Schema::dropIfExists('supplier_invites');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->string('ticket_number', 32)->unique();
                $table->string('customer_name');
                $table->string('customer_email')->index();
                $table->string('customer_phone', 40);
                $table->string('subject');
                $table->string('status', 30)->default('new')->index();
                $table->string('priority', 20)->default('normal')->index();
                $table->unsignedBigInteger('assigned_to')->nullable()->index();
                $table->text('source_url')->nullable();
                $table->timestamp('first_response_due_at')->nullable()->index();
                $table->timestamp('first_response_at')->nullable();
                $table->timestamp('last_customer_message_at')->nullable();
                $table->timestamp('last_staff_response_at')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->string('email_delivery_status', 30)->default('pending');
                $table->text('email_delivery_error')->nullable();
                $table->string('whatsapp_delivery_status', 30)->default('not_configured');
                $table->text('whatsapp_delivery_error')->nullable();
                $table->timestamps();

                $table->index(['company_id', 'status', 'created_at'], 'support_tickets_queue_idx');
            });
        }

        if (!Schema::hasTable('support_ticket_messages')) {
            Schema::create('support_ticket_messages', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('support_ticket_id')->index();
                $table->unsignedBigInteger('company_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('sender_type', 20); // customer | staff | system
                $table->string('sender_name')->nullable();
                $table->string('sender_email')->nullable();
                $table->string('visibility', 20)->default('public');
                $table->text('message');
                $table->timestamps();

                $table->foreign('support_ticket_id')
                    ->references('id')->on('support_tickets')->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_messages');
        Schema::dropIfExists('support_tickets');
    }
};

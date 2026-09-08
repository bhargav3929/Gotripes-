<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    use BelongsToCompany;

    public const STATUSES = [
        'new' => 'New',
        'open' => 'Open',
        'waiting_customer' => 'Waiting for customer',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ];

    public const PRIORITIES = [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    protected $fillable = [
        'company_id', 'ticket_number', 'customer_name', 'customer_email',
        'customer_phone', 'subject', 'status', 'priority', 'assigned_to',
        'source_url', 'first_response_due_at', 'first_response_at',
        'last_customer_message_at', 'last_staff_response_at', 'resolved_at',
        'email_delivery_status', 'email_delivery_error',
        'whatsapp_delivery_status', 'whatsapp_delivery_error',
    ];

    protected $casts = [
        'first_response_due_at' => 'datetime',
        'first_response_at' => 'datetime',
        'last_customer_message_at' => 'datetime',
        'last_staff_response_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $ticket) {
            if ($ticket->ticket_number) {
                return;
            }

            do {
                $number = 'GT-' . now()->format('ymd') . '-' . Str::upper(Str::random(6));
            } while (self::withoutCompanyScope()->where('ticket_number', $number)->exists());

            $ticket->ticket_number = $number;
        });
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportTicketMessage::class)->oldest();
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function isAwaitingFirstResponse(): bool
    {
        return $this->first_response_at === null && !in_array($this->status, ['resolved', 'closed'], true);
    }

    public function isOverdue(): bool
    {
        return $this->isAwaitingFirstResponse()
            && $this->first_response_due_at
            && $this->first_response_due_at->isPast();
    }

    public function firstResponseMinutes(): ?int
    {
        return $this->first_response_at
            ? $this->created_at->diffInMinutes($this->first_response_at)
            : null;
    }
}

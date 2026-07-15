<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FifaTicketRequest extends Model
{
    protected $table = 'fifa_ticket_requests';

    protected $fillable = [
        'name', 'email', 'phone', 'country',
        'match_id', 'ticket_id', 'match_label', 'category',
        'quoted_price', 'quantity', 'message', 'status',
        'company_id', 'order_id', 'unit_price', 'amount', 'currency',
        'payment_status', 'paid_at', 'notified_at',
    ];

    protected $casts = [
        'quoted_price' => 'decimal:2',
        'unit_price'   => 'decimal:2',
        'amount'       => 'decimal:2',
        'paid_at'      => 'datetime',
        'notified_at'  => 'datetime',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(FifaMatch::class, 'match_id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(FifaTicket::class, 'ticket_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}

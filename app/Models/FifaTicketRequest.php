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
    ];

    protected $casts = [
        'quoted_price' => 'decimal:2',
    ];

    public function match(): BelongsTo
    {
        return $this->belongsTo(FifaMatch::class, 'match_id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(FifaTicket::class, 'ticket_id');
    }
}

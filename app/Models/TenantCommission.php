<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantCommission extends Model
{
    protected $fillable = [
        'company_id',
        'source_type',
        'source_id',
        'gross_amount',
        'commission_type',
        'commission_rate',
        'commission_amount',
        'currency',
        'status',
        'available_at',
        'withdrawal_id',
    ];

    protected $casts = [
        'gross_amount'      => 'decimal:2',
        'commission_rate'   => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'available_at'      => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function withdrawal(): BelongsTo
    {
        return $this->belongsTo(TenantWithdrawal::class);
    }

    public function scopePending($q)        { return $q->where('status', 'pending'); }
    public function scopeAvailable($q)      { return $q->where('status', 'available'); }
    public function scopePaid($q)           { return $q->where('status', 'paid'); }
}

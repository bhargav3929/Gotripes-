<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TenantWithdrawal extends Model
{
    protected $fillable = [
        'company_id',
        'bank_account_id',
        'amount',
        'currency',
        'status',
        'notes',
        'admin_notes',
        'payment_reference',
        'processed_at',
        'processed_by',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(TenantBankAccount::class, 'bank_account_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(TenantCommission::class, 'withdrawal_id');
    }

    public function scopePending($q)  { return $q->where('status', 'pending'); }
    public function scopeApproved($q) { return $q->where('status', 'approved'); }
    public function scopePaid($q)     { return $q->where('status', 'paid'); }
}

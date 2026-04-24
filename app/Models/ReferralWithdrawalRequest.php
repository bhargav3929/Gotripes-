<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralWithdrawalRequest extends Model
{
    use HasFactory;

    protected $table = 'referral_withdrawal_requests';

    protected $fillable = [
        'referral_agent_id',
        'referral_bank_account_id',
        'bank_snapshot',
        'amount',
        'currency',
        'status',
        'agent_notes',
        'admin_notes',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'bank_snapshot' => 'array',
        'processed_at' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────────

    public function referralAgent()
    {
        return $this->belongsTo(ReferralAgent::class);
    }

    public function referralBankAccount()
    {
        return $this->belongsTo(ReferralBankAccount::class)->withDefault();
    }

    // ─── Scopes ──────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // ─── Accessors ───────────────────────────────────────────────────

    /**
     * Bootstrap colour class for the request's current status badge.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'warning',
            'processing' => 'info',
            'completed'  => 'success',
            'rejected'   => 'danger',
            default      => 'secondary',
        };
    }
}

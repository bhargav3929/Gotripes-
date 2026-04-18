<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralTracking extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'referral_tracking';

    protected $fillable = [
        'referral_agent_id',
        'order_id',
        'order_type',
        'customer_email',
        'customer_name',
        'order_amount',
        'currency',
        'commission_amount',
        'commission_type',
        'commission_value',
        'status',
        'payment_status',
        'is_refunded',
        'refund_amount',
        'refunded_at',
        'rejection_reason',
        'approved_at',
        'paid_at',
        'approved_by',
        'order_details',
        'ip_address',
        'user_agent',
        'company_id',
    ];

    protected $casts = [
        'order_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'is_refunded' => 'boolean',
        'order_details' => 'array',
        'refunded_at' => 'datetime',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Relationship with referral agent
     */
    public function referralAgent()
    {
        return $this->belongsTo(ReferralAgent::class);
    }

    /**
     * Relationship with approving user
     */
    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Approve the commission
     */
    public function approve(int $userId): bool
    {
        $this->status = 'approved';
        $this->approved_at = now();
        $this->approved_by = $userId;
        $result = $this->save();

        if ($result) {
            $this->referralAgent->updateStats();
        }

        return $result;
    }

    /**
     * Reject the commission
     */
    public function reject(string $reason, int $userId): bool
    {
        $this->status = 'rejected';
        $this->rejection_reason = $reason;
        $this->approved_by = $userId;
        $result = $this->save();

        if ($result) {
            $this->referralAgent->updateStats();
        }

        return $result;
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(): bool
    {
        $this->status = 'paid';
        $this->paid_at = now();
        $result = $this->save();

        if ($result) {
            $this->referralAgent->updateStats();
        }

        return $result;
    }

    /**
     * Process refund
     */
    public function processRefund(float $refundAmount = null): bool
    {
        $this->is_refunded = true;
        $this->refund_amount = $refundAmount ?? $this->order_amount;
        $this->refunded_at = now();
        $this->payment_status = 'refunded';

        // Cancel or reverse commission
        if ($this->status === 'paid') {
            // If already paid, needs to be reversed
            $this->status = 'cancelled';
        } else {
            // If not paid yet, just cancel
            $this->status = 'cancelled';
        }

        $result = $this->save();

        if ($result) {
            $this->referralAgent->updateStats();
        }

        return $result;
    }

    /**
     * Scope for pending commissions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved commissions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for paid commissions
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'info',
            'paid' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
            default => 'secondary',
        };
    }

    /**
     * Get payment status badge color
     */
    public function getPaymentStatusBadgeAttribute(): string
    {
        return match ($this->payment_status) {
            'pending' => 'warning',
            'completed' => 'success',
            'refunded' => 'danger',
            'partially_refunded' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Check if commission can be approved
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pending' &&
               $this->payment_status === 'completed' &&
               !$this->is_refunded;
    }

    /**
     * Check if commission can be marked as paid
     */
    public function canBeMarkedAsPaid(): bool
    {
        return $this->status === 'approved';
    }
}

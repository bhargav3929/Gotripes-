<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class ReferralAgent extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, BelongsToCompany;

    protected $table = 'referral_agents';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'country',
        'is_freelancer',
        'profile_headline',
        'services_offered',
        'referral_code',
        'commission_type',
        'commission_value',
        'esim_margin_type',
        'esim_margin_value',
        'status',
        'application_status',
        'contract_document_id',
        'signature_full_name',
        'signature_agreed',
        'signature_ip',
        'signed_at',
        'signed_pdf_path',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'total_earnings',
        'pending_earnings',
        'paid_earnings',
        'total_sales',
        'total_clicks',
        'notes',
        'last_login_at',
        'company_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'commission_value' => 'decimal:2',
        'esim_margin_value' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'pending_earnings' => 'decimal:2',
        'paid_earnings' => 'decimal:2',
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'signature_agreed' => 'boolean',
        'signed_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(ContractDocument::class, 'contract_document_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function hasSignedContract(): bool
    {
        return $this->signature_agreed && $this->signed_at !== null;
    }

    /**
     * Only meaningful for the freelancer flow — the older, ungated Referral
     * Agent sign-up leaves `application_status` null and is never "pending".
     */
    public function isPendingApplication(): bool
    {
        return $this->application_status === 'pending_review';
    }

    public function isApprovedApplication(): bool
    {
        return $this->application_status === 'approved';
    }

    public function isRejectedApplication(): bool
    {
        return $this->application_status === 'rejected';
    }

    /** Activate a pending freelancer application: it can log in from here on. */
    public function approveApplication(User $reviewer): void
    {
        $this->update([
            'status'            => 'active',
            'application_status' => 'approved',
            'reviewed_by'       => $reviewer->id,
            'reviewed_at'       => now(),
            'rejection_reason'  => null,
        ]);
    }

    public function rejectApplication(User $reviewer, string $reason): void
    {
        $this->update([
            'application_status' => 'rejected',
            'reviewed_by'       => $reviewer->id,
            'reviewed_at'       => now(),
            'rejection_reason'  => $reason,
        ]);
    }

    /**
     * Boot function to generate referral code
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($agent) {
            if (empty($agent->referral_code)) {
                $agent->referral_code = self::generateUniqueCode();
            }
        });
    }

    /**
     * Generate a unique referral code
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = strtolower(Str::random(8));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Generate referral code from name
     */
    public static function generateCodeFromName(string $name): string
    {
        $baseCode = Str::slug($name, '');
        $baseCode = substr($baseCode, 0, 10);

        $code = $baseCode;
        $counter = 1;

        while (self::where('referral_code', $code)->exists()) {
            $code = $baseCode . $counter;
            $counter++;
        }

        return strtolower($code);
    }

    /**
     * Relationship with referral tracking
     */
    public function referralTracking()
    {
        return $this->hasMany(ReferralTracking::class);
    }

    /**
     * Relationship with clicks
     */
    public function clicks()
    {
        return $this->hasMany(ReferralClick::class);
    }

    /**
     * Relationship with bank accounts
     */
    public function bankAccounts()
    {
        return $this->hasMany(ReferralBankAccount::class);
    }

    /**
     * Relationship with withdrawal requests
     */
    public function withdrawalRequests()
    {
        return $this->hasMany(ReferralWithdrawalRequest::class);
    }

    /**
     * Get the primary bank account for this agent.
     */
    public function primaryBankAccount(): ?ReferralBankAccount
    {
        return $this->bankAccounts()->where('is_primary', true)->first();
    }

    /**
     * Calculate the agent's available balance (approved commissions minus completed/pending withdrawals).
     */
    public function availableBalance(): float
    {
        $totalApproved = $this->referralTracking()
            ->whereIn('status', ['approved', 'paid'])
            ->sum('commission_amount');

        $totalWithdrawn = $this->withdrawalRequests()
            ->where('status', 'completed')
            ->sum('amount');

        $pendingWithdrawals = $this->withdrawalRequests()
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');

        return max(0.0, (float) $totalApproved - (float) $totalWithdrawn - (float) $pendingWithdrawals);
    }

    /**
     * Get the full referral URL
     */
    public function getReferralUrlAttribute(): string
    {
        return url('/esim?ref=' . $this->referral_code);
    }

    /**
     * Check if agent is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Calculate commission for an order
     */
    public function calculateCommission(float $orderAmount): float
    {
        if ($this->commission_type === 'percentage') {
            return round(($orderAmount * $this->commission_value) / 100, 2);
        }

        return (float) $this->commission_value;
    }

    /**
     * The margin this freelancer earns on ONE eSIM, given the platform price
     * (supplier cost + company markup). Percentage margins apply to that
     * platform price; fixed margins are AED per eSIM. Only freelancers carry
     * one; every other referral partner stays on plain commission.
     */
    public function esimMarginFor(float $platformUnitPrice): float
    {
        if (!$this->is_freelancer) {
            return 0.0;
        }

        $value = max(0.0, (float) $this->esim_margin_value);

        return $this->esim_margin_type === 'percentage'
            ? round($platformUnitPrice * $value / 100, 2)
            : round($value, 2);
    }

    /** Human label for the margin, e.g. "3%" or "AED 3.00 per eSIM". */
    public function esimMarginLabel(): string
    {
        $value = (float) $this->esim_margin_value;

        return $this->esim_margin_type === 'percentage'
            ? rtrim(rtrim(number_format($value, 2), '0'), '.') . '%'
            : 'AED ' . number_format($value, 2) . ' per eSIM';
    }

    /**
     * Update agent statistics
     */
    public function updateStats(): void
    {
        $this->total_sales = $this->referralTracking()
            ->whereIn('status', ['approved', 'paid'])
            ->count();

        $this->total_earnings = $this->referralTracking()
            ->whereIn('status', ['approved', 'paid'])
            ->sum('commission_amount');

        $this->pending_earnings = $this->referralTracking()
            ->where('status', 'pending')
            ->sum('commission_amount');

        $this->paid_earnings = $this->referralTracking()
            ->where('status', 'paid')
            ->sum('commission_amount');

        $this->total_clicks = $this->clicks()->count();

        $this->save();
    }

    /**
     * Scope for active agents
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for searching agents
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('referral_code', 'like', "%{$search}%");
        });
    }

    /**
     * Get conversion rate
     */
    public function getConversionRateAttribute(): float
    {
        if ($this->total_clicks === 0) {
            return 0;
        }

        return round(($this->total_sales / $this->total_clicks) * 100, 2);
    }

    /**
     * Get pending orders count
     */
    public function getPendingOrdersCountAttribute(): int
    {
        return $this->referralTracking()->where('status', 'pending')->count();
    }

    /**
     * Get approved orders count
     */
    public function getApprovedOrdersCountAttribute(): int
    {
        return $this->referralTracking()->where('status', 'approved')->count();
    }
}

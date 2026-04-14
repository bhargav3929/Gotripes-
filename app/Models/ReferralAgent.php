<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class ReferralAgent extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'referral_agents';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'referral_code',
        'commission_type',
        'commission_value',
        'status',
        'total_earnings',
        'pending_earnings',
        'paid_earnings',
        'total_sales',
        'total_clicks',
        'notes',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'commission_value' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'pending_earnings' => 'decimal:2',
        'paid_earnings' => 'decimal:2',
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];

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
     * Get the full referral URL
     */
    public function getReferralUrlAttribute(): string
    {
        return url('/?ref=' . $this->referral_code);
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

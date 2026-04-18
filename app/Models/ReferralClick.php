<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralClick extends Model
{
    use HasFactory, BelongsToCompany;

    protected $table = 'referral_clicks';

    protected $fillable = [
        'referral_agent_id',
        'referral_code',
        'ip_address',
        'user_agent',
        'referer_url',
        'landing_page',
        'country',
        'city',
        'device_type',
        'browser',
        'os',
        'converted',
        'converted_at',
        'company_id',
    ];

    protected $casts = [
        'converted' => 'boolean',
        'converted_at' => 'datetime',
    ];

    /**
     * Relationship with referral agent
     */
    public function referralAgent()
    {
        return $this->belongsTo(ReferralAgent::class);
    }

    /**
     * Mark as converted
     */
    public function markAsConverted(): bool
    {
        $this->converted = true;
        $this->converted_at = now();
        return $this->save();
    }

    /**
     * Parse user agent to extract device info
     */
    public static function parseUserAgent(string $userAgent): array
    {
        $deviceType = 'desktop';
        $browser = 'Unknown';
        $os = 'Unknown';

        // Detect device type
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod|webOS|BlackBerry|IEMobile|Opera Mini/i', $userAgent)) {
            $deviceType = preg_match('/iPad|Tablet/i', $userAgent) ? 'tablet' : 'mobile';
        }

        // Detect browser
        if (preg_match('/Chrome\/[\d.]+/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Firefox\/[\d.]+/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Safari\/[\d.]+/i', $userAgent) && !preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Edge\/[\d.]+/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/MSIE|Trident/i', $userAgent)) {
            $browser = 'Internet Explorer';
        }

        // Detect OS
        if (preg_match('/Windows NT/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/Mac OS X/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iOS|iPhone|iPad|iPod/i', $userAgent)) {
            $os = 'iOS';
        }

        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'os' => $os,
        ];
    }

    /**
     * Scope for converted clicks
     */
    public function scopeConverted($query)
    {
        return $query->where('converted', true);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for today's clicks
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for this week's clicks
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope for this month's clicks
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }
}

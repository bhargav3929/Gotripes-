<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'subdomain',
        'type',
        'commission_type',
        'commission_value',
        'pending_commission',
        'paid_commission',
        'total_commission',
        'logo',
        'favicon',
        'primary_color',
        'secondary_color',
        'text_color',
        'bg_color',
        'email',
        'phone',
        'address',
        'website',
        'business_name',
        'tax_id',
        'currency',
        'timezone',
        'api_keys',
        'markup_percentage',
        'plan',
        'trial_ends_at',
        'subscription_ends_at',
        'is_active',
        'settings',
        'features',
    ];

    protected $casts = [
        'api_keys' => 'encrypted:array',
        'settings' => 'array',
        'features' => 'array',
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'markup_percentage' => 'decimal:2',
        'commission_value' => 'decimal:2',
        'pending_commission' => 'decimal:2',
        'paid_commission' => 'decimal:2',
        'total_commission' => 'decimal:2',
        'total_revenue' => 'decimal:2',
    ];

    protected $hidden = [
        'api_keys',
    ];

    public const RESERVED_SUBDOMAINS = [
        'www', 'admin', 'superadmin', 'api', 'app', 'mail', 'ftp', 'smtp',
        'pop', 'imap', 'webmail', 'cpanel', 'whm', 'cdn', 'static', 'assets',
        'public', 'root', 'login', 'logout', 'signup', 'register', 'auth',
        'oauth', 'dashboard', 'manager', 'partner', 'client', 'support',
        'help', 'docs', 'blog', 'shop', 'store', 'pay', 'payment', 'checkout',
        'billing', 'subscription', 'status', 'monitor', 'test', 'staging',
        'dev', 'demo', 'sandbox', 'beta', 'alpha', 'gotrips',
        'freelancer', 'freelancers',
    ];

    public function setSubdomainAttribute($value): void
    {
        $this->attributes['subdomain'] = static::normalizeSubdomain($value);
    }

    public static function normalizeSubdomain(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = strtolower(trim($value));
        $value = preg_replace('/\.gotrips\.(ai|com)$/i', '', $value);
        $value = preg_replace('/[^a-z0-9-]+/', '-', $value);
        $value = preg_replace('/-+/', '-', $value);
        $value = trim($value, '-');

        if ($value === '' || strlen($value) > 63) {
            return null;
        }

        return $value;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            if (empty($company->slug)) {
                $company->slug = Str::slug($company->name);
            }
            if (empty($company->subdomain)) {
                $company->subdomain = static::normalizeSubdomain($company->slug);
            }
        });
    }

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function admins(): HasMany
    {
        return $this->hasMany(User::class)->whereIn('role', ['company_owner', 'company_admin']);
    }

    public function owner()
    {
        return $this->hasOne(User::class)->where('role', 'company_owner');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(EsimOrder::class);
    }

    public function referralAgents(): HasMany
    {
        return $this->hasMany(ReferralAgent::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(TenantCommission::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(TenantBankAccount::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(TenantWithdrawal::class);
    }

    public function activityBookings(): HasMany
    {
        return $this->hasMany(\App\Models\ActivityBooking::class);
    }

    // Accessors
    public function getLogoUrlAttribute(): string
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : asset('images/default-logo.png');
    }

    public function getFaviconUrlAttribute(): string
    {
        return $this->favicon
            ? asset('storage/' . $this->favicon)
            : asset('favicon.ico');
    }

    public function getFullDomainAttribute(): string
    {
        if ($this->domain) {
            return $this->domain;
        }
        return $this->subdomain . '.' . config('app.domain', 'gotrips.ai');
    }

    // Subscription helpers
    public function isOnTrial(): bool
    {
        return $this->plan === 'trial' &&
               $this->trial_ends_at &&
               $this->trial_ends_at->isFuture();
    }

    public function hasActiveSubscription(): bool
    {
        if ($this->isOnTrial()) {
            return true;
        }
        return $this->subscription_ends_at &&
               $this->subscription_ends_at->isFuture();
    }

    public function isExpired(): bool
    {
        return !$this->hasActiveSubscription();
    }

    // Canonical list of features tenants can be granted
    public const AVAILABLE_FEATURES = [
        'activities'   => 'UAE Activities',
        'visas'        => 'UAE Visas',
        'tours'        => 'Tour Packages',
        'hajj_umrah'   => 'Hajj & Umrah',
        'esim'         => 'eSIM',
        'shop'         => 'Shop Online',
        'careers'      => 'Careers / Jobs',
        'pay_online'   => 'Pay Online',
    ];

    // Feature checks
    public function hasFeature(string $feature): bool
    {
        $features = $this->features ?? [];
        // If features is empty/null, treat as full-access (legacy / main tenant)
        if (empty($features)) {
            return true;
        }
        return in_array($feature, $features, true);
    }

    public function isFreelancer(): bool
    {
        return $this->type === 'freelancer';
    }

    public function isAgency(): bool
    {
        return $this->type === 'agency';
    }

    public function getSetting(string $key, $default = null)
    {
        $settings = $this->settings ?? [];
        return $settings[$key] ?? $default;
    }

    public function setSetting(string $key, $value): void
    {
        $settings = $this->settings ?? [];
        $settings[$key] = $value;
        $this->settings = $settings;
        $this->save();
    }

    // Branding array for frontend
    public function getBrandingAttribute(): array
    {
        return [
            'name' => $this->name,
            'logo' => $this->logo_url,
            'favicon' => $this->favicon_url,
            'colors' => [
                'primary' => $this->primary_color,
                'secondary' => $this->secondary_color,
                'text' => $this->text_color,
                'background' => $this->bg_color,
            ],
            'contact' => [
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'website' => $this->website,
            ],
        ];
    }

    // Stats
    public function incrementOrderCount(): void
    {
        $this->increment('total_orders');
    }

    public function addRevenue(float $amount): void
    {
        $this->increment('total_revenue', $amount);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithSubscription($query)
    {
        return $query->where(function ($q) {
            $q->where('plan', 'trial')
              ->where('trial_ends_at', '>', now())
              ->orWhere('subscription_ends_at', '>', now());
        });
    }
}

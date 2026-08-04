<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class UAEVisaPackage extends Model
{
    use BelongsToCompany;

    protected $table = 'uae_visa_packages';

    protected $fillable = [
        'emirates_id',
        'name',
        'package_type',
        'description',
        'security_deposit',
        'deposit_admin_fee',
        'supplier_email',
        'company_email',
        'isActive',
        'company_id',
    ];

    protected $casts = [
        'security_deposit'  => 'float',
        'deposit_admin_fee' => 'float',
    ];

    public function emirate()
    {
        return $this->belongsTo(Emirates::class, 'emirates_id', 'emiratesID');
    }

    public function prices()
    {
        return $this->hasMany(UAEVisaPrice::class, 'visa_package_id');
    }

    /**
     * Refundable deposit charged per applicant for this package.
     *
     * A package-level amount wins; null falls back to the company-wide Sharjah
     * setting, so packages created before deposits became per-package keep
     * charging exactly what they did before. 0 explicitly means "no deposit".
     */
    public function depositPerApplicant(): float
    {
        if ($this->security_deposit !== null) {
            return max(0.0, (float) $this->security_deposit);
        }

        $fallback = current_company()?->getSetting('visa_sharjah_deposit', 0);

        return is_numeric($fallback) ? max(0.0, (float) $fallback) : 0.0;
    }

    /**
     * Non-refundable processing fee deducted from the deposit at refund time.
     * Never exceeds the deposit, so the refundable balance cannot go negative.
     */
    public function depositAdminFee(): float
    {
        if ($this->deposit_admin_fee !== null) {
            $fee = max(0.0, (float) $this->deposit_admin_fee);
        } else {
            $fallback = current_company()?->getSetting('visa_sharjah_deposit_admin_fee', 0);
            $fee = is_numeric($fallback) ? max(0.0, (float) $fallback) : 0.0;
        }

        return min($fee, $this->depositPerApplicant());
    }

    /**
     * Supplier addresses to copy when this package is booked. Accepts a
     * comma-separated list. Falls back to the company-wide supplier setting.
     *
     * @return array<int, string>
     */
    public function supplierEmails(): array
    {
        $raw = $this->supplier_email
            ?: current_company()?->getSetting('visa_supplier_email');

        return parse_emails($raw);
    }

    /**
     * Our own addresses that get a copy — whoever currently handles this visa
     * type. Falls back to the company-wide setting and then the company's own
     * address, so a package with nothing configured still reaches somebody.
     *
     * @return array<int, string>
     */
    public function companyEmails(): array
    {
        $company = current_company();

        $raw = $this->company_email
            ?: $company?->getSetting('visa_company_email')
            ?: $company?->email;

        return parse_emails($raw);
    }
}

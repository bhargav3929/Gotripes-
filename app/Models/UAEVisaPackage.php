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

    public function deposits()
    {
        return $this->hasMany(UAEVisaPackageDeposit::class, 'visa_package_id');
    }

    /**
     * Nationality-specific deposit row for this package: exact case-insensitive
     * match first, then the row with nationality = null (this package's own
     * default), or null when no deposit rows have been configured at all —
     * in which case callers fall back to the legacy package-level columns.
     */
    private function resolveDepositRow(?string $nationality = null): ?UAEVisaPackageDeposit
    {
        $rows = $this->deposits->where('isActive', 1);

        if ($nationality !== null && $nationality !== '') {
            $match = $rows->first(fn($d) => !empty($d->nationality) && strtolower($d->nationality) === strtolower($nationality));
            if ($match) {
                return $match;
            }
        }

        return $rows->first(fn($d) => empty($d->nationality));
    }

    /**
     * Refundable deposit charged per applicant for this package, optionally
     * varying by the applicant's nationality (e.g. India/Pakistan/Nepal carry
     * a higher deposit than everyone else).
     *
     * A matching nationality-specific row wins; then this package's own
     * default row; then the legacy package-level column; then the
     * company-wide Sharjah setting, so packages created before deposits
     * existed keep charging exactly what they did before. 0 explicitly means
     * "no deposit".
     */
    public function depositPerApplicant(?string $nationality = null): float
    {
        if ($row = $this->resolveDepositRow($nationality)) {
            return max(0.0, (float) $row->security_deposit);
        }

        if ($this->security_deposit !== null) {
            return max(0.0, (float) $this->security_deposit);
        }

        // The company-wide setting is specifically the *Sharjah* deposit — it is
        // named that, and Sharjah was the only emirate that ever charged one. So
        // it is only inherited by Sharjah packages. Letting a Dubai package fall
        // back to it would have started charging a deposit on emirates that have
        // never had one.
        if (!$this->isDepositEmirate()) {
            return 0.0;
        }

        $fallback = current_company()?->getSetting('visa_sharjah_deposit', 0);

        return is_numeric($fallback) ? max(0.0, (float) $fallback) : 0.0;
    }

    /** True when this package's emirate is the one the legacy setting was for. */
    private function isDepositEmirate(): bool
    {
        return strtolower(trim((string) $this->emirate?->emiratesName)) === 'sharjah';
    }

    /**
     * Non-refundable processing fee deducted from the deposit at refund time,
     * for the same nationality resolution as depositPerApplicant(). Never
     * exceeds the deposit, so the refundable balance cannot go negative.
     */
    public function depositAdminFee(?string $nationality = null): float
    {
        if ($row = $this->resolveDepositRow($nationality)) {
            $fee = max(0.0, (float) $row->deposit_admin_fee);
            return min($fee, $this->depositPerApplicant($nationality));
        }

        if ($this->deposit_admin_fee !== null) {
            $fee = max(0.0, (float) $this->deposit_admin_fee);
        } elseif ($this->isDepositEmirate()) {
            $fallback = current_company()?->getSetting('visa_sharjah_deposit_admin_fee', 0);
            $fee = is_numeric($fallback) ? max(0.0, (float) $fallback) : 0.0;
        } else {
            $fee = 0.0;
        }

        return min($fee, $this->depositPerApplicant($nationality));
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

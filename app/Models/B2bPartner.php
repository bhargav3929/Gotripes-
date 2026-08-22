<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * A B2B agency that self-registered, uploaded a trade license (UAE only),
 * e-signed an auto-generated contract, and is waiting for (or has received)
 * manager approval. Modeled on ReferralAgent's structure: own table, own
 * auth guard, tenant-scoped via BelongsToCompany.
 */
class B2bPartner extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, BelongsToCompany;

    protected $table = 'b2b_partners';

    /**
     * The only country value that reveals the trade-license fields and
     * produces a "national" contract — kept as a single constant so the
     * registration form's JS and the contract-type derivation can never
     * disagree on the exact string.
     */
    public const UAE_COUNTRY_NAME = 'United Arab Emirates';

    protected $fillable = [
        'company_id',
        'company_name',
        'contact_name',
        'email',
        'password',
        'phone',
        'country',
        'trade_license_number',
        'trade_license_expiry_date',
        'trade_license_document_path',
        'contract_type',
        'contract_pdf_path',
        'signature_full_name',
        'signature_agreed',
        'signature_ip',
        'signed_at',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'is_active',
        'disabled_at',
        'pending_license_review',
        'expiry_warning_sent_at',
        'commission_percent',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'trade_license_expiry_date' => 'date',
        'signature_agreed' => 'boolean',
        'signed_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'is_active' => 'boolean',
        'disabled_at' => 'datetime',
        'pending_license_review' => 'boolean',
        'expiry_warning_sent_at' => 'datetime',
        'commission_percent' => 'decimal:2',
        'last_login_at' => 'datetime',
    ];

    public function isUae(): bool
    {
        return $this->country === self::UAE_COUNTRY_NAME;
    }

    /**
     * Non-UAE partners have no expiry date to violate, so they can never be
     * considered expired.
     */
    public function isTradeLicenseExpired(): bool
    {
        return $this->isUae()
            && $this->trade_license_expiry_date
            && $this->trade_license_expiry_date->isPast();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Single source of truth for whether this partner may log in right now,
     * reused by both the login controller and the auth middleware so the
     * gate logic never has to be kept in sync across two places.
     */
    public function canLogin(): bool
    {
        return $this->isApproved() && $this->is_active && !$this->isTradeLicenseExpired();
    }

    public function approve(User $reviewer): void
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    public function reject(User $reviewer, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Called only by the scheduled expiry-check command. Kept separate from
     * approve()/reject() since this doesn't change `status` — an approved
     * partner disabled for an expired license keeps their approval history.
     */
    public function disableForExpiry(): void
    {
        $this->update([
            'is_active' => false,
            'disabled_at' => now(),
        ]);
    }

    /**
     * True only for a partner disabled by the automatic expiry check (not a
     * manually deactivated one, though no other disable path exists yet) —
     * used to decide whether the self-service renewal flow applies to them.
     */
    public function wasDisabledForExpiry(): bool
    {
        return $this->isApproved() && !$this->is_active && $this->disabled_at !== null;
    }

    /**
     * A partner submits a renewed license through the public renewal flow.
     * Deliberately does NOT flip is_active back on — the renewal sits as
     * "pending review" until a manager confirms the new document, same
     * fraud/legal rationale that disabled the account in the first place.
     */
    public function submitLicenseRenewal(string $number, string $expiryDate, ?string $documentPath): void
    {
        $this->update([
            'trade_license_number' => $number,
            'trade_license_expiry_date' => $expiryDate,
            'trade_license_document_path' => $documentPath ?? $this->trade_license_document_path,
            'pending_license_review' => true,
        ]);
    }

    /**
     * A manager confirms a renewed license — re-activates the account and
     * resets the expiry-warning flag so the new date gets its own fresh
     * 30-day warning cycle.
     */
    public function confirmRenewal(User $reviewer): void
    {
        $this->update([
            'is_active' => true,
            'disabled_at' => null,
            'pending_license_review' => false,
            'expiry_warning_sent_at' => null,
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);
    }
}

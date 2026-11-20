<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

/**
 * An access code staff give one supplier so they can fill in the supplier
 * rate form. Only people holding a live code get past /suppliers.
 */
class SupplierInvite extends Model
{
    use BelongsToCompany;

    /** No 0/O, 1/I/L: codes are read out and typed from WhatsApp. */
    private const ALPHABET = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

    protected $fillable = ['company_id', 'code', 'supplier_name', 'country', 'package_id', 'expires_on', 'revoked_at', 'last_used_at', 'created_by'];

    protected $casts = ['expires_on' => 'date', 'revoked_at' => 'datetime', 'last_used_at' => 'datetime'];

    public static function newCode(): string
    {
        do {
            $code = '';
            for ($i = 0; $i < 8; $i++) {
                $code .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
            }
        } while (static::withoutCompanyScope()->where('code', $code)->exists());
        return $code;
    }

    /** "7k3p q9wx", "7K3P-Q9WX" and "7K3PQ9WX" are the same code. */
    public static function normalize(?string $code): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $code));
    }

    /** The code as staff and suppliers see it: 7K3P-Q9WX. */
    public function getDisplayCodeAttribute(): string
    {
        return substr($this->code, 0, 4) . '-' . substr($this->code, 4);
    }

    public function isUsable(): bool
    {
        return !$this->revoked_at && (!$this->expires_on || $this->expires_on->endOfDay()->isFuture());
    }

    public function statusLabel(): string
    {
        return $this->revoked_at ? 'Turned off' : (!$this->isUsable() ? 'Expired' : 'Active');
    }

    public function package()
    {
        return $this->belongsTo(TravelPackage::class, 'package_id');
    }

    public function submissions()
    {
        return $this->hasMany(SupplierSubmission::class)->latest('id');
    }

    public function latestSubmission(): ?SupplierSubmission
    {
        return $this->submissions()->first();
    }
}

<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * A B2B Tenant Portal Agreement generated in Manager -> Tenant Agreements.
 * See database/migrations/2026_11_26_000010_create_b2b_tenant_agreements_table.php.
 *
 * `partner` holds the form fields (legal name, licence, address, contact,
 * start date, term, modules). The PDF is rendered from it on each download.
 */
class B2bTenantAgreement extends Model
{
    use BelongsToCompany;

    protected $fillable = ['company_id', 'reference', 'partner_name', 'partner', 'created_by'];

    protected $casts = [
        'partner' => 'array',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Next GT-B2B-<year>-<nnnn> reference. Counted across every company, since
     * the reference column is unique table-wide.
     */
    public static function nextReference(): string
    {
        $prefix = 'GT-B2B-' . now()->year . '-';

        $last = DB::table((new static)->getTable())
            ->where('reference', 'like', $prefix . '%')
            ->orderByDesc('reference')
            ->value('reference');

        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function field(string $key, $default = null)
    {
        return data_get($this->partner, $key, $default);
    }

    /** Digits only, as wa.me wants them; 00-prefixed numbers lose the 00. */
    public function whatsappNumber(): string
    {
        $digits = preg_replace('/\D+/', '', (string) $this->field('phone'));

        return str_starts_with($digits, '00') ? substr($digits, 2) : $digits;
    }

    public function whatsappUrl(): string
    {
        $contact = $this->field('contact_name');

        $message = "Hello {$contact},\n\n"
            . "Please find attached the GoTrips B2B Tenant Portal Agreement (ref. {$this->reference}) "
            . "for {$this->partner_name}.\n\n"
            . "Kindly review it, sign and stamp it, and send a copy back to us here. "
            . "If you have any questions, just reply to this message.\n\n"
            . "GoTrips Team";

        return 'https://wa.me/' . $this->whatsappNumber() . '?text=' . rawurlencode($message);
    }

    public function fileName(): string
    {
        return $this->reference . '-B2B-Tenant-Portal-Agreement.pdf';
    }
}

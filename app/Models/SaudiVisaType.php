<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class SaudiVisaType extends Model
{
    use BelongsToCompany;

    protected $table = 'tbl_saudi_visa_types';

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'required_documents',
        'processing_days',
        'price',
        'company_email',
        'supplier_email',
        'isActive',
    ];

    protected $casts = [
        'price'               => 'decimal:2',
        'isActive'            => 'boolean',
        'processing_days'     => 'integer',
        'required_documents'  => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('isActive', 1);
    }

    /**
     * Whether this visa type asks the applicant for an Emirates ID or GCC
     * residence copy.
     *
     * Driven off the manager-editable `required_documents` list rather than a
     * hardcoded visa-type id, so adding or removing the requirement stays a
     * portal edit. Matches "Emirates ID", "GCC Residence", "Emirates ID or GCC
     * Residence" and similar wording.
     */
    public function requiresEmiratesId(): bool
    {
        foreach ((array) $this->required_documents as $doc) {
            if (preg_match('/emirates\s*id|gcc/i', (string) $doc)) {
                return true;
            }
        }

        return false;
    }
}

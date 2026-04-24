<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralBankAccount extends Model
{
    use HasFactory;

    protected $table = 'referral_bank_accounts';

    protected $fillable = [
        'referral_agent_id',
        'bank_name',
        'account_holder_name',
        'account_number',
        'iban',
        'swift_code',
        'country',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * The agent that owns this bank account.
     */
    public function referralAgent()
    {
        return $this->belongsTo(ReferralAgent::class);
    }

    /**
     * Return an array snapshot of bank details for JSON storage.
     */
    public function toSnapshot(): array
    {
        return [
            'bank_name'            => $this->bank_name,
            'account_holder_name'  => $this->account_holder_name,
            'account_number'       => $this->account_number,
            'iban'                 => $this->iban,
            'swift_code'           => $this->swift_code,
            'country'              => $this->country,
        ];
    }
}

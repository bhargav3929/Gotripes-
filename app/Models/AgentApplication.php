<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

/**
 * A prospective agent's self-registration, pending manager review of their
 * trade license before a real `company_agent` User account is provisioned.
 * See database/migrations/2026_08_18_064145_create_agent_applications_table.php.
 */
class AgentApplication extends Model
{
    use BelongsToCompany;

    protected $table = 'agent_applications';

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'phone',
        'country',
        'services',
        'trade_license_number',
        'trade_license_expiry_date',
        'trade_license_document_path',
        'status',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'user_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'services' => 'array',
        'trade_license_expiry_date' => 'date',
        'reviewed_at' => 'datetime',
    ];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
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
     * Approve the application: provisions the real agent User account with
     * the exact services the applicant selected, and links it back here.
     * The password was already collected (and hashed) at registration, so
     * the agent can log in immediately with what they set.
     */
    public function approve(User $reviewer): User
    {
        $agent = User::create([
            'name'           => $this->name,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'password'       => $this->password,
            'role'           => 'company_agent',
            'company_id'     => $this->company_id,
            'agent_services' => $this->services,
            'is_active'      => true,
        ]);

        $this->update([
            'status'      => 'approved',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_reason' => null,
            'user_id'     => $agent->id,
        ]);

        return $agent;
    }

    public function reject(User $reviewer, string $reason): void
    {
        $this->update([
            'status'      => 'rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }
}

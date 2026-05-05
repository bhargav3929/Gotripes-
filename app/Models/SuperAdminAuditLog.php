<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuperAdminAuditLog extends Model
{
    protected $table = 'super_admin_audit_logs';
    public $timestamps = false;     // only created_at, written by DB default

    protected $fillable = [
        'actor_user_id',
        'action',
        'target_type',
        'target_id',
        'target_label',
        'changes',
        'ip',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'changes'    => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Convenience writer used from controllers.
     *
     *   SuperAdminAuditLog::log('company.toggle_status', $company, ['from' => 0, 'to' => 1]);
     */
    public static function log(string $action, ?Model $target = null, array $changes = []): self
    {
        return self::create([
            'actor_user_id' => auth()->id(),
            'action'        => $action,
            'target_type'   => $target ? get_class($target) : null,
            'target_id'     => $target?->getKey(),
            'target_label'  => $target?->name ?? $target?->title ?? null,
            'changes'       => $changes ?: null,
            'ip'            => request()->ip(),
            'user_agent'    => substr((string) request()->userAgent(), 0, 500),
            'created_at'    => now(),
        ]);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}

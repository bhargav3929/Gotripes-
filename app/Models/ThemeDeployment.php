<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One attempt to publish (or roll back, or wipe) a tenant's developer theme.
 * Failed attempts are kept too, with the reason, so the history shows the
 * tenant's developers exactly why a push did not go live.
 */
class ThemeDeployment extends Model
{
    protected $fillable = [
        'company_id', 'source', 'ref', 'commit_sha', 'status', 'message',
        'manifest', 'files', 'file_count', 'total_bytes', 'directory', 'triggered_by',
    ];

    protected $casts = [
        'manifest'    => 'array',
        'files'       => 'array',
        'file_count'  => 'integer',
        'total_bytes' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function sourceLabel(): string
    {
        return [
            'github'   => 'GitHub push',
            'upload'   => 'Zip upload',
            'rollback' => 'Rollback',
            'wipe'     => 'Wipe',
        ][$this->source] ?? ucfirst((string) $this->source);
    }

    public function shortSha(): ?string
    {
        return $this->commit_sha ? substr($this->commit_sha, 0, 7) : null;
    }

    /** Whether this deployment's files are still on disk (it can be rolled back to). */
    public function isAvailable(): bool
    {
        return $this->isSuccess() && $this->directory
            && is_dir(public_path(config('tenant_themes.public_dir') . '/' . $this->directory));
    }

    public function hasFile(string $path): bool
    {
        return in_array($path, $this->files ?? [], true);
    }
}

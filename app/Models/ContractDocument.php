<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * A contract a manager published in the portal for applicants to sign.
 * See database/migrations/2026_09_22_000001_create_contract_documents_table.php.
 *
 * Versions are immutable: publishing a new one moves `is_current`, it does not
 * rewrite the text someone already signed.
 */
class ContractDocument extends Model
{
    use BelongsToCompany;

    public const AUDIENCE_AGENT = 'agent';

    public const SOURCE_TEXT = 'text';
    public const SOURCE_UPLOAD = 'upload';

    protected $fillable = [
        'company_id', 'audience', 'title', 'version', 'source',
        'body', 'pdf_path', 'file_name', 'content_hash', 'is_current', 'created_by',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'version' => 'integer',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** The contract new applicants of this audience must sign, if any. */
    public static function current(string $audience = self::AUDIENCE_AGENT): ?self
    {
        // Ordered by id as well: two rows can share a version number across
        // tenants, and "the current one" must never depend on row order.
        return static::where('audience', $audience)
            ->where('is_current', true)
            ->orderByDesc('version')
            ->orderByDesc('id')
            ->first();
    }

    public function isUpload(): bool
    {
        return $this->source === self::SOURCE_UPLOAD;
    }

    /** Make this version the one shown at sign-up, retiring the previous one. */
    public function makeCurrent(): void
    {
        static::where('audience', $this->audience)
            ->where('id', '!=', $this->id)
            ->update(['is_current' => false]);

        $this->update(['is_current' => true]);
    }

    /**
     * How many signatures this version carries. Kept as a count rather than a
     * relation because the signing side lives on two tables (applications now,
     * users after approval) and only the application is the evidence.
     */
    public function signatureCount(): int
    {
        return AgentApplication::withoutGlobalScopes()
            ->where('contract_document_id', $this->id)
            ->whereNotNull('signed_at')
            ->count();
    }

    public function deleteFile(): void
    {
        if ($this->pdf_path && Storage::disk('public')->exists($this->pdf_path)) {
            Storage::disk('public')->delete($this->pdf_path);
        }
    }
}

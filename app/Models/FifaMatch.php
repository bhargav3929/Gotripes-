<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FifaMatch extends Model
{
    protected $table = 'fifa_matches';

    protected $fillable = [
        'match_code', 'team_a', 'team_b', 'stage',
        'match_date', 'venue', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'match_date' => 'date',
        'is_active'  => 'boolean',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(FifaTicket::class, 'match_id')->orderBy('sort_order')->orderBy('id');
    }

    public function activeTickets(): HasMany
    {
        return $this->tickets()->where('is_active', 1);
    }

    public function getTitleAttribute(): string
    {
        return "{$this->team_a} vs {$this->team_b}";
    }

    /**
     * Team name → ISO 3166-1 alpha-2 code (flagcdn). England/Scotland use the
     * GB sub-region codes flagcdn supports. Knockout placeholders (Winner 81,
     * 2K, 3rd C/D/F/…) have no nation, so they resolve to null = no flag.
     */
    public const FLAGS = [
        'Australia' => 'au', 'Turkey' => 'tr', 'Germany' => 'de', 'Curacao' => 'cw',
        'Spain' => 'es', 'Cape Verde' => 'cv', 'Iran' => 'ir', 'New Zealand' => 'nz',
        'France' => 'fr', 'Senegal' => 'sn', 'Iraq' => 'iq', 'Norway' => 'no',
        'Argentina' => 'ar', 'Algeria' => 'dz', 'Austria' => 'at', 'Jordan' => 'jo',
        'Ghana' => 'gh', 'Panama' => 'pa', 'England' => 'gb-eng', 'Croatia' => 'hr',
        'Portugal' => 'pt', 'DR Congo' => 'cd', 'Czech Republic' => 'cz', 'South Africa' => 'za',
        'Switzerland' => 'ch', 'Bosnia' => 'ba', 'Brazil' => 'br', 'Haiti' => 'ht',
        'Paraguay' => 'py', 'Ivory Coast' => 'ci', 'Ecuador' => 'ec', 'Netherlands' => 'nl',
        'Sweden' => 'se', 'Tunisia' => 'tn', 'Japan' => 'jp', 'Saudi Arabia' => 'sa',
        'Belgium' => 'be', 'Egypt' => 'eg', 'Morocco' => 'ma', 'Qatar' => 'qa',
        'South Korea' => 'kr', 'Uzbekistan' => 'uz', 'Mexico' => 'mx', 'Korea' => 'kr',
        'Uruguay' => 'uy', 'Colombia' => 'co', 'Scotland' => 'gb-sct',
    ];

    public static function flagUrl(?string $team): ?string
    {
        $code = self::FLAGS[trim((string) $team)] ?? null;
        return $code ? "https://flagcdn.com/{$code}.svg" : null;
    }

    public function getFlagAAttribute(): ?string
    {
        return self::flagUrl($this->team_a);
    }

    public function getFlagBAttribute(): ?string
    {
        return self::flagUrl($this->team_b);
    }
}

<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class TravelPackage extends Model
{
    use BelongsToCompany;

    protected $table = 'tbl_travel_packages';
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'agent_id',
        'title',
        'country',
        'package_type',
        'partner_email',
        'partner_whatsapp',
        'notification_emails',
        'image',
        'price',
        'price_adult',
        'price_child',
        'price_infant',
        'description',
        'duration',
        'isActive',
        'createdBy',
        'createdDate',
        'modifiedBy',
        'modifiedDate',
    ];

    protected $casts = [
        'isActive' => 'boolean',
        'price' => 'decimal:2',
        'price_adult' => 'decimal:2',
        'price_child' => 'decimal:2',
        'price_infant' => 'decimal:2',
        'createdDate' => 'datetime',
        'modifiedDate' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('isActive', 1);
    }

    /**
     * Parsed recipients to notify when a customer enquires about this package.
     *
     * @return array<int, string>
     */
    public function getNotificationEmailListAttribute(): array
    {
        return parse_emails($this->notification_emails);
    }

    public function images()
    {
        return $this->hasMany(TravelPackageImage::class, 'package_id')->orderBy('sort_order');
    }

    /**
     * All gallery image paths, cover image first. Falls back to the single
     * `image` column when no gallery images exist (backward compatible).
     */
    public function galleryImages(): array
    {
        $paths = $this->images->pluck('image_path')->all();
        if ($this->image && !in_array($this->image, $paths, true)) {
            array_unshift($paths, $this->image);
        }
        return $paths;
    }

    /**
     * True when this is a ready-made package the customer can pay for directly.
     */
    public function isPurchasable(): bool
    {
        return $this->package_type === 'purchase';
    }
}

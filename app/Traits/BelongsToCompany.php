<?php

namespace App\Traits;

use App\Models\Company;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        // Auto-apply company scope to all queries
        static::addGlobalScope(new CompanyScope);

        // Auto-set company_id when creating
        static::creating(function ($model) {
            if (empty($model->company_id) && app()->has('current_company') && app('current_company')) {
                $model->company_id = app('current_company')->id;
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Scope to query without company filter (for super admin)
    public function scopeWithoutCompanyScope($query)
    {
        return $query->withoutGlobalScope(CompanyScope::class);
    }

    // Scope to filter by specific company
    public function scopeForCompany($query, $companyId)
    {
        return $query->withoutGlobalScope(CompanyScope::class)
                     ->where('company_id', $companyId);
    }
}

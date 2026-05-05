<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Super-admin bypass is intentional and ROUTE-scoped, not user-scoped.
        // A super admin browsing a tenant subdomain should see THAT tenant's data,
        // not everything. The bypass only kicks in inside /superadmin/* routes.
        if (request()->routeIs('superadmin.*')) {
            return;
        }

        $companyId = current_company_id();

        // Fail closed: with no tenant context, return zero rows.
        // Better to show an empty page than to leak another tenant's data.
        if (!$companyId) {
            $builder->whereRaw('1 = 0');
            return;
        }

        $builder->where($model->getTable() . '.company_id', $companyId);
    }
}

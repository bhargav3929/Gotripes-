<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Only apply if we have a current company and user is not super admin
        if (app()->has('current_company')) {
            $company = app('current_company');

            // Check if current user is super admin
            $user = auth()->user();
            if ($user && $user->is_super_admin) {
                // Super admins can see all data - don't apply scope
                return;
            }

            $builder->where($model->getTable() . '.company_id', $company->id);
        }
    }
}

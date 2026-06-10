<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'access_type',
        'email_verified_at', // Now stores comma-separated emirates IDs
        'partner_document_path',
        'company_id',
        'role',
        'agent_services',
        'is_active',
        'is_super_admin',
        'last_login_at',
    ];

    protected $casts = [
        'is_super_admin' => 'boolean',
        'is_active' => 'boolean',
        'agent_services' => 'array',
        'last_login_at' => 'datetime',
    ];

    /**
     * Services a manager can grant to an agent account. Keys deliberately
     * match Company::AVAILABLE_FEATURES so a grant is only effective while
     * the tenant itself has the feature enabled.
     */
    public const AGENT_SERVICES = [
        'tours'      => 'Tour Packages',
        'activities' => 'Activities',
        'esim'       => 'eSIM',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Company relationship for multi-tenant
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * eSIM orders relationship
     */
    public function esimOrders()
    {
        return $this->hasMany(EsimOrder::class);
    }

    /**
     * Check if user is a super admin
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is a company owner
     */
    public function isCompanyOwner()
    {
        return $this->role === 'company_owner';
    }

    /**
     * Check if user is a company admin
     */
    public function isCompanyAdmin()
    {
        return in_array($this->role, ['company_owner', 'company_admin']);
    }

    /**
     * Check if user is a tenant agent (created by a manager via Add Agent)
     */
    public function isAgent()
    {
        return $this->role === 'company_agent';
    }

    /**
     * Check if this agent was granted a service (tours / activities / esim).
     * The grant only counts while the tenant itself has the matching feature.
     */
    public function hasService(string $service): bool
    {
        if (!$this->isAgent()) {
            return false;
        }

        $services = $this->agent_services ?? [];
        if (!is_array($services) || !in_array($service, $services, true)) {
            return false;
        }

        return !$this->company || $this->company->hasFeature($service);
    }

    /**
     * Human-readable labels for this agent's granted services.
     */
    public function agentServiceLabels(): array
    {
        $services = is_array($this->agent_services) ? $this->agent_services : [];
        return array_values(array_intersect_key(self::AGENT_SERVICES, array_flip($services)));
    }








    /**
     * Define the many-to-many relationship with roles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($roleName)
    {
        return $this->roles()->where('title', $roleName)->exists();
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole($roles)
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }

        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user is admin (has Admin role assigned)
     */
    public function isAdmin()
    {
        return $this->roles()->where('title', 'Admin')->exists();
    }

    /**
     * Check if user is an employee (has any admin panel role but is NOT an admin)
     */
    public function isEmployee()
    {
        return !$this->isAdmin() && $this->roles()->exists();
    }

    /**
     * Check if user has a specific permission via their roles
     */
    public function hasPermission($permissionTitle)
    {
        // Admins have all permissions
        if ($this->isAdmin()) {
            return true;
        }

        // Check if any of the user's roles have the requested permission
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionTitle) {
                $query->where('title', $permissionTitle);
            })
            ->exists();
    }

    /**
     * Check if user has full access type
     */
    public function hasFullAccess()
    {
        return $this->access_type === 'full' || $this->isAdmin();
    }

    /**
     * Check if user has specific (limited) access type
     */
    public function hasSpecificAccess()
    {
        return $this->access_type === 'specific';
    }

    /**
     * Check if user is Activities Manager
     */
    public function isActivitiesManager()
    {
        return $this->hasRole('Activities Manager');
    }

    /**
     * Check if user is a partner (has emirates selected)
     */
    public function isPartner()
    {
        return !empty($this->email_verified_at) && $this->email_verified_at !== null;
    }

    /**
     * Check if user has restricted access (is a partner)
     */
    public function hasRestrictedAccess()
    {
        return $this->isPartner();
    }

    /**
     * Get user type name for display
     */
    public function getUserType()
    {
        if ($this->isAdmin()) {
            return 'Admin';
        }
        if ($this->isActivitiesManager()) {
            return 'Activities Manager';
        }
        if ($this->isPartner()) {
            return 'Partner';
        }
        return 'User';
    }

    /**
     * Get selected emirates IDs as array
     */
    public function getSelectedEmiratesIds()
    {
        if (empty($this->email_verified_at)) {
            return [];
        }
        return explode(',', $this->email_verified_at);
    }

    /**
     * Get selected emirates objects
     */
    public function getSelectedEmirates()
    {
        $ids = $this->getSelectedEmiratesIds();
        if (empty($ids)) {
            return collect();
        }
        return Emirates::whereIn('id', $ids)->get();
    }

    /**
     * Check if user has access to specific emirate
     */
    public function hasEmirateAccess($emirateId)
    {
        $selectedIds = $this->getSelectedEmiratesIds();
        return in_array($emirateId, $selectedIds);
    }

    /**
     * Get emirates names as string
     */
    public function getEmiratesNamesString()
    {
        return $this->getSelectedEmirates()->pluck('name')->implode(', ');
    }

    /**
     * Check if user can access a specific route
     */
    public function canAccessRoute($route)
    {
        // If user is not a partner, they have full access
        if (!$this->isPartner()) {
            return true;
        }

        // Define allowed routes for partners
        $partnerAllowedRoutes = [
            'emirates',
            'logout',
            'profile',
            'dashboard',
        ];

        foreach ($partnerAllowedRoutes as $allowedRoute) {
            if ($allowedRoute === $route || str_starts_with($route, $allowedRoute)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get user's dashboard URL based on type
     */
    public function getDashboardUrl()
    {
        if ($this->isPartner()) {
            return '/emirates'; // Partners go to activities
        }

        return '/dashboard'; // Regular users go to dashboard
    }
}

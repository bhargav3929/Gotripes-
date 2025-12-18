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
        'email_verified_at', // Now stores comma-separated emirates IDs
        'partner_document_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];








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
        return $this->roles()->where('name', $roleName)->exists();
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
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
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
     * Check if user has full access (not a partner)
     */
    public function hasFullAccess()
    {
        return !$this->isPartner();
    }

    /**
     * Get user type name for display
     */
    public function getUserType()
    {
        return $this->isPartner() ? 'Partner' : 'Customer';
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

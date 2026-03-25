<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Valid roles for this application.
     */
    const ROLES = [
        'ADMIN',
        'MANAGER',
        'ENGINEER',
        'TECHNICIAN',
        'REQUESTER',
        'STORE',
        'VENDOR_COORDINATOR',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    /* ------------------------------------------------------------------ */
    /* Role helpers                                                         */
    /* ------------------------------------------------------------------ */

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function isAdmin(): bool        { return $this->role === 'ADMIN'; }
    public function isManager(): bool      { return $this->role === 'MANAGER'; }
    public function isEngineer(): bool     { return $this->role === 'ENGINEER'; }
    public function isTechnician(): bool   { return $this->role === 'TECHNICIAN'; }
    public function isRequester(): bool    { return $this->role === 'REQUESTER'; }
    public function isStore(): bool        { return $this->role === 'STORE'; }
    public function isVendorCoordinator(): bool { return $this->role === 'VENDOR_COORDINATOR'; }

    /* ------------------------------------------------------------------ */
    /* Relationships                                                        */
    /* ------------------------------------------------------------------ */

    public function createdWorkOrders()
    {
        return $this->hasMany(WorkOrder::class, 'created_by_user_id');
    }

    public function assignedWorkOrders()
    {
        return $this->hasMany(WorkOrder::class, 'assigned_to_user_id');
    }

    public function closedWorkOrders()
    {
        return $this->hasMany(WorkOrder::class, 'closed_by_user_id');
    }
}

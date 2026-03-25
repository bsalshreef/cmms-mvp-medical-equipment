<?php

namespace App\Policies;

use App\Models\MaintenancePlan;
use App\Models\User;

/**
 * MaintenancePlanPolicy
 *
 * ADMIN / MANAGER  – full access (via before())
 * ENGINEER         – full CRUD on maintenance plans
 * TECHNICIAN       – view only
 * REQUESTER        – no access
 * STORE            – no access
 * VENDOR_COORDINATOR – view only
 */
class MaintenancePlanPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if (in_array($user->role, ['ADMIN', 'MANAGER'])) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['ENGINEER', 'TECHNICIAN', 'VENDOR_COORDINATOR']);
    }

    public function view(User $user, MaintenancePlan $maintenancePlan): bool
    {
        return in_array($user->role, ['ENGINEER', 'TECHNICIAN', 'VENDOR_COORDINATOR']);
    }

    public function create(User $user): bool
    {
        return $user->role === 'ENGINEER';
    }

    public function update(User $user, MaintenancePlan $maintenancePlan): bool
    {
        return $user->role === 'ENGINEER';
    }

    public function delete(User $user, MaintenancePlan $maintenancePlan): bool
    {
        return false; // ADMIN/MANAGER only via before()
    }

    /*
    |--------------------------------------------------------------------------
    | execute – mark a PPM execution as done
    |--------------------------------------------------------------------------
    */
    public function execute(User $user, MaintenancePlan $maintenancePlan): bool
    {
        return in_array($user->role, ['ENGINEER', 'TECHNICIAN']);
    }
}

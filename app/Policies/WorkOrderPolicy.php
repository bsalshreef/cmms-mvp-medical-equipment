<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;

/**
 * WorkOrderPolicy
 *
 * Roles:
 *   ADMIN              – full access to everything
 *   MANAGER            – full access to everything (read + approve/close)
 *   ENGINEER           – create, view, edit, assign, close their own or any
 *   TECHNICIAN         – view and update (progress) work orders assigned to them
 *   REQUESTER          – create and view their own work orders
 *   STORE              – view only
 *   VENDOR_COORDINATOR – view only
 */
class WorkOrderPolicy
{
    /*
    |--------------------------------------------------------------------------
    | Before Gate
    |--------------------------------------------------------------------------
    | ADMIN and MANAGER bypass every check.
    */
    public function before(User $user, string $ability): ?bool
    {
        if (in_array($user->role, ['ADMIN', 'MANAGER'])) {
            return true;
        }
        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | viewAny – list all work orders
    |--------------------------------------------------------------------------
    */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            'ENGINEER', 'TECHNICIAN', 'REQUESTER',
            'STORE', 'VENDOR_COORDINATOR',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | view – show a single work order
    |--------------------------------------------------------------------------
    */
    public function view(User $user, WorkOrder $workOrder): bool
    {
        if (in_array($user->role, ['ENGINEER', 'STORE', 'VENDOR_COORDINATOR'])) {
            return true;
        }

        if ($user->role === 'TECHNICIAN') {
            return $workOrder->assigned_to_user_id === $user->id;
        }

        if ($user->role === 'REQUESTER') {
            return $workOrder->created_by_user_id === $user->id;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | create – open a new work order
    |--------------------------------------------------------------------------
    */
    public function create(User $user): bool
    {
        return in_array($user->role, ['ENGINEER', 'REQUESTER']);
    }

    /*
    |--------------------------------------------------------------------------
    | update – edit work order details
    |--------------------------------------------------------------------------
    */
    public function update(User $user, WorkOrder $workOrder): bool
    {
        if ($user->role === 'ENGINEER') {
            return true;
        }

        if ($user->role === 'TECHNICIAN') {
            // Technicians may only update progress on their assigned orders
            return $workOrder->assigned_to_user_id === $user->id;
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | assign – assign a work order to a technician
    |--------------------------------------------------------------------------
    */
    public function assign(User $user, WorkOrder $workOrder): bool
    {
        return $user->role === 'ENGINEER';
    }

    /*
    |--------------------------------------------------------------------------
    | close – close / resolve a work order
    |--------------------------------------------------------------------------
    */
    public function close(User $user, WorkOrder $workOrder): bool
    {
        return in_array($user->role, ['ENGINEER']);
    }

    /*
    |--------------------------------------------------------------------------
    | delete – hard-delete (admin-only, handled by before())
    |--------------------------------------------------------------------------
    */
    public function delete(User $user, WorkOrder $workOrder): bool
    {
        return false; // Only ADMIN/MANAGER via before()
    }

    /*
    |--------------------------------------------------------------------------
    | restore / forceDelete
    |--------------------------------------------------------------------------
    */
    public function restore(User $user, WorkOrder $workOrder): bool
    {
        return false;
    }

    public function forceDelete(User $user, WorkOrder $workOrder): bool
    {
        return false;
    }
}

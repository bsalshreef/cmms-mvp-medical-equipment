<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrderPart;

/**
 * WorkOrderPartPolicy
 *
 * ADMIN / MANAGER  – full access (via before())
 * STORE            – full CRUD (issue / return / delete spare-part usage records)
 * ENGINEER         – create + view + delete
 * TECHNICIAN       – create on their assigned work orders; view only otherwise
 * REQUESTER        – no access
 * VENDOR_COORDINATOR – no access
 */
class WorkOrderPartPolicy
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
        return in_array($user->role, ['STORE', 'ENGINEER', 'TECHNICIAN']);
    }

    public function view(User $user, WorkOrderPart $workOrderPart): bool
    {
        if (in_array($user->role, ['STORE', 'ENGINEER'])) {
            return true;
        }

        if ($user->role === 'TECHNICIAN') {
            return $workOrderPart->workOrder->assigned_to_user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['STORE', 'ENGINEER', 'TECHNICIAN']);
    }

    public function update(User $user, WorkOrderPart $workOrderPart): bool
    {
        return in_array($user->role, ['STORE', 'ENGINEER']);
    }

    public function delete(User $user, WorkOrderPart $workOrderPart): bool
    {
        return in_array($user->role, ['STORE', 'ENGINEER']);
    }
}

<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrderAttachment;

/**
 * WorkOrderAttachmentPolicy
 *
 * ADMIN / MANAGER  – full access (via before())
 * ENGINEER         – upload + delete any attachment on any work order
 * TECHNICIAN       – upload attachments on their assigned work orders; view any
 * REQUESTER        – upload attachments on their own work orders; view their own
 * STORE            – view only
 * VENDOR_COORDINATOR – view only
 */
class WorkOrderAttachmentPolicy
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
        return in_array($user->role, [
            'ENGINEER', 'TECHNICIAN', 'REQUESTER',
            'STORE', 'VENDOR_COORDINATOR',
        ]);
    }

    public function view(User $user, WorkOrderAttachment $attachment): bool
    {
        if (in_array($user->role, ['ENGINEER', 'STORE', 'VENDOR_COORDINATOR'])) {
            return true;
        }

        if ($user->role === 'TECHNICIAN') {
            return $attachment->workOrder->assigned_to_user_id === $user->id;
        }

        if ($user->role === 'REQUESTER') {
            return $attachment->workOrder->created_by_user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['ENGINEER', 'TECHNICIAN', 'REQUESTER']);
    }

    public function delete(User $user, WorkOrderAttachment $attachment): bool
    {
        if ($user->role === 'ENGINEER') {
            return true;
        }

        // Uploader can delete their own attachment
        return $attachment->uploaded_by_user_id === $user->id;
    }
}

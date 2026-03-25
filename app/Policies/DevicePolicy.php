<?php

namespace App\Policies;

use App\Models\Device;
use App\Models\User;

/**
 * DevicePolicy
 *
 * ADMIN / MANAGER  – full access (via before())
 * ENGINEER         – view + create + update
 * TECHNICIAN       – view only
 * REQUESTER        – view only
 * STORE            – view only
 * VENDOR_COORDINATOR – view only
 */
class DevicePolicy
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

    public function view(User $user, Device $device): bool
    {
        return in_array($user->role, [
            'ENGINEER', 'TECHNICIAN', 'REQUESTER',
            'STORE', 'VENDOR_COORDINATOR',
        ]);
    }

    public function create(User $user): bool
    {
        return $user->role === 'ENGINEER';
    }

    public function update(User $user, Device $device): bool
    {
        return $user->role === 'ENGINEER';
    }

    public function delete(User $user, Device $device): bool
    {
        return false; // ADMIN/MANAGER only via before()
    }
}

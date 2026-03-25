<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;

/**
 * VendorPolicy
 *
 * ADMIN / MANAGER      – full access (via before())
 * VENDOR_COORDINATOR   – full CRUD on vendors
 * ENGINEER             – view only
 * STORE                – view only
 * TECHNICIAN           – no access
 * REQUESTER            – no access
 */
class VendorPolicy
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
        return in_array($user->role, ['VENDOR_COORDINATOR', 'ENGINEER', 'STORE']);
    }

    public function view(User $user, Vendor $vendor): bool
    {
        return in_array($user->role, ['VENDOR_COORDINATOR', 'ENGINEER', 'STORE']);
    }

    public function create(User $user): bool
    {
        return $user->role === 'VENDOR_COORDINATOR';
    }

    public function update(User $user, Vendor $vendor): bool
    {
        return $user->role === 'VENDOR_COORDINATOR';
    }

    public function delete(User $user, Vendor $vendor): bool
    {
        return false; // ADMIN/MANAGER only via before()
    }
}

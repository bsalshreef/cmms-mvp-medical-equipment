<?php

namespace App\Policies;

use App\Models\SparePart;
use App\Models\User;

/**
 * SparePartPolicy
 *
 * ADMIN / MANAGER      – full access (via before())
 * STORE                – full CRUD on spare parts
 * ENGINEER             – view + create + update
 * VENDOR_COORDINATOR   – view only
 * TECHNICIAN           – view only
 * REQUESTER            – no access
 */
class SparePartPolicy
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
            'STORE', 'ENGINEER', 'VENDOR_COORDINATOR', 'TECHNICIAN',
        ]);
    }

    public function view(User $user, SparePart $sparePart): bool
    {
        return in_array($user->role, [
            'STORE', 'ENGINEER', 'VENDOR_COORDINATOR', 'TECHNICIAN',
        ]);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['STORE', 'ENGINEER']);
    }

    public function update(User $user, SparePart $sparePart): bool
    {
        return in_array($user->role, ['STORE', 'ENGINEER']);
    }

    public function delete(User $user, SparePart $sparePart): bool
    {
        return $user->role === 'STORE'; // ADMIN/MANAGER via before()
    }

    /*
    |--------------------------------------------------------------------------
    | adjustStock – a custom ability for stock in/out transactions
    |--------------------------------------------------------------------------
    */
    public function adjustStock(User $user, SparePart $sparePart): bool
    {
        return in_array($user->role, ['STORE', 'ENGINEER']);
    }
}

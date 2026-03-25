<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Password123!');

        $users = [
            ['name' => 'System Admin',           'email' => 'admin@cmms.test',      'role' => 'ADMIN'],
            ['name' => 'Operations Manager',     'email' => 'manager@cmms.test',    'role' => 'MANAGER'],
            ['name' => 'Biomedical Engineer',    'email' => 'engineer@cmms.test',   'role' => 'ENGINEER'],
            ['name' => 'Maintenance Technician', 'email' => 'technician@cmms.test', 'role' => 'TECHNICIAN'],
            ['name' => 'Department Requester',   'email' => 'requester@cmms.test',  'role' => 'REQUESTER'],
            ['name' => 'Store Officer',          'email' => 'store@cmms.test',      'role' => 'STORE'],
            ['name' => 'Vendor Coordinator',     'email' => 'vendor@cmms.test',     'role' => 'VENDOR_COORDINATOR'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name'     => $user['name'],
                    'role'     => $user['role'],
                    'password' => $password,
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            VendorSeeder::class,
            DeviceCategorySeeder::class,
            ServiceCategorySeeder::class,
            MaintenanceTypeSeeder::class,
            DeviceSeeder::class,
            SparePartSeeder::class,
            WorkOrderSeeder::class,
            MaintenancePlanSeeder::class,
        ]);
    }
}

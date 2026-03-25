<?php

namespace Database\Seeders;

use App\Models\DeviceCategory;
use Illuminate\Database\Seeder;

class DeviceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Imaging'],
            ['name' => 'Laboratory'],
            ['name' => 'ICU'],
            ['name' => 'Patient Monitoring'],
            ['name' => 'CSSD'],
        ];

        foreach ($categories as $category) {
            DeviceCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}

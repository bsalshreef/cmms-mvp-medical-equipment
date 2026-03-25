<?php

namespace Database\Seeders;

use App\Models\MaintenanceType;
use Illuminate\Database\Seeder;

class MaintenanceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name_en' => 'Electrical',  'name_ar' => 'كهربائية'],
            ['name_en' => 'Mechanical',  'name_ar' => 'ميكانيكية'],
            ['name_en' => 'Software',    'name_ar' => 'برمجية'],
            ['name_en' => 'Calibration', 'name_ar' => 'معايرة'],
            ['name_en' => 'General',     'name_ar' => 'عامة'],
        ];

        foreach ($items as $item) {
            MaintenanceType::updateOrCreate(
                ['name_en' => $item['name_en']],
                $item
            );
        }
    }
}

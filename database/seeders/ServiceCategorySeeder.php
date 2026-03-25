<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name_en' => 'Corrective Maintenance', 'name_ar' => 'صيانة تصحيحية'],
            ['name_en' => 'Preventive Maintenance',  'name_ar' => 'صيانة وقائية'],
            ['name_en' => 'Installation',            'name_ar' => 'تركيب'],
            ['name_en' => 'Inspection',              'name_ar' => 'فحص'],
            ['name_en' => 'Calibration',             'name_ar' => 'معايرة'],
            ['name_en' => 'Spare Parts Request',     'name_ar' => 'طلب قطع غيار'],
        ];

        foreach ($items as $item) {
            ServiceCategory::updateOrCreate(
                ['name_en' => $item['name_en']],
                $item
            );
        }
    }
}

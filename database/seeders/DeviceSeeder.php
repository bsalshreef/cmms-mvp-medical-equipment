<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    public function run(): void
    {
        $lab = DeviceCategory::where('name', 'Laboratory')->first();
        $icu = DeviceCategory::where('name', 'ICU')->first();
        $img = DeviceCategory::where('name', 'Imaging')->first();
        $mon = DeviceCategory::where('name', 'Patient Monitoring')->first();

        $vendor1 = Vendor::where('vendor_code', 'VND-001')->first();
        $vendor2 = Vendor::where('vendor_code', 'VND-002')->first();
        $vendor3 = Vendor::where('vendor_code', 'VND-003')->first();

        $devices = [
            [
                'device_code'    => 'DEV-001',
                'name'           => 'Hematology Analyzer',
                'category_id'    => $lab?->id,
                'vendor_id'      => $vendor3?->id,
                'department'     => 'Laboratory',
                'location'       => 'Lab Room 1',
                'critical_level' => 'HIGH',
            ],
            [
                'device_code'    => 'DEV-002',
                'name'           => 'Biochemistry Analyzer',
                'category_id'    => $lab?->id,
                'vendor_id'      => $vendor3?->id,
                'department'     => 'Laboratory',
                'location'       => 'Lab Room 2',
                'critical_level' => 'HIGH',
            ],
            [
                'device_code'    => 'DEV-003',
                'name'           => 'Portable X-Ray',
                'category_id'    => $img?->id,
                'vendor_id'      => $vendor1?->id,
                'department'     => 'Radiology',
                'location'       => 'Radiology 1',
                'critical_level' => 'MEDIUM',
            ],
            [
                'device_code'    => 'DEV-004',
                'name'           => 'Infusion Pump',
                'category_id'    => $icu?->id,
                'vendor_id'      => $vendor2?->id,
                'department'     => 'ICU',
                'location'       => 'ICU Bed 4',
                'critical_level' => 'HIGH',
            ],
            [
                'device_code'    => 'DEV-005',
                'name'           => 'Patient Monitor',
                'category_id'    => $mon?->id,
                'vendor_id'      => $vendor2?->id,
                'department'     => 'ICU',
                'location'       => 'ICU Bed 2',
                'critical_level' => 'HIGH',
            ],
            [
                'device_code'    => 'DEV-006',
                'name'           => 'Centrifuge',
                'category_id'    => $lab?->id,
                'vendor_id'      => $vendor3?->id,
                'department'     => 'Laboratory',
                'location'       => 'Lab Room 3',
                'critical_level' => 'LOW',
            ],
        ];

        foreach ($devices as $device) {
            Device::updateOrCreate(
                ['device_code' => $device['device_code']],
                $device
            );
        }
    }
}

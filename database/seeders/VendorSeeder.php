<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'vendor_code'    => 'VND-001',
                'name'           => 'Siemens Healthineers',
                'contact_person' => 'Ahmed Ali',
                'phone'          => '0500000001',
                'email'          => 'siemens@cmms.test',
                'address'        => 'Riyadh',
            ],
            [
                'vendor_code'    => 'VND-002',
                'name'           => 'Philips Medical Systems',
                'contact_person' => 'Mona Hassan',
                'phone'          => '0500000002',
                'email'          => 'philips@cmms.test',
                'address'        => 'Jeddah',
            ],
            [
                'vendor_code'    => 'VND-003',
                'name'           => 'LabTech Supplies',
                'contact_person' => 'Khalid Omar',
                'phone'          => '0500000003',
                'email'          => 'labtech@cmms.test',
                'address'        => 'Dammam',
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::updateOrCreate(
                ['vendor_code' => $vendor['vendor_code']],
                $vendor
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\SparePart;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class SparePartSeeder extends Seeder
{
    public function run(): void
    {
        $vendor = Vendor::where('vendor_code', 'VND-003')->first();

        $parts = [
            [
                'part_code'        => 'SP-001',
                'name'             => 'Sample Probe',
                'current_quantity' => 12,
                'minimum_quantity' => 5,
                'unit_cost'        => 450.00,   // column name in schema
                'vendor_id'        => $vendor?->id,
            ],
            [
                'part_code'        => 'SP-002',
                'name'             => 'Pump Motor',
                'current_quantity' => 3,
                'minimum_quantity' => 5,
                'unit_cost'        => 1800.00,
                'vendor_id'        => $vendor?->id,
            ],
            [
                'part_code'        => 'SP-003',
                'name'             => 'Power Supply Unit',
                'current_quantity' => 8,
                'minimum_quantity' => 3,
                'unit_cost'        => 950.00,
                'vendor_id'        => $vendor?->id,
            ],
            [
                'part_code'        => 'SP-004',
                'name'             => 'Temperature Sensor',
                'current_quantity' => 2,
                'minimum_quantity' => 4,
                'unit_cost'        => 220.00,
                'vendor_id'        => $vendor?->id,
            ],
            [
                'part_code'        => 'SP-005',
                'name'             => 'Printer Module',
                'current_quantity' => 6,
                'minimum_quantity' => 2,
                'unit_cost'        => 400.00,
                'vendor_id'        => $vendor?->id,
            ],
        ];

        foreach ($parts as $part) {
            SparePart::updateOrCreate(
                ['part_code' => $part['part_code']],
                $part
            );
        }
    }
}

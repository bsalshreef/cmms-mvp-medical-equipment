<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\MaintenancePlan;
use App\Models\MaintenanceType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * MaintenancePlanSeeder
 *
 * Schema reconciliation notes:
 *  - `plan_name`             → removed (not in schema; description is in notes)
 *  - `frequency`             → `frequency_type` (enum: DAILY/WEEKLY/MONTHLY/QUARTERLY/ANNUAL)
 *  - `start_date`            → `last_done_date` (closest semantic match)
 *  - `assigned_to_user_id`   → removed (not in maintenance_plans schema)
 *  - `status`                → removed (not in schema; is_active boolean used instead)
 *  - 'DUE_SOON' / 'SCHEDULED' → is_active = true; next_due_date drives urgency
 */
class MaintenancePlanSeeder extends Seeder
{
    public function run(): void
    {
        $type    = MaintenanceType::first();
        $devices = Device::take(5)->get();

        foreach ($devices as $index => $device) {
            MaintenancePlan::create([
                'device_id'           => $device->id,
                'maintenance_type_id' => $type?->id,
                'frequency_type'      => 'QUARTERLY',           // corrected column name
                'frequency_value'     => 1,
                'last_done_date'      => Carbon::now()->subMonths(2)->toDateString(),
                'next_due_date'       => Carbon::now()->addDays(($index + 1) * 3)->toDateString(),
                'is_active'           => true,
                'notes'               => 'Quarterly Preventive Maintenance – ' . $device->device_code,
            ]);
        }
    }
}

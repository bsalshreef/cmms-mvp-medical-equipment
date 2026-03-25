<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\ServiceCategory;
use App\Models\MaintenanceType;
use App\Models\SparePart;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderPart;
use App\Models\WorkOrderStatusHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * WorkOrderSeeder
 *
 * Schema reconciliation notes (original seeder → corrected):
 *  - `request_date`          → `created_at` (standard Laravel timestamp; no separate column)
 *  - `requester_name`        → removed (use created_by_user_id relationship)
 *  - `requester_department`  → removed (use device.department)
 *  - `requester_phone`       → removed (not in schema)
 *  - `request_description`   → `description`
 *  - `resolution_status`     → kept; value 'FIXED' corrected to 'RESOLVED' (valid enum)
 *  - `approval_status`       → removed (not in schema)
 *  - `completion_status`     → removed (not in schema; covered by workflow_status)
 *  - `completion_note`       → `resolution_notes`
 *  - `maintenance_type_id`   → removed from work_orders (belongs to maintenance_plans)
 *  - WorkOrderPart `quantity` → `quantity_used`
 *  - WorkOrderPart `unit_price` / `total_price` → `unit_cost` (total is derived)
 *  - `workflow_status` 'ASSIGNED' → 'IN_PROGRESS' (not a valid enum value)
 *  - $part->decrement('current_quantity', 1) added after WorkOrderPart::create()
 */
class WorkOrderSeeder extends Seeder
{
    public function run(): void
    {
        $requester  = User::where('role', 'REQUESTER')->first();
        $engineer   = User::where('role', 'ENGINEER')->first();
        $technician = User::where('role', 'TECHNICIAN')->first();

        $devices           = Device::all();
        $serviceCategories = ServiceCategory::all();
        $parts             = SparePart::all()->keyBy('part_code');

        if ($devices->isEmpty() || $serviceCategories->isEmpty()) {
            $this->command->warn('WorkOrderSeeder skipped: devices or service categories not found.');
            return;
        }

        // Seed rows — 'ASSIGNED' replaced with 'IN_PROGRESS' (valid enum)
        $seedRows = [
            ['status' => 'OPEN',        'priority' => 'HIGH',     'days_ago' => 0,  'desc' => 'Analyzer error code E17'],
            ['status' => 'IN_PROGRESS', 'priority' => 'MEDIUM',   'days_ago' => 1,  'desc' => 'Printer not responding'],
            ['status' => 'IN_PROGRESS', 'priority' => 'CRITICAL', 'days_ago' => 2,  'desc' => 'Device shutdown during operation'],
            ['status' => 'ON_HOLD',     'priority' => 'HIGH',     'days_ago' => 4,  'desc' => 'Awaiting vendor confirmation'],
            ['status' => 'CLOSED',      'priority' => 'LOW',      'days_ago' => 7,  'desc' => 'Preventive maintenance completed'],
            ['status' => 'CLOSED',      'priority' => 'MEDIUM',   'days_ago' => 10, 'desc' => 'Sensor replaced successfully'],
            ['status' => 'CANCELLED',   'priority' => 'LOW',      'days_ago' => 12, 'desc' => 'Duplicate request'],
        ];

        foreach (range(1, 20) as $i) {
            $row     = $seedRows[($i - 1) % count($seedRows)];
            $device  = $devices[($i - 1) % $devices->count()];
            $service = $serviceCategories[($i - 1) % $serviceCategories->count()];

            $requestDate = Carbon::now()->subDays($row['days_ago']);

            $isClosed    = $row['status'] === 'CLOSED';
            $isAssigned  = in_array($row['status'], ['IN_PROGRESS', 'ON_HOLD', 'CLOSED']);

            $workOrder = WorkOrder::create([
                'device_id'            => $device->id,
                'service_category_id'  => $service->id,
                'description'          => $row['desc'],           // corrected column name
                'priority'             => $row['priority'],
                'workflow_status'      => $row['status'],
                'resolution_status'    => $isClosed ? 'RESOLVED' : null,  // corrected enum value
                'created_by_user_id'   => $requester?->id,
                'assigned_to_user_id'  => $isAssigned ? $technician?->id : null,
                'closed_by_user_id'    => $isClosed ? $engineer?->id : null,
                'closed_at'            => $isClosed
                    ? Carbon::now()->subDays(max(1, $row['days_ago'] - 1))
                    : null,
                'resolution_notes'     => $isClosed             // corrected column name
                    ? 'Completed during seeded demo flow.'
                    : null,
                'created_at'           => $requestDate,
                'updated_at'           => $requestDate,
            ]);

            // ── Status history ────────────────────────────────────────────
            WorkOrderStatusHistory::create([
                'work_order_id'      => $workOrder->id,
                'old_status'         => null,
                'new_status'         => 'OPEN',
                'changed_by_user_id' => $requester?->id,
                'change_note'        => 'Work order created',
                'created_at'         => $requestDate,
                'updated_at'         => $requestDate,
            ]);

            if ($row['status'] !== 'OPEN') {
                WorkOrderStatusHistory::create([
                    'work_order_id'      => $workOrder->id,
                    'old_status'         => 'OPEN',
                    'new_status'         => $row['status'],
                    'changed_by_user_id' => $engineer?->id,
                    'change_note'        => 'Status advanced in seeded scenario',
                    'created_at'         => $requestDate->copy()->addHours(4),
                    'updated_at'         => $requestDate->copy()->addHours(4),
                ]);
            }

            // ── Spare part issue on closed orders ─────────────────────────
            if ($isClosed && $parts->has('SP-004')) {
                $part = $parts['SP-004'];

                WorkOrderPart::create([
                    'work_order_id' => $workOrder->id,
                    'spare_part_id' => $part->id,
                    'quantity_used' => 1,                       // corrected column name
                    'unit_cost'     => $part->unit_cost,        // corrected column name
                    'notes'         => 'Replaced during repair',
                ]);

                // Decrement stock — as specified
                $part->decrement('current_quantity', 1);
            }
        }
    }
}

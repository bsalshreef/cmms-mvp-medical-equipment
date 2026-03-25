<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Creates the two read-only MySQL views used by DashboardController.
 *
 * v_open_work_orders        – all non-terminal work orders with device info
 * v_device_failure_summary  – per-device work-order count (all time)
 *
 * Both views exclude soft-deleted work_orders (deleted_at IS NULL).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── v_open_work_orders ────────────────────────────────────────────────
        DB::statement("
            CREATE OR REPLACE VIEW v_open_work_orders AS
            SELECT
                wo.id,
                wo.created_at        AS request_date,
                wo.workflow_status,
                wo.priority,
                wo.assigned_to_user_id,
                d.id                 AS device_id,
                d.name               AS device_name,
                d.device_code,
                d.department,
                d.critical_level
            FROM work_orders wo
            LEFT JOIN devices d ON d.id = wo.device_id
            WHERE wo.workflow_status NOT IN ('COMPLETED', 'CLOSED', 'CANCELLED')
              AND wo.deleted_at IS NULL
        ");

        // ── v_device_failure_summary ─────────────────────────────────────────
        DB::statement("
            CREATE OR REPLACE VIEW v_device_failure_summary AS
            SELECT
                d.id             AS device_id,
                d.name           AS device_name,
                d.device_code,
                d.department,
                d.critical_level,
                COUNT(wo.id)     AS total_work_orders,
                SUM(
                    CASE WHEN wo.workflow_status NOT IN ('COMPLETED','CLOSED','CANCELLED')
                    THEN 1 ELSE 0 END
                )                AS open_work_orders
            FROM devices d
            LEFT JOIN work_orders wo
                   ON wo.device_id = d.id
                  AND wo.deleted_at IS NULL
            WHERE d.deleted_at IS NULL
            GROUP BY d.id, d.name, d.device_code, d.department, d.critical_level
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_device_failure_summary');
        DB::statement('DROP VIEW IF EXISTS v_open_work_orders');
    }
};

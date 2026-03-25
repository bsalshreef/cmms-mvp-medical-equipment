<?php

namespace App\Http\Controllers;

use App\Models\MaintenancePlan;
use App\Models\SparePart;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        /*
        |----------------------------------------------------------------------
        | Role-scoped base query factory
        | REQUESTER  → own work orders only
        | TECHNICIAN → assigned work orders only
        | All others → full dataset
        |----------------------------------------------------------------------
        */
        $baseQuery = fn () => WorkOrder::query()
            ->when($user->role === 'REQUESTER',  fn ($q) => $q->where('created_by_user_id',  $user->id))
            ->when($user->role === 'TECHNICIAN', fn ($q) => $q->where('assigned_to_user_id', $user->id));

        /*
        |----------------------------------------------------------------------
        | KPI Cards
        |----------------------------------------------------------------------
        */
        $kpis = [
            'open_work_orders' => $baseQuery()
                ->whereNotIn('workflow_status', ['COMPLETED', 'CLOSED', 'CANCELLED'])
                ->count(),

            'in_progress_work_orders' => $baseQuery()
                ->where('workflow_status', 'IN_PROGRESS')
                ->count(),

            'overdue_work_orders' => $baseQuery()
                ->whereNotIn('workflow_status', ['COMPLETED', 'CLOSED', 'CANCELLED'])
                ->whereNotNull('target_start_datetime')
                ->where('target_start_datetime', '<', now())
                ->count(),

            'ppm_this_month' => DB::table('maintenance_plan_executions')
                ->whereMonth('scheduled_date', now()->month)
                ->whereYear('scheduled_date', now()->year)
                ->count(),

            'critical_devices_open' => $baseQuery()
                ->whereNotIn('workflow_status', ['COMPLETED', 'CLOSED', 'CANCELLED'])
                ->whereHas('device', fn ($q) => $q->where('critical_level', 'HIGH'))
                ->distinct('device_id')
                ->count('device_id'),

            // Null means "not shown" for roles without stock visibility
            'low_stock_spare_parts' => in_array($user->role, ['ADMIN', 'MANAGER', 'STORE', 'ENGINEER'])
                ? SparePart::whereColumn('current_quantity', '<=', 'minimum_quantity')->count()
                : null,

            // FIX: guard against NULL closed_at with whereNotNull before month filter
            'completed_this_month' => $baseQuery()
                ->whereIn('workflow_status', ['COMPLETED', 'CLOSED'])
                ->whereNotNull('closed_at')
                ->whereMonth('closed_at', now()->month)
                ->whereYear('closed_at', now()->year)
                ->count(),
        ];

        /*
        |----------------------------------------------------------------------
        | Work Orders by Status
        |----------------------------------------------------------------------
        */
        $woByStatus = $baseQuery()
            ->select('workflow_status', DB::raw('COUNT(*) as total'))
            ->groupBy('workflow_status')
            ->orderByDesc('total')
            ->get();

        /*
        |----------------------------------------------------------------------
        | Work Orders by Service Category
        |----------------------------------------------------------------------
        */
        $woByService = $baseQuery()
            ->select('service_category_id', DB::raw('COUNT(*) as total'))
            ->with('serviceCategory:id,name_ar,name_en')
            ->groupBy('service_category_id')
            ->orderByDesc('total')
            ->get();

        /*
        |----------------------------------------------------------------------
        | Open Work Orders table (latest 10)
        |----------------------------------------------------------------------
        */
        $openWorkOrders = $baseQuery()
            ->with([
                'device:id,device_code,name,department',
                'creator:id,name',
                'assignee:id,name',
            ])
            ->whereNotIn('workflow_status', ['COMPLETED', 'CLOSED', 'CANCELLED'])
            ->latest('created_at')
            ->limit(10)
            ->get();

        /*
        |----------------------------------------------------------------------
        | Top Failure Devices  (ADMIN / MANAGER / ENGINEER only)
        |----------------------------------------------------------------------
        */
        $topFailureDevices = collect();
        if (in_array($user->role, ['ADMIN', 'MANAGER', 'ENGINEER'])) {
            $topFailureDevices = DB::table('v_device_failure_summary')
                ->orderByDesc('total_work_orders')
                ->limit(10)
                ->get();
        }

        /*
        |----------------------------------------------------------------------
        | Critical Devices with open work orders
        |----------------------------------------------------------------------
        */
        $criticalDevices = $baseQuery()
            ->select('device_id', DB::raw('COUNT(*) as open_count'))
            ->whereNotIn('workflow_status', ['COMPLETED', 'CLOSED', 'CANCELLED'])
            ->whereHas('device', fn ($q) => $q->where('critical_level', 'HIGH'))
            ->with('device:id,device_code,name,department,critical_level')
            ->groupBy('device_id')
            ->orderByDesc('open_count')
            ->get();

        /*
        |----------------------------------------------------------------------
        | Upcoming PPM  (ADMIN / MANAGER / ENGINEER / TECHNICIAN)
        |----------------------------------------------------------------------
        */
        $upcomingPpm = collect();
        if (in_array($user->role, ['ADMIN', 'MANAGER', 'ENGINEER', 'TECHNICIAN'])) {
            $upcomingPpm = MaintenancePlan::with([
                'device:id,device_code,name,department',
                'maintenanceType:id,name_ar,name_en',
            ])
                ->where('is_active', true)
                ->where('next_due_date', '>=', now()->startOfDay())
                ->orderBy('next_due_date')
                ->limit(10)
                ->get();
        }

        /*
        |----------------------------------------------------------------------
        | Low Stock Spare Parts  (ADMIN / MANAGER / STORE / ENGINEER)
        |----------------------------------------------------------------------
        */
        $lowStockParts = collect();
        if (in_array($user->role, ['ADMIN', 'MANAGER', 'STORE', 'ENGINEER'])) {
            $lowStockParts = SparePart::with('vendor:id,name')
                ->whereColumn('current_quantity', '<=', 'minimum_quantity')
                ->orderBy('current_quantity')
                ->limit(10)
                ->get();
        }

        /*
        |----------------------------------------------------------------------
        | Recent Activity
        |----------------------------------------------------------------------
        */
        $recentWorkOrders = $baseQuery()
            ->with([
                'device:id,device_code,name',
                'creator:id,name',
                'assignee:id,name',
            ])
            ->latest('created_at')
            ->limit(10)
            ->get();

        /*
        |----------------------------------------------------------------------
        | Work Orders by Priority  (open only)
        | FIX: replaced MySQL-specific FIELD() with portable CASE expression
        |----------------------------------------------------------------------
        */
        $woByPriority = $baseQuery()
            ->select('priority', DB::raw('COUNT(*) as total'))
            ->whereNotIn('workflow_status', ['COMPLETED', 'CLOSED', 'CANCELLED'])
            ->groupBy('priority')
            ->orderByRaw("CASE priority
                WHEN 'CRITICAL' THEN 1
                WHEN 'HIGH'     THEN 2
                WHEN 'MEDIUM'   THEN 3
                WHEN 'LOW'      THEN 4
                ELSE 5 END")
            ->get();

        /*
        |----------------------------------------------------------------------
        | Monthly Trend – last 6 months  (ADMIN / MANAGER / ENGINEER)
        |----------------------------------------------------------------------
        */
        $monthlyTrend = collect();
        if (in_array($user->role, ['ADMIN', 'MANAGER', 'ENGINEER'])) {
            $monthlyTrend = DB::table('work_orders')
                ->select(
                    DB::raw('YEAR(created_at)  AS year'),
                    DB::raw('MONTH(created_at) AS month'),
                    DB::raw('COUNT(*)          AS total')
                )
                ->whereNull('deleted_at')
                ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
                ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
                ->orderBy(DB::raw('YEAR(created_at)'))
                ->orderBy(DB::raw('MONTH(created_at)'))
                ->get();
        }

        return view('dashboard.index', compact(
            'kpis',
            'woByStatus',
            'woByService',
            'openWorkOrders',
            'topFailureDevices',
            'criticalDevices',
            'upcomingPpm',
            'lowStockParts',
            'recentWorkOrders',
            'woByPriority',
            'monthlyTrend'
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\ServiceCategory;
use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkOrderController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(WorkOrder::class, 'work_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Index – list work orders (filtered by role)
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = WorkOrder::with(['device:id,device_code,name,department', 'creator:id,name', 'assignee:id,name'])
            ->latest('created_at');

        // REQUESTER sees only their own requests
        if ($user->role === 'REQUESTER') {
            $query->where('created_by_user_id', $user->id);
        }

        // TECHNICIAN sees only assigned work orders
        if ($user->role === 'TECHNICIAN') {
            $query->where('assigned_to_user_id', $user->id);
        }

        // Optional filters
        if ($request->filled('status')) {
            $query->where('workflow_status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('device_id')) {
            $query->where('device_id', $request->device_id);
        }

        $workOrders = $query->paginate(20)->withQueryString();

        return view('work-orders.index', compact('workOrders'));
    }

    /*
    |--------------------------------------------------------------------------
    | Create / Store
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $devices    = Device::orderBy('name')->get(['id', 'device_code', 'name', 'department']);
        $categories = ServiceCategory::orderBy('name_ar')->get(['id', 'name_ar', 'name_en']);

        return view('work-orders.create', compact('devices', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_id'           => 'required|exists:devices,id',
            'service_category_id' => 'nullable|exists:service_categories,id',
            'priority'            => 'required|in:LOW,MEDIUM,HIGH,CRITICAL',
            'description'         => 'required|string|max:2000',
            'target_start_datetime' => 'nullable|date',
        ]);

        $validated['created_by_user_id'] = Auth::id();
        $validated['workflow_status']    = 'OPEN';

        $workOrder = WorkOrder::create($validated);

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'تم إنشاء أمر العمل بنجاح.');
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */
    public function show(WorkOrder $workOrder)
    {
        $workOrder->load([
            'device',
            'serviceCategory',
            'creator:id,name',
            'assignee:id,name',
            'closer:id,name',
            'attachments.uploader:id,name',
            'parts.sparePart',
        ]);

        return view('work-orders.show', compact('workOrder'));
    }

    /*
    |--------------------------------------------------------------------------
    | Edit / Update
    |--------------------------------------------------------------------------
    */
    public function edit(WorkOrder $workOrder)
    {
        $devices    = Device::orderBy('name')->get(['id', 'device_code', 'name', 'department']);
        $categories = ServiceCategory::orderBy('name_ar')->get(['id', 'name_ar', 'name_en']);

        return view('work-orders.edit', compact('workOrder', 'devices', 'categories'));
    }

    public function update(Request $request, WorkOrder $workOrder)
    {
        $validated = $request->validate([
            'device_id'             => 'required|exists:devices,id',
            'service_category_id'   => 'nullable|exists:service_categories,id',
            'priority'              => 'required|in:LOW,MEDIUM,HIGH,CRITICAL',
            'description'           => 'required|string|max:2000',
            'target_start_datetime' => 'nullable|date',
            'workflow_status'       => 'sometimes|in:OPEN,IN_PROGRESS,ON_HOLD,COMPLETED,CLOSED,CANCELLED',
            'resolution_status'     => 'nullable|in:RESOLVED,UNRESOLVED,PARTIAL',
            'resolution_notes'      => 'nullable|string|max:2000',
        ]);

        $workOrder->update($validated);

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'تم تحديث أمر العمل بنجاح.');
    }

    /*
    |--------------------------------------------------------------------------
    | Assign – POST /work-orders/{work_order}/assign
    |--------------------------------------------------------------------------
    */
    public function assign(Request $request, WorkOrder $workOrder)
    {
        $this->authorize('assign', $workOrder);

        $request->validate([
            'assigned_to_user_id' => 'required|exists:users,id',
        ]);

        $workOrder->update([
            'assigned_to_user_id' => $request->assigned_to_user_id,
            'workflow_status'     => 'IN_PROGRESS',
        ]);

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'تم تعيين الفني بنجاح.');
    }

    /*
    |--------------------------------------------------------------------------
    | Close – POST /work-orders/{work_order}/close
    |--------------------------------------------------------------------------
    */
    public function close(Request $request, WorkOrder $workOrder)
    {
        $this->authorize('close', $workOrder);

        $request->validate([
            'resolution_status' => 'required|in:RESOLVED,UNRESOLVED,PARTIAL',
            'resolution_notes'  => 'nullable|string|max:2000',
        ]);

        $workOrder->update([
            'workflow_status'   => 'CLOSED',
            'resolution_status' => $request->resolution_status,
            'resolution_notes'  => $request->resolution_notes,
            'closed_by_user_id' => Auth::id(),
            'closed_at'         => now(),
        ]);

        return redirect()->route('work-orders.show', $workOrder)
            ->with('success', 'تم إغلاق أمر العمل.');
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */
    public function destroy(WorkOrder $workOrder)
    {
        $workOrder->delete();

        return redirect()->route('work-orders.index')
            ->with('success', 'تم حذف أمر العمل.');
    }
}

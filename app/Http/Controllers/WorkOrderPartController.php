<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use App\Models\WorkOrder;
use App\Models\WorkOrderPart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WorkOrderPartController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Store – POST /work-orders/{work_order}/parts
    | Issue a spare part against a work order
    |--------------------------------------------------------------------------
    */
    public function store(Request $request, WorkOrder $workOrder)
    {
        $this->authorize('create', WorkOrderPart::class);

        // Technicians may only issue parts for their assigned work orders
        if (Auth::user()->role === 'TECHNICIAN' &&
            $workOrder->assigned_to_user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بإضافة قطع غيار لهذا الأمر.');
        }

        $request->validate([
            'spare_part_id' => 'required|exists:spare_parts,id',
            'quantity_used' => 'required|integer|min:1',
            'notes'         => 'nullable|string|max:500',
        ]);

        $sparePart = SparePart::findOrFail($request->spare_part_id);

        if ($sparePart->current_quantity < $request->quantity_used) {
            return back()->withErrors(['quantity_used' => 'الكمية المطلوبة تتجاوز المخزون المتاح (' . $sparePart->current_quantity . ').']);
        }

        DB::transaction(function () use ($request, $workOrder, $sparePart) {
            $workOrder->parts()->create([
                'spare_part_id'   => $sparePart->id,
                'quantity_used'   => $request->quantity_used,
                'unit_cost'       => $sparePart->unit_cost,
                'notes'           => $request->notes,
                'issued_by_user_id' => Auth::id(),
            ]);

            // Deduct from stock
            $sparePart->decrement('current_quantity', $request->quantity_used);
        });

        return back()->with('success', 'تم صرف القطعة وتسجيلها بنجاح.');
    }

    /*
    |--------------------------------------------------------------------------
    | Update – PUT /work-orders/{work_order}/parts/{part}
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, WorkOrder $workOrder, WorkOrderPart $part)
    {
        $this->authorize('update', $part);

        $request->validate([
            'quantity_used' => 'required|integer|min:1',
            'notes'         => 'nullable|string|max:500',
        ]);

        $sparePart   = $part->sparePart;
        $diff        = $request->quantity_used - $part->quantity_used;

        if ($diff > 0 && $sparePart->current_quantity < $diff) {
            return back()->withErrors(['quantity_used' => 'الكمية الإضافية تتجاوز المخزون المتاح.']);
        }

        DB::transaction(function () use ($request, $part, $sparePart, $diff) {
            $part->update([
                'quantity_used' => $request->quantity_used,
                'notes'         => $request->notes,
            ]);

            // Adjust stock accordingly
            if ($diff > 0) {
                $sparePart->decrement('current_quantity', $diff);
            } elseif ($diff < 0) {
                $sparePart->increment('current_quantity', abs($diff));
            }
        });

        return back()->with('success', 'تم تحديث سجل القطعة.');
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy – DELETE /work-orders/{work_order}/parts/{part}
    | Return the part to stock
    |--------------------------------------------------------------------------
    */
    public function destroy(WorkOrder $workOrder, WorkOrderPart $part)
    {
        $this->authorize('delete', $part);

        DB::transaction(function () use ($part) {
            // Return quantity to stock
            $part->sparePart->increment('current_quantity', $part->quantity_used);
            $part->delete();
        });

        return back()->with('success', 'تم حذف القطعة وإعادتها للمخزون.');
    }
}

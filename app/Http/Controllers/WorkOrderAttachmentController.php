<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkOrderAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WorkOrderAttachmentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Store – POST /work-orders/{work_order}/attachments
    |--------------------------------------------------------------------------
    */
    public function store(Request $request, WorkOrder $workOrder)
    {
        $this->authorize('create', WorkOrderAttachment::class);

        // Extra check: technicians can only attach to their assigned orders
        if (Auth::user()->role === 'TECHNICIAN' &&
            $workOrder->assigned_to_user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك برفع مرفقات لهذا الأمر.');
        }

        // Requesters can only attach to their own orders
        if (Auth::user()->role === 'REQUESTER' &&
            $workOrder->created_by_user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك برفع مرفقات لهذا الأمر.');
        }

        $request->validate([
            'file'        => 'required|file|max:20480', // 20 MB
            'description' => 'nullable|string|max:500',
        ]);

        $file     = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path     = $file->storeAs("work-orders/{$workOrder->id}/attachments", $filename, 'local');

        $workOrder->attachments()->create([
            'original_filename'    => $file->getClientOriginalName(),
            'stored_path'          => $path,
            'mime_type'            => $file->getMimeType(),
            'file_size'            => $file->getSize(),
            'description'          => $request->description,
            'uploaded_by_user_id'  => Auth::id(),
        ]);

        return back()->with('success', 'تم رفع المرفق بنجاح.');
    }

    /*
    |--------------------------------------------------------------------------
    | Download – GET /attachments/{attachment}/download
    |--------------------------------------------------------------------------
    */
    public function download(WorkOrderAttachment $attachment)
    {
        $this->authorize('view', $attachment);

        if (! Storage::disk('local')->exists($attachment->stored_path)) {
            abort(404, 'الملف غير موجود.');
        }

        return Storage::disk('local')->download(
            $attachment->stored_path,
            $attachment->original_filename
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy – DELETE /attachments/{attachment}
    |--------------------------------------------------------------------------
    */
    public function destroy(WorkOrderAttachment $attachment)
    {
        $this->authorize('delete', $attachment);

        Storage::disk('local')->delete($attachment->stored_path);
        $attachment->delete();

        return back()->with('success', 'تم حذف المرفق.');
    }
}

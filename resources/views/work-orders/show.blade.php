@extends('layouts.app')

@section('title', 'أمر العمل #' . $workOrder->id)

@section('content')
<div class="py-4" dir="rtl">

    {{-- ── Header ─────────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h5 class="mb-0">
                أمر العمل <span class="text-primary">#{{ $workOrder->id }}</span>
                <span class="badge badge-{{ $workOrder->workflow_status }} ms-2">{{ $workOrder->workflow_status }}</span>
                <span class="badge badge-{{ $workOrder->priority }}">{{ $workOrder->priority }}</span>
            </h5>
            <small class="text-muted">
                أُنشئ بواسطة {{ $workOrder->creator?->name ?? '—' }}
                في {{ $workOrder->created_at->format('Y-m-d H:i') }}
            </small>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @can('update', $workOrder)
            <a href="{{ route('work-orders.edit', $workOrder) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>تعديل
            </a>
            @endcan

            @can('assign', $workOrder)
            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalAssign">
                <i class="bi bi-person-check me-1"></i>تعيين فني
            </button>
            @endcan

            @can('close', $workOrder)
            @if(!in_array($workOrder->workflow_status, ['CLOSED','CANCELLED','COMPLETED']))
            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#modalClose">
                <i class="bi bi-check2-circle me-1"></i>إغلاق الأمر
            </button>
            @endif
            @endcan

            @can('delete', $workOrder)
            <form method="POST" action="{{ route('work-orders.destroy', $workOrder) }}"
                  onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash me-1"></i>حذف
                </button>
            </form>
            @endcan

            <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i>العودة
            </a>
        </div>
    </div>

    <div class="row g-3">

        {{-- ── Left column: details ────────────────────────────────────── --}}
        <div class="col-lg-8">

            {{-- Basic Info --}}
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white fw-semibold">تفاصيل الأمر</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">الجهاز</dt>
                        <dd class="col-sm-8">
                            {{ $workOrder->device?->name ?? '—' }}
                            <span class="text-muted small">({{ $workOrder->device?->device_code }})</span>
                        </dd>

                        <dt class="col-sm-4 text-muted">القسم</dt>
                        <dd class="col-sm-8">{{ $workOrder->device?->department ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted">نوع الخدمة</dt>
                        <dd class="col-sm-8">{{ $workOrder->serviceCategory?->name_ar ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted">الموعد المستهدف</dt>
                        <dd class="col-sm-8">{{ $workOrder->target_start_datetime?->format('Y-m-d H:i') ?? '—' }}</dd>

                        <dt class="col-sm-4 text-muted">الفني المعين</dt>
                        <dd class="col-sm-8">{{ $workOrder->assignee?->name ?? 'غير معين' }}</dd>

                        @if($workOrder->closed_at)
                        <dt class="col-sm-4 text-muted">تاريخ الإغلاق</dt>
                        <dd class="col-sm-8">{{ $workOrder->closed_at->format('Y-m-d H:i') }} ({{ $workOrder->closer?->name }})</dd>

                        <dt class="col-sm-4 text-muted">حالة الحل</dt>
                        <dd class="col-sm-8">{{ $workOrder->resolution_status ?? '—' }}</dd>
                        @endif

                        <dt class="col-sm-4 text-muted">وصف المشكلة</dt>
                        <dd class="col-sm-8">{{ $workOrder->description }}</dd>

                        @if($workOrder->resolution_notes)
                        <dt class="col-sm-4 text-muted">ملاحظات الإغلاق</dt>
                        <dd class="col-sm-8">{{ $workOrder->resolution_notes }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Attachments --}}
            @can('viewAny', App\Models\WorkOrderAttachment::class)
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">المرفقات ({{ $workOrder->attachments->count() }})</span>
                    @can('create', App\Models\WorkOrderAttachment::class)
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalUpload">
                        <i class="bi bi-upload me-1"></i>رفع ملف
                    </button>
                    @endcan
                </div>
                <div class="card-body p-0">
                    @if($workOrder->attachments->isEmpty())
                        <p class="text-muted text-center py-4 mb-0">لا توجد مرفقات</p>
                    @else
                    <ul class="list-group list-group-flush">
                        @foreach($workOrder->attachments as $att)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2">
                            <div>
                                <i class="bi bi-paperclip me-2 text-muted"></i>
                                <span class="small fw-semibold">{{ $att->original_filename }}</span>
                                @if($att->description)
                                    <span class="text-muted small ms-2">– {{ $att->description }}</span>
                                @endif
                                <br>
                                <span class="text-muted" style="font-size:.72rem">
                                    {{ $att->uploader?->name ?? '—' }} ·
                                    {{ $att->created_at->format('Y-m-d') }} ·
                                    {{ number_format($att->file_size / 1024, 1) }} KB
                                </span>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="{{ route('attachments.download', $att) }}"
                                   class="btn btn-sm btn-outline-secondary py-0 px-2">
                                    <i class="bi bi-download"></i>
                                </a>
                                @can('delete', $att)
                                <form method="POST" action="{{ route('attachments.destroy', $att) }}"
                                      onsubmit="return confirm('حذف المرفق؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger py-0 px-2">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
            @endcan

            {{-- Spare Parts --}}
            @can('viewAny', App\Models\WorkOrderPart::class)
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">قطع الغيار المستخدمة ({{ $workOrder->parts->count() }})</span>
                    @can('create', App\Models\WorkOrderPart::class)
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalIssuePart">
                        <i class="bi bi-plus-circle me-1"></i>صرف قطعة
                    </button>
                    @endcan
                </div>
                <div class="card-body p-0">
                    @if($workOrder->parts->isEmpty())
                        <p class="text-muted text-center py-4 mb-0">لا توجد قطع غيار مسجلة</p>
                    @else
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>القطعة</th>
                                <th class="text-center">الكمية</th>
                                <th class="text-center">التكلفة/وحدة</th>
                                <th class="text-center">الإجمالي</th>
                                <th>الصادر بواسطة</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workOrder->parts as $part)
                            <tr>
                                <td>
                                    <span class="fw-semibold small">{{ $part->sparePart?->name ?? '—' }}</span>
                                    <br><span class="text-muted" style="font-size:.72rem">{{ $part->sparePart?->part_code }}</span>
                                </td>
                                <td class="text-center">{{ $part->quantity_used }}</td>
                                <td class="text-center">{{ number_format($part->unit_cost, 2) }}</td>
                                <td class="text-center fw-semibold">
                                    {{ number_format($part->quantity_used * $part->unit_cost, 2) }}
                                </td>
                                <td class="small">{{ $part->issuedBy?->name ?? '—' }}</td>
                                <td>
                                    @can('delete', $part)
                                    <form method="POST"
                                          action="{{ route('work-orders.parts.destroy', [$workOrder, $part]) }}"
                                          onsubmit="return confirm('إعادة القطعة للمخزون وحذف السجل؟')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-0 px-2">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end fw-semibold">الإجمالي الكلي:</td>
                                <td class="text-center fw-bold text-primary">
                                    {{ number_format($workOrder->parts->sum(fn($p) => $p->quantity_used * $p->unit_cost), 2) }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                    @endif
                </div>
            </div>
            @endcan

        </div>{{-- /left col --}}

        {{-- ── Right column: timeline / meta ──────────────────────────── --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-semibold">معلومات إضافية</div>
                <div class="card-body">
                    <p class="mb-1 small text-muted">مستوى الخطورة للجهاز</p>
                    <p class="fw-semibold mb-3">
                        <span class="badge badge-{{ $workOrder->device?->critical_level ?? 'MEDIUM' }}">
                            {{ $workOrder->device?->critical_level ?? '—' }}
                        </span>
                    </p>

                    <p class="mb-1 small text-muted">الموقع</p>
                    <p class="fw-semibold mb-3">{{ $workOrder->device?->location ?? '—' }}</p>

                    <p class="mb-1 small text-muted">الرقم التسلسلي</p>
                    <p class="fw-semibold mb-3">{{ $workOrder->device?->serial_number ?? '—' }}</p>

                    <p class="mb-1 small text-muted">الشركة المصنعة / الموديل</p>
                    <p class="fw-semibold mb-0">
                        {{ $workOrder->device?->manufacturer ?? '—' }} /
                        {{ $workOrder->device?->model ?? '—' }}
                    </p>
                </div>
            </div>
        </div>

    </div>{{-- /row --}}

</div>

{{-- ══════════════════════════════════════════════════════════════════════
     MODALS
══════════════════════════════════════════════════════════════════════ --}}

{{-- Assign Modal --}}
@can('assign', $workOrder)
<div class="modal fade" id="modalAssign" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" dir="rtl">
            <form method="POST" action="{{ route('work-orders.assign', $workOrder) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">تعيين فني</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">اختر الفني</label>
                    <select name="assigned_to_user_id" class="form-select" required>
                        <option value="">-- اختر --</option>
                        @foreach(App\Models\User::whereIn('role',['TECHNICIAN','ENGINEER'])->orderBy('name')->get() as $tech)
                            <option value="{{ $tech->id }}"
                                {{ $workOrder->assigned_to_user_id == $tech->id ? 'selected' : '' }}>
                                {{ $tech->name }} ({{ $tech->role }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">تعيين</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- Close Modal --}}
@can('close', $workOrder)
<div class="modal fade" id="modalClose" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" dir="rtl">
            <form method="POST" action="{{ route('work-orders.close', $workOrder) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إغلاق أمر العمل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">حالة الحل <span class="text-danger">*</span></label>
                        <select name="resolution_status" class="form-select" required>
                            <option value="RESOLVED">تم الحل بالكامل</option>
                            <option value="PARTIAL">حل جزئي</option>
                            <option value="UNRESOLVED">لم يُحل</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات الإغلاق</label>
                        <textarea name="resolution_notes" class="form-control" rows="3"
                                  placeholder="أدخل ملاحظات الإغلاق..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">تأكيد الإغلاق</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- Upload Attachment Modal --}}
@can('create', App\Models\WorkOrderAttachment::class)
<div class="modal fade" id="modalUpload" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" dir="rtl">
            <form method="POST" action="{{ route('work-orders.attachments.store', $workOrder) }}"
                  enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">رفع مرفق</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">الملف <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" required>
                        <div class="form-text">الحد الأقصى: 20 MB</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">وصف (اختياري)</label>
                        <input type="text" name="description" class="form-control" maxlength="500">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">رفع</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- Issue Spare Part Modal --}}
@can('create', App\Models\WorkOrderPart::class)
<div class="modal fade" id="modalIssuePart" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" dir="rtl">
            <form method="POST" action="{{ route('work-orders.parts.store', $workOrder) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">صرف قطعة غيار</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">القطعة <span class="text-danger">*</span></label>
                        <select name="spare_part_id" class="form-select" required>
                            <option value="">-- اختر --</option>
                            @foreach(App\Models\SparePart::orderBy('name')->get() as $sp)
                                <option value="{{ $sp->id }}">
                                    {{ $sp->name }} ({{ $sp->part_code }}) – متاح: {{ $sp->current_quantity }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الكمية <span class="text-danger">*</span></label>
                        <input type="number" name="quantity_used" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <input type="text" name="notes" class="form-control" maxlength="500">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">صرف</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@endsection

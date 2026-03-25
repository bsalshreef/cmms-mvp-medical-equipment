@extends('layouts.app')

@section('title', 'أوامر العمل')

@section('content')
<div class="py-4" dir="rtl">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">أوامر العمل</h5>
        @can('create', App\Models\WorkOrder::class)
        <a href="{{ route('work-orders.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i>أمر عمل جديد
        </a>
        @endcan
    </div>

    {{-- Filters --}}
    <form method="GET" class="card shadow-sm border-0 mb-3">
        <div class="card-body py-2">
            <div class="row g-2 align-items-end">
                <div class="col-auto">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">كل الحالات</option>
                        @foreach(['OPEN','IN_PROGRESS','ON_HOLD','COMPLETED','CLOSED','CANCELLED'] as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select name="priority" class="form-select form-select-sm">
                        <option value="">كل الأولويات</option>
                        @foreach(['CRITICAL','HIGH','MEDIUM','LOW'] as $p)
                            <option value="{{ $p }}" {{ request('priority') === $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-funnel me-1"></i>تصفية
                    </button>
                    <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-link text-muted">إعادة تعيين</a>
                </div>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>الجهاز</th>
                            <th>القسم</th>
                            <th>الأولوية</th>
                            <th>الحالة</th>
                            <th>الطالب</th>
                            <th>الفني</th>
                            <th>التاريخ</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workOrders as $wo)
                        <tr>
                            <td class="text-muted small">{{ $wo->id }}</td>
                            <td>
                                <span class="fw-semibold small">{{ $wo->device?->name ?? '—' }}</span>
                                <br><span class="text-muted" style="font-size:.75rem">{{ $wo->device?->device_code }}</span>
                            </td>
                            <td class="small">{{ $wo->device?->department ?? '—' }}</td>
                            <td><span class="badge badge-{{ $wo->priority }}">{{ $wo->priority }}</span></td>
                            <td><span class="badge badge-{{ $wo->workflow_status }}">{{ $wo->workflow_status }}</span></td>
                            <td class="small">{{ $wo->creator?->name ?? '—' }}</td>
                            <td class="small">{{ $wo->assignee?->name ?? '—' }}</td>
                            <td class="small text-muted">{{ $wo->created_at->format('Y-m-d') }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('work-orders.show', $wo) }}"
                                       class="btn btn-sm btn-outline-secondary py-0 px-2"
                                       title="عرض">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('update', $wo)
                                    <a href="{{ route('work-orders.edit', $wo) }}"
                                       class="btn btn-sm btn-outline-primary py-0 px-2"
                                       title="تعديل">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('delete', $wo)
                                    <form method="POST" action="{{ route('work-orders.destroy', $wo) }}"
                                          onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-0 px-2" title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                لا توجد أوامر عمل مطابقة للفلتر
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($workOrders->hasPages())
        <div class="card-footer bg-white">
            {{ $workOrders->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

@extends('layouts.app')

@section('title', 'تعديل أمر العمل #' . $workOrder->id)

@section('content')
<div class="py-4" dir="rtl">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">
            تعديل أمر العمل <span class="text-primary">#{{ $workOrder->id }}</span>
        </h5>
        <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i>العودة
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="POST" action="{{ route('work-orders.update', $workOrder) }}">
                @csrf @method('PUT')

                @include('work-orders._form', ['workOrder' => $workOrder])

                {{-- Status / resolution fields – only for users who can close --}}
                @can('close', $workOrder)
                <hr>
                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label">حالة سير العمل</label>
                        <select name="workflow_status" class="form-select">
                            @foreach(['OPEN','IN_PROGRESS','ON_HOLD','COMPLETED','CLOSED','CANCELLED'] as $s)
                                <option value="{{ $s }}"
                                    {{ old('workflow_status', $workOrder->workflow_status) === $s ? 'selected' : '' }}>
                                    {{ $s }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">حالة الحل</label>
                        <select name="resolution_status" class="form-select">
                            <option value="">-- غير محدد --</option>
                            @foreach(['RESOLVED','PARTIAL','UNRESOLVED'] as $r)
                                <option value="{{ $r }}"
                                    {{ old('resolution_status', $workOrder->resolution_status) === $r ? 'selected' : '' }}>
                                    {{ $r }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">ملاحظات الإغلاق</label>
                        <textarea name="resolution_notes" class="form-control" rows="3">{{ old('resolution_notes', $workOrder->resolution_notes) }}</textarea>
                    </div>
                </div>
                @endcan

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>حفظ التعديلات
                    </button>
                    <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-outline-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

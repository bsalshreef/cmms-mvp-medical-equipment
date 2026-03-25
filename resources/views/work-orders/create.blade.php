@extends('layouts.app')

@section('title', 'أمر عمل جديد')

@section('content')
<div class="py-4" dir="rtl">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0">إنشاء أمر عمل جديد</h5>
        <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i>العودة
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="POST" action="{{ route('work-orders.store') }}">
                @csrf
                @include('work-orders._form', ['workOrder' => null])
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>حفظ الأمر
                    </button>
                    <a href="{{ route('work-orders.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

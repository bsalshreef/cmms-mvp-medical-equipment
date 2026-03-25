@extends('layouts.app')

@section('title', 'لوحة التحكم')

@push('styles')
<style>
    .chart-container { position: relative; height: 260px; }
    .ppm-overdue  { background-color: #fff5f5; }
    .ppm-warning  { background-color: #fffbf0; }
    .section-title { font-size: .85rem; font-weight: 600; text-transform: uppercase;
                     letter-spacing: .06em; color: #6c757d; margin-bottom: .75rem; }
</style>
@endpush

@section('content')
<div class="py-4" dir="rtl">

    {{-- ══════════════════════════════════════════════════════════════════
         KPI CARDS
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        {{-- Open Work Orders --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-folder2-open"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold lh-1">{{ $kpis['open_work_orders'] }}</div>
                        <div class="text-muted small">أوامر مفتوحة</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- In Progress --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold lh-1">{{ $kpis['in_progress_work_orders'] }}</div>
                        <div class="text-muted small">قيد التنفيذ</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Overdue --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold lh-1 {{ $kpis['overdue_work_orders'] > 0 ? 'text-danger' : '' }}">
                            {{ $kpis['overdue_work_orders'] }}
                        </div>
                        <div class="text-muted small">متأخرة</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Completed this month --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold lh-1">{{ $kpis['completed_this_month'] }}</div>
                        <div class="text-muted small">مغلقة هذا الشهر</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PPM this month --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold lh-1">{{ $kpis['ppm_this_month'] }}</div>
                        <div class="text-muted small">PPM هذا الشهر</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Critical devices --}}
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-exclamation-diamond"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold lh-1 {{ $kpis['critical_devices_open'] > 0 ? 'text-danger' : '' }}">
                            {{ $kpis['critical_devices_open'] }}
                        </div>
                        <div class="text-muted small">أجهزة حرجة مفتوحة</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Low stock (hidden from REQUESTER / TECHNICIAN) --}}
        @if($kpis['low_stock_spare_parts'] !== null)
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card kpi-card shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="kpi-icon bg-secondary bg-opacity-10 text-secondary">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold lh-1 {{ $kpis['low_stock_spare_parts'] > 0 ? 'text-warning' : '' }}">
                            {{ $kpis['low_stock_spare_parts'] }}
                        </div>
                        <div class="text-muted small">قطع منخفضة المخزون</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>{{-- /KPI row --}}

    {{-- ══════════════════════════════════════════════════════════════════
         CHARTS ROW
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="row g-3 mb-4">

        {{-- Status donut --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p class="section-title">أوامر العمل حسب الحالة</p>
                    <div class="chart-container">
                        <canvas id="chartStatus"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Priority bar --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p class="section-title">أوامر مفتوحة حسب الأولوية</p>
                    <div class="chart-container">
                        <canvas id="chartPriority"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly trend (admin/manager/engineer only) --}}
        @if(in_array(Auth::user()->role, ['ADMIN','MANAGER','ENGINEER']))
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <p class="section-title">الاتجاه الشهري (آخر 6 أشهر)</p>
                    <div class="chart-container">
                        <canvas id="chartMonthly"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>{{-- /charts row --}}

    {{-- ══════════════════════════════════════════════════════════════════
         OPEN WORK ORDERS TABLE
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="section-title mb-0">أوامر العمل المفتوحة</span>
            <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-outline-primary">
                عرض الكل <i class="bi bi-arrow-left ms-1"></i>
            </a>
        </div>
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
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($openWorkOrders as $wo)
                        <tr>
                            <td class="text-muted small">{{ $wo->id }}</td>
                            <td>
                                <span class="fw-semibold">{{ $wo->device?->name ?? '—' }}</span>
                                <br><span class="text-muted small">{{ $wo->device?->device_code }}</span>
                            </td>
                            <td class="small">{{ $wo->device?->department ?? '—' }}</td>
                            <td>
                                <span class="badge badge-{{ $wo->priority }}">{{ $wo->priority }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $wo->workflow_status }}">{{ $wo->workflow_status }}</span>
                            </td>
                            <td class="small">{{ $wo->creator?->name ?? '—' }}</td>
                            <td class="small">{{ $wo->assignee?->name ?? '—' }}</td>
                            <td class="small text-muted">{{ $wo->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('work-orders.show', $wo) }}"
                                   class="btn btn-xs btn-outline-secondary btn-sm py-0 px-2">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">لا توجد أوامر عمل مفتوحة</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         BOTTOM GRID: Top Failures | Critical Devices | PPM | Low Stock | Activity
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="row g-3">

        {{-- Top Failure Devices --}}
        @if(in_array(Auth::user()->role, ['ADMIN','MANAGER','ENGINEER']))
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <span class="section-title mb-0">أكثر الأجهزة أعطالاً</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>الجهاز</th><th>القسم</th><th class="text-center">إجمالي</th><th class="text-center">مفتوح</th></tr>
                        </thead>
                        <tbody>
                            @forelse($topFailureDevices as $d)
                            <tr>
                                <td>
                                    <span class="fw-semibold small">{{ $d->device_name }}</span>
                                    <br><span class="text-muted" style="font-size:.75rem">{{ $d->device_code }}</span>
                                </td>
                                <td class="small">{{ $d->department ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $d->total_work_orders }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $d->open_work_orders > 0 ? 'bg-danger' : 'bg-success' }}">
                                        {{ $d->open_work_orders }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">لا توجد بيانات</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Critical Devices --}}
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <span class="section-title mb-0">الأجهزة الحرجة – أوامر مفتوحة</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>الجهاز</th><th>القسم</th><th class="text-center">مفتوح</th></tr>
                        </thead>
                        <tbody>
                            @forelse($criticalDevices as $row)
                            <tr>
                                <td>
                                    <span class="fw-semibold small">{{ $row->device?->name ?? '—' }}</span>
                                    <br><span class="text-muted" style="font-size:.75rem">{{ $row->device?->device_code }}</span>
                                </td>
                                <td class="small">{{ $row->device?->department ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-danger">{{ $row->open_count }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">لا توجد أجهزة حرجة</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Upcoming PPM --}}
        @if(in_array(Auth::user()->role, ['ADMIN','MANAGER','ENGINEER','TECHNICIAN']))
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <span class="section-title mb-0">الصيانة الوقائية القادمة (PPM)</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>الجهاز</th><th>النوع</th><th>الموعد</th></tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingPpm as $ppm)
                            @php
                                $daysLeft = now()->startOfDay()->diffInDays($ppm->next_due_date, false);
                                $rowClass = $daysLeft <= 3 ? 'ppm-overdue' : ($daysLeft <= 7 ? 'ppm-warning' : '');
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td class="small fw-semibold">{{ $ppm->device?->name ?? '—' }}</td>
                                <td class="small">{{ $ppm->maintenanceType?->name_ar ?? '—' }}</td>
                                <td class="small">
                                    {{ $ppm->next_due_date?->format('Y-m-d') }}
                                    @if($daysLeft <= 3)
                                        <span class="badge bg-danger ms-1">{{ $daysLeft }}د</span>
                                    @elseif($daysLeft <= 7)
                                        <span class="badge bg-warning text-dark ms-1">{{ $daysLeft }}د</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">لا توجد مهام PPM قادمة</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Low Stock Spare Parts --}}
        @if(in_array(Auth::user()->role, ['ADMIN','MANAGER','STORE','ENGINEER']))
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <span class="section-title mb-0">قطع الغيار منخفضة المخزون</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>القطعة</th><th>المورد</th><th class="text-center">المتاح</th><th class="text-center">الحد الأدنى</th></tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockParts as $part)
                            <tr>
                                <td>
                                    <span class="fw-semibold small">{{ $part->name }}</span>
                                    <br><span class="text-muted" style="font-size:.75rem">{{ $part->part_code }}</span>
                                </td>
                                <td class="small">{{ $part->vendor?->name ?? '—' }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $part->current_quantity == 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                        {{ $part->current_quantity }}
                                    </span>
                                </td>
                                <td class="text-center text-muted small">{{ $part->minimum_quantity }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">المخزون كافٍ</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- Recent Activity --}}
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <span class="section-title mb-0">آخر النشاطات</span>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($recentWorkOrders as $wo)
                        <li class="list-group-item px-3 py-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <a href="{{ route('work-orders.show', $wo) }}"
                                       class="text-decoration-none fw-semibold small">
                                        #{{ $wo->id }} – {{ $wo->device?->name ?? '—' }}
                                    </a>
                                    <div class="text-muted" style="font-size:.75rem">
                                        {{ $wo->creator?->name ?? '—' }}
                                    </div>
                                </div>
                                <span class="badge badge-{{ $wo->workflow_status }} ms-2">
                                    {{ $wo->workflow_status }}
                                </span>
                            </div>
                            <div class="text-muted" style="font-size:.7rem">
                                {{ $wo->created_at->diffForHumans() }}
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted py-4">لا توجد نشاطات</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

    </div>{{-- /bottom grid --}}

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// ── Shared helpers ────────────────────────────────────────────────────────
const statusColors = {
    OPEN:        '#0d6efd',
    IN_PROGRESS: '#fd7e14',
    ON_HOLD:     '#6c757d',
    COMPLETED:   '#198754',
    CLOSED:      '#343a40',
    CANCELLED:   '#dc3545',
};
const priorityColors = {
    CRITICAL: '#dc3545',
    HIGH:     '#fd7e14',
    MEDIUM:   '#ffc107',
    LOW:      '#0dcaf0',
};

// ── Status Donut ──────────────────────────────────────────────────────────
(function () {
    const raw   = @json($woByStatus);
    const labels = raw.map(r => r.workflow_status);
    const data   = raw.map(r => r.total);
    const colors = labels.map(l => statusColors[l] ?? '#adb5bd');

    new Chart(document.getElementById('chartStatus'), {
        type: 'doughnut',
        data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: 2 }] },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } },
        },
    });
})();

// ── Priority Bar ──────────────────────────────────────────────────────────
(function () {
    const raw    = @json($woByPriority);
    const labels = raw.map(r => r.priority);
    const data   = raw.map(r => r.total);
    const colors = labels.map(l => priorityColors[l] ?? '#adb5bd');

    new Chart(document.getElementById('chartPriority'), {
        type: 'bar',
        data: { labels, datasets: [{ label: 'أوامر مفتوحة', data, backgroundColor: colors, borderRadius: 6 }] },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        },
    });
})();

// ── Monthly Trend ─────────────────────────────────────────────────────────
@if(in_array(Auth::user()->role, ['ADMIN','MANAGER','ENGINEER']))
(function () {
    const raw    = @json($monthlyTrend);
    const months = ['يناير','فبراير','مارس','أبريل','مايو','يونيو',
                    'يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
    const labels = raw.map(r => months[r.month - 1] + ' ' + r.year);
    const data   = raw.map(r => r.total);

    new Chart(document.getElementById('chartMonthly'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'أوامر العمل',
                data,
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
            }],
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
        },
    });
})();
@endif
</script>
@endpush

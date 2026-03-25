<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CMMS – نظام إدارة الصيانة')</title>

    {{-- Bootstrap 5 RTL --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css"
          integrity="sha384-dpuaG1suU0eT09tx5plTaGMLBsfDLX5UkJWmy1bnQVYzsViDtqjDyETQ4GUQB0"
          crossorigin="anonymous">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body          { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, sans-serif; }
        .sidebar      { min-height: 100vh; background: #1e2a3a; }
        .sidebar a    { color: #adb5bd; text-decoration: none; display: block; padding: .5rem 1rem; border-radius: .375rem; }
        .sidebar a:hover, .sidebar a.active { background: #2d3e50; color: #fff; }
        .sidebar .nav-section { font-size: .7rem; text-transform: uppercase; letter-spacing: .08em;
                                color: #6c757d; padding: .75rem 1rem .25rem; }
        .kpi-card     { border: none; border-radius: .75rem; }
        .kpi-icon     { width: 48px; height: 48px; border-radius: .5rem;
                        display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
        .badge-OPEN          { background-color: #0d6efd; }
        .badge-IN_PROGRESS   { background-color: #fd7e14; }
        .badge-ON_HOLD       { background-color: #6c757d; }
        .badge-COMPLETED     { background-color: #198754; }
        .badge-CLOSED        { background-color: #343a40; }
        .badge-CANCELLED     { background-color: #dc3545; }
        .badge-LOW           { background-color: #0dcaf0; color: #000; }
        .badge-MEDIUM        { background-color: #ffc107; color: #000; }
        .badge-HIGH          { background-color: #fd7e14; }
        .badge-CRITICAL      { background-color: #dc3545; }
    </style>

    @stack('styles')
</head>
<body>

<div class="d-flex">

    {{-- ── Sidebar ─────────────────────────────────────────────────────── --}}
    <nav class="sidebar p-3" style="width:240px; flex-shrink:0;">
        <div class="text-white fw-bold fs-5 mb-4 px-2">
            <i class="bi bi-hospital me-2"></i>CMMS
        </div>

        <div class="nav-section">القائمة الرئيسية</div>
        <a href="{{ route('dashboard') }}"
           class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2 me-2"></i>لوحة التحكم
        </a>

        <div class="nav-section">أوامر العمل</div>
        <a href="{{ route('work-orders.index') }}"
           class="{{ request()->routeIs('work-orders.*') ? 'active' : '' }}">
            <i class="bi bi-tools me-2"></i>أوامر العمل
        </a>

        @can('viewAny', App\Models\MaintenancePlan::class)
        <a href="{{ route('maintenance-plans.index') }}"
           class="{{ request()->routeIs('maintenance-plans.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check me-2"></i>خطط الصيانة
        </a>
        @endcan

        @can('viewAny', App\Models\Device::class)
        <div class="nav-section">الأجهزة والمخزون</div>
        <a href="{{ route('devices.index') }}"
           class="{{ request()->routeIs('devices.*') ? 'active' : '' }}">
            <i class="bi bi-cpu me-2"></i>الأجهزة
        </a>
        @endcan

        @can('viewAny', App\Models\SparePart::class)
        <a href="{{ route('spare-parts.index') }}"
           class="{{ request()->routeIs('spare-parts.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam me-2"></i>قطع الغيار
        </a>
        @endcan

        @can('viewAny', App\Models\Vendor::class)
        <a href="{{ route('vendors.index') }}"
           class="{{ request()->routeIs('vendors.*') ? 'active' : '' }}">
            <i class="bi bi-building me-2"></i>الموردون
        </a>
        @endcan

        @if(in_array(Auth::user()->role, ['ADMIN','MANAGER','ENGINEER']))
        <div class="nav-section">التقارير</div>
        <a href="{{ route('reports.index') }}"
           class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line me-2"></i>التقارير
        </a>
        @endif

        @if(Auth::user()->role === 'ADMIN')
        <div class="nav-section">الإدارة</div>
        <a href="{{ route('users.index') }}"
           class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="bi bi-people me-2"></i>المستخدمون
        </a>
        @endif

        <hr class="border-secondary my-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link sidebar-link p-0 text-secondary">
                <i class="bi bi-box-arrow-left me-2"></i>تسجيل الخروج
            </button>
        </form>
    </nav>

    {{-- ── Main content ─────────────────────────────────────────────────── --}}
    <div class="flex-grow-1">

        {{-- Top bar --}}
        <header class="bg-white border-bottom px-4 py-2 d-flex align-items-center justify-content-between">
            <span class="text-muted small">@yield('title', 'لوحة التحكم')</span>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-secondary">{{ Auth::user()->role }}</span>
                <span class="fw-semibold">{{ Auth::user()->name }}</span>
            </div>
        </header>

        {{-- Flash messages --}}
        <div class="px-4 pt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <main class="px-4 pb-5">
            @yield('content')
        </main>
    </div>

</div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmLdFdQBZJbFBpHdvOIgIlGEFfS"
        crossorigin="anonymous"></script>

@stack('scripts')
</body>
</html>

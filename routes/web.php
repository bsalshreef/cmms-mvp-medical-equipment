<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\WorkOrderAttachmentController;
use App\Http\Controllers\WorkOrderPartController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
| All routes below require the user to be authenticated.
| Authorization is handled by Policies inside each controller.
*/

Route::middleware(['auth'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | Dashboard
    |----------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Work Orders
    |----------------------------------------------------------------------
    */
    Route::resource('work-orders', WorkOrderController::class);

    // Custom work-order actions
    Route::post('work-orders/{work_order}/assign', [WorkOrderController::class, 'assign'])
        ->name('work-orders.assign');

    Route::post('work-orders/{work_order}/close', [WorkOrderController::class, 'close'])
        ->name('work-orders.close');

    /*
    |----------------------------------------------------------------------
    | Work Order Attachments
    |----------------------------------------------------------------------
    */
    Route::post(
        'work-orders/{work_order}/attachments',
        [WorkOrderAttachmentController::class, 'store']
    )->name('work-orders.attachments.store');

    Route::get(
        'attachments/{attachment}/download',
        [WorkOrderAttachmentController::class, 'download']
    )->name('attachments.download');

    Route::delete(
        'attachments/{attachment}',
        [WorkOrderAttachmentController::class, 'destroy']
    )->name('attachments.destroy');

    /*
    |----------------------------------------------------------------------
    | Work Order Spare Parts
    |----------------------------------------------------------------------
    */
    Route::post(
        'work-orders/{work_order}/parts',
        [WorkOrderPartController::class, 'store']
    )->name('work-orders.parts.store');

    Route::put(
        'work-orders/{work_order}/parts/{part}',
        [WorkOrderPartController::class, 'update']
    )->name('work-orders.parts.update');

    Route::delete(
        'work-orders/{work_order}/parts/{part}',
        [WorkOrderPartController::class, 'destroy']
    )->name('work-orders.parts.destroy');

    /*
    |----------------------------------------------------------------------
    | Maintenance Plans  (placeholder – wire up your own controller)
    |----------------------------------------------------------------------
    */
    Route::resource('maintenance-plans', \App\Http\Controllers\MaintenancePlanController::class);

    /*
    |----------------------------------------------------------------------
    | Spare Parts  (placeholder)
    |----------------------------------------------------------------------
    */
    Route::resource('spare-parts', \App\Http\Controllers\SparePartController::class);

    /*
    |----------------------------------------------------------------------
    | Devices  (placeholder)
    |----------------------------------------------------------------------
    */
    Route::resource('devices', \App\Http\Controllers\DeviceController::class);

    /*
    |----------------------------------------------------------------------
    | Vendors  (placeholder)
    |----------------------------------------------------------------------
    */
    Route::resource('vendors', \App\Http\Controllers\VendorController::class);

    /*
    |----------------------------------------------------------------------
    | Reports  (placeholder)
    |----------------------------------------------------------------------
    */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
    });

    /*
    |----------------------------------------------------------------------
    | Users  (admin only – guard via policy or middleware)
    |----------------------------------------------------------------------
    */
    Route::resource('users', \App\Http\Controllers\UserController::class);

});

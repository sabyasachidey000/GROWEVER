<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\EmployeeApiController;
use App\Http\Controllers\Api\MachineApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\ProductionApiController;
use App\Http\Controllers\Api\BreakdownApiController;
use App\Http\Controllers\Api\ApprovalApiController;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Controllers\Api\RejectionApiController;
use App\Http\Controllers\Api\QualityApiController;
use App\Http\Controllers\Api\ShiftApiController;
use App\Http\Controllers\Api\OeeApiController;
use App\Http\Controllers\Api\WhatsAppWebhookController;
use App\Http\Controllers\RfidController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WhatsAppController;
use App\Http\Controllers\ShiftController;

// Public API routes
Route::post('/auth/login', [AuthController::class, 'apiLogin'])->name('api.auth.login');

Route::post('/device/log', [RfidController::class, 'store']);
Route::post('/rfid/punch', [RfidController::class, 'punch']);
Route::post('/rfid/button', [RfidController::class, 'button']);
Route::post('/rfid/breakdown-status', [RfidController::class, 'breakdownStatus']);
Route::post(
    '/rfid/limit-switch',
    [RfidController::class, 'limitSwitch']
);
Route::get('/machine/scada', [MachineApiController::class, 'machineScadaApi'])
    ->name('api.machine.scada');


Route::get('/current-shift', [ShiftController::class, 'currentShift']);


// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'apiLogout'])->name('api.auth.logout');
    Route::get('/auth/user', [AuthController::class, 'apiUser'])->name('api.auth.user');

    // Employees
    Route::get('/employees', [EmployeeApiController::class, 'index'])->name('api.employees.index');
    Route::get('/employees/{employee}', [EmployeeApiController::class, 'show'])->name('api.employees.show');

    // Machines
    Route::get('/machines', [MachineApiController::class, 'index'])->name('api.machines.index');
    Route::get('/machines/{machine}', [MachineApiController::class, 'show'])->name('api.machines.show');
    Route::get('/machines/{machine}/status', [MachineApiController::class, 'status'])->name('api.machines.status');

    // Products
    Route::get('/products', [ProductApiController::class, 'index'])->name('api.products.index');
    Route::get('/products/{product}', [ProductApiController::class, 'show'])->name('api.products.show');

    // Production
    Route::get('/production', [ProductionApiController::class, 'index'])->name('api.production.index');
    Route::post('/production', [ProductionApiController::class, 'store'])->name('api.production.store');
    Route::get('/production/{productionRun}', [ProductionApiController::class, 'show'])->name('api.production.show');

    // Breakdowns
    Route::get('/breakdowns', [BreakdownApiController::class, 'index'])->name('api.breakdowns.index');
    Route::post('/breakdowns', [BreakdownApiController::class, 'store'])->name('api.breakdowns.store');
    Route::get('/breakdowns/{breakdown}', [BreakdownApiController::class, 'show'])->name('api.breakdowns.show');

    // Approvals
    Route::get('/approvals/pending', [ApprovalApiController::class, 'pending'])->name('api.approvals.pending');
    Route::post('/approvals/{approval}/approve', [ApprovalApiController::class, 'approve'])->name('api.approvals.approve');
    Route::post('/approvals/{approval}/reject', [ApprovalApiController::class, 'reject'])->name('api.approvals.reject');

    // Rejections
    Route::get('/rejections', [RejectionApiController::class, 'index'])->name('api.rejections.index');

    // Quality
    Route::get('/quality', [QualityApiController::class, 'index'])->name('api.quality.index');

    // Shifts
    Route::get('/shifts/current', [ShiftApiController::class, 'getCurrentShift'])->name('api.shifts.current');

    // OEE
    Route::get('/oee/summary', [OeeApiController::class, 'summary'])->name('api.oee.summary');
    Route::get('/oee/ranking', [OeeApiController::class, 'ranking'])->name('api.oee.ranking');

    // RFID
    Route::post('/rfid/swipe', [RfidController::class, 'apiSwipe'])->name('api.rfid.swipe');
    Route::get('/rfid/logs', [RfidController::class, 'logs'])->name('api.rfid.logs');
    Route::get('/rfid/punch-logs', [RfidController::class, 'apiPunchLogs'])->name('api.rfid.punch-logs');

    // Reports
    Route::get('/reports/daily-production', [ReportApiController::class, 'dailyProduction'])->name('api.reports.daily-production');
    Route::get('/reports/machine-utilization', [ReportApiController::class, 'machineUtilization'])->name('api.reports.machine-utilization');
    Route::get('/reports/breakdown', [ReportApiController::class, 'breakdown'])->name('api.reports.breakdown');
    Route::get('/reports/operator-performance', [ReportApiController::class, 'operatorPerformance'])->name('api.reports.operator-performance');
    Route::get('/reports/rejection-analysis', [ReportApiController::class, 'rejectionAnalysis'])->name('api.reports.rejection-analysis');
    Route::get('/reports/oee', [ReportApiController::class, 'oeeReport'])->name('api.reports.oee');


});
Route::get('/whatsapp/webhook', [WhatsAppWebhookController::class, 'verify']);
Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'receive']);
Route::get('/whatsapp/send', [WhatsAppController::class, 'send']);


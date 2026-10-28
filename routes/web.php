<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MachineController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTypeController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\BreakdownController;
use App\Http\Controllers\RejectionController;
use App\Http\Controllers\QualityController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\RfidController;
use App\Http\Controllers\FaceRecognitionController;
use App\Http\Controllers\OperatorPortalController;
use App\Http\Controllers\OeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Api\ModbusController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QcInspectionController;

// Public routes (no auth required)
Route::post('/api/production', ModbusController::class);

// Operator face login panel
Route::get('/operator/login', [OperatorPortalController::class, 'login'])->name('operator.login');
Route::post('/operator/login', [OperatorPortalController::class, 'authenticate'])->name('operator.authenticate');
Route::get('/operator/dashboard', [OperatorPortalController::class, 'dashboard'])->name('operator.dashboard');
Route::get('/operator/production', [OperatorPortalController::class, 'productionEntry'])->name('operator.production-entry');
Route::post('/operator/production', [OperatorPortalController::class, 'storeProduction'])->name('operator.production.store');
Route::get('/operator/breakdowns', [OperatorPortalController::class, 'breakdownEntry'])->name('operator.breakdown-entry');
Route::post('/operator/breakdowns', [OperatorPortalController::class, 'storeBreakdown'])->name('operator.breakdowns.store');
Route::post('/operator/logout', [OperatorPortalController::class, 'logout'])->name('operator.logout');


Route::get('/daily_production_update', [ReportController::class, 'daily_production_report_insert'])->name('daily_production_update');
// Public routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::middleware('menu:dashboard')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/api/dashboard/stats', [DashboardController::class, 'getStats'])->name('api.dashboard.stats');
        Route::get('/api/dashboard/machines', [DashboardController::class, 'getMachineStatus'])->name('api.dashboard.machines');
        Route::get('/api/dashboard/production-chart', [DashboardController::class, 'getProductionChart'])->name('api.dashboard.production-chart');
        Route::get('/api/dashboard/oee-summary', [DashboardController::class, 'getOeeSummary'])->name('api.dashboard.oee-summary');
    });
     Route::get('/machine_scada', [DashboardController::class, 'machine_scada'])->name('machine_scada');
     
    // Users
    Route::middleware('menu:users')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    });

    // Departments
    Route::resource('departments', DepartmentController::class)->middleware('menu:departments');

    // Designations
    Route::resource('designations', DesignationController::class)->middleware('menu:designations');
    Route::get('/designations/{designation}/menu-permissions', [\App\Http\Controllers\DesignationMenuPermissionController::class, 'edit'])
        ->middleware('menu:designations')->name('designations.menu-permissions.edit');
    Route::put('/designations/{designation}/menu-permissions', [\App\Http\Controllers\DesignationMenuPermissionController::class, 'update'])
        ->middleware('menu:designations')->name('designations.menu-permissions.update');

    // Employees
    Route::middleware('menu:employees')->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::get('/api/employees/search', [EmployeeController::class, 'search'])->name('api.employees.search');
        Route::post('/employees/{employee}/assign-rfid', [EmployeeController::class, 'assignRfid'])->name('employees.assign-rfid');
    });

    // Machines
    Route::middleware('menu:machines')->group(function () {
        Route::resource('machines', MachineController::class);
        Route::post('/machines/{machine}/status', [MachineController::class, 'updateStatus'])->name('machines.update-status');
        Route::post('/machines/{machine}/report-breakdown', [MachineController::class, 'reportBreakdown'])->name('machines.report-breakdown');
    });

    // Products
    Route::resource('products', ProductController::class)->middleware('menu:products');

    // Product Types
    Route::resource('product-types', ProductTypeController::class)->middleware('menu:product_types');

    // Shifts
    Route::middleware('menu:shifts')->group(function () {
        Route::resource('shifts', ShiftController::class);
        Route::post('/shifts/assign', [ShiftController::class, 'assign'])->name('shifts.assign');
    });

    // Production
    Route::middleware('menu:production')->group(function () {
        Route::resource('production', ProductionController::class);
        Route::post('/production/{productionRun}/complete', [ProductionController::class, 'complete'])->name('production.complete');
        Route::post('/production/{productionRun}/submit-approval', [ProductionController::class, 'submitForApproval'])->name('production.submit-approval');
        Route::get('/api/production/machine-data/{machineId}', [ProductionController::class, 'apiMachineData'])->name('api.production.machine-data');
        Route::get('/api/production/operator-data/{operatorId}', [ProductionController::class, 'apiOperatorData'])->name('api.production.operator-data');
    });

    // Breakdowns
    Route::middleware('menu:breakdowns')->group(function () {
        Route::get('/breakdowns/history', [BreakdownController::class, 'history'])->name('breakdowns.history');
        Route::resource('breakdowns', BreakdownController::class);
        Route::post('/breakdowns/{breakdown}/close', [BreakdownController::class, 'close'])->name('breakdowns.close');
    });

    // Rejections
    // Rejections (custom routes BEFORE resource to avoid conflict)
    Route::get('/rejections/analysis', [RejectionController::class, 'analysis'])->middleware('menu:rejection_analysis')->name('rejections.analysis');
    Route::resource('rejections', RejectionController::class)->middleware('menu:rejections');

    // Quality
    Route::resource('quality', QualityController::class)->middleware('menu:quality');

    // Pending Production Approvals
    Route::middleware('menu:pending_approvals')->group(function () {
        Route::get('/approvals/pending', [ApprovalController::class, 'index'])->name('pending_approvals.index');
    });

    // Approved Production
    Route::middleware('menu:approved_approvals')->group(function () {
        Route::get('/approvals/approved', [ApprovalController::class, 'index'])->name('approved_approvals.index');
    });

    // Approvals
    Route::middleware('menu:approvals')->group(function () {
        Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::get('/approvals/{approval}', [ApprovalController::class, 'show'])->name('approvals.show');
        Route::post('/approvals/{approval}/approve', [ApprovalController::class, 'approve'])->name('approvals.approve');
        Route::post('/approvals/{approval}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
    });

    // RFID Management
    Route::middleware('menu:rfid')->group(function () {
    Route::get('/rfid', [RfidController::class, 'index'])->name('rfid.index');
    Route::post('/rfid/assign', [RfidController::class, 'assign'])->name('rfid.assign');
    Route::post('/rfid/unassign/{employee}', [RfidController::class, 'unassign'])->name('rfid.unassign');

    // RFID Swipe
    Route::get('/rfid/swipe', [RfidController::class, 'swipe'])->name('rfid.swipe');
    Route::post('/rfid/swipe/process', [RfidController::class, 'processSwipe'])->name('rfid.swipe.process');
    Route::post('/rfid/force-logout', [RfidController::class, 'forceLogout'])->name('rfid.force-logout');
    Route::get('/rfid/punch-logs', [RfidController::class, 'punchLogs'])->name('rfid.punch-logs');

    Route::get('/rfid/machine/{machineId}/status', [RfidController::class, 'machineStatus'])->name('rfid.machine-status');
    Route::get('/api/rfid/logs', [RfidController::class, 'logs'])->name('api.rfid.logs');
    Route::post('/api/rfid/swipe', [RfidController::class, 'apiSwipe'])->name('api.rfid.swipe');
    Route::get('/api/rfid/punch-logs', [RfidController::class, 'apiPunchLogs'])->name('api.rfid.punch-logs');
    });

    // Browser Face Recognition Attendance
    Route::get('/face-recognition', [FaceRecognitionController::class, 'index'])->name('face-recognition.index');
    Route::post('/face-recognition/enroll', [FaceRecognitionController::class, 'enroll'])->name('face-recognition.enroll');
    Route::post('/face-recognition/unassign/{employee}', [FaceRecognitionController::class, 'unassign'])->name('face-recognition.unassign');
    Route::get('/face-recognition/attendance', [FaceRecognitionController::class, 'attendance'])->name('face-recognition.attendance');
    Route::post('/face-recognition/attendance/process', [FaceRecognitionController::class, 'processAttendance'])->name('face-recognition.attendance.process');

    // OEE
    Route::middleware('menu:oee')->group(function () {
        Route::get('/oee', [OeeController::class, 'index'])->name('oee.index');
        Route::post('/oee/calculate', [OeeController::class, 'calculate'])->name('oee.calculate');
        Route::post('/oee/calculate-daily', [OeeController::class, 'calculateDaily'])->name('oee.calculate-daily');
        Route::get('/oee/history', [OeeController::class, 'history'])->name('oee.history');
    });

    // Reports
    Route::middleware('menu:reports')->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/daily-production', [ReportController::class, 'dailyProduction'])->name('reports.daily-production');
    Route::get('/reports/shift-production', [ReportController::class, 'shiftProduction'])->name('reports.shift-production');
    Route::get('/reports/machine-utilization', [ReportController::class, 'machineUtilization'])->name('reports.machine-utilization');
    Route::get('/reports/breakdown', [ReportController::class, 'breakdown'])->name('reports.breakdown');
    Route::get('/reports/operator-performance', [ReportController::class, 'operatorPerformance'])->name('reports.operator-performance');
    Route::get('/reports/rejection-analysis', [ReportController::class, 'rejectionAnalysis'])->name('reports.rejection-analysis');
    Route::get('/reports/oee', [ReportController::class, 'oeeReport'])->name('reports.oee');
    Route::get('/reports/maintenance', [ReportController::class, 'maintenanceReport'])->name('reports.maintenance');
    Route::get('/reports/machine-energy-consumption', [ReportController::class, 'machineEnergyConsumption'])->name('reports.machine-energy-consumption');
    Route::get('/reports/qc-inspection', [ReportController::class, 'qcInspection'])->name('reports.qc-inspection');
    Route::get('/reports/component-daily-production', [ReportController::class, 'componentDailyProduction'])->name('reports.component-daily-production');
    Route::get('/reports/qc-inspection/pdf',[ReportController::class, 'downloadQcInspectionPdf'])->name('reports.qc-inspection.pdf');
    Route::get('/reports/qc-inspection/pdf',[ReportController::class, 'downloadQcInspectionPdf'])->name('reports.qc-inspection.pdf');
    Route::get('/reports/daily/pdf',[ReportController::class, 'downloaddailyPdf'])->name('reports.daily.production.pdf');
    
    });

    // Notifications
    Route::middleware('menu:notifications')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/api/notifications/unread', [NotificationController::class, 'unread'])->name('api.notifications.unread');
    Route::get('/api/notifications/count', [NotificationController::class, 'count'])->name('api.notifications.count');
    Route::get('/api/notifications/recent-alerts', [NotificationController::class, 'recentAlerts'])->name('api.notifications.recent-alerts');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    });

    // Activity Logs
    Route::middleware('menu:activity_logs')->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    });

    // Home redirect
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
    Route::get(
        '/breakdown/{id}/memo-pdf',
        [BreakdownController::class, 'generateMemo']
    )->name('breakdown.memo.pdf');
    
    // QC inspection uses the same Quality menu permission as the quality module.
    Route::middleware('menu:quality')->group(function () {
        Route::get('/qc-inspections', [QcInspectionController::class, 'index'])->name('qc-inspections.index');
        Route::get('/qc-inspections/create', [QcInspectionController::class, 'create'])->name('qc-inspections.create');
        Route::get('/qc-inspections/machines/{machine}/product', [QcInspectionController::class, 'machineProduct'])
            ->name('qc-inspections.machine-product');
        Route::post('/qc-inspections', [QcInspectionController::class, 'store'])->name('qc-inspections.store');
    });
});

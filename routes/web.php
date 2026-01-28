<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Treasurer\BillController;
use App\Http\Controllers\Treasurer\BillTypeController;
use App\Http\Controllers\Treasurer\ReportController as TreasurerReportController;
use App\Http\Controllers\Treasurer\ReconciliationController;
use App\Http\Controllers\Parents\PaymentController;
use App\Http\Controllers\Parents\ProfileController as ParentProfileController;
use App\Http\Controllers\Principal\ReportController as PrincipalReportController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Webhook (no auth required)
Route::post('/webhook/midtrans', [WebhookController::class, 'midtrans'])->name('webhook.midtrans');
Route::post('/webhook/xendit', [WebhookController::class, 'xendit'])->name('webhook.xendit');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Students
        Route::resource('students', StudentController::class);
        Route::post('/students/{student}/assign-parent', [StudentController::class, 'assignParent'])->name('students.assign-parent');
        Route::delete('/students/{student}/parents/{parent}', [StudentController::class, 'removeParent'])->name('students.remove-parent');
        Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
        Route::get('/students-export', [StudentController::class, 'export'])->name('students.export');
        Route::get('/students-template', [StudentController::class, 'template'])->name('students.template');

        // Users
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Roles
        Route::resource('roles', RoleController::class);

        // Permissions
        Route::resource('permissions', PermissionController::class)->except(['show']);
    });

    // Treasurer routes
    Route::middleware('role:bendahara,admin')->prefix('treasurer')->name('treasurer.')->group(function () {
        // Bills
        Route::resource('bills', BillController::class);
        Route::post('/bills/generate-bulk', [BillController::class, 'generateBulk'])->name('bills.generate-bulk');
        Route::post('/bills/{bill}/cancel', [BillController::class, 'cancel'])->name('bills.cancel');
        Route::post('/bills/{bill}/send-reminder', [BillController::class, 'sendReminder'])->name('bills.send-reminder');
        Route::post('/bills/send-bulk-reminders', [BillController::class, 'sendBulkReminders'])->name('bills.send-bulk-reminders');

        // Bill Types
        Route::resource('bill-types', BillTypeController::class)->except(['show']);
        Route::post('/bill-types/{billType}/toggle-status', [BillTypeController::class, 'toggleStatus'])->name('bill-types.toggle-status');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [TreasurerReportController::class, 'index'])->name('index');
            Route::get('/payments', [TreasurerReportController::class, 'payments'])->name('payments');
            Route::get('/receivables', [TreasurerReportController::class, 'receivables'])->name('receivables');
            Route::get('/monthly', [TreasurerReportController::class, 'monthly'])->name('monthly');
            Route::get('/by-class', [TreasurerReportController::class, 'byClass'])->name('by-class');
            Route::get('/by-type', [TreasurerReportController::class, 'byType'])->name('by-type');
            Route::get('/export-pdf', [TreasurerReportController::class, 'exportPdf'])->name('export-pdf');
            Route::post('/export', [TreasurerReportController::class, 'export'])->name('export');
        });

        // Reconciliation
        Route::prefix('reconciliation')->name('reconciliation.')->group(function () {
            Route::get('/', [ReconciliationController::class, 'index'])->name('index');
            Route::get('/create', [ReconciliationController::class, 'create'])->name('create');
            Route::post('/', [ReconciliationController::class, 'store'])->name('store');
            Route::get('/{reconciliation}', [ReconciliationController::class, 'show'])->name('show');
            Route::post('/items/{item}/resolve', [ReconciliationController::class, 'resolveItem'])->name('resolve-item');
            Route::get('/report', [ReconciliationController::class, 'report'])->name('report');
        });
    });

    // Parent routes
    Route::middleware('role:orangtua')->prefix('parent')->name('parent.')->group(function () {
        // Profile
        Route::get('/profile', [ParentProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile', [ParentProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ParentProfileController::class, 'changePassword'])->name('profile.password');

        // Students
        Route::get('/students', [ParentProfileController::class, 'students'])->name('students.index');
        Route::get('/students/{student}', [ParentProfileController::class, 'showStudent'])->name('students.show');

        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/history', [PaymentController::class, 'history'])->name('payments.history');
        Route::get('/payments/{bill}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('/payments/{bill}/checkout', [PaymentController::class, 'checkout'])->name('payments.checkout');
        Route::post('/payments/{bill}/process', [PaymentController::class, 'process'])->name('payments.process');
        Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
        Route::get('/payments/{payment}/status', [PaymentController::class, 'checkStatus'])->name('payments.check-status');
        Route::post('/payments/{payment}/sync-status', [PaymentController::class, 'syncStatus'])->name('payments.sync-status');
    });

    // Payment callbacks (accessible by parents)
    Route::get('/payment/finish', [PaymentController::class, 'finish'])->name('payment.finish');
    Route::get('/payment/pending', [PaymentController::class, 'pending'])->name('payment.pending');
    Route::get('/payment/error', [PaymentController::class, 'error'])->name('payment.error');

    // Principal routes
    Route::middleware('role:kepala_sekolah,admin')->prefix('principal')->name('principal.')->group(function () {
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [PrincipalReportController::class, 'index'])->name('index');
            Route::get('/income', [PrincipalReportController::class, 'income'])->name('income');
            Route::get('/collection', [PrincipalReportController::class, 'collection'])->name('collection');
            Route::get('/outstanding', [PrincipalReportController::class, 'outstanding'])->name('outstanding');
            Route::get('/trends', [PrincipalReportController::class, 'trends'])->name('trends');
        });
    });
});

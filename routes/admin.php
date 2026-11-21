<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\StaffRepairRequestController;
use App\Http\Controllers\RepairRequestController;
use App\Http\Middleware\EnsureActive;
use App\Http\Controllers\LibraryController;
use App\Models\LibraryTask;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectOperationsController;
use App\Http\Controllers\BusinessDirectoryController;
use App\Http\Controllers\ProjectChangeOrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/login', '/')->name('login');
    Route::redirect('/forgot-password', '/forgot-password')->name('forgot-password');

    Route::middleware(['auth', EnsureActive::class, 'can:access-panel,"admin"', 'verified'])->group(function () {
        Route::controller(AdminController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');


            Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
            Route::get('/customers', [BusinessDirectoryController::class, 'customers'])->name('customers');
            Route::get('/estimators', [BusinessDirectoryController::class, 'estimators'])->name('estimators');
            Route::get('/reports', [BusinessDirectoryController::class, 'reports'])->name('reports');
            Route::get('/notifications', [\App\Http\Controllers\AccountNoticeController::class, 'index'])->name('notifications');
            Route::patch('/notifications/read', [\App\Http\Controllers\AccountNoticeController::class, 'readAll'])->name('notifications.read-all');
            Route::patch('/notifications/{notice}', [\App\Http\Controllers\AccountNoticeController::class, 'read'])->whereNumber('notice')->name('notifications.read');
            Route::delete('/notifications/{notice}', [\App\Http\Controllers\AccountNoticeController::class, 'destroy'])->whereNumber('notice')->name('notifications.delete');
            Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile');
            Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->middleware('throttle:10,1')->name('profile.update');
            Route::get('/profile/photo', [\App\Http\Controllers\ProfileController::class, 'photo'])->name('profile.photo');

            Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'password'])->middleware('throttle:6,1')->name('profile.password');
            Route::delete('/profile/sessions/{session}', [\App\Http\Controllers\ProfileController::class, 'revoke'])->name('profile.sessions.revoke');
        });
        Route::prefix('library')->name('library.')->controller(LibraryController::class)->group(function () {
            Route::get('/', 'index')->middleware('can:viewAny,'.LibraryTask::class)->name('index');
            Route::view('/export', 'admin.library.export')->middleware('can:viewAny,'.LibraryTask::class)->name('export-options');
            Route::get('/export/{format}', 'export')->whereIn('format', ['csv', 'json'])->middleware('can:viewAny,'.LibraryTask::class)->name('export');
            Route::middleware('can:manage,'.LibraryTask::class)->group(function () {
                Route::view('/import', 'admin.library.import')->name('import');
                Route::post('/import', 'upload')->name('upload');
                Route::get('/import/{preview}', 'preview')->whereUuid('preview')->name('preview');
                Route::post('/import/{preview}', 'apply')->whereUuid('preview')->name('apply');
                Route::get('/tasks/{task}/edit', 'edit')->name('edit');
                Route::patch('/tasks/{task}', 'update')->name('update');
            });
            Route::get('/tasks/{task}/history', 'history')->middleware('can:viewAny,'.LibraryTask::class)->name('history');
            Route::get('/tasks/{task}', 'show')->middleware('can:viewAny,'.LibraryTask::class)->name('show');
        });
        Route::get('/estimates', [StaffRepairRequestController::class, 'index'])->name('estimates');
        Route::get('/estimates/review/{repair?}', [StaffRepairRequestController::class, 'show'])->whereUuid('repair')->name('estimate-review');
        Route::patch('/request-inbox/{repair}/review', [StaffRepairRequestController::class, 'save'])->whereUuid('repair')->name('repair-requests.save');
        Route::post('/request-inbox/{repair}/release', [StaffRepairRequestController::class, 'release'])->whereUuid('repair')->name('repair-requests.release');
        Route::get('/request-inbox', [StaffRepairRequestController::class, 'index'])->name('repair-requests.index');
        Route::get('/request-inbox/{repair}', [StaffRepairRequestController::class, 'show'])->whereUuid('repair')->name('repair-requests.show');
        Route::patch('/request-inbox/{repair}/nte', [StaffRepairRequestController::class, 'nte'])->whereUuid('repair')->name('repair-requests.nte');
        Route::post('/request-inbox/{repair}/disposition', [StaffRepairRequestController::class, 'disposition'])->whereUuid('repair')->name('repair-requests.disposition');
        Route::get('/request-inbox/{repair}/files/{file}/image', [RepairRequestController::class, 'image'])->whereUuid('repair')->name('repair-requests.image');
        Route::post('/request-inbox/{repair}/manual', [StaffRepairRequestController::class, 'manual'])->whereUuid('repair')->name('repair-requests.manual');
        Route::post('/request-inbox/{repair}/retry', [StaffRepairRequestController::class, 'retry'])->whereUuid('repair')->name('repair-requests.retry');
        Route::get('/request-inbox/{repair}/files/{file}', [RepairRequestController::class, 'download'])->whereUuid('repair')->name('repair-requests.download');
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::get('/customers/{user}', [BusinessDirectoryController::class, 'customer'])->name('customers.show');
        Route::post('/estimators', [BusinessDirectoryController::class, 'saveEstimator'])->name('estimators.store');
        Route::get('/estimators/{estimator}', [BusinessDirectoryController::class, 'estimator'])->name('estimators.show');
        Route::patch('/estimators/{estimator}', [BusinessDirectoryController::class, 'saveEstimator'])->name('estimators.update');
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
        Route::get('/projects/{project}/files/{file}', [ProjectController::class, 'file'])->name('projects.file');
        Route::delete('/projects/{project}/files/{file}', [ProjectOperationsController::class, 'removeFile'])->name('projects.files.remove');
        Route::patch('/projects/{project}', [ProjectOperationsController::class, 'update'])->name('projects.update');
        Route::post('/projects/{project}/daily-reports', [ProjectOperationsController::class, 'report'])->name('projects.report');
        Route::post('/projects/{project}/documents', [ProjectOperationsController::class, 'document'])->name('projects.document');
        Route::post('/projects/{project}/walkthrough', [ProjectOperationsController::class, 'walkthrough'])->name('projects.walkthrough');
        Route::post('/projects/{project}/change-orders/{order?}', [ProjectChangeOrderController::class, 'save'])->name('projects.orders.save');
        Route::patch('/projects/{project}/change-orders/{order}', [ProjectChangeOrderController::class, 'status'])->name('projects.orders.status');
        Route::post('/projects/{project}/invoices/{invoice?}', [\App\Http\Controllers\ProjectBillingController::class, 'invoice'])->name('projects.invoice');
        Route::post('/projects/{project}/invoices/{invoice}/payments', [\App\Http\Controllers\ProjectBillingController::class, 'record'])->name('projects.payment');
        Route::patch('/projects/{project}/invoices/{invoice}/refunds/{payment}', [\App\Http\Controllers\ProjectBillingController::class, 'refundDetails'])->name('projects.refund');
        Route::post('/projects/{project}/invoices/{invoice}/cancel-payment', [\App\Http\Controllers\ProjectPaymentController::class, 'cancel'])->name('projects.payment.cancel');
        Route::post('/projects/{project}/invoices/{invoice}/verify-payment', [\App\Http\Controllers\ProjectPaymentController::class, 'verify'])->middleware('throttle:12,1')->name('projects.payment.verify');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    });
});

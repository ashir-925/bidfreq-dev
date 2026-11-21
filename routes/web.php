<?php

use App\Http\Controllers\front\WebsiteController;
use App\Http\Controllers\RepairRequestController;
use App\Http\Middleware\EnsureActive;
use Illuminate\Support\Facades\Route;

Route::redirect('/admin', '/admin/dashboard');
Route::redirect('/user', '/user/dashboard');

Route::prefix('user')->middleware(['auth', EnsureActive::class, 'can:access-panel,"customer"', 'verified'])
    ->controller(WebsiteController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/new-estimate-request', 'new_estimate_request')->name('new-estimate-request');
        Route::get('/estimate-requests', [RepairRequestController::class, 'index'])->name('estimate-request');
        Route::post('/estimate-requests', [RepairRequestController::class, 'store'])->middleware('throttle:10,1')->name('repair-requests.store');
        Route::get('/estimate-requests/{repair}', [RepairRequestController::class, 'show'])->whereUuid('repair')->name('repair-requests.show');
        Route::get('/estimate-requests/{repair}/files/{file}', [RepairRequestController::class, 'download'])->whereUuid('repair')->name('repair-requests.download');
        Route::get('/estimate-request-detail/{repair?}', [RepairRequestController::class, 'show'])->whereUuid('repair')->name('estimate_request_detail');
        Route::post('/estimate-requests/{repair}/respond', [RepairRequestController::class, 'respond'])->whereUuid('repair')->name('repair-requests.respond');
        Route::get('/estimate-requests/{repair}/files/{file}/image', [RepairRequestController::class, 'image'])->whereUuid('repair')->name('repair-requests.image');
        Route::post('/estimate-requests/{repair}/decision', [RepairRequestController::class, 'decide'])->whereUuid('repair')->name('repair-requests.decision');
        Route::get('/estimate-requests/{repair}/versions/{release}/pdf', [RepairRequestController::class, 'pdf'])->whereUuid('repair')->name('repair-requests.pdf');
        Route::get('/projects', [\App\Http\Controllers\ProjectController::class, 'index'])->name('projects');
        Route::get('/project-detail/{project?}', [\App\Http\Controllers\ProjectController::class, 'show'])->whereUuid('project')->name('project-detail');
        Route::get('/projects/{project}/files/{file}', [\App\Http\Controllers\ProjectController::class, 'file'])->name('projects.file');
        Route::post('/projects/{project}/invoices/{invoice}/payment-session', [\App\Http\Controllers\ProjectPaymentController::class, 'start'])->middleware('throttle:12,1')->name('projects.payment.start');
        Route::post('/projects/{project}/invoices/{invoice}/cancel-payment', [\App\Http\Controllers\ProjectPaymentController::class, 'cancel'])->middleware('throttle:12,1')->name('projects.payment.cancel');
        Route::post('/projects/{project}/invoices/{invoice}/verify-payment', [\App\Http\Controllers\ProjectPaymentController::class, 'verify'])->middleware('throttle:12,1')->name('projects.payment.verify');
        Route::get('/notifications', [\App\Http\Controllers\AccountNoticeController::class, 'index'])->name('notifications');
        Route::patch('/notifications/read', [\App\Http\Controllers\AccountNoticeController::class, 'readAll'])->name('notifications.read-all');
        Route::patch('/notifications/{notice}', [\App\Http\Controllers\AccountNoticeController::class, 'read'])->whereNumber('notice')->name('notifications.read');
        Route::delete('/notifications/{notice}', [\App\Http\Controllers\AccountNoticeController::class, 'destroy'])->whereNumber('notice')->name('notifications.delete');
        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile');
        Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->middleware('throttle:10,1')->name('profile.update');
        Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'password'])->middleware('throttle:6,1')->name('profile.password');
        Route::delete('/profile/sessions/{session}', [\App\Http\Controllers\ProfileController::class, 'revoke'])->name('profile.sessions.revoke');
    });

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';

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
        Route::get('/projects', 'projects')->name('projects');
        Route::get('/project-detail/{id?}', 'project_detail')->name('project-detail');
        Route::get('/notifications', 'notifications')->name('notifications');
        Route::get('/profile', 'profile')->name('profile');
    });

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';

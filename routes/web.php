<?php

use App\Http\Controllers\front\WebsiteController;
use App\Http\Middleware\EnsureActive;
use Illuminate\Support\Facades\Route;

Route::redirect('/admin', '/admin/dashboard');
Route::redirect('/user', '/user/dashboard');

Route::prefix('user')->middleware(['auth', EnsureActive::class, 'can:access-panel,"customer"', 'verified'])
    ->controller(WebsiteController::class)->group(function () {
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/new-estimate-request', 'new_estimate_request')->name('new-estimate-request');
        Route::get('/estimate-requests', 'estimate_requests')->name('estimate-request');
        Route::get('/estimate-request-detail/{id?}', 'estimate_request_detail')->name('estimate_request_detail');
        Route::get('/projects', 'projects')->name('projects');
        Route::get('/project-detail/{id?}', 'project_detail')->name('project-detail');
        Route::get('/notifications', 'notifications')->name('notifications');
        Route::get('/profile', 'profile')->name('profile');
    });

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';

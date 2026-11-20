<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Middleware\EnsureActive;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/login', '/')->name('login');
    Route::redirect('/forgot-password', '/forgot-password')->name('forgot-password');

    Route::middleware(['auth', EnsureActive::class, 'can:access-panel,"admin"', 'verified'])->group(function () {
        Route::controller(AdminController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/estimates', 'estimates')->name('estimates');
            Route::get('/estimates/review/{id?}', 'estimates_review')->name('estimate-review');
            Route::get('/projects', 'projects')->name('projects');
            Route::get('/customers', 'customers')->name('customers');
            Route::get('/estimators', 'estimators')->name('estimators');
            Route::get('/reports', 'reports')->name('reports');
            Route::get('/notifications', 'notifications')->name('notifications');
            Route::get('/profile', 'profile')->name('profile');
        });
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    });
});

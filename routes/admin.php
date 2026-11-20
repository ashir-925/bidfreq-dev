<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\AdminUserController;
use App\Http\Middleware\EnsureActive;
use App\Http\Controllers\LibraryController;
use App\Models\LibraryTask;
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
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    });
});

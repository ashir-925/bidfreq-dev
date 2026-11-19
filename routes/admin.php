<?php

use App\Http\Controllers\admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

     Route::get('/login', [AdminController::class, 'login'])->name('login');
    Route::get('/forgot-password', [AdminController::class, 'forgotPassword'])->name('forgot-password');

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/estimates', [AdminController::class, 'estimates'])->name('estimates');
    Route::get('/estimates/review/{id?}', [AdminController::class, 'estimates_review'])->name('estimate-review');
    Route::get('/projects',[AdminController::class,'projects'])->name('projects');
    Route::get('/customers',[AdminController::class,'customers'])->name('customers');
    Route::get('/estimators',[AdminController::class,'estimators'])->name('estimators');
    Route::get('/reports',[AdminController::class,'reports'])->name('reports');
    Route::get('/notifications',[AdminController::class,'notifications'])->name('notifications');
    Route::get('/profile',[AdminController::class,'profile'])->name('profile');


   
});
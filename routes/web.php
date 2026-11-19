<?php

use App\Http\Controllers\front\WebsiteController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::redirect('/admin', '/admin/login');
Route::redirect('/user', '/');

Route::get('/',[WebsiteController::class,'login'])->name('login');
Route::get('/register',[WebsiteController::class,'register'])->name('register');
Route::get('/forgot-password',[WebsiteController::class,'forgotPassword'])->name('forgot-password');

Route::get('/user/dashboard',[WebsiteController::class,'dashboard'])->name('dashboard');
Route::get('/user/new-estimate-request',[WebsiteController::class,'new_estimate_request'])->name('new-estimate-request');
Route::get('/user/estimate-requests',[WebsiteController::class,'estimate_requests'])->name('estimate-request');
Route::get('/user/estimate-request-detail/{id?}',[WebsiteController::class,'estimate_request_detail'])->name('estimate_request_detail');
Route::get('/user/projects',[WebsiteController::class,'projects'])->name('projects');
Route::get('/user/project-detail/{id?}',[WebsiteController::class,'project_detail'])->name('project-detail');
Route::get('/user/notifications',[WebsiteController::class,'notifications'])->name('notifications');
Route::get('/user/profile',[WebsiteController::class,'profile'])->name('profile');


require __DIR__.'/admin.php';
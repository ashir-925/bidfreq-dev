<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function login()
    {
        return view('admin.auth.login');
    }
    public function forgotPassword()
    {
        return view('admin.auth.forgot-password');
    }

    public function dashboard()
    {
        return view('admin.pages.dashboard');
    }

    public function estimates()
    {
        return view('admin.pages.estimates');
    }
    public function estimates_review(){
        return view('admin.pages.estimates_review');
    }
    public function projects()
    {
        return view('admin.pages.projects');
    }
    public function customers()
    {
        return view('admin.pages.customers');
    }
    public function estimators()
    {
        return view('admin.pages.estimators');
    }
    public function reports()
    {
        return view('admin.pages.reports');
    }
    public function notifications()
    {
        return view('admin.pages.notifications');
    }
    public function profile()
    {
        return view('admin.pages.profile');

    }
}

<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function login()
    {
        return view('front.auth.login');
    }
    public function register()
    {
        return view('front.auth.register');
    }
    public function forgotPassword()
    {
        return view('front.auth.forgot-password');
    }

    public function dashboard()
    {
        return view('front.pages.dashboard');
    }
    public function new_estimate_request()
    {
        return view('front.pages.new_estimate_request');
    }
    public function estimate_requests()
    {
        return view('front.pages.estimate_request');
    }
    public function estimate_request_detail($id)
    {
        return view('front.pages.estimate_request_detail', compact('id'));
    }
    public function projects()
    {
        return view('front.pages.projects');
    }
    public function project_detail($id)
    {
        return view('front.pages.project_detail', compact('id'));
    }
    public function notifications()
    {
        return view('front.pages.notifications');
    }
    public function profile()
    {
        return view('front.pages.profile');
    }
}

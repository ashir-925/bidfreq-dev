<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\RepairRequest;
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
        $estimates = RepairRequest::where('user_id', request()->user()->id);
        $customerActions = (clone $estimates)->whereIn('status', ['approved', 'needs_info']);
        return view('front.pages.dashboard', [
            'openEstimateCount' => (clone $estimates)->whereIn('status', [...RepairRequest::ADMIN_ACTION_STATUSES, 'ai_pending', 'ai_retrying', 'needs_info', 'approved'])->count(),
            'projectCounts' => request()->user()->projects()->selectRaw('status, COUNT(*) AS aggregate')->groupBy('status')->pluck('aggregate', 'status'),
            'estimateCounts' => (clone $estimates)->selectRaw('status, COUNT(*) AS aggregate')->groupBy('status')->pluck('aggregate', 'status'),
            'awaitingApprovalCount' => (clone $estimates)->where('status', 'approved')->count(),
            'revisionCount' => (clone $estimates)->where('status', 'revision_requested')->count(),
            'customerActionCount' => (clone $customerActions)->count(),
            'customerActions' => $customerActions->latest('updated_at')->orderByDesc('id')->limit(5)->get(),
            'recentEstimates' => $estimates->with('currentRelease')->latest('updated_at')->orderByDesc('id')->limit(5)->get(),
        ]);
    }
    public function new_estimate_request()
    {
        return view('front.pages.new_estimate_request');
    }
    public function estimate_requests()
    {
        return view('front.pages.estimate_request');
    }
    public function estimate_request_detail($id = null)
    {
        return view('front.pages.estimate_request_detail', compact('id'));
    }
    public function projects()
    {
        return view('front.pages.projects');
    }
    public function project_detail($id = null)
    {
        return view('front.pages.project_detail', compact('id'));
    }
    public function notifications()
    {
        $completed = request()->user()->projects()->where('status', 'completed')->latest('updated_at')->paginate(20);
        return view('front.pages.notifications', ['completed' => $completed]);
    }
    public function profile()
    {
        return view('front.pages.profile');
    }
}

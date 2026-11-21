<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\RepairRequest;
use App\Models\RepairEstimateRelease;
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
        $queue = RepairRequest::query()->whereIn('status', RepairRequest::ADMIN_ACTION_STATUSES);
        return view('admin.pages.dashboard', [
            'estimateQueueCount' => (clone $queue)->count(),
            'urgentEstimateCount' => (clone $queue)->whereIn('priority', ['high', 'urgent'])->count(),
            'estimateCounts' => RepairRequest::query()->selectRaw('status, COUNT(*) AS aggregate')->groupBy('status')->pluck('aggregate', 'status'),
            'projectCounts' => \App\Models\Project::query()->selectRaw('status, COUNT(*) AS aggregate')->groupBy('status')->pluck('aggregate', 'status'),
            'estimatorWorkload' => \App\Models\Estimator::withCount(['projects' => fn ($q) => $q->whereIn('status', ['scheduled', 'in_progress'])])->orderByDesc('projects_count')->orderBy('name')->limit(8)->get(),
            'dailyReportCount' => \App\Models\ProjectDailyReport::count(),
            'verifiedToday' => RepairEstimateRelease::whereDate('created_at', today())->count(),
            'estimateQueue' => $queue->with(['customer', 'draft'])->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 ELSE 4 END")
                ->oldest()->orderBy('id')->limit(5)->get(),
        ]);
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
        $events = \App\Models\ProjectEvent::with('project')->latest('id')->paginate(20);
        return view('admin.pages.notifications', ['events' => $events]);
    }
    public function profile()
    {
        return view('admin.pages.profile');

    }
}

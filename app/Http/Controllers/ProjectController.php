<?php

namespace App\Http\Controllers;

use App\Models\Estimator;
use App\Models\LibraryTask;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Models\RepairRequest;
use Brick\Math\BigDecimal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $staff = $request->routeIs('admin.*');
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(Project::STATUSES)],
            'sort' => ['nullable', 'in:newest,oldest,address'],
        ]);
        $query = Project::with($staff ? ['customer', 'estimator'] : []);
        if ($staff) {
            Gate::authorize('manage', Project::class);
        } else {
            $query->where('user_id', $request->user()->id);
        }
        $counts = (clone $query)->selectRaw('status, COUNT(*) AS aggregate')->groupBy('status')->pluck('aggregate', 'status');
        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }
        if ($search = $filters['search'] ?? null) {
            $match = '%'.$search.'%';
            $query->where(function ($query) use ($match, $staff) {
                $query->whereLike('address', $match)->orWhereLike('reference', $match);
                if ($staff) {
                    $query->orWhereHas('customer', fn ($users) => $users->whereLike('email', $match)->orWhereLike('company_name', $match)->orWhereLike('first_name', $match)->orWhereLike('last_name', $match));
                }
            });
        }

        match ($filters['sort'] ?? 'newest') {
            'address' => $query->orderBy('address')->orderBy('id'),
            'oldest' => $query->orderBy('id'),
            default => $query->orderByDesc('id'),
        };

        return view($staff ? 'admin.pages.projects' : 'front.pages.projects', [
            'projects' => $query->paginate(15)->withQueryString(), 'filters' => $filters, 'counts' => $counts, 'staff' => $staff,
        ]);
    }

    public function show(Request $request, ?Project $project = null)
    {
        $staff = $request->routeIs('admin.*');
        if (! $project?->exists) {
            return view('front.pages.project_detail', ['project' => null]);
        }
        Gate::authorize('view', $project);
        $project->load(['release', 'repairRequest']);
        if ($staff) {
            $project->load(['customer', 'estimator']);
        }
        $orders = $project->changeOrders()->when(! $staff, fn ($query) => $query->where('status', 'approved'))->get();
        $extras = $orders->where('status', 'approved')->reduce(fn ($total, $order) => $total->plus($order->total), BigDecimal::zero());
        $reports = $project->reports()->with('files')->paginate(10)->withQueryString();

        return view($staff ? 'admin.pages.project_detail' : 'front.pages.project_detail', [
            'project' => $project, 'staff' => $staff, 'orders' => $orders,
            'invoices' => $project->invoices()->when(! $staff, fn ($query) => $query->where('status', 'sent'))->with('payments')->get(),
            'original' => $project->release->snapshot['total'],
            'extras' => (string) $extras,
            'combined' => (string) $extras->plus($project->release->snapshot['total']),
            'reports' => $reports,
            'reportOptions' => $staff ? $project->reports()->get(['id', 'report_date']) : collect(),
            'documents' => $project->files()->whereNull('project_daily_report_id')->get(),
            'events' => $staff ? $project->events()->limit(25)->get() : collect(),
            'estimators' => $staff ? Estimator::where('active', true)->orderBy('name')->get() : collect(),
            'tasks' => $staff ? LibraryTask::orderBy('task_name')->get(['task_id', 'task_name', 'unit', 'bidfreq_standard_rate']) : collect(),
        ]);
    }

    public function file(Project $project, ProjectFile $file, Request $request)
    {
        Gate::authorize('view', $project);
        abort_unless((int) $file->project_id === $project->id, 404);
        $disk = Storage::disk('local');
        abort_unless($disk->exists($file->path), 404);
        $headers = ['Content-Type' => $file->mime, 'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store'];
        if ($request->boolean('preview') && in_array($file->mime, RepairRequest::PHOTO_MIMES, true)) {
            return $disk->response($file->path, null, $headers);
        }

        return $disk->download($file->path, $file->name, $headers);
    }
}




<?php

namespace App\Http\Controllers;

use App\Models\{Estimator, Project, ProjectDailyReport, User};
use App\Support\ReportFailure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Gate, Log};
use Illuminate\Validation\Rule;
use Throwable;

class BusinessDirectoryController extends Controller
{
    public function customers(Request $request)
    {
        Gate::authorize('manage', Project::class);
        $query = User::where('role', 'customer')->whereHas('projects')->withCount('projects');
        return $this->listing($request, $query, 'customers', ['first_name', 'last_name', 'email', 'company_name'], 'last_name');
    }

    public function customer(User $user)
    {
        Gate::authorize('manage', Project::class);
        abort_unless($user->role === 'customer' && $user->projects()->exists(), 404);
        return view('admin.pages.account_detail', ['account' => $user, 'estimates' => $user->repairRequests()->latest('id')->paginate(15, ['*'], 'estimate_page'), 'projects' => $user->projects()->latest('id')->paginate(15)]);
    }

    public function estimators(Request $request)
    {
        Gate::authorize('manage', Project::class);
        return $this->listing($request, Estimator::withCount('projects'), 'estimators', ['name', 'email', 'phone'], 'name');
    }

    private function listing(Request $request, $query, string $type, array $columns, string $name)
    {
        $filters = $request->validate(['search' => 'nullable|string|max:100', 'status' => 'nullable|in:active,inactive', 'sort' => 'nullable|in:name,newest']);
        if (filled($filters['search'] ?? null)) {
            $query->where(function ($query) use ($columns, $filters) {
                foreach ($columns as $column) {
                    $query->orWhereLike($column, '%'.$filters['search'].'%');
                }
            });
        }
        if ($status = $filters['status'] ?? null) {
            $type === 'estimators' ? $query->where('active', $status === 'active') : $query->where('status', $status);
        }
        ($filters['sort'] ?? '') === 'newest' ? $query->orderByDesc('id') : $query->orderBy($name)->orderBy('id');

        return view('admin.pages.'.$type, [$type => $query->paginate(20)->withQueryString(), 'filters' => $filters]);
    }

    public function estimator(Estimator $estimator)
    {
        Gate::authorize('manage', Project::class);
        return view('admin.pages.estimator_detail', ['estimator' => $estimator, 'projects' => $estimator->projects()->latest('id')->paginate(15)]);
    }

    public function saveEstimator(Request $request, ?Estimator $estimator = null)
    {
        Gate::authorize('manage', Project::class);
        if (is_string($request->input('email'))) {
            $request->merge(['email' => strtolower(trim($request->input('email')))]);
        }
        $data = $request->validate([
            'name' => 'required|string|max:150', 'phone' => 'required|string|max:40',
            'email' => ['required', 'email', 'max:255', Rule::unique('estimators')->ignore($estimator?->id)],
            'active' => 'required|boolean',
        ]);
        try {
            $contact = $estimator ?? new Estimator();
            $before = $contact->exists ? $contact->only(array_keys($data)) : null;
            $contact->fill($data)->save();
            Log::channel('daily')->info('Estimator contact saved', ['user_id' => $request->user()->id, 'estimator_id' => $contact->id, 'before' => $before, 'after' => $data]);
            return redirect()->route('admin.estimators.show', $contact)->with('status', 'Estimator saved.');
        } catch (Throwable $exception) {
            ReportFailure::log($exception, 'estimator.save', $request->user()->id);
            return back()->withInput()->with('error', 'Could not save the estimator.');
        }
    }

    public function reports(Request $request)
    {
        Gate::authorize('manage', Project::class);
        $data = $request->validate(['search' => 'nullable|string|max:100', 'sort' => 'nullable|in:newest,oldest', 'date' => 'nullable|date_format:Y-m-d']);
        $query = ProjectDailyReport::with('project');
        if (filled($data['search'] ?? null)) {
            $match = '%'.$data['search'].'%';
            $query->where(fn ($q) => $q->whereLike('reported_by', $match)->orWhereHas('project', fn ($p) => $p->whereLike('address', $match)));
        }
        if ($date = $data['date'] ?? null) {
            $query->whereDate('report_date', $date);
        }
        return view('admin.pages.reports', ['reports' => $query->orderBy('report_date', ($data['sort'] ?? '') === 'oldest' ? 'asc' : 'desc')->orderBy('id')->paginate(20)->withQueryString()]);
    }
}


<?php

namespace App\Http\Controllers;

use App\Jobs\AnalyzeRepairRequest;
use App\Jobs\SendRepairRequestNotice;
use App\Support\ReportFailure;
use App\Models\RepairRequest;
use App\Models\LibraryTask;
use App\Models\LibraryTaskChange;
use App\Services\Estimates\DraftPricing;
use App\Services\Estimates\EstimateReview;
use Brick\Math\BigDecimal;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class StaffRepairRequestController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('manage', RepairRequest::class);
        $filters = $request->validate([
            'priority' => ['nullable', 'in:low,normal,high,urgent'],
            'filter' => ['nullable', 'in:high'],
            'status' => ['nullable', 'in:ai_pending,ai_retrying,ai_failed,draft,under_review,needs_info,declined,approved,revision_requested,customer_approved,customer_rejected'],
            'property' => ['nullable', 'string', 'max:100'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'trade' => ['nullable', \Illuminate\Validation\Rule::in(RepairRequest::TRADES)],
            'sort' => ['nullable', 'in:sla,newest,oldest,amount-high'],
        ]);

        if (! isset($filters['priority']) && ($filters['filter'] ?? '') === 'high') {
            $filters['priority'] = 'high';
        }
        unset($filters['filter']);

        $query = RepairRequest::query()->with(['draft.lines', 'customer', 'currentRelease'])->orderByRaw(
            "CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'normal' THEN 3 ELSE 4 END"
        );
        $counts = RepairRequest::query()->selectRaw('status, COUNT(*) AS aggregate')->groupBy('status')->pluck('aggregate', 'status');
        foreach (['priority', 'status'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }
        if ($property = $filters['property'] ?? null) {
            $match = '%'.$property.'%';
            $query->where(fn ($q) => $q->whereLike('property_name', $match)->orWhereLike('street_address', $match)
                ->orWhereLike('zip', $match)->orWhereLike('reference', '%'.\Illuminate\Support\Str::after(strtolower($property), 'req-').'%')
                ->orWhereHas('customer', fn ($user) => $user->whereLike('first_name', $match)->orWhereLike('last_name', $match)->orWhereLike('company_name', $match)));
        }
        if ($trade = $filters['trade'] ?? null) {
            $query->forTrade($trade);
        }
        $sort = $filters['sort'] ?? 'sla';
        if ($sort === 'amount-high') {
            $query->orderByDesc(\App\Models\RepairDraft::select('total')->whereColumn('repair_request_id', 'repair_requests.id'));
        }
        $direction = $sort === 'newest' ? 'desc' : 'asc';
        $query->orderBy('created_at', $direction)->orderBy('id', $direction);
        if ($date = $filters['date'] ?? null) {
            $query->whereDate('created_at', $date);
        }

        return view('admin.pages.estimates', ['requests' => $query->paginate(10)->withQueryString(), 'filters' => $filters, 'counts' => $counts]);
    }

    public function show(?RepairRequest $repair = null)
    {
        Gate::authorize('manage', RepairRequest::class);

        if (! $repair?->exists) {
            return view('admin.pages.estimates_review', ['repair' => null]);
        }

        $repair->load(['customer', 'files', 'draft.lines.task', 'currentRelease', 'events', 'attempts' => fn ($q) => $q->latest('id')]);
        $lines = $repair->draft?->lines ?? collect();
        $librarySnapshots = LibraryTaskChange::with('version')
            ->whereIn('task_id', $lines->pluck('task_id')->filter()->unique())
            ->whereIn('library_version_id', $lines->pluck('library_version_id')->filter()->unique())
            ->get()->keyBy(fn ($change) => $change->task_id.':'.$change->library_version_id);

        return view('admin.pages.estimates_review', [
            'librarySnapshots' => $librarySnapshots,
            'libraryTasks' => in_array($repair->status, ['ai_failed', 'draft', 'under_review', 'revision_requested'], true) ? LibraryTask::orderBy('task_id')->get() : collect(),
            'repair' => $repair,
        ]);
    }

    public function retry(RepairRequest $repair)
    {
        Gate::authorize('manage', RepairRequest::class);
        $queued = DB::transaction(function () use ($repair) {
            $current = RepairRequest::lockForUpdate()->findOrFail($repair->id);
            if (! in_array($current->status, ['ai_failed', 'under_review'], true)) {
                return false;
            }

            if ($draft = $current->draft) {
                $current->events()->create([
                    'user_id' => request()->user()->id, 'action' => 'ai_requeued',
                    'details' => ['previous_draft' => $draft->load('lines')->toArray()],
                ]);
                $draft->delete();
            }

            $current->update(['status' => 'ai_pending', 'ai_run' => $current->ai_run + 1]);
            AnalyzeRepairRequest::dispatch($current->id, $current->ai_run)->onConnection(config('estimate_requests.queue_connection'));

            return true;
        });

        return back()->with($queued ? 'status' : 'error', $queued ? 'A fresh AI analysis is queued.' : 'This request is no longer eligible for an AI retry.');
    }

    public function manual(Request $request, RepairRequest $repair, DraftPricing $pricing)
    {
        Gate::authorize('manage', RepairRequest::class);
        $input = $request->validate([
            'complaint' => ['required', 'string', 'max:5000'],
            'cause' => ['required', 'string', 'max:5000'],
            'correction' => ['required', 'string', 'max:5000'],
            'complications' => ['required', 'string', 'max:5000'],
            'lines' => ['required', 'array', 'list', 'min:1', 'max:100'],
            'lines.*' => ['array:task_id,quantity,rate'],
            'lines.*.task_id' => ['required', 'string', 'max:32', 'exists:library_tasks,task_id'],
            'lines.*.quantity' => ['required', 'regex:/^\d{1,8}(?:\.\d{1,6})?$/D', 'numeric', 'gt:0'],
            'lines.*.rate' => ['nullable', 'regex:/^\d{1,10}(?:\.\d{1,6})?$/D'],
        ]);

        DB::transaction(function () use ($request, $repair, $pricing, $input) {
            $repair = RepairRequest::lockForUpdate()->findOrFail($repair->id);
            abort_unless(in_array($repair->status, ['ai_failed', 'under_review'], true) && ! $repair->draft()->exists(), 409);
            $analysis = array_diff_key($input, ['lines' => true]) + ['prepared_by' => $request->user()->id];
            $result = $pricing->manual($repair, $analysis, $input['lines']);
            $repair->draft()->create($result['draft'])->lines()->createMany($result['lines']);
            $repair->update(['status' => 'draft']);
        });

        return back()->with('status', 'The manual draft was saved for internal review.');
    }

    public function nte(Request $request, RepairRequest $repair)
    {
        Gate::authorize('manage', RepairRequest::class);
        $new = $request->validate(['nte' => ['present', 'nullable', 'regex:/^\d{1,12}(?:\.\d{1,2})?$/D']])['nte'];
        $changes = DB::transaction(function () use ($repair, $new) {
            $repair = RepairRequest::lockForUpdate()->findOrFail($repair->id);
            $before = $repair->nte;
            $repair->update(['nte' => $new]);
            $draft = $repair->draft;
            if ($draft) {
                $draft->update(['over_nte' => $new !== null && BigDecimal::of($draft->total)->isGreaterThan($new)]);
            }

            return ['before' => $before, 'after' => $repair->nte];
        });
        if ($changes['before'] !== $changes['after']) {
            Log::channel('daily')->info('Estimate request NTE updated', $changes + [
                'repair_request_id' => $repair->id, 'user_id' => $request->user()->id,
            ]);
        }

        return back()->with('status', 'The Not-to-Exceed amount was updated.');
    }

    public function save(Request $request, RepairRequest $repair, EstimateReview $review)
    {
        Gate::authorize('manage', RepairRequest::class);
        $input = $request->validate([
            'review_version' => ['required', 'integer', 'min:0'],
            'complaint' => ['required', 'string', 'max:5000'],
            'cause' => ['required', 'string', 'max:5000'],
            'correction' => ['required', 'string', 'max:5000'],
            'complications' => ['required', 'string', 'max:5000'],
            'assumptions' => ['nullable', 'string', 'max:5000'],
            'exclusions' => ['nullable', 'string', 'max:5000'],
            'review_note' => ['required', 'string', 'max:2000'],
            'evidence_links' => ['nullable', 'array:complaint,cause,correction,complications'],
            'evidence_links.*' => ['array', 'max:20'],
            'evidence_links.*.*' => ['integer', \Illuminate\Validation\Rule::exists('repair_request_files', 'id')->where('repair_request_id', $repair->id)],
            'lines' => ['required', 'array', 'list', 'min:1', 'max:600'],
            'lines.*' => ['array:line_id,task_id,quantity,rate,override_reason,confirmed,exclude'],
            'lines.*.line_id' => ['nullable', 'integer'],
            'lines.*.task_id' => ['nullable', 'string', 'max:32'],
            'lines.*.quantity' => ['nullable', 'regex:/^\d{1,8}(?:\.\d{1,6})?$/D'],
            'lines.*.rate' => ['nullable', 'regex:/^\d{1,10}(?:\.\d{1,6})?$/D'],
            'lines.*.override_reason' => ['nullable', 'string', 'max:1000'],
            'lines.*.confirmed' => ['nullable', 'boolean'],
            'lines.*.exclude' => ['nullable', 'boolean'],
        ]);
        try {
            $review->save($repair, $request->user(), $input);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            ReportFailure::log($e, 'repair.review.save', $request->user()->id);

            return back()->withInput()->with('error', 'We could not save your review. Please try again.');
        }

        return redirect()->route('admin.estimate-review', $repair)->with('status', 'Estimate review saved.');
    }

    public function release(Request $request, RepairRequest $repair, EstimateReview $review)
    {
        Gate::authorize('manage', RepairRequest::class);
        $expected = $request->validate(['review_version' => ['required', 'integer', 'min:0']])['review_version'];
        try {
            $release = $review->release($repair, $request->user(), (int) $expected);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            ReportFailure::log($e, 'repair.review.release', $request->user()->id);

            return back()->with('error', 'We could not release the estimate. Please try again.');
        }

        return back()->with('status', 'Estimate version '.$release->version.' was released to the customer.');
    }

    public function disposition(Request $request, RepairRequest $repair)
    {
        Gate::authorize('manage', RepairRequest::class);
        $data = $request->validate([
            'action' => ['required', 'in:needs_info,declined'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        try {
            DB::transaction(function () use ($request, $repair, $data) {
                $current = RepairRequest::lockForUpdate()->findOrFail($repair->id);
                if (! in_array($current->status, ['draft', 'under_review', 'ai_failed', 'revision_requested'], true)) {
                    throw ValidationException::withMessages(['action' => 'This request is no longer eligible for that action.']);
                }
                $current->events()->create(['user_id' => $request->user()->id, 'action' => $data['action'],
                    'details' => ['message' => $data['message'], 'previous_status' => $current->status]]);
                $current->update(['status' => $data['action']]);
                SendRepairRequestNotice::dispatch($current->id, $data['action'], $current->user_id)
                    ->onConnection(config('estimate_requests.queue_connection'));
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            ReportFailure::log($e, 'repair.disposition', $request->user()->id);

            return back()->with('error', 'We could not update the request. Please try again.');
        }

        return back()->with('status', 'The customer was notified of the request update.');
    }
}

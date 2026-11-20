<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitRepairRequest;
use App\Jobs\AnalyzeRepairRequest;
use App\Jobs\SendRepairRequestNotice;
use App\Models\RepairRequest;
use App\Models\RepairRequestFile;
use App\Support\ReportFailure;
use App\Models\RepairEstimateRelease;
use App\Services\Estimates\EstimateReview;
use App\Services\Estimates\EstimatePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class RepairRequestController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'status' => ['nullable', 'in:under_review,needs_info,declined,approved,revision_requested,customer_approved,customer_rejected,in_progress,completed'],
            'search' => ['nullable', 'string', 'max:100'],
            'trade' => ['nullable', \Illuminate\Validation\Rule::in(RepairRequest::TRADES)],
            'date' => ['nullable', 'in:today,7,30,90'],
            'sort' => ['nullable', 'in:newest,oldest,amount-high,amount-low'],
        ]);
        $query = RepairRequest::where('user_id', $request->user()->id);
        $counts = (clone $query)->selectRaw('status, COUNT(*) AS aggregate')->groupBy('status')->pluck('aggregate', 'status');
        if ($status = $filters['status'] ?? null) {
            $status === 'under_review' ? $query->whereIn('status', ['ai_pending', 'ai_retrying', 'ai_failed', 'draft', 'under_review']) : $query->where('status', $status);
        }
        if ($search = $filters['search'] ?? null) {
            $query->where(fn ($q) => $q->whereLike('street_address', '%'.$search.'%')->orWhereLike('zip', '%'.$search.'%')
                ->orWhereLike('descriptions', '%'.$search.'%')->orWhereLike('property_name', '%'.$search.'%')
                ->orWhereLike('reference', '%'.Str::after(strtoupper($search), 'REQ-').'%'));
        }
        if ($trade = $filters['trade'] ?? null) {
            $query->forTrade($trade);
        }
        if ($date = $filters['date'] ?? null) {
            $query->where('created_at', '>=', $date === 'today' ? now()->startOfDay() : now()->subDays((int) $date));
        }
        $sort = $filters['sort'] ?? 'newest';
        if (str_starts_with($sort, 'amount-')) {
            $direction = $sort === 'amount-high' ? 'DESC' : 'ASC';
            $query->orderByRaw("(SELECT CAST(JSON_EXTRACT(snapshot, '$.total') AS DECIMAL(24,2)) FROM repair_estimate_releases
                WHERE repair_request_id = repair_requests.id ORDER BY version DESC LIMIT 1) {$direction}");
        }
        $query->orderBy('id', $sort === 'oldest' ? 'asc' : 'desc');

        return view('front.pages.estimate_request', ['requests' => $query->with('currentRelease')->withCount(['files' => fn ($q) => $q->where('kind', 'photo')])->paginate(8)->withQueryString(), 'counts' => $counts, 'filters' => $filters]);
    }

    public function store(SubmitRepairRequest $request)
    {
        $data = $request->validated();
        $data['dimensions'] ??= [];
        $owner = $request->user()->id;
        $existing = fn () => RepairRequest::where('user_id', $owner)->where('submission_key', $data['submission_key'])->first();
        if ($repair = $existing()) {
            return $this->received($repair);
        }

        $paths = [];
        $reference = (string) Str::uuid();
        $disk = Storage::disk(config('estimate_requests.disk'));

        try {
            $attachments = [];
            foreach (['photos' => 'photo', 'documents' => 'document'] as $field => $kind) {
                foreach ($request->file($field, []) as $index => $file) {
                    $path = $file->store('repair-requests/'.$reference, config('estimate_requests.disk'));
                    if ($path === false) {
                        throw new \RuntimeException('A private upload could not be stored.');
                    }
                    $paths[] = $path;
                    $attachments[] = [
                        'kind' => $kind, 'trade' => $kind === 'photo' ? ($data['photo_trades'][$index] ?? null) : null,
                        'name' => Str::limit(basename(str_replace('\\', '/', $file->getClientOriginalName())), 200, ''),
                        'mime' => $file->getMimeType(), 'bytes' => $file->getSize(), 'path' => $path,
                    ];
                }
            }

            $repair = DB::transaction(function () use ($data, $owner, $reference, $attachments) {
                $repair = RepairRequest::create(Arr::except($data, ['photos', 'documents', 'photo_trades', 'consent']) + [
                    'user_id' => $owner, 'reference' => $reference, 'dimensions' => [], 'status' => 'ai_pending',
                ]);
                $repair->files()->createMany($attachments);

                $connection = config('estimate_requests.queue_connection');
                SendRepairRequestNotice::dispatch($repair->id, 'received', $owner)->onConnection($connection);
                SendRepairRequestNotice::notifyStaff($repair, 'new');
                AnalyzeRepairRequest::dispatch($repair->id, 1)->onConnection($connection);

                return $repair;
            });
        } catch (Throwable $e) {
            $disk->delete($paths);
            if ($repair = $existing()) {
                return $this->received($repair);
            }

            ReportFailure::log($e, 'repair.submit', $owner);
            $message = 'We could not save your request. Please try again.';

            return $request->expectsJson()
                ? response()->json(['message' => $message], 503)
                : back()->withInput()->with('error', $message);
        }

        return $this->received($repair);
    }

    public function show(?RepairRequest $repair = null)
    {
        if (! $repair?->exists) {
            return view('front.pages.estimate_request_detail', ['repair' => null]);
        }
        Gate::authorize('view', $repair);

        if (request()->routeIs('estimate_request_detail')) {
            abort_unless($repair->currentRelease, 404);
        }

        return view('front.pages.estimate_request_detail', ['repair' => $repair->load(['files', 'currentRelease', 'events', 'releases' => fn ($q) => $q->latest('version')])]);
    }

    public function decide(Request $request, RepairRequest $repair, EstimateReview $review)
    {
        Gate::authorize('view', $repair);
        abort_unless($request->user()->id === $repair->user_id, 403);
        $input = $request->validate([
            'release_id' => ['required', 'integer'],
            'decision' => ['required', 'in:approve,reject,revise'],
            'comment' => ['required_if:decision,revise', 'nullable', 'string', 'max:2000'],
            'signature' => ['required_if:decision,approve', 'nullable', 'string', 'min:3', 'max:150'],
            'acceptance' => ['accepted_if:decision,approve'],
        ]);
        $input['release_id'] = (int) $input['release_id'];
        $review->decide($repair, $request->user(), $input);

        return back()->with('status', 'Your estimate decision was recorded.');
    }

    public function pdf(RepairRequest $repair, RepairEstimateRelease $release, EstimatePdf $pdf)
    {
        Gate::authorize('view', $repair);
        abort_unless((int) $release->repair_request_id === $repair->id, 404);

        return response($pdf->render($release), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="BidFreq-estimate-v'.$release->version.'.pdf"',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function respond(Request $request, RepairRequest $repair)
    {
        Gate::authorize('view', $repair);
        abort_unless($request->user()->id === $repair->user_id, 403);
        $input = $request->validate([
            'message' => ['nullable', 'required_without:files', 'string', 'max:3000'],
            'files' => ['nullable', 'required_without:message', 'array', 'list', 'max:6'],
            'files.*' => ['required', 'file', 'mimetypes:'.implode(',', RepairRequest::DOCUMENT_MIMES), 'max:5120'],
        ]);
        $files = $request->file('files', []);
        if (array_sum(array_map(fn ($file) => $file->getSize(), $files)) > 12 * 1024 * 1024) {
            throw \Illuminate\Validation\ValidationException::withMessages(['files' => 'Supporting files can total up to 12 MB.']);
        }

        $paths = [];
        try {
            DB::transaction(function () use ($request, $repair, $input, $files, &$paths) {
                $current = RepairRequest::lockForUpdate()->findOrFail($repair->id);
                if ($current->status !== 'needs_info') {
                    throw \Illuminate\Validation\ValidationException::withMessages(['message' => 'This request is no longer waiting for more information.']);
                }
                foreach ($files as $file) {
                    $path = $file->store('repair-requests/'.$current->reference, config('estimate_requests.disk'));
                    if ($path === false) {
                        throw new \RuntimeException('A supporting file could not be stored.');
                    }
                    $paths[] = $path;
                    $mime = $file->getMimeType();
                    $current->files()->create([
                        'path' => $path, 'mime' => $mime, 'bytes' => $file->getSize(),
                        'kind' => in_array($mime, RepairRequest::PHOTO_MIMES, true) ? 'photo' : 'document',
                        'name' => Str::limit(basename(str_replace('\\', '/', $file->getClientOriginalName())), 200, ''),
                    ]);
                }
                $current->events()->create(['user_id' => $request->user()->id, 'action' => 'information_received',
                    'details' => ['message' => $input['message'] ?? null]]);
                $previous = $current->events()->where('action', 'needs_info')->first()->details['previous_status'] ?? null;
                $current->update(['status' => $previous === 'revision_requested' ? 'revision_requested' : 'under_review']);
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Storage::disk(config('estimate_requests.disk'))->delete($paths);
            ReportFailure::log($e, 'repair.respond', $request->user()->id);

            return back()->with('error', 'We could not save your response. Please try again.');
        }

        return back()->with('status', 'Your information was sent for review.');
    }

    public function image(RepairRequest $repair, RepairRequestFile $file)
    {
        Gate::authorize('view', $repair);
        abort_unless($file->repair_request_id === $repair->id && in_array($file->mime, RepairRequest::PHOTO_MIMES, true), 404);
        $disk = Storage::disk(config('estimate_requests.disk'));
        abort_unless($disk->exists($file->path), 404);

        return $disk->response($file->path, null, [
            'Content-Type' => $file->mime, 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function download(RepairRequest $repair, RepairRequestFile $file)
    {
        Gate::authorize('view', $repair);
        abort_unless($file->repair_request_id === $repair->id, 404);
        $disk = Storage::disk(config('estimate_requests.disk'));
        abort_unless($disk->exists($file->path), 404);

        return $disk->download($file->path, $file->name, [
            'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function received(RepairRequest $repair)
    {
        $url = route('repair-requests.show', $repair);
        $message = 'Your request '.$repair->number().' has been received and is now under review.';

        if (request()->expectsJson()) {
            request()->session()->flash('status', $message);

            return response()->json(['redirect' => $url], 201);
        }

        return redirect($url)->with('status', $message);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\LibraryImportPreview;
use App\Models\LibraryTask;
use App\Models\LibraryTaskChange;
use App\Models\LibraryVersion;
use App\Services\Library\LibraryFormat;
use App\Services\Library\PricingLibrary;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function __construct(private PricingLibrary $library) {}

    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'unit' => ['nullable', 'string', 'max:100'],
            'mode' => ['nullable', 'in:fixed,quote_by_scope'],
            'flag' => ['nullable', 'in:ambiguous,wide_range,provisional'],
        ]);
        $query = LibraryTask::query();
        if ($search = $filters['search'] ?? null) {
            $query->where(fn ($query) => $query->whereLike('task_id', '%'.$search.'%')->orWhereLike('task_name', '%'.$search.'%'));
        }
        if ($unit = $filters['unit'] ?? null) {
            $query->where('unit', $unit);
        }
        if ($mode = $filters['mode'] ?? null) {
            $query->where('pricing_mode', $mode);
        }
        match ($filters['flag'] ?? null) {
            'ambiguous' => $query->whereIn('task_id', LibraryTask::AMBIGUOUS),
            'wide_range' => $query->whereIn('task_id', LibraryTask::WIDE_RANGE),
            'provisional' => $query->where('times_seen_scopes', 1),
            default => null,
        };

        return view('admin.library.index', [
            'tasks' => $query->orderBy('task_id')->paginate(25)->withQueryString(),
            'units' => LibraryTask::distinct()->orderBy('unit')->pluck('unit'),
            'filters' => $filters,
            'versions' => LibraryVersion::with('user')->latest('id')->limit(5)->get(),
        ]);
    }

    public function show(LibraryTask $task)
    {
        return view('admin.library.show', ['task' => $task, 'fields' => $this->library->format->row($task)]);
    }

    public function edit(LibraryTask $task)
    {
        return view('admin.library.edit', ['task' => $task, 'fields' => $this->library->format->row($task)]);
    }

    public function update(Request $request, LibraryTask $task)
    {
        $version = $request->validate(['library_version_id' => ['required', 'integer']])['library_version_id'];
        $changed = $this->library->edit($task, $request->only(LibraryFormat::FIELDS), $request->user(), (string) $version);

        return redirect()->route('admin.library.show', $task)->with('status', $changed ? 'Library task updated.' : 'No task values changed.');
    }

    public function history(LibraryTask $task)
    {
        return view('admin.library.history', [
            'task' => $task,
            'changes' => LibraryTaskChange::with('version.user')->where('task_id', $task->task_id)->latest('id')->paginate(20),
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'extensions:csv,json', 'max:2048']]);
        $file = $request->file('file');
        $preview = $this->library->preview($file->getPathname(), $file->getClientOriginalName(), $request->user());

        return redirect()->route('admin.library.preview', $preview);
    }

    public function preview(Request $request, string $preview)
    {
        $record = LibraryImportPreview::whereKey($preview)->where('user_id', $request->user()->id)->firstOrFail();
        if ($record->expires_at->isPast()) {
            return redirect()->route('admin.library.import')->with('error', 'The preview expired. Upload the file again.');
        }

        [$counts, $changes] = $this->library->changes($record->rows);

        return view('admin.library.preview', [
            'preview' => $record, 'counts' => $counts, 'changes' => $changes,
            'stale' => $this->library->isStale($record),
        ]);
    }

    public function apply(Request $request, string $preview)
    {
        $version = $this->library->apply($preview, $request->user());

        return redirect()->route('admin.library.index')->with('status', 'Library import applied. Version #'.$version->id.' was recorded.');
    }

    public function export(string $format)
    {
        return response()->streamDownload(function () use ($format) {
            $out = fopen('php://output', 'wb');
            if ($format === 'csv') {
                fputcsv($out, LibraryFormat::FIELDS, escape: '');
            } else {
                fwrite($out, "[\n");
            }

            $first = true;
            foreach (LibraryTask::orderBy('task_id')->cursor() as $task) {
                $row = $this->library->format->row($task);
                if ($format === 'csv') {
                    fputcsv($out, $row, escape: '');
                } else {
                    fwrite($out, ($first ? '' : ",\n").$this->library->format->jsonRow($row));
                    $first = false;
                }
            }

            if ($format === 'json') {
                fwrite($out, "\n]\n");
            }

            fclose($out);
        }, 'bidfreq-library.'.$format, [
            'Content-Type' => $format === 'csv' ? 'text/csv; charset=UTF-8' : 'application/json',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}

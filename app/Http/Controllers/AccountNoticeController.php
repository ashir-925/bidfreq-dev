<?php

namespace App\Http\Controllers;

use App\Models\AccountNotice;
use Illuminate\Http\Request;

class AccountNoticeController extends Controller
{
    public function index(Request $request)
    {
        $staff = $request->routeIs('admin.*');
        $filter = $request->validate(['category' => 'nullable|in:estimates,projects,billing', 'status' => 'nullable|in:unread,read']);
        $query = $this->owned($request);
        if ($category = $filter['category'] ?? null) $query->where('category', $category);
        match ($filter['status'] ?? null) {
            'unread' => $query->whereNull('read_at'),
            'read' => $query->whereNotNull('read_at'),
            default => null,
        };

        return view($staff ? 'admin.pages.notifications' : 'front.pages.notifications', [
            'notices' => $query->latest('id')->paginate(15)->withQueryString(),
            'noticeRoute' => $staff ? 'admin.notifications' : 'notifications',
        ]);
    }

    public function read(Request $request, int $notice)
    {
        $record = $this->owned($request)->findOrFail($notice);
        if (!$record->read_at) $record->update(['read_at' => now()]);
        return back()->with('status', 'Notification marked as read.');
    }

    public function readAll(Request $request)
    {
        $this->owned($request)->whereNull('read_at')->update(['read_at' => now()]);
        return back()->with('status', 'All notifications marked as read.');
    }

    public function destroy(Request $request, int $notice)
    {
        $this->owned($request)->findOrFail($notice)->delete();
        return back()->with('status', 'Notification deleted.');
    }

    private function owned(Request $request)
    {
        return AccountNotice::where('user_id', $request->user()->id);
    }
}

@php($unreadNotices = \App\Models\AccountNotice::where('user_id', request()->user()->id)->whereNull('read_at')->count())
@if ($unreadNotices)<span class="nav-item__badge is-danger" aria-label="{{ $unreadNotices }} unread">{{ $unreadNotices > 99 ? '99+' : $unreadNotices }}</span>@endif

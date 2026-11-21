<header class="page-header"><div class="page-header__text"><h1 class="page-header__title">Notifications</h1></div>
<form action="{{ route($noticeRoute.'.read-all') }}" method="POST">@csrf @method('PATCH')
<button type="submit" class="btn btn--secondary" data-loading-text="Updating…">Mark all as read</button></form></header>

<form class="filter-bar" method="GET" action="{{ route($noticeRoute) }}">
<label class="label">Category <select class="select" name="category"><option value="">All categories</option>
@foreach (['estimates','projects','billing'] as $category)<option value="{{ $category }}" @selected(request('category') === $category)>{{ ucfirst($category) }}</option>@endforeach
</select></label>
<label class="label">Read status <select class="select" name="status"><option value="">All</option>
@foreach (['unread','read'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach
</select></label>
<button class="btn btn--primary btn--sm" type="submit">Apply filters</button>
</form>
<div class="panel-stack">
@forelse ($notices as $notice)
<article class="sec">
<div class="sec__head"><div class="sec__head-left"><div>
<h2 class="sec__title"><a href="{{ $notice->link }}">{{ $notice->title }}</a></h2>
<div class="sec__sub">{{ ucfirst($notice->category) }} · <time datetime="{{ $notice->created_at->toAtomString() }}">{{ $notice->created_at->format('M j, Y · g:i A') }}</time></div>
</div></div><span @class(['chip','chip--blue' => !$notice->read_at])>{{ $notice->read_at ? 'Read' : 'Unread' }}</span></div>
<div class="sec__body"><p>{{ $notice->message }}</p><div class="page-header__actions">
@if (!$notice->read_at)<form action="{{ route($noticeRoute.'.read', $notice) }}" method="POST">@csrf @method('PATCH')
<button type="submit" class="btn btn--secondary btn--sm">Mark as read</button></form>@endif
<form action="{{ route($noticeRoute.'.delete', $notice) }}" method="POST">@csrf @method('DELETE')
<button type="submit" class="btn btn--ghost btn--sm">Delete notification</button></form>
</div></div>
</article>
@empty
<section class="sec"><div class="sec__body">No notifications match these filters.</div></section>
@endforelse
{{ $notices->links() }}
</div>

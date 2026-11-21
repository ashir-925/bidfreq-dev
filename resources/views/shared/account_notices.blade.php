<main class="app-content notifications-page">
    <nav class="breadcrumbs" aria-label="Breadcrumb">
        <a href="{{ route($noticeRoute === 'admin.notifications' ? 'admin.dashboard' : 'dashboard') }}">{{ $noticeRoute === 'admin.notifications' ? 'Console' : 'Home' }}</a>
        <span class="breadcrumbs__sep" aria-hidden="true">/</span>
        <span class="breadcrumbs__current" aria-current="page">Notifications</span>
    </nav>

    <header class="page-header">
        <div class="page-header__text">
            <h1 class="page-header__title">Notifications</h1>
            <p class="page-header__subtitle">{{ $noticeRoute === 'admin.notifications' ? 'Updates on estimates, projects, and payments.' : 'Updates on your estimates, projects, and payments.' }}</p>
        </div>
        <form action="{{ route($noticeRoute.'.read-all') }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn--secondary" data-loading-text="Updating…">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m3 12 4 4L16 7m-4 9L21 7"/></svg>
                Mark all as read
            </button>
        </form>
    </header>

    <section class="notifications-page__inbox" aria-label="Notification inbox">
        <nav class="notif-tabs" aria-label="Notification category">
            @foreach (['' => 'All notifications', 'estimates' => 'Estimates', 'projects' => 'Projects', 'billing' => 'Billing'] as $category => $label)
                @php($active = (request('category') ?? '') === $category)
                <a @class(['tab', 'is-active' => $active])
                    href="{{ route($noticeRoute, array_filter(['category' => $category, 'status' => request('status')])) }}"
                    @if ($active) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>

        <div class="notif-toolbar">
            <p class="notif-toolbar__left">{{ $notices->total() }} {{ Str::plural('notification', $notices->total()) }}</p>
            <form method="GET" action="{{ route($noticeRoute) }}" class="notifications-page__filter">
                @if (request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                <label class="label" for="notification-read-status">Read status</label>
                <select class="select" id="notification-read-status" name="status">
                    <option value="">All statuses</option>
                    <option value="unread" @selected(request('status') === 'unread')>Unread</option>
                    <option value="read" @selected(request('status') === 'read')>Read</option>
                </select>
                <button type="submit" class="btn btn--secondary btn--sm">Apply filter</button>
            </form>
        </div>

        <div class="notif-list">
            @php($previousDay = null)
            @forelse ($notices as $notice)
                @php($day = $notice->created_at->toDateString())
                @if ($day !== $previousDay)
                    <h2 class="notif-day">{{ $notice->created_at->isToday() ? 'Today' : ($notice->created_at->isYesterday() ? 'Yesterday' : $notice->created_at->format('F j, Y')) }}</h2>
                    @php($previousDay = $day)
                @endif
                @php($tone = match ($notice->category) { 'estimates' => 'violet', 'projects' => 'blue', 'billing' => 'emerald', default => 'neutral' })
                <article @class(['notif', 'is-unread' => !$notice->read_at])>
                    <span class="notif__icon notif__icon--{{ $tone }}" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round" stroke-linecap="round">
                        @switch($notice->category)
                            @case('estimates')<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M8 13h8m-8 4h5"/>@break
                            @case('projects')<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2zM9 22v-8h6v8"/>@break
                            @case('billing')<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/>@break
                            @default<path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9zM10 21h4"/>
                        @endswitch
                        </svg>
                    </span>
                    <div class="notif__body">
                        <div class="notif__row">
                            <h3 class="notif__title"><a href="{{ $notice->link }}">{{ $notice->title }}</a></h3>
                            <time class="notif__time" datetime="{{ $notice->created_at->toAtomString() }}">{{ $notice->created_at->format('M j · g:i A') }}</time>
                        </div>
                        <p class="notif__text">{{ $notice->message }}</p>
                        <div class="notif__meta">
                            <span class="chip">{{ ucfirst($notice->category) }}</span>
                            <span @class(['chip', 'chip--blue' => !$notice->read_at])>{{ $notice->read_at ? 'Read' : 'Unread' }}</span>
                        </div>
                        <div class="notif__actions">
                            @if (!$notice->read_at)
                                <form action="{{ route($noticeRoute.'.read', $notice) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="notif__action-btn" data-loading-text="Updating…">Mark as read</button>
                                </form>
                            @endif
                            <form action="{{ route($noticeRoute.'.delete', $notice) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="notif__action-btn" data-loading-text="Deleting…">Delete notification</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="empty notif-empty">
                    <div class="empty__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" width="24" height="24"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9zM10 21h4"/></svg>
                    </div>
                    <h2 class="empty__title">{{ request('category') || request('status') ? 'No matching notifications' : 'You’re all caught up' }}</h2>
                    @if (request('category') || request('status'))<a class="btn btn--secondary btn--sm" href="{{ route($noticeRoute) }}">Clear filters</a>@endif
                </div>
            @endforelse
        </div>
        @if ($notices->hasPages())
            <div class="notifications-page__pagination">{{ $notices->links() }}</div>
        @endif
    </section>
</main>

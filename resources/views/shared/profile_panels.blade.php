<div class="profile-grid">
<nav class="settings-nav" aria-label="Profile sections">
    <div class="settings-nav__title">Profile Settings</div>
    @foreach (['general' => 'General', 'security' => 'Security', 'sessions' => 'Sessions'] as $key => $label)
        {{-- Hide the Admin Sessions link in this shared navigation. --}}
        @continue($baseRoute === 'admin.profile' && $key === 'sessions')
        <a href="{{ route($baseRoute, ['tab' => $key]) }}" @class(['settings-nav__item', 'is-active' => $tab === $key]) @if ($tab === $key) aria-current="page" @endif>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                @if ($key === 'general')<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                @elseif ($key === 'security')<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                @else<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8m-4-4v4"/>@endif
            </svg>{{ $label }}
        </a>
    @endforeach
</nav>
<div class="panel-stack"><div class="panel-tab is-active" data-panel="{{ $tab }}">
@if ($tab === 'general')
<form id="profile-general" method="POST" action="{{ route($baseRoute.'.update') }}" @if ($baseRoute === 'admin.profile') enctype="multipart/form-data" @endif>@csrf @method('PATCH')
<section class="profile-header">
    <div class="profile-header__cover"></div><div class="profile-header__inner"><div class="profile-header__row">
        <div class="profile-header__avatar" aria-hidden="true">
            @if ($baseRoute === 'admin.profile' && $account->profile_photo_path)
                <img class="admin-profile__photo" src="{{ route('admin.profile.photo') }}" alt="">
            @else
                {{ Str::upper(Str::substr($account->first_name, 0, 1).Str::substr($account->last_name, 0, 1)) }}
            @endif
        </div>
        <div class="profile-header__text"><h2 class="profile-header__name">{{ $account->name }}</h2>
            <div class="profile-header__meta"><span>{{ $account->email }}</span>
            @if ($account->company_name)<span class="dot"></span><span>{{ $account->company_name }}</span>@endif
            <span class="profile-header__role">{{ Str::headline($account->role) }}</span></div>
        </div>
        <div class="profile-header__actions"><button type="reset" class="btn btn--secondary">Reset fields</button><button class="btn btn--primary" data-loading-text="Saving profile…">Save changes</button></div>
    </div></div>
</section>
<section class="sec mt-4"><div class="sec__head"><h2 class="sec__title">Personal information</h2></div><div class="sec__body"><div class="form-row">
    @foreach (['first_name' => ['First name', 'text', 'given-name', 100], 'last_name' => ['Last name', 'text', 'family-name', 100], 'email' => ['Email address', 'email', 'email', 255], 'phone' => ['Phone', 'tel', 'tel', 40]] as $field => [$label, $type, $autocomplete, $limit])
        <div class="form-group"><label class="label" for="profile-{{ $field }}">{{ $label }}</label>
        <input class="input" id="profile-{{ $field }}" type="{{ $type }}" name="{{ $field }}" value="{{ old($field, $account->$field) }}" autocomplete="{{ $autocomplete }}" maxlength="{{ $limit }}" required>
        @include('shared.auth-field-error', ['name' => $field])</div>
    @endforeach
    @if ($account->role === 'customer')
        <div class="form-group"><label class="label" for="profile-company">Company name (optional)</label>
        <input class="input" id="profile-company" name="company_name" value="{{ old('company_name', $account->company_name) }}" autocomplete="organization" maxlength="255">
        @include('shared.auth-field-error', ['name' => 'company_name'])</div>
    @endif
    <div class="form-group"><label class="label" for="profile-email-password">Current password (only when changing email)</label>
    <input class="input" id="profile-email-password" type="password" name="current_password" autocomplete="current-password">
    @include('shared.auth-field-error', ['name' => 'current_password'])</div>
    @if ($baseRoute === 'admin.profile')
        <div class="form-group admin-profile__photo-field">
            <label for="admin-photo" class="label">Profile photo (optional)</label>
            <input id="admin-photo" class="input" type="file" name="photo" accept="image/jpeg,image/png,image/webp" aria-describedby="admin-photo-help">
            <p id="admin-photo-help" class="sec__sub">JPEG, PNG, or WebP, up to 2 MB and 4096 × 4096 pixels.</p>
            @include('shared.auth-field-error', ['name' => 'photo'])
        </div>
    @endif
</div></div></section>
</form>
@elseif ($tab === 'security')
<section class="sec"><div class="sec__head"><div><h2 class="sec__title">Change password</h2><p class="sec__sub">Use at least 8 characters.</p></div></div><div class="sec__body">
<form method="POST" action="{{ route($baseRoute.'.password') }}">@csrf @method('PUT')
<div class="form-row">
    @foreach (['current_password' => 'Current password', 'password' => 'New password', 'password_confirmation' => 'Confirm new password'] as $field => $label)
        <div class="form-group"><label class="label" for="profile-{{ $field }}">{{ $label }}</label>
        <input class="input" id="profile-{{ $field }}" type="password" name="{{ $field }}" autocomplete="{{ $field === 'current_password' ? 'current-password' : 'new-password' }}" required>
        @include('shared.auth-field-error', ['name' => $field])</div>
    @endforeach
</div><button class="btn btn--primary" data-loading-text="Updating password…">Update password</button>
</form></div></section>
@else
<section class="sec"><div class="sec__head"><div><h2 class="sec__title">Active sessions</h2><p class="sec__sub">Review browsers signed into your account.</p></div></div><div class="sec__body">
    @if (!$databaseSessions)<p class="text-muted">Only this browser is available to display.</p>@endif
    @forelse ($sessions as $item)
        <div class="status-row"><div><strong>{{ $item->id === session()->getId() ? 'This browser' : 'Other browser' }}</strong>
        <div class="text-muted">{{ Str::limit($item->user_agent ?? 'Browser information unavailable', 100) }} · {{ \Illuminate\Support\Carbon::createFromTimestamp($item->last_activity)->diffForHumans() }}</div></div>
        @if ($item->id !== session()->getId())
            <form method="POST" action="{{ route($baseRoute.'.sessions.revoke', $item->id) }}">@csrf @method('DELETE')
            <button class="btn btn--secondary btn--sm" data-loading-text="Signing out…">Sign out browser</button></form>
        @endif</div>
    @empty
        <p class="text-muted">No other sessions to show.</p>
    @endforelse
</div></section>
@endif
</div></div>
</div>

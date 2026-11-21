@extends('admin.layout.app')

@section('title', 'Users · BidFreq')

@section('content')
<main class="app-content">
    <header class="page-header"><h1 class="page-header__title">Users</h1></header>
    <section class="card" style="padding: 20px; margin-bottom: 24px">
    <h2>Create an Admin</h2>
    <form method="POST" action="{{ route('admin.users.store') }}" class="auth-form">
    @csrf
    @include('shared.field', ['name' => 'first_name', 'label' => 'First name', 'type' => 'text'])
    @include('shared.field', ['name' => 'last_name', 'label' => 'Last name', 'type' => 'text'])
    @include('shared.field', ['name' => 'email', 'label' => 'Email address', 'type' => 'email'])
    @include('shared.field', ['name' => 'phone', 'label' => 'Phone', 'type' => 'tel'])
    @include('shared.field', ['name' => 'password', 'label' => 'Password', 'type' => 'password'])
    @include('shared.field', ['name' => 'password_confirmation', 'label' => 'Confirm password', 'type' => 'password'])
    <button type="submit" class="btn btn--primary" data-loading-text="Creating Admin…"><span>Create Admin</span></button>
</form>
</section>

<section class="card" style="padding: 20px; overflow-x: auto">
    <h2>Accounts</h2>
    <form method="GET" class="filter-bar"><label class="label">Search accounts <input class="input" type="search" name="search" value="{{ request('search') }}"></label>
    <label class="label">Status <select class="select" name="status"><option value="">Any</option>@foreach (['active','inactive'] as $status)<option @selected(request('status') === $status) value="{{ $status }}">{{ ucfirst($status) }}</option>@endforeach</select></label>
    <label class="label">Sort <select class="select" name="sort"><option value="email">Email</option><option value="newest" @selected(request('sort') === 'newest')>Newest first</option></select></label>
    <button class="btn btn--primary btn--sm">Apply filters</button></form>
    <table class="table">
        <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td><a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a> @if ($user->projects_count)<span class="chip">Customer</span>@endif <span class="text-muted">{{ $user->repair_requests_count }} requests</span></td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role === 'customer' && !$user->projects_count ? 'Registered user' : ucfirst($user->role) }}</td>
                    <td>{{ ucfirst($user->status) }}</td>
                    <td>
                        @unless (auth()->user()->is($user))
                            @can('changeStatus', $user)
                                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="{{ $user->status === 'active' ? 'inactive' : 'active' }}">
                                    <button type="submit" class="btn btn--secondary" data-loading-text="Updating account…">
                                        {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            @endcan
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No accounts to display.</td></tr>
            @endforelse
        </tbody>
    </table>
    @error('status') <p role="alert">{{ $message }}</p> @enderror
    @if ($users->hasPages())
        <nav aria-label="User list pages">
            @if ($users->previousPageUrl()) <a href="{{ $users->previousPageUrl() }}">Previous</a> @endif
            <span>Page {{ $users->currentPage() }} of {{ $users->lastPage() }}</span>
            @if ($users->nextPageUrl()) <a href="{{ $users->nextPageUrl() }}">Next</a> @endif
        </nav>
    @endif
</section>
</main>
@endsection

@push('scripts')
    <script src="{{ asset('admin_assets/pages_js/dashboard.js') }}"></script>
@endpush

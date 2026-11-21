<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\ReportFailure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Throwable;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        $filters = $request->validate(['search' => 'nullable|string|max:100', 'status' => 'nullable|in:active,inactive', 'sort' => 'nullable|in:email,newest']);
        $query = User::where('role', '!=', 'super_admin')->withCount(['projects', 'repairRequests']);
        if ($status = $filters['status'] ?? null) {
            $query->where('status', $status);
        }
        if (filled($filters['search'] ?? null)) {
            $match = '%'.$filters['search'].'%';
            $query->where(fn ($q) => $q->whereLike('email', $match)->orWhereLike('first_name', $match)->orWhereLike('last_name', $match)->orWhereLike('company_name', $match));
        }
        ($filters['sort'] ?? null) === 'newest' ? $query->orderByDesc('id') : $query->orderBy('email');
        return view('admin.pages.users', ['users' => $query->paginate(25)->withQueryString()]);
    }

    public function show(User $user)
    {
        Gate::authorize('viewAny', User::class);
        abort_if($user->role === 'super_admin', 404);

        return view('admin.pages.account_detail', ['account' => $user,
            'estimates' => $user->repairRequests()->latest('id')->paginate(15, ['*'], 'estimate_page'),
            'projects' => $user->projects()->latest('id')->paginate(15),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', User::class);
        $email = $request->input('email');
        if (is_string($email)) {
            $request->merge(['email' => strtolower(trim($email))]);
        }
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:40'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        try {
            $user = new User($data);
            $user->role = 'admin';
            $user->save();
        } catch (Throwable $e) {
            ReportFailure::log($e, 'admin.create');
            return back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'We could not create the Admin. Please try again.');
        }

        Log::channel('daily')->info('Admin account created', [
            'user_id' => $request->user()->id, 'target_user_id' => $user->id,
            'old' => null, 'new' => ['role' => 'admin', 'status' => $user->status ?? 'active'],
        ]);

        return back()->with('status', 'Admin account created. They can verify their email after signing in.');
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->role === 'super_admin' || $request->user()->is($user), 403);
        Gate::authorize('changeStatus', $user);
        $data = $request->validate(['status' => ['required', 'in:active,inactive']]);

        $previousStatus = $user->status;
        try {
            $user->status = $data['status'];
            $user->save();
        } catch (Throwable $e) {
            ReportFailure::log($e, 'admin.change-status');
            return back()->with('error', 'We could not update the account. Please try again.');
        }

        Log::channel('daily')->info('Account status updated', [
            'user_id' => $request->user()->id, 'target_user_id' => $user->id,
            'old' => $previousStatus, 'new' => $user->status,
        ]);

        return back()->with('status', 'Account status updated.');
    }
}

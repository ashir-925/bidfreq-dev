<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\ReportFailure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Throwable;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $staff = $request->routeIs('admin.*');
        $tab = $request->query('tab');
        $tab = in_array($tab, ['general', 'security', 'sessions'], true) ? $tab : 'general';
        $databaseSessions = config('session.driver') === 'database';
        $sessions = $tab === 'sessions' && $databaseSessions
            ? $this->sessions($request)->where('last_activity', '>=', now()->subMinutes((int) config('session.lifetime'))->timestamp)
                ->orderByDesc('last_activity')->get(['id', 'user_agent', 'last_activity'])
            : collect();

        return view($staff ? 'admin.pages.profile' : 'front.pages.profile', [
            'account' => $request->user(), 'baseRoute' => $staff ? 'admin.profile' : 'profile',
            'tab' => $tab, 'sessions' => $sessions, 'databaseSessions' => $databaseSessions,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $email = $request->input('email');
        if (is_string($email)) {
            $request->merge(['email' => Str::lower(trim($email))]);
        }
        $changingEmail = $request->input('email') !== $user->email;
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:40',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'company_name' => 'nullable|string|max:255',
            'current_password' => $changingEmail ? 'required|current_password' : 'nullable',
            'photo' => $request->routeIs('admin.profile.update')
                ? ['nullable', 'file', 'image', 'mimetypes:image/jpeg,image/png,image/webp', 'max:2048', 'dimensions:max_width=4096,max_height=4096']
                : ['prohibited'],
        ]);
        $photo = $data['photo'] ?? null;
        unset($data['current_password'], $data['photo']);
        if (! $changingEmail) {
            unset($data['email']);
        }
        if ($user->role !== 'customer') {
            unset($data['company_name']);
        }

        $path = null;
        try {
            if ($photo) {
                $path = $photo->store('admin-profile-photos/'.$user->id, 'local');
                if (! $path) {
                    throw new \RuntimeException('Could not write the profile photo.');
                }
            }

            [$account, $previousPhoto, $changingEmail] = DB::transaction(function () use ($user, $data, $path) {
                $account = User::lockForUpdate()->findOrFail($user->id);
                $changingEmail = isset($data['email']) && $account->email !== $data['email'];
                $previousPhoto = $path ? $account->profile_photo_path : null;
                $account->fill($data);
                if ($changingEmail) {
                    $account->email_verified_at = null;
                }
                if ($path) {
                    $account->profile_photo_path = $path;
                }
                $account->save();

                return [$account, $previousPhoto, $changingEmail];
            });
        } catch (Throwable $exception) {
            if ($path) {
                $this->deletePhoto($path, $user->id);
            }
            ReportFailure::log($exception, 'profile.update', $user->id);
            return back()->withInput($request->except('current_password', 'photo'))->with('error', 'Profile changes could not be saved.');
        }

        $user->setRawAttributes($account->getAttributes(), true);
        if ($previousPhoto) {
            $this->deletePhoto($previousPhoto, $user->id);
        }

        if ($changingEmail) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (Throwable $exception) {
                ReportFailure::log($exception, 'profile.verify_email', $user->id);
                return redirect()->route('verification.notice')->with('error', 'Email updated. We could not send the verification email. Use Resend to try again.');
            }
            return redirect()->route('verification.notice')->with('status', 'Email updated. Verify your new address to return to your dashboard.');
        }

        return redirect()->route($this->profileRoute($request))->with('status', 'Profile saved.');
    }

    public function photo(Request $request)
    {
        $path = $request->user()->profile_photo_path;
        $disk = Storage::disk('local');
        abort_unless($path && $disk->exists($path), 404);

        return $disk->response($path, null, ['Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    private function deletePhoto(string $path, int $userId): void
    {
        try {
            if (! Storage::disk('local')->delete($path)) {
                throw new \RuntimeException('Could not delete the previous profile photo.');
            }
        } catch (Throwable $exception) {
            ReportFailure::log($exception, 'profile.photo.cleanup', $userId);
        }
    }

    public function password(Request $request)
    {
        $data = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', 'different:current_password', Password::defaults()],
        ]);
        $user = $request->user();
        try {
            $user->password = $data['password'];
            $user->setRememberToken(Str::random(60));
            $user->save();
            $request->session()->regenerate();
        } catch (Throwable $exception) {
            ReportFailure::log($exception, 'profile.password', $user->id);
            return back()->with('error', 'Your password could not be updated.');
        }

        return redirect()->route($this->profileRoute($request), ['tab' => 'security'])->with('status', 'Password updated.');
    }

    public function revoke(Request $request, string $session)
    {
        abort_unless(config('session.driver') === 'database', 404);
        abort_if($request->session()->getId() === $session, 422);
        try {
            $removed = $this->sessions($request)->where('id', $session)->delete();
        } catch (Throwable $exception) {
            ReportFailure::log($exception, 'profile.session_revoke', $request->user()->id);
            return back()->with('error', 'The session could not be signed out.');
        }

        abort_unless($removed, 404);
        return redirect()->route($this->profileRoute($request), ['tab' => 'sessions'])->with('status', 'Session signed out.');
    }

    private function sessions(Request $request)
    {
        return DB::connection(config('session.connection'))->table(config('session.table'))
            ->where('user_id', $request->user()->id);
    }

    private function profileRoute(Request $request): string
    {
        return $request->routeIs('admin.*') ? 'admin.profile' : 'profile';
    }
}

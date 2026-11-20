<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ReportFailure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Throwable;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('front.auth.forgot-password');
    }

    public function store(Request $request)
    {
        $email = strtolower($request->validate(['email' => ['required', 'email', 'max:255']])['email']);

        try {
            $broker = Password::broker();
            $status = $broker->sendResetLink(['email' => $email], function (User $user, string $token) use ($broker) {
                try {
                    $user->sendPasswordResetNotification($token);
                } catch (Throwable $e) {
                    ReportFailure::log($e, 'password.email', $user->id);
                    $broker->deleteToken($user);

                    return 'mail_failed';
                }

                return Password::RESET_LINK_SENT;
            });
        } catch (Throwable $e) {
            ReportFailure::log($e, 'password.email');
            $status = 'mail_failed';
        }

        if ($status === 'mail_failed') {
            return back()->withInput(['email' => $email])
                ->with('error', 'We could not send the email, please try again');
        }

        return back()->withInput(['email' => $email])
            ->with('status', 'If an account matches this email, a password reset link will be sent.');
    }
}

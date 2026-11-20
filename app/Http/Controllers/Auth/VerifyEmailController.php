<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\ReportFailure;
use Throwable;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->to(route($request->user()->dashboardRoute(), absolute: false).'?verified=1');
        }

        try {
            if ($request->user()->markEmailAsVerified()) {
                event(new Verified($request->user()));
            }
        } catch (Throwable $e) {
            ReportFailure::log($e, 'verification.confirm');

            return redirect()->route('verification.notice')->with('error', 'We could not verify your email. Please try again.');
        }

        return redirect()->to(route($request->user()->dashboardRoute(), absolute: false).'?verified=1');
    }
}

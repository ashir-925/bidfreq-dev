<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\ReportFailure;
use Illuminate\Http\Request;
use Throwable;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route($request->user()->dashboardRoute());
        }

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (Throwable $e) {
            ReportFailure::log($e, 'verification.resend');
            return back()->with('error', 'We could not send the email, please try again');
        }

        return back()->with('status', 'Verification link sent.');
    }
}

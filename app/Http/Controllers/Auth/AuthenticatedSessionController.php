<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\ReportFailure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('front.auth.login');
    }

    public function store(LoginRequest $request)
    {
        try {
            $request->authenticate();
            $request->session()->regenerate();
            $request->session()->put(
                'password_hash_web',
                Auth::guard('web')->hashPasswordForCookie($request->user()->getAuthPassword())
            );
            $request->user()->forceFill(['last_login_at' => now()])->save();
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            ReportFailure::log($e, 'login');
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->with('error', 'We could not sign you in. Please try again.');
        }

        $request->session()->forget('url.intended');

        return redirect()->route($request->user()->dashboardRoute())->with('status', 'Signed in.');
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Signed out.');
    }
}

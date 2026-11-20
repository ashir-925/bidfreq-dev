<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->status !== 'inactive') {
            return $next($request);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $message = 'Your account is inactive. Please contact BidFreq for help.';

        return $request->expectsJson()
            ? response()->json(['message' => $message], 403)
            : redirect()->route('login')->with('error', $message);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ReportFailure;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Throwable;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('front.auth.register');
    }

    public function store(Request $request)
    {
        $email = $request->input('email');
        if (is_string($email)) {
            $request->merge(['email' => strtolower(trim($email))]);
        }
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:40'],
            'terms' => ['accepted'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        try {
            $user = new User($data);
            $user->role = 'customer';
            $user->save();
        } catch (Throwable $e) {
            ReportFailure::log($e, 'register');
            return back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'We could not create your account. Please try again.');
        }

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put(
            'password_hash_web',
            Auth::guard('web')->hashPasswordForCookie($request->user()->getAuthPassword())
        );

        try {
            event(new Registered($user));
        } catch (Throwable $e) {
            ReportFailure::log($e, 'verification.send', $user->id);
            return redirect()->route('verification.notice')
                ->with('error', 'We could not send the email, please try again');
        }

        return redirect()->route('verification.notice')->with('status', 'Account created. Check your email for the verification link.');
    }
}

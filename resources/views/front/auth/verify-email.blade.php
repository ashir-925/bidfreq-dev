@extends('front.auth.layout')

@section('title', 'Verify your email · BidFreq')

@section('content')
<div class="auth-form">
    <header class="auth-form__head">
        <h2 class="auth-form__title">Verify your email</h2>
        <p class="auth-form__sub">Open the verification link sent to {{ auth()->user()->email }} to continue.</p>
    </header>
    @include('shared.flash')
    <form method="POST" action="{{ route('verification.send') }}" style="margin-bottom: 10px">
        @csrf
        <button type="submit" data-loading-text="Sending verification email…" class="btn btn--primary btn--lg btn--block">Resend verification email</button>
    </form>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" data-loading-text="Signing out…" class="btn btn--secondary btn--block">Sign out</button>
    </form>
</div>
@endsection

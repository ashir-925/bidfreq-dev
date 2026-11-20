@extends('front.auth.layout')

@section('title', 'Choose a new password · BidFreq')

@section('content')
<form class="auth-form" method="POST" action="{{ route('password.store') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">
    <header class="auth-form__head">
        <h2 class="auth-form__title">Choose a new password</h2>
    </header>
    @include('shared.flash')
    @include('shared.auth-field-error', ['name' => 'token'])
    @include('shared.field', ['name' => 'email', 'label' => 'Work email', 'type' => 'email', 'value' => $request->query('email')])
    @include('shared.field', ['name' => 'password', 'label' => 'Password', 'type' => 'password'])
    @include('shared.field', ['name' => 'password_confirmation', 'label' => 'Confirm password', 'type' => 'password'])
    <button type="submit" data-loading-text="Resetting password…" class="btn btn--primary btn--lg btn--block">Reset password</button>
    <a class="auth-back" href="{{ route('login') }}">Back to sign in</a>
</form>
@endsection

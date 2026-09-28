@extends('layouts.app', ['narrow' => true])
@section('title', __('club.reset.title'))

@section('content')
<div class="auth">
  <div class="auth-card">
    <h1>{{ __('club.reset.title') }}</h1>

    @if ($errors->any())
      <div class="alert alert--err">{{ $errors->first() }}</div>
    @endif

    <div class="card">
      <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="field">
          <label for="email">{{ __('club.form.email') }}</label>
          <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required>
        </div>

        <div class="field">
          <label for="password">{{ __('club.profile.new_password') }}</label>
          <input id="password" type="password" name="password" required autocomplete="new-password">
          <p class="hint">{{ __('club.form.password_hint') }}</p>
        </div>

        <div class="field">
          <label for="password_confirmation">{{ __('club.form.password2') }}</label>
          <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn--block">{{ __('club.reset.submit') }}</button>
      </form>
    </div>
  </div>
</div>
@endsection

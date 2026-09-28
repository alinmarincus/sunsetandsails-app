@extends('layouts.app', ['narrow' => true])
@section('title', __('club.login.title'))

@section('content')
<div class="auth">
  <div class="auth-card">
    <h1>{{ __('club.login.title') }}</h1>
    <p class="muted" style="margin-bottom:24px">{{ __('club.login.lead') }}</p>

    @if ($errors->any())
      <div class="alert alert--err">{{ $errors->first() }}</div>
    @endif

    <div class="card">
      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
          <label for="email">{{ __('club.form.email') }}</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
        </div>

        <div class="field">
          <label for="password">{{ __('club.form.password') }}</label>
          <input id="password" type="password" name="password" required autocomplete="current-password">
        </div>

        <div class="check">
          <input id="remember" type="checkbox" name="remember" value="1">
          <label for="remember">{{ __('club.form.remember') }}</label>
        </div>

        <button type="submit" class="btn btn--block">{{ __('club.form.login') }}</button>
      </form>
    </div>

    <p class="auth-foot">
      <a href="{{ route('password.request') }}">{{ __('club.form.forgot') }}</a>
      <br><br>
      {{ __('club.login.no_account') }}
      <a href="{{ route('club.join', app()->getLocale()) }}">{{ __('club.nav.join') }}</a>
    </p>
  </div>
</div>
@endsection

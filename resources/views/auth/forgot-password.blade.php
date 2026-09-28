@extends('layouts.app', ['narrow' => true])
@section('title', __('club.forgot.title'))

@section('content')
<div class="auth">
  <div class="auth-card">
    <h1>{{ __('club.forgot.title') }}</h1>
    <p class="muted" style="margin-bottom:24px">{{ __('club.forgot.lead') }}</p>

    @if ($errors->any())
      <div class="alert alert--err">{{ $errors->first() }}</div>
    @endif

    <div class="card">
      <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="field">
          <label for="email">{{ __('club.form.email') }}</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
        <button type="submit" class="btn btn--block">{{ __('club.forgot.submit') }}</button>
      </form>
    </div>

    <p class="auth-foot"><a href="{{ route('login') }}">{{ __('club.back_to_login') }}</a></p>
  </div>
</div>
@endsection

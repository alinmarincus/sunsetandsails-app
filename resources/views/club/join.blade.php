@extends('layouts.app')
@section('title', __('club.join.eyebrow'))

@section('content')

  <section style="max-width:640px">
    <p class="eyebrow">{{ __('club.join.eyebrow') }}</p>
    <h1>{!! __('club.join.title') !!}</h1>
    <p class="lead" style="margin-top:16px">{{ __('club.join.lead') }}</p>
  </section>

  <section class="section grid grid--2">
    @foreach (['b1', 'b2', 'b3', 'b4'] as $b)
      <div class="card">
        <h3 style="color:var(--gold)">{{ __("club.join.$b.title") }}</h3>
        <p class="muted" style="margin-top:6px">{{ __("club.join.$b.text") }}</p>
      </div>
    @endforeach
  </section>

  <section class="section" id="form" style="max-width:460px">
    <p class="eyebrow">{{ __('club.join.form') }}</p>

    <div class="card">
      <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="field">
          <label for="name">{{ __('club.form.name') }}</label>
          <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
          @error('name') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
          <label for="email">{{ __('club.form.email') }}</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
          @error('email') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
          <label for="phone">{{ __('club.form.phone') }}</label>
          <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel">
          @error('phone') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
          <label for="password">{{ __('club.form.password') }}</label>
          <input id="password" type="password" name="password" required autocomplete="new-password">
          <p class="hint">{{ __('club.form.password_hint') }}</p>
          @error('password') <p class="error">{{ $message }}</p> @enderror
        </div>

        <div class="field">
          <label for="password_confirmation">{{ __('club.form.password2') }}</label>
          <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
        </div>

        <div class="check">
          <input id="gdpr" type="checkbox" name="gdpr" value="1" required {{ old('gdpr') ? 'checked' : '' }}>
          <label for="gdpr">{{ __('club.form.consent_gdpr') }}</label>
        </div>
        @error('gdpr') <p class="error">{{ $message }}</p> @enderror

        <button type="submit" class="btn btn--block" style="margin-top:8px">{{ __('club.form.submit_join') }}</button>
      </form>

      <p class="auth-foot">
        {{ __('club.join.have') }} <a href="{{ route('login') }}">{{ __('club.form.login') }}</a>
      </p>
    </div>
  </section>

@endsection

@extends('layouts.app', ['narrow' => true])
@section('title', __('club.profile.title'))

@section('content')

  <h1 style="margin-bottom:24px">{{ __('club.profile.title') }}</h1>

  @if ($errors->any())
    <div class="alert alert--err">{{ $errors->first() }}</div>
  @endif

  {{-- Date personale --}}
  <div class="card">
    <form method="POST" action="{{ route('club.profile.update', app()->getLocale()) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="field">
        <label>{{ __('club.profile.photo') }}</label>
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:10px">
          <img src="{{ $user->avatarUrl() }}" alt="" class="avatar avatar--lg" onerror="this.style.visibility='hidden'">
          <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp">
        </div>
        <p class="hint">{{ __('club.profile.photo_hint') }}</p>
        @error('avatar') <p class="error">{{ $message }}</p> @enderror
      </div>

      <div class="field">
        <label for="name">{{ __('club.form.name') }}</label>
        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required>
      </div>

      <div class="field">
        <label for="email">{{ __('club.form.email') }}</label>
        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required>
      </div>

      <div class="field">
        <label for="phone">{{ __('club.form.phone') }}</label>
        <input id="phone" type="tel" name="phone" value="{{ old('phone', $user->phone) }}">
      </div>

      <div class="check">
        <input id="wall_public" type="checkbox" name="wall_public" value="1" {{ $user->wall_public ? 'checked' : '' }}>
        <label for="wall_public">{{ __('club.profile.wall_public') }}</label>
      </div>
      <p class="hint" style="margin:-8px 0 18px 28px">{{ __('club.profile.wall_hint') }}</p>

      <button type="submit" class="btn btn--block">{{ __('club.profile.save') }}</button>
    </form>
  </div>

  {{-- Parola --}}
  <div class="card">
    <h3 style="margin-bottom:16px">{{ __('club.profile.password') }}</h3>
    <form method="POST" action="{{ route('user-password.update') }}">
      @csrf
      @method('PUT')

      <div class="field">
        <label for="current_password">{{ __('club.profile.current_password') }}</label>
        <input id="current_password" type="password" name="current_password" autocomplete="current-password">
      </div>

      <div class="field">
        <label for="new_password">{{ __('club.profile.new_password') }}</label>
        <input id="new_password" type="password" name="password" autocomplete="new-password">
        <p class="hint">{{ __('club.form.password_hint') }}</p>
      </div>

      <div class="field">
        <label for="new_password_confirmation">{{ __('club.form.password2') }}</label>
        <input id="new_password_confirmation" type="password" name="password_confirmation" autocomplete="new-password">
      </div>

      <button type="submit" class="btn btn--ghost btn--block">{{ __('club.profile.password') }}</button>
    </form>
  </div>

@endsection

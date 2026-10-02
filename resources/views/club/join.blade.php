@extends('layouts.app')
@section('title', __('club.join.eyebrow'))

@section('content')

  {{-- ── Hero cu video ─────────────────────────────────────────────────── --}}
  <section class="join-hero">
    <video class="join-hero__video" id="join-video"
           autoplay muted loop playsinline preload="none"
           poster="{{ asset('images/club-poster-tall.webp') }}" aria-hidden="true"></video>
    <div class="join-hero__veil"></div>

    <div class="join-hero__body">
      <p class="eyebrow">{{ __('club.join.eyebrow') }}</p>
      <h1 class="join-hero__title">{!! __('club.join.title') !!}</h1>
      <p class="join-hero__lead">{{ __('club.join.lead') }}</p>

      <div class="join-hero__actions">
        <a href="#form" class="btn btn--light">
          {{ __('club.join.cta') }}
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor"
               stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><polyline points="19,12 12,19 5,12"/>
          </svg>
        </a>
        <a href="{{ route('login') }}" class="btn btn--ghost">{{ __('club.form.login') }}</a>
      </div>
    </div>
  </section>

  {{-- ── Ce primești ───────────────────────────────────────────────────── --}}
  <section class="section">
    <div class="grid grid--2">
      @foreach (['b1', 'b2', 'b3', 'b4'] as $b)
        <div class="card">
          <h3 style="color:var(--gold)">{{ __("club.join.$b.title") }}</h3>
          <p class="muted" style="margin-top:6px">{{ __("club.join.$b.text") }}</p>
        </div>
      @endforeach
    </div>
  </section>

  {{-- ── Formular ──────────────────────────────────────────────────────── --}}
  <section class="section" id="form" style="max-width:460px">
    <p class="eyebrow">{{ __('club.join.form') }}</p>

    <div class="card">
      <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="field">
          <label for="name">{{ __('club.form.name') }}</label>
          <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name">
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

<script>
/* Video de fundal în hero: montaj vertical pe telefon, orizontal pe desktop.
   Pe conexiuni lente sau cu reducerea animațiilor activată, rămâne coperta. */
(function () {
  var v = document.getElementById('join-video');
  if (!v) return;

  var wide = window.matchMedia('(min-width: 760px)').matches;
  if (wide) v.poster = @json(asset('images/hero-poster-wide.webp'));

  var c = navigator.connection || {};
  var slab = c.saveData === true || /(^|-)2g$/.test(c.effectiveType || '');
  if (slab || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  /* Montajul vertical e filmat special pentru club. Cel orizontal urmeaza;
     pana atunci, pe desktop ramane montajul de pe site. */
  var baza = wide
    ? @json(asset('video/sunset_and_sails_hero_horizontal'))
    : @json(asset('video/sunset_and_sails_club_hero_vertical'));

  [['.webm', 'video/webm'], ['.mp4', 'video/mp4']].forEach(function (s) {
    var el = document.createElement('source');
    el.src = baza + s[0]; el.type = s[1];
    v.appendChild(el);
  });

  v.preload = 'metadata';
  v.load();
  var p = v.play();
  if (p && p.catch) p.catch(function () {});
})();
</script>

@endsection

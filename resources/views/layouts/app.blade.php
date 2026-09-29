<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="theme-color" content="#0A1628">
  <title>@yield('title', __('club.meta.title'))</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap">
  <link rel="stylesheet" href="{{ asset('css/club.css') }}">
</head>
<body>

<header class="site-header">
  <div class="wrap">
    <a href="{{ route('club.dashboard', app()->getLocale()) }}" class="brand">
      <img src="{{ asset('images/logo-horizontal.svg') }}" alt="Sunset &amp; Sails">
      <b>{{ __('club.brand') }}</b>
    </a>

    <div class="header-right">
      <nav class="header-nav">
        @auth
          <a href="{{ route('club.dashboard', app()->getLocale()) }}">{{ __('club.nav.dashboard') }}</a>
          <a href="{{ route('club.profile', app()->getLocale()) }}">{{ __('club.nav.profile') }}</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn--ghost btn--sm">{{ __('club.nav.logout') }}</button>
          </form>
        @else
          <a href="{{ route('login') }}">{{ __('club.nav.login') }}</a>
          <a href="{{ route('club.join', app()->getLocale()) }}" class="btn btn--sm">{{ __('club.nav.join') }}</a>
        @endauth
      </nav>

      @php
        /* Paginile cu limba in adresa isi schimba prefixul; cele fara
           (login, resetare parola) trec prin ruta de schimbare a limbii. */
        $switchUrl = function (string $loc) {
            if (request()->route('locale')) {
                $segments = request()->segments();
                $segments[0] = $loc;

                return url(implode('/', $segments));
            }

            return route('lang.switch', $loc);
        };
      @endphp

      <span class="lang-switch">
        @foreach (['ro', 'en'] as $loc)
          <a href="{{ $switchUrl($loc) }}"
             class="{{ app()->getLocale() === $loc ? 'is-active' : '' }}">{{ $loc }}</a>
        @endforeach
      </span>

      {{-- Pe mobil, navigatia intra intr-un meniu --}}
      <button class="burger" id="burger" aria-label="{{ __('club.nav.menu') }}" aria-expanded="false">
        <span></span><span></span>
      </button>
    </div>
  </div>
</header>

<div class="nav-drawer" id="nav-drawer" aria-hidden="true">
  @auth
    <a href="{{ route('club.dashboard', app()->getLocale()) }}">{{ __('club.nav.dashboard') }}</a>
    <a href="{{ route('club.profile', app()->getLocale()) }}">{{ __('club.nav.profile') }}</a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit">{{ __('club.nav.logout') }}</button>
    </form>
  @else
    <a href="{{ route('login') }}">{{ __('club.nav.login') }}</a>
    <a href="{{ route('club.join', app()->getLocale()) }}">{{ __('club.nav.join') }}</a>
  @endauth
</div>

<main>
  <div class="wrap {{ $narrow ?? false ? 'wrap--narrow' : '' }}">
    @if (session('status'))
      <div class="alert alert--ok">{{ session('status') }}</div>
    @endif
    @yield('content')
  </div>
</main>

<script>
(function () {
  var burger = document.getElementById('burger');
  var drawer = document.getElementById('nav-drawer');
  if (!burger || !drawer) return;

  function toggle(open) {
    drawer.classList.toggle('is-open', open);
    burger.classList.toggle('is-open', open);
    burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    drawer.setAttribute('aria-hidden', open ? 'false' : 'true');
    document.body.style.overflow = open ? 'hidden' : '';
  }

  burger.addEventListener('click', function () {
    toggle(!drawer.classList.contains('is-open'));
  });
  drawer.addEventListener('click', function (e) {
    if (e.target === drawer) toggle(false);
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') toggle(false);
  });
})();
</script>

<footer class="site-footer">
  © {{ date('Y') }} Sunset &amp; Sails · {{ __('club.footer.rights') }}
</footer>

</body>
</html>

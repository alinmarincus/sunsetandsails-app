@extends('layouts.app')
@section('title', __('club.nav.dashboard'))

@section('content')

  {{-- Salut --}}
  <section class="hello">
    <img src="{{ $user->avatarUrl() }}" alt="" class="avatar avatar--lg"
         onerror="this.style.display='none'">
    <div>
      <h1 style="font-size:1.9rem">{{ __('club.dash.hello', ['name' => $user->firstName()]) }}</h1>
      @if ($user->joined_club_at)
        <p class="muted">{{ __('club.dash.member_since', ['date' => $user->joined_club_at->translatedFormat('F Y')]) }}</p>
      @endif
    </div>
  </section>

  {{-- Croaziera curenta sau urmatoarea --}}
  @php $focus = $current ?? $upcoming->first(); @endphp

  @if ($focus)
    <section class="section">
      <p class="eyebrow">{{ $current ? __('club.dash.current') : __('club.dash.next') }}</p>

      <article class="trip" style="min-height:240px">
        @if ($focus->coverUrl())
          <div class="trip-bg" style="background-image:url('{{ $focus->coverUrl() }}')"></div>
        @endif
        <div class="trip-veil"></div>
        <div class="trip-body">
          @if ($focus->destination)
            <span class="trip-tag">{{ $focus->destination }}</span>
          @endif
          <h2 class="trip-title">{{ $focus->title }}</h2>
          <p class="trip-dates">
            {{ $focus->start_date->translatedFormat('j M') }} – {{ $focus->end_date->translatedFormat('j M Y') }}
            · {{ __('club.dash.nights', ['n' => $focus->nights()]) }}
          </p>

          @if ($focus->isCurrent())
            <p class="countdown"><span>{{ __('club.dash.on_board') }}</span></p>
          @else
            @php $d = $focus->daysUntilStart(); @endphp
            <p class="countdown">
              <b>{{ $d }}</b>
              <span>{{ $d === 1 ? __('club.dash.day_left') : __('club.dash.days_left') }}</span>
            </p>
          @endif
        </div>
      </article>

      {{-- Lista cu necesarul --}}
      @if ($checklist->isNotEmpty())
        @php $done = $checklist->where('checked', true)->count(); @endphp
        <div class="card" style="margin-top:16px">
          <div style="display:flex;align-items:baseline;justify-content:space-between;gap:12px">
            <h3>{{ __('club.dash.checklist') }}</h3>
            <span class="muted">{{ __('club.dash.checklist_done', ['done' => $done, 'total' => $checklist->count()]) }}</span>
          </div>

          <div class="progress-line"><i style="width:{{ $checklist->count() ? round($done / $checklist->count() * 100) : 0 }}%"></i></div>

          <ul class="checklist" style="margin-top:8px">
            @foreach ($checklist as $item)
              <li>
                <label>
                  <input type="checkbox"
                         data-item="{{ $item['id'] }}"
                         {{ $item['checked'] ? 'checked' : '' }}>
                  <span>
                    {{ $item['label'] }}
                    @if ($item['hint'])<span class="hint">{{ $item['hint'] }}</span>@endif
                  </span>
                </label>
              </li>
            @endforeach
          </ul>
        </div>
      @endif
    </section>
  @else
    <section class="section">
      <div class="card empty">
        <p>{{ __('club.dash.no_trips') }}</p>
        <a href="https://sunsetandsails.com/calendar.html" class="btn btn--ghost" target="_blank" rel="noopener">
          {{ __('club.dash.see_calendar') }}
        </a>
      </div>
    </section>
  @endif

  {{-- Alte croaziere viitoare --}}
  @php $others = $current ? $upcoming : $upcoming->skip(1); @endphp
  @if ($others->isNotEmpty())
    <section class="section">
      <p class="eyebrow">{{ __('club.dash.upcoming') }}</p>
      <div class="grid grid--2">
        @foreach ($others as $trip)
          @include('club.partials.trip-card', ['trip' => $trip])
        @endforeach
      </div>
    </section>
  @endif

  {{-- Anunturi --}}
  <section class="section">
    <p class="eyebrow">{{ __('club.dash.news') }}</p>
    <div class="card">
      @forelse ($announcements as $note)
        <div class="note">
          @if ($note->pinned)<span class="pin">★ {{ __('club.dash.pinned') }}</span>@endif
          <h3>{{ $note->title }}</h3>
          <time>{{ $note->published_at?->translatedFormat('j F Y') }}</time>
          <p>{{ $note->body }}</p>
        </div>
      @empty
        <p class="muted">{{ __('club.dash.no_news') }}</p>
      @endforelse
    </div>
  </section>

  {{-- Croaziere trecute --}}
  <section class="section">
    <p class="eyebrow">{{ __('club.dash.past') }}</p>
    @if ($past->isNotEmpty())
      <div class="grid grid--2">
        @foreach ($past as $trip)
          @include('club.partials.trip-card', ['trip' => $trip])
        @endforeach
      </div>
    @else
      <div class="card empty"><p>{{ __('club.dash.no_past') }}</p></div>
    @endif
  </section>

<script>
/* Bifarea listei de bagaj, fara reincarcarea paginii */
document.querySelectorAll('.checklist input[data-item]').forEach(function (box) {
  box.addEventListener('change', function () {
    fetch(@json(route('club.checklist.toggle', app()->getLocale())), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': @json(csrf_token()),
      },
      body: JSON.stringify({ item: box.dataset.item, checked: box.checked }),
    }).then(function (r) {
      if (!r.ok) box.checked = !box.checked;
      else refreshProgress();
    }).catch(function () { box.checked = !box.checked; });
  });
});

function refreshProgress() {
  var boxes = document.querySelectorAll('.checklist input[data-item]');
  var done  = document.querySelectorAll('.checklist input[data-item]:checked').length;
  var bar   = document.querySelector('.progress-line i');
  if (bar && boxes.length) bar.style.width = Math.round(done / boxes.length * 100) + '%';
}
</script>

@endsection

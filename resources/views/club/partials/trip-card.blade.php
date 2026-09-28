<article class="trip">
  @if ($trip->coverUrl())
    <div class="trip-bg" style="background-image:url('{{ $trip->coverUrl() }}')"></div>
  @endif
  <div class="trip-veil"></div>
  <div class="trip-body">
    @if ($trip->destination)
      <span class="trip-tag">{{ $trip->destination }}</span>
    @endif
    <h3 class="trip-title" style="font-size:1.15rem">{{ $trip->title }}</h3>
    <p class="trip-dates">
      {{ $trip->start_date->translatedFormat('j M') }} – {{ $trip->end_date->translatedFormat('j M Y') }}
    </p>
  </div>
</article>

{{-- Plecarea: marina, orele si contactele de pe boarding pass --}}
<div class="card" style="margin-top:16px">
  <h3>{{ __('club.dash.departure') }}</h3>

  <dl class="plecare">
    @if ($trip->departure_marina)
      <dt>{{ __('club.dash.departure_from') }}</dt>
      <dd>
        <b>{{ $trip->departure_marina }}</b>
        @if ($trip->marina_address)<span class="muted">{{ $trip->marina_address }}</span>@endif
      </dd>
    @endif

    @if ($trip->marinaDeIntoarcere())
      <dt>{{ __('club.dash.return_to') }}</dt>
      <dd>{{ $trip->marinaDeIntoarcere() }}</dd>
    @endif

    @if ($trip->boarding_time)
      <dt>{{ __('club.dash.boarding_time') }}</dt>
      <dd>{{ $trip->boarding_time->format('H:i') }}</dd>
    @endif

    @if ($trip->disembark_time)
      <dt>{{ __('club.dash.disembark_time') }}</dt>
      <dd>{{ $trip->disembark_time->format('H:i') }}</dd>
    @endif

    @if ($trip->base_contact)
      <dt>{{ __('club.dash.base_contact') }}</dt>
      <dd>{{ $trip->base_contact }}</dd>
    @endif

    @if ($trip->emergency_phone)
      <dt>{{ __('club.dash.emergency') }}</dt>
      <dd><a href="tel:{{ preg_replace('/\s+/', '', $trip->emergency_phone) }}">{{ $trip->emergency_phone }}</a></dd>
    @endif
  </dl>

  @if ($trip->getting_there)
    <p class="checklist-sectiune">{{ __('club.dash.getting_there') }}</p>
    <p class="muted" style="margin-top:0">{{ $trip->getting_there }}</p>
  @endif

  <div class="plecare-actiuni">
    @if ($trip->marina_map_url)
      <a class="btn btn--light" href="{{ $trip->marina_map_url }}" target="_blank" rel="noopener">
        {{ __('club.dash.open_map') }}
      </a>
    @endif

    @if ($trip->boardingPassUrl())
      <a class="btn" href="{{ $trip->boardingPassUrl() }}" target="_blank" rel="noopener">
        {{ __('club.dash.boarding_pass') }}
      </a>
    @endif
  </div>
</div>

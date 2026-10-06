{{-- Elementele dintr-o secțiune sau subsecțiune din lista de bagaj --}}
<ul class="checklist">
  @foreach ($elemente as $element)
    <li>
      <label>
        <input type="checkbox"
               data-item="{{ $element['id'] }}"
               {{ $element['bifat'] ? 'checked' : '' }}>
        <span>
          {{ $element['titlu'] }}
          @if ($element['detaliu'])
            <span class="hint">{{ $element['detaliu'] }}</span>
          @endif
        </span>
      </label>

      @if ($element['imagine'] || $element['link'])
        <div class="checklist-extra">
          @if ($element['imagine'])
            <img src="{{ $element['imagine'] }}" alt="" loading="lazy">
          @endif
          @if ($element['link'])
            <a href="{{ $element['link'] }}" target="_blank" rel="noopener">
              {{ $element['linkText'] ?: __('club.dash.where_to_buy') }} →
            </a>
          @endif
        </div>
      @endif
    </li>
  @endforeach
</ul>

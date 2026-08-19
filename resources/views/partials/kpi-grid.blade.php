<div class="kpi-grid">
    @foreach ($cards as $card)
        <div class="kpi-card">
            <div class="kpi-label">{{ $card['label'] }}</div>
            <div class="kpi-value" @if (! empty($card['id'])) id="{{ $card['id'] }}" @endif>
                {{ ($card['integer'] ?? false)
                    ? number_format((float) $card['value'], 0, ',', ' ')
                    : number_format((float) $card['value'], 2, ',', ' ') }}
                @if (! empty($card['unit']))
                    <span>{{ $card['unit'] }}</span>
                @endif
            </div>
        </div>
    @endforeach
</div>

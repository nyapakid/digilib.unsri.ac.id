@if($relatedItems->isNotEmpty())
    <aside class="detail-sidebar" aria-label="{{ $sectionTitle }} lainnya">
        <h2>{{ $sectionTitle }} Lainnya</h2>
        <div class="related-list">
            @foreach($relatedItems as $related)
                @php($relatedImage = data_get($related, 'image_url'))
                @php($relatedDate = data_get($related, 'published_at') ?: data_get($related, 'event_date'))
                @php($relatedSummary = data_get($related, $excerptColumn) ?: data_get($related, 'position') ?: data_get($related, 'description'))

                <a class="related-card" href="{{ route($type.'.show', $related) }}">
                    @if($relatedImage)
                        <span class="related-thumb"><img src="{{ $relatedImage }}" alt="{{ $related->title }}"></span>
                    @elseif(data_get($related, 'icon'))
                        <span class="related-icon"><x-icon :name="data_get($related, 'icon')" :size="24" /></span>
                    @else
                        <span class="related-letter">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr((string) $related->title, 0, 1)) }}</span>
                    @endif

                    <span class="related-copy">
                        <strong>{{ $related->title }}</strong>
                        @if($relatedDate)
                            <small>{{ $relatedDate->translatedFormat('j F Y') }}</small>
                        @elseif($relatedSummary)
                            <small>{{ \Illuminate\Support\Str::limit(strip_tags((string) $relatedSummary), 82) }}</small>
                        @endif
                    </span>
                </a>
            @endforeach
        </div>
    </aside>
@endif

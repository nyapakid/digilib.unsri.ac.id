@extends('layouts.site')

@section('title', $item->title.' - '.$site->site_name)

@section('content')
<main>
    <section class="page-hero">
        @include('partials.page-hero-image')
        <div class="container">
            <p class="crumb"><a href="{{ route('home') }}">Beranda</a> / <a href="{{ route($type.'.index') }}">{{ $sectionTitle }}</a></p>
            <h1>{{ $item->title }}</h1>
            @if($type !== 'galleries' && data_get($item, $excerptColumn))
                <p>{{ data_get($item, $excerptColumn) }}</p>
            @endif
        </div>
    </section>

    @php($imageUrl = data_get($item, 'image_url'))
    @php($detailBody = data_get($item, $bodyColumn) ?: data_get($item, $excerptColumn))
    @php($containsHtml = filled($detailBody) && $detailBody !== strip_tags((string) $detailBody))
    @php($showRelated = in_array($type, ['news', 'services', 'announcements', 'facilities', 'staff'], true))

    <section class="page-content">
        <div @class([
            'container',
            'detail-shell',
            'detail-shell--single' => !$showRelated || $relatedItems->isEmpty(),
            'staff-detail-shell' => $type === 'staff',
        ])>
            @if($type === 'galleries')
                <article class="gallery-detail">
                    <div class="detail-meta">
                        @if(isset($item->published_at) && $item->published_at)
                            <span class="meta-pill">{{ $item->published_at->translatedFormat('j F Y') }}</span>
                        @endif
                        <span class="meta-pill">{{ $item->photos->count() }} foto</span>
                    </div>

                    <div class="gallery-detail-grid">
                        @forelse($item->photos as $photo)
                            <figure class="gallery-photo-card">
                                <div class="gallery-photo-frame">
                                    <img src="{{ $photo->image_url }}" alt="{{ $photo->description ?: $item->title }}">
                                </div>
                                @if($photo->description)
                                    <figcaption>{{ $photo->description }}</figcaption>
                                @endif
                            </figure>
                        @empty
                            <div class="empty-content">
                                <h2>Belum ada foto</h2>
                                <p>Foto kegiatan akan tampil setelah ditambahkan dari backend.</p>
                            </div>
                        @endforelse
                    </div>

                    <p><a class="back-link" href="{{ route($type.'.index') }}">Kembali ke {{ $sectionTitle }}</a></p>
                </article>
            @elseif($type === 'staff')
                <article class="staff-detail-card">
                    <div class="staff-detail-photo">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $item->title }}">
                        @else
                            <span class="staff-detail-placeholder"><x-icon name="user" :size="52" /></span>
                        @endif
                    </div>

                    <div class="staff-detail-copy">
                        <div class="detail-meta">
                            @if(data_get($item, 'position'))
                                <span class="meta-pill">{{ data_get($item, 'position') }}</span>
                            @endif
                            <span class="meta-pill">{{ $sectionTitle }}</span>
                        </div>

                        <div class="detail-body">
                            @if($containsHtml)
                                {!! $detailBody !!}
                            @elseif(filled($detailBody))
                                {!! nl2br(e($detailBody)) !!}
                            @else
                                <p>Informasi detail staff belum tersedia.</p>
                            @endif
                        </div>

                        <p><a class="back-link" href="{{ route($type.'.index') }}">Kembali ke {{ $sectionTitle }}</a></p>
                    </div>
                </article>

                @includeWhen($showRelated, 'content.partials.related-sidebar')
            @else
                <article class="detail-main">
                    @if($imageUrl)
                        <div class="detail-image-frame">
                            <img class="detail-image" src="{{ $imageUrl }}" alt="{{ $item->title }}">
                        </div>
                    @endif

                    <div class="detail-meta">
                        @if(isset($item->published_at) && $item->published_at)
                            <span class="meta-pill">{{ $item->published_at->translatedFormat('j F Y') }}</span>
                        @endif

                        @if(isset($item->event_date) && $item->event_date)
                            <span class="meta-pill">{{ $item->event_date->translatedFormat('j F Y') }}</span>
                        @endif

                        @if(isset($item->icon) && $item->icon)
                            <span class="meta-pill"><x-icon :name="$item->icon" :size="16" /> {{ $sectionTitle }}</span>
                        @endif
                    </div>

                    <div class="detail-body">
                        @if($containsHtml)
                            {!! $detailBody !!}
                        @elseif(filled($detailBody))
                            {!! nl2br(e($detailBody)) !!}
                        @else
                            <p>Informasi detail belum tersedia.</p>
                        @endif
                    </div>

                    @if($type === 'resources' && $item->relationLoaded('items') && $item->items->isNotEmpty())
                        <div class="resource-link-panel">
                            <h2>Tautan e-Resource</h2>
                            <div class="resource-link-items">
                                @foreach($item->items as $resourceItem)
                                    <a class="resource-link-item" href="{{ $resourceItem->url }}" target="_blank" rel="noopener" aria-label="{{ $resourceItem->title }}">
                                        @if($resourceItem->image_url)
                                            <img src="{{ $resourceItem->image_url }}" alt="{{ $resourceItem->title }}">
                                        @else
                                            <span>{{ $resourceItem->url }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(isset($item->url) && $item->url && $item->url !== '#')
                        <p><a class="btn" href="{{ $item->url }}" target="_blank" rel="noopener">Buka Tautan <span aria-hidden="true">&rarr;</span></a></p>
                    @endif

                    <p><a class="back-link" href="{{ route($type.'.index') }}">Kembali ke {{ $sectionTitle }}</a></p>
                </article>

                @includeWhen($showRelated, 'content.partials.related-sidebar')
            @endif
        </div>
    </section>
</main>
@endsection

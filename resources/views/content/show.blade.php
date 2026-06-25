@extends('layouts.site')

@section('title', $item->title.' - '.$site->site_name)

@section('content')
<main>
    <section class="page-hero">
        @include('partials.page-hero-image')
        <div class="container">
            <p class="crumb"><a href="{{ route('home') }}">Beranda</a> / <a href="{{ route($type.'.index') }}">{{ $sectionTitle }}</a></p>
            <h1>{{ $item->title }}</h1>
            @if(data_get($item, $excerptColumn))
                <p>{{ data_get($item, $excerptColumn) }}</p>
            @endif
        </div>
    </section>

    <section class="page-content">
        <div class="container narrow">
            @php($imageUrl = data_get($item, 'image_url'))

            @if($imageUrl)
                <img class="detail-image" src="{{ $imageUrl }}" alt="{{ $item->title }}">
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

            @php($detailBody = data_get($item, $bodyColumn) ?: data_get($item, $excerptColumn))
            @php($containsHtml = $detailBody !== strip_tags((string) $detailBody))

            <div class="detail-body">
                @if($containsHtml)
                    {!! $detailBody !!}
                @else
                    {!! nl2br(e($detailBody)) !!}
                @endif
            </div>

            @if(isset($item->url) && $item->url && $item->url !== '#')
                <p><a class="btn" href="{{ $item->url }}" target="_blank" rel="noopener">Buka Tautan <span aria-hidden="true">&rarr;</span></a></p>
            @endif

            <p><a class="back-link" href="{{ route($type.'.index') }}">Kembali ke {{ $sectionTitle }}</a></p>
        </div>
    </section>
</main>
@endsection

@extends('layouts.site')

@section('title', $title.' - '.$site->site_name)

@section('content')
<main>
    <section class="page-hero">
        <div class="container">
            <p class="crumb"><a href="{{ route('home') }}">Beranda</a> / {{ $title }}</p>
            <h1>{{ $title }}</h1>
            <p>Daftar informasi {{ strtolower($title) }} yang tersedia dan dapat dikelola melalui backend.</p>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            <div class="content-grid">
                @forelse($items as $item)
                    @php($imageUrl = data_get($item, 'image_url'))
                    <article class="content-card">
                        @if($imageUrl)
                            <a href="{{ route($type.'.show', $item) }}">
                                <img src="{{ $imageUrl }}" alt="{{ $item->title }}">
                            </a>
                        @elseif(isset($item->icon) && $item->icon)
                            <a class="content-icon" href="{{ route($type.'.show', $item) }}">
                                <x-icon :name="$item->icon" :size="30" />
                            </a>
                        @endif

                        <div class="content-card-body">
                            @if(isset($item->published_at) && $item->published_at)
                                <time>{{ $item->published_at->translatedFormat('j F Y') }}</time>
                            @elseif(isset($item->event_date) && $item->event_date)
                                <time>{{ $item->event_date->translatedFormat('j F Y') }}</time>
                            @endif

                            <h2><a href="{{ route($type.'.show', $item) }}">{{ $item->title }}</a></h2>

                            @if(data_get($item, $excerptColumn))
                                <p>{{ data_get($item, $excerptColumn) }}</p>
                            @endif

                            <a class="small-btn" href="{{ route($type.'.show', $item) }}">Selengkapnya <span aria-hidden="true">&rarr;</span></a>
                        </div>
                    </article>
                @empty
                    <div class="empty-content">
                        <h2>Belum ada data</h2>
                        <p>Konten akan tampil di halaman ini setelah ditambahkan dari backend.</p>
                    </div>
                @endforelse
            </div>

            <div class="pagination-wrap">
                {{ $items->links() }}
            </div>
        </div>
    </section>
</main>
@endsection

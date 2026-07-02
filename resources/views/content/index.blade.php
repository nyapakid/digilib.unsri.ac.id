@extends('layouts.site')

@section('title', $title.' - '.$site->site_name)

@section('content')
<main>
    <section class="page-hero">
        @include('partials.page-hero-image')
        <div class="container">
            <p class="crumb"><a href="{{ route('home') }}">Beranda</a> / {{ $title }}</p>
            <h1>{{ $title }}</h1>
            <p>{{ $pageDescription ?? 'Daftar informasi '.strtolower($title).' yang tersedia dan dapat dikelola melalui backend.' }}</p>
        </div>
    </section>

    <section class="page-content">
        <div class="container">
            @if($type === 'staff' && blank($selectedStaffCategory))
                <div class="staff-category-grid">
                    @foreach($staffCategories as $categoryValue => $categoryLabel)
                        <a class="staff-category-card" href="{{ route('staff.index', ['category' => $categoryValue]) }}">
                            <span class="icon" aria-hidden="true"><x-icon name="user" :size="30" /></span>
                            <strong>{{ $categoryLabel }}</strong>
                            <span>{{ (int) ($staffCategoryCounts[$categoryValue] ?? 0) }} staff</span>
                        </a>
                    @endforeach
                </div>
            @else
                @if($type === 'staff')
                    <div class="staff-category-nav">
                        @foreach($staffCategories as $categoryValue => $categoryLabel)
                            <a @class(['active' => $selectedStaffCategory === $categoryValue]) href="{{ route('staff.index', ['category' => $categoryValue]) }}">{{ $categoryLabel }}</a>
                        @endforeach
                    </div>
                @endif

                <div class="content-grid">
                    @forelse($items as $item)
                        @php($imageUrl = data_get($item, 'image_url'))
                        <article class="content-card" @if($type === 'resources' && data_get($item, 'background_color')) style="--resource-bg: {{ data_get($item, 'background_color') }};" @endif>
                            @if($type === 'galleries')
                                <div class="content-card-body gallery-card-heading">
                                    @if(isset($item->published_at) && $item->published_at)
                                        <time>{{ $item->published_at->translatedFormat('j F Y') }}</time>
                                    @endif
                                    <h2><a href="{{ route($type.'.show', $item) }}">{{ $item->title }}</a></h2>
                                </div>

                                @if($imageUrl)
                                    <a class="gallery-cover-link" href="{{ route($type.'.show', $item) }}">
                                        <img src="{{ $imageUrl }}" alt="{{ $item->title }}">
                                    </a>
                                @endif
                            @elseif($imageUrl)
                                <a href="{{ route($type.'.show', $item) }}">
                                    <img src="{{ $imageUrl }}" alt="{{ $item->title }}">
                                </a>
                            @elseif(isset($item->icon) && $item->icon)
                                <a class="content-icon" href="{{ route($type.'.show', $item) }}">
                                    <x-icon :name="$item->icon" :size="30" />
                                </a>
                            @endif

                            @if($type !== 'galleries')
                                <div class="content-card-body">
                                    @if(isset($item->published_at) && $item->published_at)
                                        <time>{{ $item->published_at->translatedFormat('j F Y') }}</time>
                                    @elseif(isset($item->event_date) && $item->event_date)
                                        <time>{{ $item->event_date->translatedFormat('j F Y') }}</time>
                                    @elseif($type === 'staff' && data_get($item, 'category_label'))
                                        <time>{{ data_get($item, 'category_label') }}</time>
                                    @endif

                                    <h2><a href="{{ route($type.'.show', $item) }}">{{ $item->title }}</a></h2>

                                    @if(data_get($item, $excerptColumn))
                                        <p>{{ data_get($item, $excerptColumn) }}</p>
                                    @endif

                                    <a class="small-btn" href="{{ route($type.'.show', $item) }}">Selengkapnya <span aria-hidden="true">&rarr;</span></a>
                                </div>
                            @endif
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
            @endif
        </div>
    </section>
</main>
@endsection

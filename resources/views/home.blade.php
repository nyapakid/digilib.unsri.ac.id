@extends('layouts.site')

@section('content')
<main>
    <section class="hero hero-carousel" id="profil" aria-label="Slide utama">
        @forelse($heroSlides as $slide)
            @php($zoomClass = random_int(0, 1) ? 'hero-zoom-in' : 'hero-zoom-out')
            <article @class(['hero-slide', $zoomClass, 'active' => $loop->first])>
                <span class="hero-slide-bg" aria-hidden="true" style="background-image: linear-gradient(90deg, rgba(6, 32, 74, 0.96) 0%, rgba(6, 32, 74, 0.88) 37%, rgba(6, 32, 74, 0.18) 65%, rgba(6, 32, 74, 0.04) 100%), url('{{ $slide->image_url }}'); background-position: center center; background-size: cover; background-repeat: no-repeat;"></span>
                <canvas class="hero-matrix-canvas" data-hero-matrix aria-hidden="true"></canvas>
                <div class="container">
                    <div class="hero-copy">
                        <h1>{{ $slide->title }} @if($slide->highlight)<span>{{ $slide->highlight }}</span>@endif</h1>
                        <p>{{ $slide->description }}</p>
                        @if($slide->fact_1_title || $slide->fact_1_text || $slide->fact_2_title || $slide->fact_2_text)
                            <div class="hero-facts">
                                @if($slide->fact_1_title || $slide->fact_1_text)
                                    <div class="hero-fact">
                                        <span class="icon" aria-hidden="true"><x-icon :name="$slide->fact_1_icon ?: 'award'" :size="23" /></span>
                                        {{ $slide->fact_1_title }}<br>{{ $slide->fact_1_text }}
                                    </div>
                                @endif
                                @if($slide->fact_2_title || $slide->fact_2_text)
                                    <div class="hero-fact">
                                        <span class="icon" aria-hidden="true"><x-icon :name="$slide->fact_2_icon ?: 'clock'" :size="23" /></span>
                                        {{ $slide->fact_2_title }}<br>{{ $slide->fact_2_text }}
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if($slide->button_text && $slide->button_url)
                            <a class="btn" href="{{ $slide->button_url }}">{{ $slide->button_text }}
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14"/><path d="m13 6 6 6-6 6"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <article class="hero-slide hero-zoom-out active">
                <span class="hero-slide-bg" aria-hidden="true" style="background-image: linear-gradient(90deg, rgba(6, 32, 74, 0.96) 0%, rgba(6, 32, 74, 0.88) 37%, rgba(6, 32, 74, 0.18) 65%, rgba(6, 32, 74, 0.04) 100%), url('{{ $site->hero_image_url }}'); background-position: center center; background-size: cover; background-repeat: no-repeat;"></span>
                <canvas class="hero-matrix-canvas" data-hero-matrix aria-hidden="true"></canvas>
                <div class="container">
                    <div class="hero-copy">
                        <h1>{{ $site->hero_title }} <span>{{ $site->hero_highlight }}</span></h1>
                        <p>{{ $site->hero_description }}</p>
                    </div>
                </div>
            </article>
        @endforelse

        @if($heroSlides->count() > 1)
            <div class="slider-dots">
                @foreach($heroSlides as $slide)
                    <button type="button" @class(['active' => $loop->first]) aria-label="Tampilkan slide {{ $loop->iteration }}" data-hero-slide="{{ $loop->index }}"></button>
                @endforeach
            </div>
        @endif
    </section>

    <section class="resources" id="resources">
        <div class="container">
            <div class="section-head">
                <h2>Digilib e-Resources</h2>
                <a class="see-all" href="{{ route('resources.index') }}">Lihat Semua <span aria-hidden="true">&rarr;</span></a>
            </div>
            <div class="resource-grid">
                @foreach ($resources as $resource)
                    <a class="resource-card" href="{{ route('resources.show', $resource) }}" style="--resource-bg: {{ $resource->background_color ?: '#ffffff' }};">
                        <span class="icon"><x-icon :name="$resource->icon" :size="25" /></span>
                        <strong>{{ $resource->title }}</strong><span>{{ $resource->description }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @if($banners->isNotEmpty())
        <section class="banner-section" id="banner" aria-label="Banner">
            <div class="banner-stack">
                @foreach($banners as $banner)
                    <article @class([
                        'banner-item',
                        'banner-item--full' => $banner->image_size === 'full',
                        'banner-item--original' => $banner->image_size === 'original',
                    ])>
                        @if($banner->image_url)
                            <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}">
                        @endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section id="layanan">
        <div class="container two-columns">
            <div>
                <div class="section-head">
                    <h2>Layanan</h2>
                    <a class="see-all" href="{{ route('services.index') }}">Lihat Semua <span aria-hidden="true">&rarr;</span></a>
                </div>
                <div class="service-grid">
                    @foreach ($services as $service)
                        <article class="service-card">
                            @if($service->image_url)
                                <a href="{{ route('services.show', $service) }}"><img src="{{ $service->image_url }}" alt="{{ $service->title }}"></a>
                            @endif
                            <div class="service-body">
                                <h3><a href="{{ route('services.show', $service) }}">{{ $service->title }}</a></h3>
                                <p>{{ $service->description }}</p>
                                <a class="small-btn" href="{{ route('services.show', $service) }}">Selengkapnya <span aria-hidden="true">&rarr;</span></a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <aside id="fasilitas">
                <div class="section-head">
                    <h2>Fasilitas</h2>
                    <a class="see-all" href="{{ route('facilities.index') }}">Lihat Semua <span aria-hidden="true">&rarr;</span></a>
                </div>
                <div class="facility-list">
                    @foreach ($facilities as $facility)
                        <a class="facility-item" href="{{ route('facilities.show', $facility) }}">
                            <span class="icon"><x-icon :name="$facility->icon" :size="23" /></span>
                            <div><h3>{{ $facility->title }}</h3><p>{{ $facility->description }}</p></div>
                        </a>
                    @endforeach
                </div>
            </aside>
        </div>
    </section>
<!--
    <section class="staff-section" id="staff">
        <div class="container">
            <div class="section-head">
                <h2>Staff</h2>
                <a class="see-all" href="{{ route('staff.index') }}">Lihat Semua <span aria-hidden="true">&rarr;</span></a>
            </div>
            <div class="staff-grid">
                @foreach($staffMembers as $staff)
                    <article class="staff-card">
                        <a href="{{ route('staff.show', $staff) }}">
                            @if($staff->photo_url)
                                <span class="staff-photo">
                                    <img src="{{ $staff->photo_url }}" alt="{{ $staff->name }}">
                                </span>
                            @endif
                            <div class="staff-body">
                                <h3>{{ $staff->name }}</h3>
                                @if($staff->position)
                                    <p>{{ $staff->position }}</p>
                                @endif
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

 -->

    <section class="updates">
        <div class="container update-grid">
            <div class="panel">
                <div class="section-head">
                    <h2>Pengumuman</h2>
                    <a class="see-all" href="{{ route('announcements.index') }}">Lihat Semua <span aria-hidden="true">&rarr;</span></a>
                </div>
                <div class="list">
                    @foreach ($announcements as $announcement)
                        <a class="date-row" href="{{ route('announcements.show', $announcement) }}">
                            <div class="date-badge"><strong>{{ optional($announcement->published_at)->format('d') }}</strong><span>{{ optional($announcement->published_at)->translatedFormat('M') }}</span></div>
                            <div><h3>{{ $announcement->title }}</h3><p>{{ $announcement->excerpt }}</p></div>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="panel agenda">
                <div class="section-head">
                    <h2>Agenda</h2>
                    <a class="see-all" href="{{ route('agenda.index') }}">Lihat Semua <span aria-hidden="true">&rarr;</span></a>
                </div>
                <div class="list">
                    @foreach ($agendaItems as $agenda)
                        <a class="date-row" href="{{ route('agenda.show', $agenda) }}">
                            <div class="date-badge"><strong>{{ optional($agenda->event_date)->format('d') }}</strong><span>{{ optional($agenda->event_date)->translatedFormat('M') }}</span></div>
                            <div><h3>{{ $agenda->title }}</h3><p>{{ $agenda->description }}</p></div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section id="berita">
        <div class="container latest-grid">
            <div>
                <div class="section-head">
                    <h2>Berita Terbaru</h2>
                    <a class="see-all" href="{{ route('news.index') }}">Lihat Semua <span aria-hidden="true">&rarr;</span></a>
                </div>
                <div class="news-list">
                    @foreach ($newsPosts as $post)
                        <article class="news-card">
                            @if($post->image_url)
                                <a href="{{ route('news.show', $post) }}"><img src="{{ $post->image_url }}" alt="{{ $post->title }}"></a>
                            @endif
                            <div class="news-body">
                                <time>{{ optional($post->published_at)->translatedFormat('j F Y') }}</time>
                                <h3><a href="{{ route('news.show', $post) }}">{{ $post->title }}</a></h3>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <div id="galeri">
                <div class="section-head">
                    <h2>Galeri</h2>
                    <a class="see-all" href="{{ route('galleries.index') }}">Lihat Semua <span aria-hidden="true">&rarr;</span></a>
                </div>
                <div class="gallery-grid">
                    @foreach ($galleryItems as $gallery)
                        <a class="gallery-card" href="{{ route('galleries.show', $gallery) }}">
                            <span class="gallery-card-title">{{ $gallery->title }}</span>
                            <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}">
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="stats">
        <div class="container">
            <h2>{{ $site->stats_title }} <span>{{ $site->stats_subtitle }}</span></h2>
            <div class="stats-grid">
                @foreach ($statistics as $stat)
                    <div class="stat"><span class="icon"><x-icon :name="$stat->icon" :size="27" /></span><div><strong>{{ $stat->value }}</strong><span>{{ $stat->label }}</span></div></div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="partners">
        <div class="container">
            <div class="section-head">
                <h2>Mitra & Database</h2>
            </div>
            <div class="partner-carousel" data-partner-carousel aria-label="Carousel Mitra dan Database">
                <div class="partner-viewport">
                    <div class="partner-track">
                        @foreach ($partners as $partner)
                            <a class="partner" href="{{ $partner->url }}" target="_blank" rel="noopener" data-partner-item draggable="false">
                                @if($partner->logo_url)
                                    <span class="partner-logo">
                                        <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}" draggable="false">
                                    </span>
                                @else
                                    <span class="partner-name">{{ $partner->name }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

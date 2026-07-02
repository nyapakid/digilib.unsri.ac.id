@extends('layouts.site')

@section('title', $page->title.' - '.$site->site_name)

@section('content')
<main>
    <section class="page-hero">
        @include('partials.page-hero-image')
        <div class="container">
            <h1>{{ $page->title }}</h1>
            @if($page->excerpt)
                <p>{{ $page->excerpt }}</p>
            @endif
        </div>
    </section>

    <section class="page-content">
        <div class="container narrow">
            @php($containsHtml = $page->body !== strip_tags((string) $page->body))

            @if($containsHtml)
                {!! \App\Support\ContentSanitizer::sanitizeRichHtml($page->body) !!}
            @else
                {!! nl2br(e($page->body)) !!}
            @endif

            @if($page->embed_html)
                <div class="safe-embed">
                    {!! \App\Support\ContentSanitizer::sanitizeEmbedHtml($page->embed_html) !!}
                </div>
            @endif
        </div>
    </section>
</main>
@endsection

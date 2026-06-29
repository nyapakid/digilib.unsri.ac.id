<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $site->site_name)</title>
    <link rel="stylesheet" href="{{ asset('css/site.css') }}?v={{ filemtime(public_path('css/site.css')) }}">
</head>
<body id="top">
    @php($whatsapp = preg_replace('/\D+/', '', (string) $site->whatsapp_number))
    @php($topMenus = $menus->filter(fn ($menu) => blank($menu->parent_id)))
    @php($menuChildren = $menus->filter(fn ($menu) => filled($menu->parent_id))->groupBy('parent_id'))
    @php($menuUrl = fn ($menu) => str_starts_with($menu->url, '#') ? route('home').$menu->url : $menu->url)

    <div class="topbar">
        <div class="container">
            <div class="top-items">
                <span class="icon-text">
                    <span class="icon" aria-hidden="true"><x-icon name="map-pin" :size="19" /></span>
                    {{ $site->motto ?: $site->site_name }}
                </span>
            </div>
            <div class="top-items">
                <span class="icon-text">
                    <span class="icon" aria-hidden="true"><x-icon name="mail" :size="19" /></span>
                    {{ $site->email }}
                </span>
            </div>
        </div>
    </div>

    <header class="brandbar">
        <div class="container">
            <a class="brand" href="{{ route('home') }}">
                @if($site->logo_path)
                    <img class="brand-logo" src="{{ $site->logo_path }}" alt="{{ $site->site_name }}">
                @else
                    <span class="seal" aria-hidden="true"><span>{{ $site->logo_text }}</span></span>
                @endif
                <span class="brand-title">
                    <strong>{{ $site->brand_name }}</strong>
                    <span>{{ $site->university_name }}</span>
                </span>
            </a>
            <div class="helpbox">
                <span class="icon" aria-hidden="true"><x-icon name="chat" :size="27" /></span>
                <span>
                    {{ $site->help_text }}
                    <strong>{{ $site->whatsapp_number }}</strong>
                </span>
            </div>
        </div>
    </header>

    <nav class="navbar" aria-label="Navigasi utama">
        <div class="container">
            <button class="menu-toggle" type="button" aria-label="Buka menu" aria-expanded="false">
                <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
            <span class="mobile-only">Menu</span>
            <ul class="menu" id="mainMenu">
                @foreach ($topMenus as $menu)
                    @php($children = $menuChildren->get($menu->id, collect()))
                    <li @class(['has-dropdown' => $children->isNotEmpty()])>
                        <a @class(['active' => $loop->first && request()->routeIs('home')])
                           href="{{ $menuUrl($menu) }}"
                           @if($menu->opens_new_tab) target="_blank" rel="noopener" @endif>
                            {{ $menu->label }}
                            @if($children->isNotEmpty())
                                <span class="dropdown-caret" aria-hidden="true">&#9662;</span>
                            @endif
                        </a>
                        @if($children->isNotEmpty())
                            <ul class="menu-dropdown">
                                @foreach($children as $child)
                                    <li>
                                        <a href="{{ $menuUrl($child) }}" @if($child->opens_new_tab) target="_blank" rel="noopener" @endif>{{ $child->label }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </nav>

    @yield('content')

    <footer class="footer" id="kontak">
        <div class="container footer-main">
            <div>
                <div class="footer-brand">
                    @if($site->logo_path)
                        <img class="brand-logo" src="{{ $site->logo_path }}" alt="{{ $site->site_name }}">
                    @else
                        <span class="seal" aria-hidden="true"><span>{{ $site->logo_text }}</span></span>
                    @endif
                    <span><strong>DIGILIB</strong><span>{{ $site->university_name }}</span></span>
                </div>
                <p>{{ $site->footer_description }}</p>
            </div>
            <div>
                <h3>Tautan</h3>
                <ul>
                    @foreach ($topMenus as $menu)
                        <li><a href="{{ $menuUrl($menu) }}">{{ $menu->label }}</a></li>
                        @foreach($menuChildren->get($menu->id, collect()) as $child)
                            <li><a href="{{ $menuUrl($child) }}">- {{ $child->label }}</a></li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
            <div>
                <h3>Kontak Kami</h3>
                <p>{{ $site->address }}</p>
                <p>{{ $site->phone }}<br>{{ $site->email }}</p>
            </div>
            <div>
                <h3>Jam Layanan</h3>
                <p>{{ $site->office_hours }}</p>
                <p>{{ $site->weekend_hours }}</p>
            </div>
        </div>
        <div class="copyright">{{ $site->copyright_text }}</div>
    </footer>
<!--
    <div class="floating" aria-label="Bantuan cepat">
        <a class="chat" href="{{ $whatsapp ? 'https://wa.me/'.$whatsapp : '#' }}">Butuh bantuan? Klik di sini!</a>
        <a class="wa" href="{{ $whatsapp ? 'https://wa.me/'.$whatsapp : '#' }}" aria-label="WhatsApp">
            <svg viewBox="0 0 24 24" width="27" height="27" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.4 8.4 0 0 1-12.5 7.3L3 20l1.3-5.2A8.4 8.4 0 1 1 21 11.5Z"/><path d="M8.5 8.5c.4 3 2.3 5.1 5.3 6 .7.2 1.3-.4 1.7-1l.3-.5c.2-.4.1-.8-.3-1l-1.4-.7c-.3-.2-.7-.1-.9.2l-.4.5c-1-.5-1.8-1.2-2.3-2.2l.5-.4c.3-.2.4-.6.2-.9l-.7-1.4c-.2-.4-.7-.5-1-.3l-.5.3c-.4.2-.6.7-.5 1.4Z"/></svg>
        </a>
        <a class="to-top" href="#top" aria-label="Kembali ke atas">↑</a>
    </div>
-->

    <script src="{{ asset('js/site.js') }}?v={{ filemtime(public_path('js/site.js')) }}"></script>
</body>
</html>

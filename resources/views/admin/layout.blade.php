<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Digilib')</title>
    @stack('styles')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    @php($sections = \App\Support\Admin\ContentRegistry::all())

    <div class="admin-shell">
        <aside class="sidebar">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}">
                <span>US</span>
                <strong>Admin Digilib</strong>
            </a>
            <nav class="admin-nav">
                <a @class(['active' => request()->routeIs('admin.dashboard')]) href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a @class(['active' => request()->routeIs('admin.settings.*')]) href="{{ route('admin.settings.edit') }}">Pengaturan Situs</a>
                @foreach ($sections as $type => $section)
                    <a @class(['active' => request()->route('type') === $type]) href="{{ route('admin.content.index', $type) }}">{{ $section['label'] }}</a>
                @endforeach
            </nav>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div>
                    <span class="eyebrow">Backend</span>
                    <h1>@yield('heading', 'Admin Digilib')</h1>
                </div>
                <div class="topbar-actions">
                    <a class="ghost-btn" href="{{ route('home') }}" target="_blank" rel="noopener">Lihat Website</a>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="ghost-btn danger" type="submit">Keluar</button>
                    </form>
                </div>
            </header>

            @if(session('status'))
                <div class="alert success">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="alert error">
                    <strong>Periksa kembali input:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <main class="admin-content">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>

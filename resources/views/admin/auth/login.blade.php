<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin Digilib</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="login-page">
    <main class="login-card">
        <div class="admin-brand login-brand">
            <span>US</span>
            <strong>Admin Digilib</strong>
        </div>
        <h1>Masuk Backend</h1>
        <p>Kelola menu, layanan, pengumuman, berita, galeri, statistik, halaman, dan pengaturan situs.</p>

        @if($errors->any())
            <div class="alert error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}" class="stack-form">
            @csrf
            <label>
                Email
                <input type="email" name="email" value="{{ old('email') }}" required autofocus>
            </label>
            <label>
                Password
                <input type="password" name="password" required>
            </label>
            <label class="checkbox-row">
                <input type="checkbox" name="remember" value="1">
                Ingat saya
            </label>
            <button class="primary-btn" type="submit">Masuk</button>
        </form>
    </main>
</body>
</html>

@extends('admin.layout')

@section('title', 'Pengaturan Situs')
@section('heading', 'Pengaturan Situs')

@section('content')
    <section class="panel-card">
        <form method="POST" action="{{ route('admin.settings.update') }}" class="content-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-grid">
                @foreach($fields as [$name, $label, $type])
                    <label @class(['full' => in_array($type, ['textarea', 'file'], true)])>
                        {{ $label }}
                        @if($type === 'textarea')
                            <textarea name="{{ $name }}" rows="4">{{ old($name, $site->{$name}) }}</textarea>
                        @elseif($type === 'file')
                            @if($site->{$name})
                                <img class="upload-preview" src="{{ $site->{$name} }}" alt="{{ $label }}">
                            @endif
                            <input type="file" name="{{ $name }}" accept="image/*">
                            <small class="field-help">{{ $name === 'logo_path' ? 'Kosongkan jika tidak ingin mengganti logo.' : 'Kosongkan jika tidak ingin mengganti gambar.' }}</small>
                        @else
                            <input type="text" name="{{ $name }}" value="{{ old($name, $site->{$name}) }}">
                        @endif
                    </label>
                @endforeach
            </div>

            <div class="settings-module-section">
                <div>
                    <h2>Daftar Nama Modul</h2>
                    <p>Edit judul dan deskripsi yang tampil pada hero halaman modul.</p>
                </div>
                <div class="settings-module-table-wrap">
                    <table class="settings-module-table">
                        <thead>
                            <tr>
                                <th>Modul</th>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($moduleFields as $module)
                                <tr>
                                    <td><strong>{{ $module['label'] }}</strong></td>
                                    <td>
                                        <label>
                                            <span class="sr-only">Judul {{ $module['label'] }}</span>
                                            <input type="text" name="{{ $module['title'] }}" value="{{ old($module['title'], $site->{$module['title']}) }}">
                                        </label>
                                    </td>
                                    <td>
                                        <label>
                                            <span class="sr-only">Deskripsi {{ $module['label'] }}</span>
                                            <textarea name="{{ $module['description'] }}" rows="3">{{ old($module['description'], $site->{$module['description']}) }}</textarea>
                                        </label>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @php($checkedFooterMenuIds = collect(old('footer_menu_ids', $footerMenuOptions->where('show_in_footer', true)->pluck('id')->all()))->map(fn ($id) => (string) $id)->all())
            <div class="footer-checklist-section">
                <div>
                    <h2>Menu Footer</h2>
                    <p>Centang menu yang ingin ditampilkan pada bagian tautan footer.</p>
                </div>
                <div class="footer-menu-table-wrap">
                    <table class="footer-menu-table">
                        <thead>
                            <tr>
                                <th>Tampil</th>
                                <th>Menu</th>
                                <th>URL</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($footerMenuOptions as $menu)
                                <tr>
                                    <td>
                                        <label class="footer-menu-check">
                                            <input type="checkbox" name="footer_menu_ids[]" value="{{ $menu->id }}" @checked(in_array((string) $menu->id, $checkedFooterMenuIds, true))>
                                            <span class="sr-only">Tampilkan {{ $menu->label }} di footer</span>
                                        </label>
                                    </td>
                                    <td>
                                        <strong @class(['footer-menu-name', 'is-child' => filled($menu->parent_id)])>
                                            @if($menu->parent)
                                                {{ $menu->parent->label }} / {{ $menu->label }}
                                            @else
                                                {{ $menu->label }}
                                            @endif
                                        </strong>
                                    </td>
                                    <td><span class="footer-menu-url">{{ $menu->url }}</span></td>
                                    <td>
                                        <span @class(['status-badge', 'on' => $menu->is_active, 'off' => ! $menu->is_active])>
                                            {{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="form-actions">
                <button class="primary-btn" type="submit">Simpan Pengaturan</button>
            </div>
        </form>
    </section>
@endsection

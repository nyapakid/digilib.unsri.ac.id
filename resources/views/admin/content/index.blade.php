@extends('admin.layout')

@section('title', $definition['label'])
@section('heading', $definition['label'])

@section('content')
    <section class="panel-card">
        <div class="panel-head">
            <div>
                <h2>Daftar {{ $definition['label'] }}</h2>
                <p>Tambah, ubah, nonaktifkan, atau hapus konten yang tampil di website.</p>
            </div>
            <a class="primary-btn" href="{{ route('admin.content.create', $type) }}">Tambah {{ $definition['singular'] }}</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Urutan</th>
                        <th>Status</th>
                        <th>Diperbarui</th>
                        <th class="actions-col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>
                                <strong>{{ data_get($item, $definition['title']) }}</strong>
                                @if(isset($item->url) && $type !== 'galleries')
                                    <small>{{ $item->url }}</small>
                                @elseif(isset($item->slug))
                                    <small>/halaman/{{ $item->slug }}</small>
                                @endif
                                @if($type === 'galleries')
                                    <small>{{ optional($item->published_at)->format('d/m/Y') ?: 'Tanpa tanggal' }}</small>
                                @endif
                                @if($type === 'menus' && $item->parent)
                                    <small>Child dari: {{ $item->parent->label }}</small>
                                @endif
                            </td>
                            <td>{{ $item->sort_order }}</td>
                            <td>
                                <span @class(['status-badge', 'on' => $item->is_active, 'off' => ! $item->is_active])>
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>{{ $item->updated_at?->format('d/m/Y H:i') }}</td>
                            <td class="actions">
                                <a class="ghost-btn" href="{{ route('admin.content.edit', [$type, $item->id]) }}">Edit</a>
                                <form method="POST" action="{{ route('admin.content.destroy', [$type, $item->id]) }}" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="ghost-btn danger" type="submit">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $items->links() }}
    </section>
@endsection

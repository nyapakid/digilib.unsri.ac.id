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
                            <small class="field-help">Kosongkan jika tidak ingin mengganti logo.</small>
                        @else
                            <input type="text" name="{{ $name }}" value="{{ old($name, $site->{$name}) }}">
                        @endif
                    </label>
                @endforeach
            </div>

            <div class="form-actions">
                <button class="primary-btn" type="submit">Simpan Pengaturan</button>
            </div>
        </form>
    </section>
@endsection

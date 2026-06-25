@extends('admin.layout')

@php($isEdit = $item->exists)
@php($hasSummernote = collect($definition['fields'])->contains(fn (array $field) => $field['type'] === 'summernote'))

@if($hasSummernote)
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css">
    @endpush

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                if (!window.jQuery || !jQuery.fn.summernote) {
                    return;
                }

                jQuery(".summernote-editor").summernote({
                    height: 280,
                    minHeight: 180,
                    toolbar: [
                        ["style", ["style"]],
                        ["font", ["bold", "italic", "underline", "clear"]],
                        ["para", ["ul", "ol", "paragraph"]],
                        ["insert", ["link", "table"]],
                        ["view", ["fullscreen", "codeview"]]
                    ]
                });
            });
        </script>
    @endpush
@endif

@section('title', ($isEdit ? 'Edit ' : 'Tambah ').$definition['singular'])
@section('heading', ($isEdit ? 'Edit ' : 'Tambah ').$definition['singular'])

@section('content')
    <section class="panel-card">
        <form method="POST" action="{{ $isEdit ? route('admin.content.update', [$type, $item->id]) : route('admin.content.store', $type) }}" class="content-form" enctype="multipart/form-data">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="form-grid">
                @foreach($definition['fields'] as $field)
                    @php($value = old($field['name'], $item->{$field['name']}))

                    @if($field['type'] === 'checkbox')
                        <label class="checkbox-row">
                            <input type="checkbox" name="{{ $field['name'] }}" value="1" @checked((bool) $value)>
                            {{ $field['label'] }}
                        </label>
                    @else
                        <label @class(['full' => in_array($field['type'], ['textarea', 'summernote', 'file'], true)])>
                            {{ $field['label'] }}
                            @if(in_array($field['type'], ['textarea', 'summernote'], true))
                                <textarea @class(['summernote-editor' => $field['type'] === 'summernote']) name="{{ $field['name'] }}" rows="5">{{ $value }}</textarea>
                            @elseif($field['type'] === 'file')
                                @if($value)
                                    <img class="upload-preview" src="{{ $value }}" alt="{{ $field['label'] }}">
                                @endif
                                <input type="file" name="{{ $field['name'] }}" accept="image/*">
                                <small class="field-help">Kosongkan jika tidak ingin mengganti gambar.</small>
                            @elseif($field['type'] === 'select')
                                <select name="{{ $field['name'] }}">
                                    @foreach($field['options'] as $optionValue => $optionLabel)
                                        <option value="{{ $optionValue }}" @selected($value === $optionValue)>{{ $optionLabel }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" value="{{ $value }}">
                            @endif
                        </label>
                    @endif
                @endforeach
            </div>

            <div class="form-actions">
                <a class="ghost-btn" href="{{ route('admin.content.index', $type) }}">Batal</a>
                <button class="primary-btn" type="submit">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Data' }}</button>
            </div>
        </form>
    </section>
@endsection

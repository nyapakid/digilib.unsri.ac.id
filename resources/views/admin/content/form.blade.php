@extends('admin.layout')

@php($isEdit = $item->exists)
@php($hasSummernote = collect($definition['fields'])->contains(fn (array $field) => $field['type'] === 'summernote'))
@php($hasMenuLink = collect($definition['fields'])->contains(fn (array $field) => $field['type'] === 'menu_link'))

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

@if($hasMenuLink)
    @push('scripts')
        <script>
            function initMenuLinkFields() {
                document.querySelectorAll("[data-menu-link]").forEach(function (field) {
                    var radios = field.querySelectorAll('input[name="link_type"]');
                    var panels = field.querySelectorAll("[data-link-mode]");

                    function syncMenuLinkMode() {
                        var checked = field.querySelector('input[name="link_type"]:checked');
                        var mode = checked ? checked.value : "page";

                        panels.forEach(function (panel) {
                            var isActive = panel.dataset.linkMode === mode;
                            panel.hidden = !isActive;
                            panel.querySelectorAll("input, select").forEach(function (control) {
                                control.disabled = !isActive;
                            });
                        });
                    }

                    radios.forEach(function (radio) {
                        radio.addEventListener("change", syncMenuLinkMode);
                    });

                    syncMenuLinkMode();
                });
            }

            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", initMenuLinkFields);
            } else {
                initMenuLinkFields();
            }
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
                    @elseif($field['type'] === 'menu_link')
                        @php($pages = \App\Models\Page::query()->orderBy('sort_order')->orderByDesc('id')->get())
                        @php($savedUrl = (string) ($value ?: ''))
                        @php($urlPath = parse_url($savedUrl, PHP_URL_PATH) ?: $savedUrl)
                        @php($normalizedPath = '/'.ltrim($urlPath, '/'))
                        @php($pageSlug = str_starts_with($normalizedPath, '/halaman/') ? trim(substr($normalizedPath, strlen('/halaman/')), '/') : null)
                        @php($matchedPage = $pageSlug ? $pages->firstWhere('slug', $pageSlug) : null)
                        @php($selectedLinkType = old('link_type', $matchedPage ? 'page' : ($item->exists ? 'url' : 'page')))
                        @php($selectedPageId = old('page_id', $matchedPage?->id))
                        @php($customUrl = old('custom_url', $matchedPage ? '#' : ($value ?: '#')))

                        <div class="field-group full menu-link-field" data-menu-link>
                            <span class="field-label">{{ $field['label'] }}</span>
                            <div class="radio-group" role="radiogroup" aria-label="Pilih tujuan menu">
                                <label class="radio-row">
                                    <input type="radio" name="link_type" value="page" @checked($selectedLinkType === 'page')>
                                    Halaman
                                </label>
                                <label class="radio-row">
                                    <input type="radio" name="link_type" value="url" @checked($selectedLinkType === 'url')>
                                    URL / Anchor
                                </label>
                            </div>

                            <label data-link-mode="page" @if($selectedLinkType !== 'page') hidden @endif>
                                Pilih Halaman
                                <select name="page_id" @disabled($selectedLinkType !== 'page')>
                                    <option value="">Pilih halaman</option>
                                    @foreach($pages as $page)
                                        <option value="{{ $page->id }}" @selected((string) $selectedPageId === (string) $page->id)>
                                            {{ $page->title }}{{ $page->is_active ? '' : ' (Nonaktif)' }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>

                            <label data-link-mode="url" @if($selectedLinkType !== 'url') hidden @endif>
                                URL / Anchor
                                <input type="text" name="custom_url" value="{{ $customUrl }}" placeholder="#layanan atau https://contoh.ac.id" @disabled($selectedLinkType !== 'url')>
                            </label>

                            <small class="field-help">Mode Halaman mengambil data dari menu Halaman. Pilih URL / Anchor untuk tautan manual seperti #layanan atau https://contoh.ac.id.</small>
                        </div>
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
                            @elseif($field['type'] === 'menu_parent')
                                @php($parentMenus = \App\Models\MenuItem::query()
                                    ->whereNull('parent_id')
                                    ->when($item->exists, fn ($query) => $query->whereKeyNot($item->id))
                                    ->orderBy('sort_order')
                                    ->orderByDesc('id')
                                    ->get())
                                <select name="{{ $field['name'] }}">
                                    <option value="">Menu Utama</option>
                                    @foreach($parentMenus as $parentMenu)
                                        <option value="{{ $parentMenu->id }}" @selected((string) $value === (string) $parentMenu->id)>{{ $parentMenu->label }}</option>
                                    @endforeach
                                </select>
                                <small class="field-help">Kosongkan untuk menu utama. Pilih menu utama untuk menjadikannya dropdown child.</small>
                            @elseif($field['type'] === 'select')
                                <select name="{{ $field['name'] }}">
                                    @foreach($field['options'] as $optionValue => $optionLabel)
                                        <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                                    @endforeach
                                </select>
                            @elseif($field['type'] === 'color')
                                <input type="color" name="{{ $field['name'] }}" value="{{ $value ?: '#ffffff' }}">
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

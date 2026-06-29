@extends('admin.layout')

@php($isEdit = $item->exists)
@php($hasSummernote = collect($definition['fields'])->contains(fn (array $field) => $field['type'] === 'summernote'))
@php($hasMenuLink = collect($definition['fields'])->contains(fn (array $field) => $field['type'] === 'menu_link'))
@php($hasGalleryPhotos = $type === 'galleries')
@php($hasResourceItems = $type === 'resources')

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

@if($hasGalleryPhotos)
    @push('scripts')
        <script>
            function initGalleryPhotoManager() {
                document.querySelectorAll("[data-gallery-photo-manager]").forEach(function (manager) {
                    var list = manager.querySelector("[data-gallery-photo-list]");
                    var template = manager.querySelector("[data-gallery-photo-template]");
                    var addButton = manager.querySelector("[data-add-gallery-photo]");
                    var nextIndex = Number(manager.dataset.nextIndex || 1);

                    if (!list || !template || !addButton) {
                        return;
                    }

                    addButton.addEventListener("click", function () {
                        var wrapper = document.createElement("template");
                        wrapper.innerHTML = template.innerHTML.replaceAll("__INDEX__", String(nextIndex)).trim();
                        list.appendChild(wrapper.content.firstElementChild);
                        nextIndex += 1;
                        manager.dataset.nextIndex = String(nextIndex);
                    });

                    list.addEventListener("click", function (event) {
                        var button = event.target.closest("[data-remove-new-photo]");

                        if (button) {
                            button.closest(".gallery-photo-row")?.remove();
                        }
                    });
                });
            }

            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", initGalleryPhotoManager);
            } else {
                initGalleryPhotoManager();
            }
        </script>
    @endpush
@endif

@if($hasResourceItems)
    @push('scripts')
        <script>
            function initResourceItemManager() {
                document.querySelectorAll("[data-resource-item-manager]").forEach(function (manager) {
                    var list = manager.querySelector("[data-resource-item-list]");
                    var template = manager.querySelector("[data-resource-item-template]");
                    var addButton = manager.querySelector("[data-add-resource-item]");
                    var nextIndex = Number(manager.dataset.nextIndex || 1);

                    if (!list || !template || !addButton) {
                        return;
                    }

                    addButton.addEventListener("click", function () {
                        var wrapper = document.createElement("template");
                        wrapper.innerHTML = template.innerHTML.replaceAll("__INDEX__", String(nextIndex)).trim();
                        list.appendChild(wrapper.content.firstElementChild);
                        nextIndex += 1;
                        manager.dataset.nextIndex = String(nextIndex);
                    });

                    list.addEventListener("click", function (event) {
                        var button = event.target.closest("[data-remove-new-resource-item]");

                        if (button) {
                            button.closest(".resource-item-row")?.remove();
                        }
                    });
                });
            }

            if (document.readyState === "loading") {
                document.addEventListener("DOMContentLoaded", initResourceItemManager);
            } else {
                initResourceItemManager();
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

                @if($hasGalleryPhotos)
                    @php($oldCover = old('cover_photo'))
                    @php($existingPhotos = $item->relationLoaded('photos') ? $item->photos : collect())
                    @php($newPhotoDescriptions = old('new_photo_descriptions', ['']))
                    @php($nextPhotoIndex = max(1, count($newPhotoDescriptions)))

                    <div class="field-group full gallery-photo-manager" data-gallery-photo-manager data-next-index="{{ $nextPhotoIndex }}">
                        <div>
                            <span class="field-label">Foto Galeri</span>
                            <small class="field-help">Tambahkan foto kegiatan sebanyak mungkin, isi deskripsi foto, lalu pilih salah satu sebagai foto cover.</small>
                        </div>

                        @if($existingPhotos->isNotEmpty())
                            <div class="gallery-photo-list existing">
                                @foreach($existingPhotos as $photo)
                                    @php($existingToken = 'existing-'.$photo->id)
                                    <div class="gallery-photo-row">
                                        <img src="{{ $photo->image_url }}" alt="{{ $item->title }}">
                                        <div class="gallery-photo-fields">
                                            <label class="radio-row cover-choice">
                                                <input type="radio" name="cover_photo" value="{{ $existingToken }}" @checked($oldCover ? $oldCover === $existingToken : $photo->is_cover)>
                                                Foto cover
                                            </label>
                                            <label>
                                                Deskripsi Foto
                                                <input type="text" name="existing_photo_descriptions[{{ $photo->id }}]" value="{{ old('existing_photo_descriptions.'.$photo->id, $photo->description) }}" placeholder="Contoh: Suasana kegiatan akreditasi">
                                            </label>
                                            <label class="checkbox-row remove-photo">
                                                <input type="checkbox" name="remove_photo_ids[]" value="{{ $photo->id }}" @checked(in_array($photo->id, old('remove_photo_ids', [])))>
                                                Hapus foto ini
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="gallery-photo-list" data-gallery-photo-list>
                            @foreach($newPhotoDescriptions as $index => $description)
                                @php($newToken = 'new-'.$index)
                                <div class="gallery-photo-row new">
                                    <div class="new-photo-placeholder">Foto Baru</div>
                                    <div class="gallery-photo-fields">
                                        <label class="radio-row cover-choice">
                                            <input type="radio" name="cover_photo" value="{{ $newToken }}" @checked($oldCover ? $oldCover === $newToken : (! $isEdit && $loop->first))>
                                            Jadikan cover
                                        </label>
                                        <label>
                                            Upload Foto
                                            <input type="file" name="new_photos[{{ $index }}]" accept="image/*">
                                        </label>
                                        <label>
                                            Deskripsi Foto
                                            <input type="text" name="new_photo_descriptions[{{ $index }}]" value="{{ $description }}" placeholder="Deskripsi singkat foto">
                                        </label>
                                        <button class="ghost-btn danger" type="button" data-remove-new-photo>Hapus Baris</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <template data-gallery-photo-template>
                            <div class="gallery-photo-row new">
                                <div class="new-photo-placeholder">Foto Baru</div>
                                <div class="gallery-photo-fields">
                                    <label class="radio-row cover-choice">
                                        <input type="radio" name="cover_photo" value="new-__INDEX__">
                                        Jadikan cover
                                    </label>
                                    <label>
                                        Upload Foto
                                        <input type="file" name="new_photos[__INDEX__]" accept="image/*">
                                    </label>
                                    <label>
                                        Deskripsi Foto
                                        <input type="text" name="new_photo_descriptions[__INDEX__]" placeholder="Deskripsi singkat foto">
                                    </label>
                                    <button class="ghost-btn danger" type="button" data-remove-new-photo>Hapus Baris</button>
                                </div>
                            </div>
                        </template>

                        <button class="ghost-btn add-photo-btn" type="button" data-add-gallery-photo>Tambah Foto</button>
                    </div>
                @endif

                @if($hasResourceItems)
                    @php($existingItems = $item->relationLoaded('items') ? $item->items : collect())
                    @php($newItemTitles = old('new_resource_item_titles', ['']))
                    @php($nextResourceIndex = max(1, count($newItemTitles)))

                    <div class="field-group full resource-item-manager" data-resource-item-manager data-next-index="{{ $nextResourceIndex }}">
                        <div>
                            <span class="field-label">List Tautan e-Resource</span>
                            <small class="field-help">Tambahkan tautan yang akan tampil di detail e-Resource. Di frontend hanya gambar yang tampil sebagai link; jika gambar kosong, URL link akan tampil sebagai teks.</small>
                        </div>

                        <div class="resource-item-table-wrap">
                            <table class="resource-item-table">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Link</th>
                                        <th>Gambar</th>
                                        <th>Urutan</th>
                                        <th>Hapus</th>
                                    </tr>
                                </thead>
                                <tbody data-resource-item-list>
                                @foreach($existingItems as $resourceItem)
                                    <tr class="resource-item-row">
                                        <td>
                                            <label>
                                                <span class="sr-only">Judul</span>
                                                <input type="text" name="existing_resource_item_titles[{{ $resourceItem->id }}]" value="{{ old('existing_resource_item_titles.'.$resourceItem->id, $resourceItem->title) }}">
                                            </label>
                                        </td>
                                        <td>
                                            <label>
                                                <span class="sr-only">Link</span>
                                                <input type="text" name="existing_resource_item_urls[{{ $resourceItem->id }}]" value="{{ old('existing_resource_item_urls.'.$resourceItem->id, $resourceItem->url) }}">
                                            </label>
                                        </td>
                                        <td>
                                            <div class="resource-item-image-cell">
                                            @if($resourceItem->image_url)
                                                    <img class="resource-item-thumb" src="{{ $resourceItem->image_url }}" alt="{{ $resourceItem->title }}">
                                            @else
                                                    <span class="resource-item-empty-image">Tanpa gambar</span>
                                            @endif
                                                <label>
                                                    <span class="sr-only">Ganti Gambar</span>
                                                <input type="file" name="existing_resource_item_images[{{ $resourceItem->id }}]" accept="image/*">
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <label>
                                                <span class="sr-only">Urutan</span>
                                                <input type="number" name="existing_resource_item_orders[{{ $resourceItem->id }}]" value="{{ old('existing_resource_item_orders.'.$resourceItem->id, $resourceItem->sort_order) }}" min="0">
                                            </label>
                                        </td>
                                        <td>
                                            <label class="checkbox-row remove-photo resource-item-remove">
                                                <input type="checkbox" name="remove_resource_item_ids[]" value="{{ $resourceItem->id }}" @checked(in_array($resourceItem->id, old('remove_resource_item_ids', [])))>
                                                <span>Hapus</span>
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                            @foreach($newItemTitles as $index => $newTitle)
                                    <tr class="resource-item-row new">
                                        <td>
                                        <label>
                                                <span class="sr-only">Judul</span>
                                            <input type="text" name="new_resource_item_titles[{{ $index }}]" value="{{ $newTitle }}" placeholder="Contoh: ScienceDirect">
                                        </label>
                                        </td>
                                        <td>
                                        <label>
                                                <span class="sr-only">Link</span>
                                            <input type="text" name="new_resource_item_urls[{{ $index }}]" value="{{ old('new_resource_item_urls.'.$index) }}" placeholder="https://contoh.ac.id">
                                        </label>
                                        </td>
                                        <td>
                                        <label>
                                                <span class="sr-only">Upload Gambar</span>
                                            <input type="file" name="new_resource_item_images[{{ $index }}]" accept="image/*">
                                        </label>
                                        </td>
                                        <td>
                                            <label>
                                                <span class="sr-only">Urutan</span>
                                                <input type="number" name="new_resource_item_orders[{{ $index }}]" value="{{ old('new_resource_item_orders.'.$index, 0) }}" min="0">
                                            </label>
                                        </td>
                                        <td>
                                            <button class="ghost-btn danger table-action-btn" type="button" data-remove-new-resource-item>Hapus</button>
                                        </td>
                                    </tr>
                            @endforeach
                                </tbody>
                            </table>
                        </div>

                        <template data-resource-item-template>
                            <tr class="resource-item-row new">
                                <td>
                                    <label>
                                        <span class="sr-only">Judul</span>
                                        <input type="text" name="new_resource_item_titles[__INDEX__]" placeholder="Contoh: ScienceDirect">
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <span class="sr-only">Link</span>
                                        <input type="text" name="new_resource_item_urls[__INDEX__]" placeholder="https://contoh.ac.id">
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <span class="sr-only">Upload Gambar</span>
                                        <input type="file" name="new_resource_item_images[__INDEX__]" accept="image/*">
                                    </label>
                                </td>
                                <td>
                                    <label>
                                        <span class="sr-only">Urutan</span>
                                        <input type="number" name="new_resource_item_orders[__INDEX__]" value="0" min="0">
                                    </label>
                                </td>
                                <td>
                                    <button class="ghost-btn danger table-action-btn" type="button" data-remove-new-resource-item>Hapus</button>
                                </td>
                            </tr>
                        </template>

                        <button class="ghost-btn add-photo-btn" type="button" data-add-resource-item>Tambah Tautan</button>
                    </div>
                @endif
            </div>

            <div class="form-actions">
                <a class="ghost-btn" href="{{ route('admin.content.index', $type) }}">Batal</a>
                <button class="primary-btn" type="submit">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Data' }}</button>
            </div>
        </form>
    </section>
@endsection

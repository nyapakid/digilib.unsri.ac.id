<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Models\GalleryPhoto;
use App\Models\MenuItem;
use App\Models\Page;
use App\Support\Admin\ContentRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(string $type): View
    {
        $definition = ContentRegistry::get($type);
        $query = $definition['model']::query();

        if (($definition['model']) === MenuItem::class) {
            $query->with('parent');
        }

        if (($definition['model']) === GalleryItem::class) {
            $query->with('coverPhoto');
        }

        $items = $query->orderBy('sort_order')
            ->latest()
            ->paginate(15);

        return view('admin.content.index', compact('type', 'definition', 'items'));
    }

    public function create(string $type): View
    {
        $definition = ContentRegistry::get($type);
        $item = new $definition['model']([
            'sort_order' => 0,
            'is_active' => true,
            'url' => '#',
        ]);

        return view('admin.content.form', compact('type', 'definition', 'item'));
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $definition = ContentRegistry::get($type);
        $data = $this->validatedData($request, $definition);

        if (($definition['model']) === GalleryItem::class) {
            $this->storeGallery($request, $data);
        } else {
            $definition['model']::create($data);
        }

        return redirect()
            ->route('admin.content.index', $type)
            ->with('status', $definition['singular'].' berhasil ditambahkan.');
    }

    public function edit(string $type, int $id): View
    {
        $definition = ContentRegistry::get($type);
        $item = $this->findItem($definition, $id);

        if ($item instanceof GalleryItem) {
            $item->load('photos');
        }

        return view('admin.content.form', compact('type', 'definition', 'item'));
    }

    public function update(Request $request, string $type, int $id): RedirectResponse
    {
        $definition = ContentRegistry::get($type);
        $item = $this->findItem($definition, $id);
        $oldPageUrl = ($definition['model']) === Page::class ? route('pages.show', $item->slug, false) : null;
        $data = $this->validatedData($request, $definition, $item);

        if ($item instanceof GalleryItem) {
            DB::transaction(function () use ($item, $request, $data) {
                $item->update($data);
                $this->syncGalleryPhotos($item, $request);
            });
        } else {
            $item->update($data);
        }

        if (($definition['model']) === Page::class) {
            $newPageUrl = route('pages.show', $item->slug, false);

            if ($oldPageUrl !== $newPageUrl) {
                MenuItem::query()->where('url', $oldPageUrl)->update(['url' => $newPageUrl]);
            }
        }

        return redirect()
            ->route('admin.content.index', $type)
            ->with('status', $definition['singular'].' berhasil diperbarui.');
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        $definition = ContentRegistry::get($type);
        $this->findItem($definition, $id)->delete();

        return redirect()
            ->route('admin.content.index', $type)
            ->with('status', $definition['singular'].' berhasil dihapus.');
    }

    private function validatedData(Request $request, array $definition, ?Model $item = null): array
    {
        if (($definition['model']) === Page::class && ! $request->filled('slug') && $request->filled('title')) {
            $request->merge(['slug' => Str::slug($request->input('title'))]);
        }

        $rules = [];
        $checkboxes = [];
        $files = [];

        foreach ($definition['fields'] as $field) {
            if ($field['type'] === 'menu_link') {
                continue;
            }

            $rules[$field['name']] = $field['rules'];

            if ($field['type'] === 'checkbox') {
                $checkboxes[] = $field['name'];
            }

            if ($field['type'] === 'file') {
                $files[] = $field['name'];
            }
        }

        if (($definition['model']) === Page::class) {
            $rules['slug'] = [
                'nullable',
                'string',
                'alpha_dash',
                'max:255',
                Rule::unique('pages', 'slug')->ignore($item?->id),
            ];
        }

        if (($definition['model']) === MenuItem::class) {
            $rules['link_type'] = ['required', 'in:page,url'];
            $rules['page_id'] = ['required_if:link_type,page', 'nullable', 'integer', 'exists:pages,id'];
            $rules['custom_url'] = ['required_if:link_type,url', 'nullable', 'string', 'max:255'];
        }

        $galleryPhotoFields = [
            'new_photos',
            'new_photo_descriptions',
            'existing_photo_descriptions',
            'remove_photo_ids',
            'cover_photo',
        ];

        if (($definition['model']) === GalleryItem::class) {
            $rules['new_photos'] = ['nullable', 'array'];
            $rules['new_photos.*'] = ['nullable', 'image', 'max:4096'];
            $rules['new_photo_descriptions'] = ['nullable', 'array'];
            $rules['new_photo_descriptions.*'] = ['nullable', 'string', 'max:1000'];
            $rules['existing_photo_descriptions'] = ['nullable', 'array'];
            $rules['existing_photo_descriptions.*'] = ['nullable', 'string', 'max:1000'];
            $rules['remove_photo_ids'] = ['nullable', 'array'];
            $rules['remove_photo_ids.*'] = ['integer'];
            $rules['cover_photo'] = ['nullable', 'string', 'max:80'];
        }

        if (($definition['model']) === MenuItem::class && $item?->id) {
            $rules['parent_id'] = [
                'nullable',
                'integer',
                'exists:menu_items,id',
                Rule::notIn([$item->id]),
            ];
        }

        $data = $request->validate($rules, [
            'link_type.required' => 'Pilih tujuan menu.',
            'link_type.in' => 'Pilihan tujuan menu tidak valid.',
            'page_id.required_if' => 'Pilih halaman yang akan dibuka.',
            'page_id.exists' => 'Halaman yang dipilih tidak ditemukan.',
            'custom_url.required_if' => 'Isi URL atau anchor tujuan menu.',
            'slug.unique' => 'Judul halaman menghasilkan URL yang sudah digunakan. Gunakan judul lain.',
            'new_photos.*.image' => 'File foto galeri harus berupa gambar.',
            'new_photos.*.max' => 'Ukuran foto galeri maksimal 4 MB.',
        ]);

        foreach ($galleryPhotoFields as $field) {
            unset($data[$field]);
        }

        if (($definition['model']) === MenuItem::class) {
            if ($data['link_type'] === 'page') {
                $page = Page::query()->findOrFail($data['page_id']);
                $data['url'] = route('pages.show', $page->slug, false);
            } else {
                $data['url'] = $data['custom_url'];
            }

            unset($data['link_type'], $data['page_id'], $data['custom_url']);
            $data['parent_id'] = $data['parent_id'] ?? null;

            if ($data['parent_id'] && ! MenuItem::query()->whereKey($data['parent_id'])->whereNull('parent_id')->exists()) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Induk menu harus berupa menu utama.',
                ]);
            }

            if ($item?->exists && $data['parent_id'] && $item->children()->exists()) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Menu yang sudah memiliki child tidak dapat dijadikan child.',
                ]);
            }
        }

        foreach ($checkboxes as $checkbox) {
            $data[$checkbox] = $request->boolean($checkbox);
        }

        foreach ($files as $file) {
            unset($data[$file]);

            if ($request->hasFile($file)) {
                $data[$file] = Storage::url($request->file($file)->store('uploads/content', 'public'));
            }
        }

        return $data;
    }

    private function storeGallery(Request $request, array $data): GalleryItem
    {
        $uploads = $this->storeUploadedGalleryPhotos($request);

        if ($uploads === []) {
            throw ValidationException::withMessages([
                'new_photos.0' => 'Tambahkan minimal satu foto galeri.',
            ]);
        }

        $coverKey = $this->selectedNewPhotoKey($request, $uploads);
        $coverUpload = $uploads[$coverKey] ?? reset($uploads);

        return DB::transaction(function () use ($data, $uploads, $coverKey, $coverUpload) {
            $gallery = GalleryItem::query()->create([
                ...$data,
                'image_url' => $coverUpload['image_url'],
                'url' => '#',
            ]);

            foreach ($uploads as $key => $upload) {
                $gallery->photos()->create([
                    'image_url' => $upload['image_url'],
                    'description' => $upload['description'],
                    'sort_order' => $upload['sort_order'],
                    'is_cover' => $key === $coverKey,
                ]);
            }

            return $gallery;
        });
    }

    private function syncGalleryPhotos(GalleryItem $gallery, Request $request): void
    {
        $removeIds = collect((array) $request->input('remove_photo_ids', []))
            ->map(fn (mixed $id) => (int) $id)
            ->filter()
            ->values()
            ->all();

        $newFiles = collect($request->file('new_photos', []))->filter()->values();
        $remainingCount = $gallery->photos()
            ->when($removeIds !== [], fn ($query) => $query->whereNotIn('id', $removeIds))
            ->count() + $newFiles->count();

        if ($remainingCount < 1) {
            throw ValidationException::withMessages([
                'new_photos.0' => 'Foto galeri minimal satu. Jangan hapus semua foto tanpa menambahkan foto baru.',
            ]);
        }

        foreach ((array) $request->input('existing_photo_descriptions', []) as $photoId => $description) {
            GalleryPhoto::query()
                ->where('gallery_item_id', $gallery->id)
                ->whereKey((int) $photoId)
                ->update(['description' => $description]);
        }

        if ($removeIds !== []) {
            GalleryPhoto::query()
                ->where('gallery_item_id', $gallery->id)
                ->whereIn('id', $removeIds)
                ->delete();
        }

        $newPhotos = [];
        $nextOrder = (int) $gallery->photos()->max('sort_order');

        foreach ($this->storeUploadedGalleryPhotos($request, $nextOrder) as $key => $upload) {
            $newPhotos[$key] = $gallery->photos()->create([
                'image_url' => $upload['image_url'],
                'description' => $upload['description'],
                'sort_order' => $upload['sort_order'],
                'is_cover' => false,
            ]);
        }

        $coverPhoto = $this->resolveGalleryCover($gallery, $request, $newPhotos);

        if (! $coverPhoto) {
            $coverPhoto = $gallery->photos()->first();
        }

        if ($coverPhoto) {
            $gallery->photos()->update(['is_cover' => false]);
            $coverPhoto->forceFill(['is_cover' => true])->save();
            $gallery->forceFill([
                'image_url' => $coverPhoto->image_url,
                'url' => '#',
            ])->save();
        }
    }

    /**
     * @return array<string, array{image_url: string, description: ?string, sort_order: int}>
     */
    private function storeUploadedGalleryPhotos(Request $request, int $startOrder = 0): array
    {
        $descriptions = (array) $request->input('new_photo_descriptions', []);
        $uploads = [];
        $order = $startOrder;

        foreach ((array) $request->file('new_photos', []) as $index => $file) {
            if (! $file) {
                continue;
            }

            $order++;
            $uploads['new-'.$index] = [
                'image_url' => Storage::url($file->store('uploads/content/gallery', 'public')),
                'description' => $descriptions[$index] ?? null,
                'sort_order' => $order,
            ];
        }

        return $uploads;
    }

    /**
     * @param  array<string, array{image_url: string, description: ?string, sort_order: int}>  $uploads
     */
    private function selectedNewPhotoKey(Request $request, array $uploads): string
    {
        $coverToken = (string) $request->input('cover_photo');

        return array_key_exists($coverToken, $uploads)
            ? $coverToken
            : array_key_first($uploads);
    }

    /**
     * @param  array<string, GalleryPhoto>  $newPhotos
     */
    private function resolveGalleryCover(GalleryItem $gallery, Request $request, array $newPhotos): ?GalleryPhoto
    {
        $coverToken = (string) $request->input('cover_photo');

        if (str_starts_with($coverToken, 'existing-')) {
            $photoId = (int) Str::after($coverToken, 'existing-');

            return GalleryPhoto::query()
                ->where('gallery_item_id', $gallery->id)
                ->whereKey($photoId)
                ->first();
        }

        if (isset($newPhotos[$coverToken])) {
            return $newPhotos[$coverToken];
        }

        return $gallery->photos()->where('is_cover', true)->first();
    }

    private function findItem(array $definition, int $id): Model
    {
        return $definition['model']::query()->findOrFail($id);
    }
}

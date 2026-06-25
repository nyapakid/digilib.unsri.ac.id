<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Page;
use App\Support\Admin\ContentRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $definition['model']::create($data);

        return redirect()
            ->route('admin.content.index', $type)
            ->with('status', $definition['singular'].' berhasil ditambahkan.');
    }

    public function edit(string $type, int $id): View
    {
        $definition = ContentRegistry::get($type);
        $item = $this->findItem($definition, $id);

        return view('admin.content.form', compact('type', 'definition', 'item'));
    }

    public function update(Request $request, string $type, int $id): RedirectResponse
    {
        $definition = ContentRegistry::get($type);
        $item = $this->findItem($definition, $id);
        $oldPageUrl = ($definition['model']) === Page::class ? route('pages.show', $item->slug, false) : null;
        $data = $this->validatedData($request, $definition, $item);

        $item->update($data);

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
        ]);

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

    private function findItem(array $definition, int $id): Model
    {
        return $definition['model']::query()->findOrFail($id);
    }
}

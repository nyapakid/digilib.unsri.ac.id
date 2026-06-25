<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Admin\ContentRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContentController extends Controller
{
    public function index(string $type): View
    {
        $definition = ContentRegistry::get($type);
        $items = $definition['model']::query()
            ->orderBy('sort_order')
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
        $data = $this->validatedData($request, $definition, $item);

        $item->update($data);

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
        if (($definition['model']) === \App\Models\Page::class && ! $request->filled('slug') && $request->filled('title')) {
            $request->merge(['slug' => Str::slug($request->input('title'))]);
        }

        $rules = [];
        $checkboxes = [];
        $files = [];

        foreach ($definition['fields'] as $field) {
            $rules[$field['name']] = $field['rules'];

            if ($field['type'] === 'checkbox') {
                $checkboxes[] = $field['name'];
            }

            if ($field['type'] === 'file') {
                $files[] = $field['name'];
            }
        }

        if (($definition['model']) === \App\Models\Page::class) {
            $rules['slug'] = [
                'nullable',
                'string',
                'alpha_dash',
                'max:255',
                Rule::unique('pages', 'slug')->ignore($item?->id),
            ];
        }

        $data = $request->validate($rules);

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

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'site' => SiteSetting::current(),
            'fields' => $this->fields(),
            'moduleFields' => $this->moduleFields(),
            'footerMenuOptions' => MenuItem::query()
                ->with('parent')
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate(array_merge($this->rules(), [
            'footer_menu_ids' => ['nullable', 'array'],
            'footer_menu_ids.*' => ['integer', 'exists:menu_items,id'],
        ]));
        unset($data['footer_menu_ids']);
        $fileFields = collect($this->fields())
            ->filter(fn (array $field) => $field[2] === 'file')
            ->pluck(0);

        foreach ($fileFields as $field) {
            unset($data[$field]);
        }

        $fileFields->each(function (string $field) use ($request, &$data): void {
            if ($request->hasFile($field)) {
                $data[$field] = Storage::url($request->file($field)->store('uploads/site', 'public'));
            }
        });

        SiteSetting::current()->update($data);
        $this->syncFooterMenuChecklist($request);

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'Pengaturan situs berhasil diperbarui.');
    }

    private function fields(): array
    {
        return [
            ['site_name', 'Nama Situs', 'text'],
            ['brand_name', 'Nama Brand Header', 'text'],
            ['university_name', 'Nama Institusi', 'text'],
            ['motto', 'Motto Header Atas', 'text'],
            ['logo_path', 'Upload Logo Gambar', 'file'],
            ['page_hero_image_path', 'Gambar Background Page Hero', 'file'],
            ['address', 'Alamat', 'textarea'],
            ['email', 'Email', 'text'],
            ['phone', 'Telepon', 'text'],
            ['whatsapp_number', 'Nomor WhatsApp', 'text'],
            ['office_hours', 'Jam Layanan', 'text'],
            ['weekend_hours', 'Jam Akhir Pekan', 'text'],
            ['help_text', 'Teks Bantuan', 'text'],
            ['footer_description', 'Deskripsi Footer', 'textarea'],
            ['copyright_text', 'Copyright', 'text'],
            ['stats_title', 'Judul Statistik', 'text'],
            ['stats_subtitle', 'Subjudul Statistik', 'text'],
        ];
    }

    private function moduleFields(): array
    {
        return [
            'services' => [
                'label' => 'Layanan',
                'title' => 'services_module_title',
                'description' => 'services_module_description',
            ],
            'facilities' => [
                'label' => 'Fasilitas',
                'title' => 'facilities_module_title',
                'description' => 'facilities_module_description',
            ],
            'staff' => [
                'label' => 'Staff',
                'title' => 'staff_module_title',
                'description' => 'staff_module_description',
            ],
            'galleries' => [
                'label' => 'Galeri',
                'title' => 'galleries_module_title',
                'description' => 'galleries_module_description',
            ],
        ];
    }

    private function rules(): array
    {
        $fieldRules = collect($this->fields())
            ->mapWithKeys(fn (array $field) => [
                $field[0] => $field[2] === 'file'
                    ? ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096']
                    : ['nullable', 'string'],
            ])
            ->all();

        $moduleRules = collect($this->moduleFields())
            ->flatMap(fn (array $field) => [
                $field['title'] => ['nullable', 'string', 'max:255'],
                $field['description'] => ['nullable', 'string'],
            ])
            ->all();

        return array_merge($fieldRules, $moduleRules);
    }

    private function syncFooterMenuChecklist(Request $request): void
    {
        $selectedIds = collect((array) $request->input('footer_menu_ids', []))
            ->map(fn (mixed $id) => (int) $id)
            ->filter()
            ->values();

        MenuItem::query()->update(['show_in_footer' => false]);

        if ($selectedIds->isNotEmpty()) {
            MenuItem::query()
                ->whereIn('id', $selectedIds)
                ->update(['show_in_footer' => true]);
        }
    }
}

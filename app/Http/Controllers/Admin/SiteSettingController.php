<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
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

    private function rules(): array
    {
        return collect($this->fields())
            ->mapWithKeys(fn (array $field) => [
                $field[0] => $field[2] === 'file'
                    ? ['nullable', 'image', 'max:4096']
                    : ['nullable', 'string'],
            ])
            ->all();
    }
}

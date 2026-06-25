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

        unset($data['logo_path']);

        if ($request->hasFile('logo_path')) {
            $data['logo_path'] = Storage::url($request->file('logo_path')->store('uploads/site', 'public'));
        }

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
            ['logo_path', 'Upload Logo Gambar', 'file'],
            ['address', 'Alamat', 'textarea'],
            ['email', 'Email', 'text'],
            ['phone', 'Telepon', 'text'],
            ['whatsapp_number', 'Nomor WhatsApp', 'text'],
            ['office_hours', 'Jam Layanan', 'text'],
            ['weekend_hours', 'Jam Akhir Pekan', 'text'],
            ['help_text', 'Teks Bantuan', 'text'],
            ['footer_description', 'Deskripsi Footer', 'textarea'],
            ['copyright_text', 'Copyright', 'text'],
            ['hero_fact_1_title', 'Fakta Hero 1 - Judul', 'text'],
            ['hero_fact_1_text', 'Fakta Hero 1 - Teks', 'text'],
            ['hero_fact_1_icon', 'Fakta Hero 1 - Ikon', 'text'],
            ['hero_fact_2_title', 'Fakta Hero 2 - Judul', 'text'],
            ['hero_fact_2_text', 'Fakta Hero 2 - Teks', 'text'],
            ['hero_fact_2_icon', 'Fakta Hero 2 - Ikon', 'text'],
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

<?php

namespace App\Support\Admin;

use App\Models\AgendaItem;
use App\Models\Announcement;
use App\Models\Facility;
use App\Models\GalleryItem;
use App\Models\HeroSlide;
use App\Models\MenuItem;
use App\Models\NewsPost;
use App\Models\Page;
use App\Models\Partner;
use App\Models\ResourceLink;
use App\Models\Service;
use App\Models\StaffMember;
use App\Models\Statistic;
use InvalidArgumentException;

class ContentRegistry
{
    public static function all(): array
    {
        return [
            'menus' => [
                'label' => 'Menu',
                'singular' => 'Menu',
                'model' => MenuItem::class,
                'title' => 'label',
                'fields' => [
                    self::field('label', 'Label', 'text', 'required|string|max:255'),
                    self::field('url', 'Tujuan Menu', 'menu_link', 'nullable|string|max:255'),
                    self::field('parent_id', 'Induk Menu', 'menu_parent', 'nullable|integer|exists:menu_items,id'),
                    self::field('sort_order', 'Urutan', 'number', 'required|integer|min:0'),
                    self::field('opens_new_tab', 'Buka di tab baru', 'checkbox', 'boolean'),
                    self::field('is_active', 'Aktif', 'checkbox', 'boolean'),
                ],
            ],
            'resources' => [
                'label' => 'e-Resources',
                'singular' => 'e-Resource',
                'model' => ResourceLink::class,
                'title' => 'title',
                'fields' => [
                    self::field('title', 'Judul', 'text', 'required|string|max:255'),
                    self::field('description', 'Deskripsi', 'textarea', 'nullable|string'),
                    self::field('body', 'Isi Detail', 'textarea', 'nullable|string'),
                    self::field('url', 'URL', 'text', 'required|string|max:255'),
                    self::field('icon', 'Ikon', 'select', 'required|string|max:50', self::icons()),
                    self::field('background_color', 'Warna Background', 'color', 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/'),
                    self::field('sort_order', 'Urutan', 'number', 'required|integer|min:0'),
                    self::field('is_active', 'Aktif', 'checkbox', 'boolean'),
                ],
            ],
            'services' => [
                'label' => 'Layanan',
                'singular' => 'Layanan',
                'model' => Service::class,
                'title' => 'title',
                'fields' => [
                    self::field('title', 'Judul', 'text', 'required|string|max:255'),
                    self::field('description', 'Deskripsi', 'textarea', 'nullable|string'),
                    self::field('body', 'Isi Detail', 'summernote', 'nullable|string'),
                    self::field('image_url', 'Upload Gambar', 'file', 'nullable|image|max:4096'),
                    self::field('url', 'URL Tombol', 'text', 'required|string|max:255'),
                    self::field('sort_order', 'Urutan', 'number', 'required|integer|min:0'),
                    self::field('is_active', 'Aktif', 'checkbox', 'boolean'),
                ],
            ],
            'hero-slides' => [
                'label' => 'Slide',
                'singular' => 'Slide',
                'model' => HeroSlide::class,
                'title' => 'title',
                'fields' => [
                    self::field('title', 'Judul', 'text', 'required|string|max:255'),
                    self::field('highlight', 'Highlight Judul', 'text', 'nullable|string|max:255'),
                    self::field('description', 'Deskripsi', 'textarea', 'nullable|string'),
                    self::field('button_text', 'Teks Tombol', 'text', 'nullable|string|max:255'),
                    self::field('button_url', 'URL Tombol', 'text', 'nullable|string|max:255'),
                    self::field('image_url', 'Upload Gambar Slide', 'file', 'nullable|image|max:4096'),
                    self::field('fact_1_title', 'Tag 1 - Judul', 'text', 'nullable|string|max:255'),
                    self::field('fact_1_text', 'Tag 1 - Teks', 'text', 'nullable|string|max:255'),
                    self::field('fact_1_icon', 'Tag 1 - Ikon', 'select', 'nullable|string|max:50', self::icons()),
                    self::field('fact_2_title', 'Tag 2 - Judul', 'text', 'nullable|string|max:255'),
                    self::field('fact_2_text', 'Tag 2 - Teks', 'text', 'nullable|string|max:255'),
                    self::field('fact_2_icon', 'Tag 2 - Ikon', 'select', 'nullable|string|max:50', self::icons()),
                    self::field('sort_order', 'Urutan', 'number', 'required|integer|min:0'),
                    self::field('is_active', 'Aktif', 'checkbox', 'boolean'),
                ],
            ],
            'facilities' => [
                'label' => 'Fasilitas',
                'singular' => 'Fasilitas',
                'model' => Facility::class,
                'title' => 'title',
                'fields' => [
                    self::field('title', 'Judul', 'text', 'required|string|max:255'),
                    self::field('description', 'Deskripsi', 'textarea', 'nullable|string'),
                    self::field('body', 'Isi Detail', 'summernote', 'nullable|string'),
                    self::field('image_url', 'Upload Gambar', 'file', 'nullable|image|max:4096'),
                    self::field('icon', 'Ikon', 'select', 'required|string|max:50', self::icons()),
                    self::field('sort_order', 'Urutan', 'number', 'required|integer|min:0'),
                    self::field('is_active', 'Aktif', 'checkbox', 'boolean'),
                ],
            ],
            'announcements' => [
                'label' => 'Pengumuman',
                'singular' => 'Pengumuman',
                'model' => Announcement::class,
                'title' => 'title',
                'fields' => [
                    self::field('title', 'Judul', 'text', 'required|string|max:255'),
                    self::field('excerpt', 'Ringkasan', 'textarea', 'nullable|string'),
                    self::field('body', 'Isi Pengumuman', 'summernote', 'nullable|string'),
                    self::field('published_at', 'Tanggal Terbit', 'date', 'nullable|date'),
                    self::field('sort_order', 'Urutan', 'number', 'required|integer|min:0'),
                    self::field('is_active', 'Aktif', 'checkbox', 'boolean'),
                ],
            ],
            'agenda' => [
                'label' => 'Agenda',
                'singular' => 'Agenda',
                'model' => AgendaItem::class,
                'title' => 'title',
                'fields' => [
                    self::field('title', 'Judul', 'text', 'required|string|max:255'),
                    self::field('description', 'Detail Waktu/Lokasi', 'textarea', 'nullable|string'),
                    self::field('body', 'Isi Agenda', 'summernote', 'nullable|string'),
                    self::field('event_date', 'Tanggal Agenda', 'date', 'nullable|date'),
                    self::field('sort_order', 'Urutan', 'number', 'required|integer|min:0'),
                    self::field('is_active', 'Aktif', 'checkbox', 'boolean'),
                ],
            ],
            'news' => [
                'label' => 'Berita',
                'singular' => 'Berita',
                'model' => NewsPost::class,
                'title' => 'title',
                'fields' => [
                    self::field('title', 'Judul', 'text', 'required|string|max:255'),
                    self::field('excerpt', 'Ringkasan', 'textarea', 'nullable|string|max:255'),
                    self::field('body', 'Isi Berita', 'summernote', 'nullable|string'),
                    self::field('image_url', 'Upload Gambar', 'file', 'nullable|image|max:4096'),
                    self::field('published_at', 'Tanggal Terbit', 'date', 'nullable|date'),
                    self::field('sort_order', 'Urutan', 'number', 'required|integer|min:0'),
                    self::field('is_active', 'Aktif', 'checkbox', 'boolean'),
                ],
            ],
            'galleries' => [
                'label' => 'Galeri',
                'singular' => 'Galeri',
                'model' => GalleryItem::class,
                'title' => 'title',
                'fields' => [
                    self::field('title', 'Judul / Alt Text', 'text', 'required|string|max:255'),
                    self::field('description', 'Deskripsi', 'textarea', 'nullable|string'),
                    self::field('image_url', 'Upload Gambar', 'file', 'nullable|image|max:4096'),
                    self::field('url', 'URL Tujuan', 'text', 'required|string|max:255'),
                    self::field('sort_order', 'Urutan', 'number', 'required|integer|min:0'),
                    self::field('is_active', 'Aktif', 'checkbox', 'boolean'),
                ],
            ],
            'staff' => [
                'label' => 'Staff',
                'singular' => 'Staff',
                'model' => StaffMember::class,
                'title' => 'name',
                'fields' => [
                    self::field('name', 'Nama Staff', 'text', 'required|string|max:255'),
                    self::field('position', 'Jabatan', 'text', 'nullable|string|max:255'),
                    self::field('photo_url', 'Upload Foto', 'file', 'nullable|image|max:4096'),
                    self::field('bio', 'Profil Singkat', 'textarea', 'nullable|string'),
                    self::field('sort_order', 'Urutan', 'number', 'required|integer|min:0'),
                    self::field('is_active', 'Aktif', 'checkbox', 'boolean'),
                ],
            ],
            'statistics' => [
                'label' => 'Statistik',
                'singular' => 'Statistik',
                'model' => Statistic::class,
                'title' => 'label',
                'fields' => [
                    self::field('label', 'Label', 'text', 'required|string|max:255'),
                    self::field('value', 'Nilai', 'text', 'required|string|max:255'),
                    self::field('icon', 'Ikon', 'select', 'required|string|max:50', self::icons()),
                    self::field('sort_order', 'Urutan', 'number', 'required|integer|min:0'),
                    self::field('is_active', 'Aktif', 'checkbox', 'boolean'),
                ],
            ],
            'partners' => [
                'label' => 'Mitra & Database',
                'singular' => 'Mitra',
                'model' => Partner::class,
                'title' => 'name',
                'fields' => [
                    self::field('name', 'Nama', 'text', 'required|string|max:255'),
                    self::field('url', 'URL', 'text', 'required|string|max:255'),
                    self::field('logo_url', 'Upload Logo Mitra', 'file', 'nullable|image|max:4096'),
                    self::field('sort_order', 'Urutan', 'number', 'required|integer|min:0'),
                    self::field('is_active', 'Aktif', 'checkbox', 'boolean'),
                ],
            ],
            'pages' => [
                'label' => 'Halaman',
                'singular' => 'Halaman',
                'model' => Page::class,
                'title' => 'title',
                'fields' => [
                    self::field('title', 'Judul', 'text', 'required|string|max:255'),
                    self::field('excerpt', 'Ringkasan', 'textarea', 'nullable|string|max:255'),
                    self::field('body', 'Isi Halaman', 'summernote', 'nullable|string'),
                    self::field('sort_order', 'Urutan', 'number', 'required|integer|min:0'),
                    self::field('is_active', 'Aktif', 'checkbox', 'boolean'),
                ],
            ],
        ];
    }

    public static function get(string $type): array
    {
        $definition = self::all()[$type] ?? null;

        if (! $definition) {
            throw new InvalidArgumentException("Unknown content type [{$type}].");
        }

        return $definition;
    }

    public static function types(): array
    {
        return array_keys(self::all());
    }

    private static function field(string $name, string $label, string $type, string $rules, array $options = []): array
    {
        return compact('name', 'label', 'type', 'rules', 'options');
    }

    private static function icons(): array
    {
        return [
            'award' => 'Medali',
            'book' => 'Buku',
            'chat' => 'Chat',
            'clock' => 'Jam',
            'computer' => 'Komputer',
            'document' => 'Dokumen',
            'download' => 'Unduhan',
            'file' => 'File',
            'grid' => 'Grid',
            'lock' => 'Kunci',
            'mail' => 'Email',
            'map-pin' => 'Lokasi',
            'search' => 'Pencarian',
            'shield' => 'Perisai',
            'user' => 'Pengguna',
            'wifi' => 'Wi-Fi',
        ];
    }
}

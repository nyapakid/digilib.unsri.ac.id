<?php

namespace Database\Seeders;

use App\Models\AgendaItem;
use App\Models\Announcement;
use App\Models\Banner;
use App\Models\Facility;
use App\Models\GalleryItem;
use App\Models\HeroSlide;
use App\Models\MenuItem;
use App\Models\NewsPost;
use App\Models\Page;
use App\Models\Partner;
use App\Models\ResourceLink;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\StaffMember;
use App\Models\Statistic;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@digilib.unsri.ac.id'],
            [
                'name' => 'Administrator Digilib',
                'password' => 'password',
            ]
        );

        SiteSetting::query()->updateOrCreate(['id' => 1], [
            'site_name' => 'Digilib Universitas Sriwijaya',
            'brand_name' => 'SIneRGiS',
            'university_name' => 'Universitas Sriwijaya',
            'logo_text' => 'US',
            'logo_path' => '/images/unsri-logo.svg',
            'address' => 'Jl. Palembang - Prabumulih Km. 32, Indralaya, Ogan Ilir 30662, Sumatera Selatan',
            'email' => 'digilib@unsri.ac.id',
            'phone' => '(0711) 580069',
            'whatsapp_number' => '628117874088',
            'office_hours' => 'Senin - Jumat 08.00 - 16.00 WIB',
            'weekend_hours' => 'Sabtu - Minggu Tutup',
            'help_text' => 'Butuh Bantuan? hubungi kami via WhatsApp',
            'footer_description' => 'Pusat informasi digital yang mendukung kegiatan akademik, penelitian, pengabdian masyarakat, dan publikasi ilmiah Universitas Sriwijaya.',
            'copyright_text' => '(c) 2026 Digilib Universitas Sriwijaya. All rights reserved.',
            'hero_title' => 'Digilib',
            'hero_highlight' => 'Universitas Sriwijaya',
            'hero_description' => 'Pusat informasi digital untuk mendukung pembelajaran, penelitian, publikasi ilmiah, dan literasi sivitas akademika Universitas Sriwijaya.',
            'hero_button_text' => 'Telusuri Koleksi',
            'hero_button_url' => '#resources',
            'hero_image_url' => 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1800&q=85',
            'hero_fact_1_title' => 'Terakreditasi A',
            'hero_fact_1_text' => 'Perpustakaan Nasional RI',
            'hero_fact_1_icon' => 'award',
            'hero_fact_2_title' => 'Jam Layanan',
            'hero_fact_2_text' => 'Senin - Jumat 08.00 - 16.00 WIB',
            'hero_fact_2_icon' => 'clock',
            'stats_title' => 'Statistik Pengunjung',
            'stats_subtitle' => '(Tahun 2026)',
        ]);

        $this->seedOrdered(HeroSlide::class, 'title', [
            [
                'title' => 'Digilib',
                'highlight' => 'Universitas Sriwijaya',
                'description' => 'Pusat informasi digital untuk mendukung pembelajaran, penelitian, publikasi ilmiah, dan literasi sivitas akademika Universitas Sriwijaya.',
                'button_text' => 'Telusuri Koleksi',
                'button_url' => '#resources',
                'image_url' => 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1800&q=85',
                'fact_1_title' => 'Terakreditasi A',
                'fact_1_text' => 'Perpustakaan Nasional RI',
                'fact_1_icon' => 'award',
                'fact_2_title' => 'Jam Layanan',
                'fact_2_text' => 'Senin - Jumat 08.00 - 16.00 WIB',
                'fact_2_icon' => 'clock',
                'sort_order' => 1,
            ],
            [
                'title' => 'Akses Riset',
                'highlight' => 'Lebih Terpadu',
                'description' => 'Temukan repository, katalog online, database langganan, dan sumber elektronik Unsri dalam satu gerbang layanan.',
                'button_text' => 'Buka e-Resources',
                'button_url' => '#resources',
                'image_url' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=1800&q=85',
                'fact_1_title' => 'Repository',
                'fact_1_text' => 'Karya ilmiah Unsri',
                'fact_1_icon' => 'document',
                'fact_2_title' => 'Database',
                'fact_2_text' => 'Sumber elektronik akademik',
                'fact_2_icon' => 'shield',
                'sort_order' => 2,
            ],
            [
                'title' => 'Layanan Literasi',
                'highlight' => 'Untuk Sivitas Akademika',
                'description' => 'Dapatkan pendampingan penelusuran referensi, publikasi ilmiah, dan pemanfaatan sumber informasi digital.',
                'button_text' => 'Lihat Layanan',
                'button_url' => '#layanan',
                'image_url' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1800&q=85',
                'fact_1_title' => 'Referensi',
                'fact_1_text' => 'Pendampingan pencarian sumber',
                'fact_1_icon' => 'search',
                'fact_2_title' => 'Literasi',
                'fact_2_text' => 'Pelatihan dan konsultasi',
                'fact_2_icon' => 'book',
                'sort_order' => 3,
            ],
        ]);

        $this->seedOrdered(MenuItem::class, 'label', [
            ['label' => 'Beranda', 'url' => '/', 'sort_order' => 1],
            ['label' => 'Profil', 'url' => '#profil', 'sort_order' => 2],
            ['label' => 'Fasilitas', 'url' => '#fasilitas', 'sort_order' => 3],
            ['label' => 'Layanan', 'url' => '#layanan', 'sort_order' => 4],
            ['label' => 'Peraturan', 'url' => '/halaman/peraturan', 'sort_order' => 5],
            ['label' => 'Staff', 'url' => '/staff', 'sort_order' => 6],
            ['label' => 'Lokasi', 'url' => '/halaman/lokasi', 'sort_order' => 7],
            ['label' => 'Berita', 'url' => '#berita', 'sort_order' => 8],
            ['label' => 'Galeri', 'url' => '#galeri', 'sort_order' => 9],
            ['label' => 'Kontak', 'url' => '#kontak', 'sort_order' => 10],
        ]);

        $this->seedOrdered(ResourceLink::class, 'title', [
            ['title' => 'myUNSRI Library', 'description' => 'Akses akun perpustakaan', 'body' => 'Gunakan layanan myUNSRI Library untuk mengakses akun perpustakaan, memantau peminjaman, dan melihat status layanan yang tersedia bagi sivitas akademika.', 'url' => '#', 'icon' => 'search', 'background_color' => '#ffffff', 'sort_order' => 1],
            ['title' => 'Repository', 'description' => 'Karya ilmiah Unsri', 'body' => 'Repository menyimpan karya ilmiah Universitas Sriwijaya seperti skripsi, tesis, disertasi, artikel, dan publikasi akademik lainnya.', 'url' => '#', 'icon' => 'book', 'background_color' => '#f7fbff', 'sort_order' => 2],
            ['title' => 'Online Catalog', 'description' => 'Penelusuran koleksi', 'body' => 'Online Catalog membantu pengguna menelusuri koleksi perpustakaan, informasi bibliografi, dan status ketersediaan koleksi.', 'url' => '#', 'icon' => 'document', 'background_color' => '#fffaf0', 'sort_order' => 3],
            ['title' => 'SIneRGIS', 'description' => 'System for Integrated e-Resources & Library Gateway of Sriwijaya', 'body' => 'SIneRGIS menjadi gerbang terpadu untuk mengakses sumber informasi digital dan layanan perpustakaan Universitas Sriwijaya.', 'url' => '#', 'icon' => 'grid', 'background_color' => '#f7fff9', 'sort_order' => 4],
            ['title' => 'e-Proceeding', 'description' => 'Prosiding konferensi', 'body' => 'e-Proceeding menyediakan akses ke prosiding kegiatan ilmiah, seminar, dan konferensi yang berkaitan dengan Universitas Sriwijaya.', 'url' => '#', 'icon' => 'file', 'background_color' => '#fff7fb', 'sort_order' => 5],
            ['title' => 'Database', 'description' => 'Basis data langganan', 'body' => 'Database langganan menyediakan akses ke sumber referensi elektronik yang mendukung pembelajaran, penelitian, dan publikasi ilmiah.', 'url' => '#', 'icon' => 'shield', 'background_color' => '#f8f7ff', 'sort_order' => 6],
        ]);

        $this->seedOrdered(Banner::class, 'title', [
            [
                'title' => 'Akses e-Resources Terpadu',
                'image_url' => 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=1600&q=85',
                'image_size' => 'full',
                'sort_order' => 1,
            ],
            [
                'title' => 'Layanan Digital Perpustakaan',
                'image_url' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=1100&q=85',
                'image_size' => 'original',
                'sort_order' => 2,
            ],
        ]);

        $this->seedOrdered(Service::class, 'title', [
            [
                'title' => 'Layanan Sirkulasi',
                'description' => 'Peminjaman, perpanjangan, dan pengembalian koleksi perpustakaan.',
                'body' => 'Layanan sirkulasi mencakup proses peminjaman, perpanjangan masa pinjam, pengembalian koleksi, serta informasi status pinjaman pengguna perpustakaan.',
                'image_url' => 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=700&q=80',
                'url' => '#',
                'sort_order' => 1,
            ],
            [
                'title' => 'Layanan Referensi',
                'description' => 'Bantuan penelusuran informasi akademik dan sumber rujukan ilmiah.',
                'body' => 'Layanan referensi membantu mahasiswa, dosen, dan peneliti menemukan sumber rujukan yang relevan melalui katalog, repository, database elektronik, dan sumber akademik lainnya.',
                'image_url' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=700&q=80',
                'url' => '#',
                'sort_order' => 2,
            ],
            [
                'title' => 'Layanan Digital',
                'description' => 'Akses sumber daya digital, repository, dan literasi informasi.',
                'body' => 'Layanan digital menyediakan pendampingan akses sumber daya elektronik, repository institusi, dan program literasi informasi untuk menunjang aktivitas akademik.',
                'image_url' => 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=700&q=80',
                'url' => '#',
                'sort_order' => 3,
            ],
        ]);

        $this->seedOrdered(Facility::class, 'title', [
            ['title' => 'Ruang Baca Nyaman', 'description' => 'Area baca ber-AC dengan suasana kondusif untuk belajar.', 'body' => 'Ruang baca disediakan untuk mendukung aktivitas belajar mandiri, diskusi akademik ringan, dan penelusuran koleksi dengan suasana yang kondusif.', 'image_url' => 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=400&q=80', 'icon' => 'book', 'sort_order' => 1],
            ['title' => 'Hotspot Area', 'description' => 'Wi-Fi cepat untuk mendukung akses internet mahasiswa.', 'body' => 'Hotspot area membantu pengguna mengakses katalog, repository, jurnal elektronik, dan sumber pembelajaran digital selama berada di area perpustakaan.', 'image_url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=400&q=80', 'icon' => 'wifi', 'sort_order' => 2],
            ['title' => 'Komputer & Multimedia', 'description' => 'Unit komputer dan perangkat multimedia untuk akses digital.', 'body' => 'Fasilitas komputer dan multimedia dapat digunakan untuk penelusuran informasi, akses sumber digital, serta kebutuhan akademik terkait literasi informasi.', 'image_url' => 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=400&q=80', 'icon' => 'computer', 'sort_order' => 3],
            ['title' => 'OPAC', 'description' => 'Aplikasi penelusuran koleksi dan informasi ketersediaan buku.', 'body' => 'OPAC memudahkan pengguna mencari koleksi berdasarkan judul, penulis, subjek, atau kata kunci dan memeriksa ketersediaan koleksi.', 'image_url' => 'https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=400&q=80', 'icon' => 'document', 'sort_order' => 4],
            ['title' => 'Loker Penyimpanan', 'description' => 'Tempat penyimpanan barang pengunjung dengan akses aman.', 'body' => 'Loker penyimpanan tersedia untuk membantu pengunjung menyimpan barang bawaan selama menggunakan layanan perpustakaan.', 'image_url' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=400&q=80', 'icon' => 'lock', 'sort_order' => 5],
        ]);

        $this->seedOrdered(Announcement::class, 'title', [
            ['title' => 'Perubahan Jam Layanan Perpustakaan', 'excerpt' => 'Sehubungan dengan kegiatan akademik, jam layanan perpustakaan menyesuaikan jadwal semester.', 'body' => 'Pengguna diharapkan memperhatikan perubahan jam layanan selama periode kegiatan akademik. Informasi lebih lanjut dapat diperoleh melalui kanal resmi Digilib Universitas Sriwijaya.', 'published_at' => '2026-06-23', 'sort_order' => 1],
            ['title' => 'Pelatihan Literasi Informasi', 'excerpt' => 'Ayo ikuti pelatihan literasi informasi untuk mahasiswa baru Universitas Sriwijaya.', 'body' => 'Pelatihan literasi informasi dirancang untuk membantu mahasiswa mengenal strategi pencarian referensi, penggunaan katalog, repository, dan database akademik.', 'published_at' => '2026-06-18', 'sort_order' => 2],
            ['title' => 'Maintenance Sistem Repository', 'excerpt' => 'Sistem repository akan dilakukan pemeliharaan pada pukul 20.00 - 23.00 WIB.', 'body' => 'Selama proses maintenance, akses repository dapat mengalami gangguan sementara. Layanan akan kembali normal setelah proses pemeliharaan selesai.', 'published_at' => '2026-06-10', 'sort_order' => 3],
        ]);

        $this->seedOrdered(AgendaItem::class, 'title', [
            ['title' => 'Workshop Penulisan Karya Ilmiah', 'description' => '09.00 - 12.00 WIB | Ruang Seminar Lantai 3', 'body' => 'Workshop ini membahas struktur karya ilmiah, pencarian referensi, sitasi, dan strategi menulis akademik yang efektif.', 'event_date' => '2026-06-25', 'sort_order' => 1],
            ['title' => 'Bedah Buku', 'description' => '13.00 - 15.00 WIB | Ruang Diskusi Perpustakaan', 'body' => 'Kegiatan bedah buku menghadirkan diskusi terbuka untuk memperkaya pemahaman literasi dan wawasan akademik peserta.', 'event_date' => '2026-07-02', 'sort_order' => 2],
            ['title' => 'Pelatihan Mendeley', 'description' => '09.00 - 12.00 WIB | Lab Komputer Perpustakaan', 'body' => 'Pelatihan Mendeley membantu peserta mengelola referensi, membuat kutipan, dan menyusun daftar pustaka secara lebih rapi.', 'event_date' => '2026-07-09', 'sort_order' => 3],
        ]);

        $this->seedOrdered(NewsPost::class, 'title', [
            [
                'title' => 'Digilib UNSRI Perkuat Layanan Akademik Digital',
                'excerpt' => 'Digilib UNSRI memperkuat layanan akademik digital untuk sivitas akademika.',
                'body' => 'Digilib Universitas Sriwijaya terus memperluas layanan digital agar mahasiswa, dosen, dan peneliti dapat mengakses sumber informasi akademik dengan lebih mudah.',
                'image_url' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=700&q=80',
                'published_at' => '2026-06-20',
                'sort_order' => 1,
            ],
            [
                'title' => 'Dukungan Literasi untuk Program Kampus Merdeka',
                'excerpt' => 'Program literasi informasi mendukung pembelajaran mandiri dan riset mahasiswa.',
                'body' => 'Kegiatan literasi informasi disiapkan untuk membantu mahasiswa menemukan, mengevaluasi, dan menggunakan referensi akademik secara bertanggung jawab.',
                'image_url' => 'https://images.unsplash.com/photo-1523580494863-6f3031224c94?auto=format&fit=crop&w=700&q=80',
                'published_at' => '2026-06-15',
                'sort_order' => 2,
            ],
            [
                'title' => 'Kelas Riset Bantu Mahasiswa Menemukan Referensi',
                'excerpt' => 'Kelas riset membantu mahasiswa mengoptimalkan pencarian sumber ilmiah.',
                'body' => 'Kelas riset menghadirkan pendampingan praktis untuk penelusuran database, repository, dan katalog perpustakaan.',
                'image_url' => 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&w=700&q=80',
                'published_at' => '2026-06-08',
                'sort_order' => 3,
            ],
        ]);

        $this->seedOrdered(GalleryItem::class, 'title', [
            ['title' => 'Rak buku perpustakaan', 'description' => 'Koleksi cetak yang tersedia di ruang perpustakaan.', 'image_url' => 'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?auto=format&fit=crop&w=700&q=80', 'url' => '#', 'sort_order' => 1],
            ['title' => 'Interior perpustakaan', 'description' => 'Suasana interior ruang perpustakaan untuk aktivitas akademik.', 'image_url' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=700&q=80', 'url' => '#', 'sort_order' => 2],
            ['title' => 'Ruang belajar', 'description' => 'Area belajar yang mendukung kegiatan membaca dan diskusi.', 'image_url' => 'https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=700&q=80', 'url' => '#', 'sort_order' => 3],
            ['title' => 'Mahasiswa di komputer', 'description' => 'Fasilitas komputer untuk akses informasi digital.', 'image_url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=700&q=80', 'url' => '#', 'sort_order' => 4],
        ]);

        $this->seedOrdered(Statistic::class, 'label', [
            ['label' => 'Total Pengunjung', 'value' => '125.680', 'icon' => 'user', 'sort_order' => 1],
            ['label' => 'Koleksi Digital', 'value' => '48.230', 'icon' => 'book', 'sort_order' => 2],
            ['label' => 'Unduhan', 'value' => '96.412', 'icon' => 'download', 'sort_order' => 3],
            ['label' => 'Anggota Aktif', 'value' => '12.875', 'icon' => 'file', 'sort_order' => 4],
        ]);

        $this->seedOrdered(Partner::class, 'name', [
            ['name' => 'Perpusnas', 'url' => 'https://www.perpusnas.go.id', 'logo_url' => '/images/partners/perpusnas.svg', 'sort_order' => 1],
            ['name' => 'Garuda', 'url' => 'https://garuda.kemdikbud.go.id', 'logo_url' => '/images/partners/garuda.svg', 'sort_order' => 2],
            ['name' => 'EBSCO', 'url' => 'https://www.ebsco.com', 'logo_url' => '/images/partners/ebsco.svg', 'sort_order' => 3],
            ['name' => 'ProQuest', 'url' => 'https://www.proquest.com', 'logo_url' => '/images/partners/proquest.svg', 'sort_order' => 4],
            ['name' => 'ScienceDirect', 'url' => 'https://www.sciencedirect.com', 'logo_url' => '/images/partners/sciencedirect.svg', 'sort_order' => 5],
            ['name' => 'SINTA', 'url' => 'https://sinta.kemdikbud.go.id', 'logo_url' => '/images/partners/sinta.svg', 'sort_order' => 6],
        ]);

        $this->seedOrdered(StaffMember::class, 'name', [
            [
                'name' => 'Dr. Rina Marlina',
                'category' => 'pustakawan',
                'position' => 'Kepala Layanan Digilib',
                'photo_url' => '/images/staff/staff-1.svg',
                'bio' => 'Mengkoordinasikan pengembangan layanan digital, literasi informasi, dan akses sumber elektronik untuk sivitas akademika Universitas Sriwijaya.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Andi Saputra, S.IP.',
                'category' => 'pustakawan',
                'position' => 'Pustakawan Referensi',
                'photo_url' => '/images/staff/staff-2.svg',
                'bio' => 'Mendampingi penelusuran referensi akademik, penggunaan katalog, repository, dan database ilmiah.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Maya Lestari, M.Kom.',
                'category' => 'administrasi',
                'position' => 'Pengelola Sistem Digital',
                'photo_url' => '/images/staff/staff-3.svg',
                'bio' => 'Mengelola sistem informasi perpustakaan, unggah konten digital, dan integrasi akses layanan daring.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Budi Hartono',
                'category' => 'administrasi',
                'position' => 'Layanan Sirkulasi',
                'photo_url' => '/images/staff/staff-4.svg',
                'bio' => 'Melayani administrasi peminjaman, pengembalian, dan informasi ketersediaan koleksi perpustakaan.',
                'sort_order' => 4,
            ],
        ]);

        $this->seedOrdered(Page::class, 'slug', [
            [
                'title' => 'Peraturan Perpustakaan',
                'slug' => 'peraturan',
                'excerpt' => 'Tata tertib dan ketentuan penggunaan layanan Digilib Universitas Sriwijaya.',
                'body' => "Pengunjung wajib menjaga ketertiban ruang layanan.\nKoleksi yang dipinjam harus dikembalikan sesuai jadwal.\nAkses sumber daya digital digunakan untuk kepentingan akademik.",
                'sort_order' => 1,
            ],
            [
                'title' => 'Staff Digilib',
                'slug' => 'staff',
                'excerpt' => 'Informasi tim pengelola layanan Digilib Universitas Sriwijaya.',
                'body' => "Halaman ini dapat diisi dengan struktur organisasi, nama staff, jabatan, dan kontak layanan.",
                'sort_order' => 2,
            ],
            [
                'title' => 'Lokasi Digilib',
                'slug' => 'lokasi',
                'excerpt' => 'Alamat dan informasi lokasi layanan Digilib Universitas Sriwijaya.',
                'body' => "Digilib Universitas Sriwijaya berlokasi di Jl. Palembang - Prabumulih Km. 32, Indralaya, Ogan Ilir 30662, Sumatera Selatan.",
                'sort_order' => 3,
            ],
        ]);
    }

    /**
     * @param  class-string<Model>  $model
     * @param  array<int, array<string, mixed>>  $records
     */
    private function seedOrdered(string $model, string $identityColumn, array $records): void
    {
        foreach ($records as $record) {
            $record['is_active'] ??= true;

            $model::query()->updateOrCreate(
                [$identityColumn => $record[$identityColumn]],
                $record
            );
        }
    }
}

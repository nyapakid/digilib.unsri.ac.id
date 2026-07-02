<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('services_module_title')->nullable()->after('stats_subtitle');
            $table->text('services_module_description')->nullable()->after('services_module_title');
            $table->string('facilities_module_title')->nullable()->after('services_module_description');
            $table->text('facilities_module_description')->nullable()->after('facilities_module_title');
            $table->string('staff_module_title')->nullable()->after('facilities_module_description');
            $table->text('staff_module_description')->nullable()->after('staff_module_title');
            $table->string('galleries_module_title')->nullable()->after('staff_module_description');
            $table->text('galleries_module_description')->nullable()->after('galleries_module_title');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->longText('embed_html')->nullable()->after('body');
        });

        Schema::create('footer_menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('url');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('opens_new_tab')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('site_settings')->update([
            'services_module_title' => 'Layanan',
            'services_module_description' => 'Daftar informasi layanan yang tersedia dan dapat dikelola melalui backend.',
            'facilities_module_title' => 'Fasilitas',
            'facilities_module_description' => 'Daftar informasi fasilitas yang tersedia dan dapat dikelola melalui backend.',
            'staff_module_title' => 'Staff',
            'staff_module_description' => 'Daftar informasi staff yang tersedia dan dapat dikelola melalui backend.',
            'galleries_module_title' => 'Galeri',
            'galleries_module_description' => 'Daftar informasi galeri yang tersedia dan dapat dikelola melalui backend.',
        ]);

        $now = now();

        DB::table('menu_items')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get()
            ->each(function (object $menu) use ($now) {
                DB::table('footer_menu_items')->insert([
                    'label' => $menu->label,
                    'url' => $menu->url,
                    'sort_order' => $menu->sort_order,
                    'opens_new_tab' => (bool) $menu->opens_new_tab,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_menu_items');

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('embed_html');
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'services_module_title',
                'services_module_description',
                'facilities_module_title',
                'facilities_module_description',
                'staff_module_title',
                'staff_module_description',
                'galleries_module_title',
                'galleries_module_description',
            ]);
        });
    }
};

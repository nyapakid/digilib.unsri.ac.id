<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->boolean('show_in_footer')->default(false)->after('opens_new_tab');
        });

        if (Schema::hasTable('footer_menu_items')) {
            $footerMenus = DB::table('footer_menu_items')
                ->where('is_active', true)
                ->get(['label', 'url']);

            foreach ($footerMenus as $footerMenu) {
                DB::table('menu_items')
                    ->where('label', $footerMenu->label)
                    ->where('url', $footerMenu->url)
                    ->update(['show_in_footer' => true]);
            }
        }

        if (! DB::table('menu_items')->where('show_in_footer', true)->exists()) {
            DB::table('menu_items')->where('is_active', true)->update(['show_in_footer' => true]);
        }
    }

    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('show_in_footer');
        });
    }
};

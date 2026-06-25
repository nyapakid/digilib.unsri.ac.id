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
            $table->foreignId('parent_id')
                ->nullable()
                ->after('url')
                ->constrained('menu_items')
                ->nullOnDelete();
        });

        Schema::table('hero_slides', function (Blueprint $table) {
            $table->string('fact_1_title')->nullable()->after('image_url');
            $table->string('fact_1_text')->nullable()->after('fact_1_title');
            $table->string('fact_1_icon')->nullable()->after('fact_1_text');
            $table->string('fact_2_title')->nullable()->after('fact_1_icon');
            $table->string('fact_2_text')->nullable()->after('fact_2_title');
            $table->string('fact_2_icon')->nullable()->after('fact_2_text');
        });

        $site = DB::table('site_settings')->first();

        if ($site) {
            DB::table('hero_slides')->update([
                'fact_1_title' => $site->hero_fact_1_title,
                'fact_1_text' => $site->hero_fact_1_text,
                'fact_1_icon' => $site->hero_fact_1_icon,
                'fact_2_title' => $site->hero_fact_2_title,
                'fact_2_text' => $site->hero_fact_2_text,
                'fact_2_icon' => $site->hero_fact_2_icon,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn([
                'fact_1_title',
                'fact_1_text',
                'fact_1_icon',
                'fact_2_title',
                'fact_2_text',
                'fact_2_icon',
            ]);
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parent_id');
        });
    }
};

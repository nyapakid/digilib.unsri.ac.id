<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gallery_items', function (Blueprint $table) {
            $table->date('published_at')->nullable()->after('description');
        });

        Schema::create('gallery_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gallery_item_id')->constrained()->cascadeOnDelete();
            $table->text('image_url');
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_cover')->default(false);
            $table->timestamps();
        });

        $now = now();

        DB::table('gallery_items')->orderBy('id')->get()->each(function (object $gallery) use ($now) {
            DB::table('gallery_items')
                ->where('id', $gallery->id)
                ->update(['published_at' => $gallery->created_at ? date('Y-m-d', strtotime($gallery->created_at)) : $now->toDateString()]);

            if (! blank($gallery->image_url)) {
                DB::table('gallery_photos')->insert([
                    'gallery_item_id' => $gallery->id,
                    'image_url' => $gallery->image_url,
                    'description' => $gallery->description,
                    'sort_order' => 1,
                    'is_cover' => true,
                    'created_at' => $gallery->created_at ?: $now,
                    'updated_at' => $gallery->updated_at ?: $now,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_photos');

        Schema::table('gallery_items', function (Blueprint $table) {
            $table->dropColumn('published_at');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('resource_links')
            ->whereNotNull('url')
            ->where('url', '<>', '#')
            ->orderBy('id')
            ->get()
            ->each(function (object $resource) use ($now) {
                $exists = DB::table('resource_link_items')
                    ->where('resource_link_id', $resource->id)
                    ->where('url', $resource->url)
                    ->exists();

                if (! $exists) {
                    DB::table('resource_link_items')->insert([
                        'resource_link_id' => $resource->id,
                        'title' => $resource->title,
                        'image_url' => null,
                        'url' => $resource->url,
                        'sort_order' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            });
    }

    public function down(): void
    {
        DB::table('resource_link_items')
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('resource_links')
                    ->whereColumn('resource_links.id', 'resource_link_items.resource_link_id')
                    ->whereColumn('resource_links.url', 'resource_link_items.url')
                    ->whereColumn('resource_links.title', 'resource_link_items.title');
            })
            ->delete();
    }
};

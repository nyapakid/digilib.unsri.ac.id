<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $seen = [];
        $deleteIds = [];

        DB::table('resource_link_items')
            ->orderBy('resource_link_id')
            ->orderBy('url')
            ->orderBy('id')
            ->get()
            ->each(function (object $item) use (&$seen, &$deleteIds) {
                $key = $item->resource_link_id.'|'.$item->url;

                if (isset($seen[$key])) {
                    $deleteIds[] = $item->id;

                    return;
                }

                $seen[$key] = true;
            });

        if ($deleteIds !== []) {
            DB::table('resource_link_items')->whereIn('id', $deleteIds)->delete();
        }
    }

    public function down(): void
    {
        //
    }
};

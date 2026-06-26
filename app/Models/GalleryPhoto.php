<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryPhoto extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_cover' => 'boolean'];
    }

    public function galleryItem(): BelongsTo
    {
        return $this->belongsTo(GalleryItem::class);
    }
}

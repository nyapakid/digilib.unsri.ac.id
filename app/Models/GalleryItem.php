<?php

namespace App\Models;

use App\Models\Concerns\PublishesContent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    use PublishesContent;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'published_at' => 'date',
        ];
    }

    public function photos(): HasMany
    {
        return $this->hasMany(GalleryPhoto::class)->orderBy('sort_order')->orderBy('id');
    }

    public function coverPhoto(): HasOne
    {
        return $this->hasOne(GalleryPhoto::class)->where('is_cover', true)->orderBy('sort_order')->orderBy('id');
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(function (?string $value) {
            $cover = $this->relationLoaded('coverPhoto')
                ? $this->getRelation('coverPhoto')
                : $this->coverPhoto()->first();

            if ($cover?->image_url) {
                return $cover->image_url;
            }

            $firstPhoto = $this->relationLoaded('photos')
                ? $this->getRelation('photos')->sortBy([['sort_order', 'asc'], ['id', 'asc']])->first()
                : $this->photos()->first();

            return $firstPhoto?->image_url ?: $value;
        });
    }
}

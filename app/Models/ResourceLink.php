<?php

namespace App\Models;

use App\Models\Concerns\PublishesContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResourceLink extends Model
{
    use PublishesContent;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(ResourceLinkItem::class)->orderBy('sort_order')->orderBy('id');
    }
}

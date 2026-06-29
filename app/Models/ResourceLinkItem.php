<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceLinkItem extends Model
{
    protected $guarded = [];

    public function resourceLink(): BelongsTo
    {
        return $this->belongsTo(ResourceLink::class);
    }
}

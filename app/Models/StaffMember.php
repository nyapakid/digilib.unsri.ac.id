<?php

namespace App\Models;

use App\Models\Concerns\PublishesContent;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class StaffMember extends Model
{
    use PublishesContent;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    protected function title(): Attribute
    {
        return Attribute::get(fn () => $this->name);
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->photo_url);
    }
}

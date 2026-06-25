<?php

namespace App\Models;

use App\Models\Concerns\PublishesContent;
use Illuminate\Database\Eloquent\Model;

class AgendaItem extends Model
{
    use PublishesContent;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'event_date' => 'date',
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $guarded = [];

    public static function current(): self
    {
        return self::query()->firstOrCreate([], [
            'site_name' => 'Digilib Universitas Sriwijaya',
            'brand_name' => 'SIneRGIS',
            'university_name' => 'Universitas Sriwijaya',
            'logo_text' => 'US',
        ]);
    }
}

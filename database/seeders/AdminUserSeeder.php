<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@digilib.unsri.ac.id')],
            [
                'name' => env('ADMIN_NAME', 'Administrator Digilib'),
                'password' => env('ADMIN_PASSWORD', 'password'),
            ]
        );
    }
}

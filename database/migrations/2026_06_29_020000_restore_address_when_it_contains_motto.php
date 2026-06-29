<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('site_settings')
            ->where('address', 'System for Integrated e-Resources & Library Gateway of Sriwijaya (SIneRGiS)')
            ->update([
                'address' => 'Jl. Palembang - Prabumulih Km. 32, Indralaya, Ogan Ilir 30662, Sumatera Selatan',
            ]);
    }

    public function down(): void
    {
        //
    }
};

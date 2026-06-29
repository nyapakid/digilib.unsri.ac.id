<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('motto')->nullable()->after('university_name');
        });

        DB::table('site_settings')->update([
            'motto' => 'System for Integrated e-Resources & Library Gateway of Sriwijaya (SIneRGiS)',
        ]);

        Schema::create('resource_link_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resource_link_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('image_url')->nullable();
            $table->string('url');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_link_items');

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn('motto');
        });
    }
};

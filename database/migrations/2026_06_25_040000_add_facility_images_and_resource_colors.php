<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->text('image_url')->nullable()->after('description');
        });

        Schema::table('resource_links', function (Blueprint $table) {
            $table->string('background_color', 20)->default('#ffffff')->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('resource_links', function (Blueprint $table) {
            $table->dropColumn('background_color');
        });

        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn('image_url');
        });
    }
};

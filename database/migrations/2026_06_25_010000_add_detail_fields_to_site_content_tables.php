<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resource_links', function (Blueprint $table) {
            $table->longText('body')->nullable()->after('description');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->longText('body')->nullable()->after('description');
        });

        Schema::table('facilities', function (Blueprint $table) {
            $table->longText('body')->nullable()->after('description');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->longText('body')->nullable()->after('excerpt');
        });

        Schema::table('agenda_items', function (Blueprint $table) {
            $table->longText('body')->nullable()->after('description');
        });

        Schema::table('gallery_items', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('gallery_items', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('agenda_items', function (Blueprint $table) {
            $table->dropColumn('body');
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('body');
        });

        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn('body');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('body');
        });

        Schema::table('resource_links', function (Blueprint $table) {
            $table->dropColumn('body');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pages', 'seo_title')) {
            Schema::table('pages', function (Blueprint $table): void {
                $table->string('seo_title')->nullable()->after('title');
            });
        }

        if (! Schema::hasColumn('pages', 'meta_description')) {
            Schema::table('pages', function (Blueprint $table): void {
                $table->text('meta_description')->nullable()->after('seo_title');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pages', 'meta_description')) {
            Schema::table('pages', function (Blueprint $table): void {
                $table->dropColumn('meta_description');
            });
        }

        if (Schema::hasColumn('pages', 'seo_title')) {
            Schema::table('pages', function (Blueprint $table): void {
                $table->dropColumn('seo_title');
            });
        }
    }
};

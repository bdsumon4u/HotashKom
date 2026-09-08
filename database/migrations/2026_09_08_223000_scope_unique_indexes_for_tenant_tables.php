<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Brands
        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
                $table->dropUnique('brands_name_unique');
                $table->dropUnique('brands_slug_unique');
                $table->index(['tenant_id', 'slug']);
            });
        }

        // 2. Categories
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropUnique('categories_name_unique');
                $table->dropUnique('categories_slug_unique');
                $table->index(['tenant_id', 'slug']);
            });
        }

        // 3. Products
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropUnique('products_slug_unique');
                $table->dropUnique('products_sku_unique');
                $table->index(['tenant_id', 'slug']);
            });
        }

        // 4. Landing Page Pros
        if (Schema::hasTable('landing_page_pros')) {
            Schema::table('landing_page_pros', function (Blueprint $table) {
                $table->dropUnique('landing_page_pros_slug_unique');
                $table->index(['tenant_id', 'slug']);
            });
        }

        // 5. Pages
        if (Schema::hasTable('pages')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropUnique('pages_slug_unique');
                $table->index(['tenant_id', 'slug']);
            });
        }

        // 6. Blogs
        if (Schema::hasTable('blogs')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->dropUnique('blogs_slug_unique');
                $table->index(['tenant_id', 'slug']);
            });
        }

        // 7. Menus
        if (Schema::hasTable('menus')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropUnique('menus_slug_unique');
                $table->index(['tenant_id', 'slug']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
                $table->dropIndex(['tenant_id', 'slug']);
                $table->unique('name');
                $table->unique('slug');
            });
        }

        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropIndex(['tenant_id', 'slug']);
                $table->unique('name');
                $table->unique('slug');
            });
        }

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropIndex(['tenant_id', 'slug']);
                $table->unique('slug');
                $table->unique('sku');
            });
        }

        if (Schema::hasTable('landing_page_pros')) {
            Schema::table('landing_page_pros', function (Blueprint $table) {
                $table->dropIndex(['tenant_id', 'slug']);
                $table->unique('slug');
            });
        }

        if (Schema::hasTable('pages')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropIndex(['tenant_id', 'slug']);
                $table->unique('slug');
            });
        }

        if (Schema::hasTable('blogs')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->dropIndex(['tenant_id', 'slug']);
                $table->unique('slug');
            });
        }

        if (Schema::hasTable('menus')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropIndex(['tenant_id', 'slug']);
                $table->unique('slug');
            });
        }
    }
};

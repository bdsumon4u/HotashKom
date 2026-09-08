<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The tables that should have a tenant_id column.
     *
     * @var array<string>
     */
    protected array $tables = [
        'products',
        'categories',
        'brands',
        'attributes',
        'options',
        'images',
        'slides',
        'home_sections',
        'menus',
        'menu_items',
        'category_menus',
        'pages',
        'blogs',
        'landing_page_pros',
        'landing_page_pro_items',
        'coupons',
        'leads',
        'reviews',
        'settings',
        'orders',
        'order_notes',
        'purchases',
        'product_purchases',
        'shopping_cart',
        'admins',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && ! Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->string('tenant_id')->nullable()->index();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tenant_id')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropIndex(['tenant_id']);
                    $blueprint->dropColumn('tenant_id');
                });
            }
        }
    }
};

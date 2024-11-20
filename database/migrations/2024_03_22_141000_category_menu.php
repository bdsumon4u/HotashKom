<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CategoryMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        require_once '2020_12_11_120455_create_category_menus_table.php';

        app(CreateCategoryMenusTable::class)->down();

        Schema::table('categories', function (Blueprint $table) {
            $table->integer('order')
                ->default(0)
                ->after('slug');
            $table->foreignId('image_id')
                ->nullable()
                ->after('parent_id')
                ->constrained('images')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        app(CreateCategoryMenusTable::class)->up();
    }
}

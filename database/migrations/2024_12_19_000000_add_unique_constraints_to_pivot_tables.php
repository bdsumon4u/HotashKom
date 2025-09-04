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
        // Add unique constraint to category_product table
        Schema::table('category_product', function (Blueprint $table) {
            $table->unique(['product_id', 'category_id'], 'category_product_unique');
        });

        // Add unique constraint to image_product table
        Schema::table('image_product', function (Blueprint $table) {
            $table->unique(['product_id', 'image_id', 'img_type'], 'image_product_unique');
        });

        // Add unique constraint to option_product table
        Schema::table('option_product', function (Blueprint $table) {
            $table->unique(['product_id', 'option_id'], 'option_product_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove unique constraint from category_product table
        Schema::table('category_product', function (Blueprint $table) {
            $table->dropUnique('category_product_unique');
        });

        // Remove unique constraint from image_product table
        Schema::table('image_product', function (Blueprint $table) {
            $table->dropUnique('image_product_unique');
        });

        // Remove unique constraint from option_product table
        Schema::table('option_product', function (Blueprint $table) {
            $table->dropUnique('option_product_unique');
        });
    }
};

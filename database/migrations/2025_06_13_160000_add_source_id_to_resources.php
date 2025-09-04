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
        // Add source_id to brands table
        Schema::table('brands', function (Blueprint $table) {
            $table->unsignedBigInteger('source_id')->nullable()->after('id')->index();
        });

        // Add source_id to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('source_id')->nullable()->after('id')->index();
        });

        // Add source_id to attributes table
        Schema::table('attributes', function (Blueprint $table) {
            $table->unsignedBigInteger('source_id')->nullable()->after('id')->index();
        });

        // Add source_id to options table
        Schema::table('options', function (Blueprint $table) {
            $table->unsignedBigInteger('source_id')->nullable()->after('id')->index();
        });

        // Add source_id to images table
        Schema::table('images', function (Blueprint $table) {
            $table->unsignedBigInteger('source_id')->nullable()->after('id')->index();
        });

        // Add source_id to products table
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('source_id')->nullable()->after('id')->index();
        });

        // Add source_id to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('source_id')->nullable()->after('id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove source_id from brands table
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('source_id');
        });

        // Remove source_id from categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('source_id');
        });

        // Remove source_id from attributes table
        Schema::table('attributes', function (Blueprint $table) {
            $table->dropColumn('source_id');
        });

        // Remove source_id from options table
        Schema::table('options', function (Blueprint $table) {
            $table->dropColumn('source_id');
        });

        // Remove source_id from images table
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn('source_id');
        });

        // Remove source_id from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('source_id');
        });

        // Remove source_id from orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('source_id');
        });
    }
};

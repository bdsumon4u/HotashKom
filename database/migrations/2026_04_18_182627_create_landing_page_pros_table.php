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
        Schema::create('landing_page_pros', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('template_key')->default('template-one');
            $table->boolean('is_published')->default(false);
            $table->json('seo')->nullable();
            $table->json('section_settings')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('landing_page_pro_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_pro_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedSmallInteger('default_quantity')->default(1);
            $table->boolean('free_delivery')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['landing_page_pro_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_page_pro_items');
        Schema::dropIfExists('landing_page_pros');
    }
};

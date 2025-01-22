<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(config('filament-fabricator.table_name', 'pages'), function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->index();
            $table->string('title')->index();
            $table->string('slug');
            $table->string('layout')->default('default')->index();
            $table->json('blocks');
            $table->foreignId('parent_id')->nullable()->constrained(config('filament-fabricator.table_name', 'pages'))->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            $table->unique(['slug', 'parent_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('filament-fabricator.table_name', 'pages'));
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->string('stock_group')->default('uncategorized')->after('stock_count');
            $table->index('stock_group');
        });

        // Ensure all existing records are populated with 'uncategorized'
        DB::table('products')
            ->whereNull('stock_group')
            ->orWhere('stock_group', '')
            ->update(['stock_group' => 'uncategorized']);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropIndex(['stock_group']);
            $table->dropColumn('stock_group');
        });
    }
};

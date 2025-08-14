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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('inside_dhaka_shipping')->default(0)->after('logo');
            $table->integer('outside_dhaka_shipping')->default(0)->after('inside_dhaka_shipping');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'inside_dhaka_shipping',
                'outside_dhaka_shipping',
            ]);
        });
    }
};

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
            $table->string('shop_name')->nullable()->after('name');
            $table->string('logo')->nullable()->after('shop_name');
            $table->string('bkash_number')->nullable()->after('phone_number');
            $table->boolean('is_verified')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('shop_name');
            $table->dropColumn('logo');
            $table->dropColumn('bkash_number');
            $table->dropColumn('is_verified');
        });
    }
};

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
            $table->string('domain')->nullable()->after('address');
            $table->string('order_prefix', 80)->nullable()->after('domain')->index();
            $table->boolean('is_active')->default(false)->after('order_prefix');

            // Database configuration
            $table->string('db_host')->nullable()->after('is_active');
            $table->string('db_name')->nullable()->after('db_host');
            $table->string('db_username')->nullable()->after('db_name');
            $table->string('db_password')->nullable()->after('db_username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'domain', 'api_token', 'is_active',
                'db_name', 'db_username', 'db_password',
            ]);
        });
    }
};

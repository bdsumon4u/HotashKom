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
        if (Schema::hasTable('product_purchase') && ! Schema::hasColumn('product_purchase', 'tenant_id')) {
            Schema::table('product_purchase', function (Blueprint $table) {
                $table->string('tenant_id')->nullable()->index()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_purchase') && Schema::hasColumn('product_purchase', 'tenant_id')) {
            Schema::table('product_purchase', function (Blueprint $table) {
                $table->dropIndex(['tenant_id']);
                $table->dropColumn('tenant_id');
            });
        }
    }
};

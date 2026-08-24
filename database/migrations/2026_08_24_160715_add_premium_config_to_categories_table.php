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
        Schema::table('categories', function (Blueprint $table): void {
            if (! Schema::hasColumn('categories', 'premium_header_config')) {
                $table->json('premium_header_config')->nullable()->after('image_id');
            }
            if (! Schema::hasColumn('categories', 'premium_guide_config')) {
                $table->json('premium_guide_config')->nullable()->after('premium_header_config');
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            if (Schema::hasColumn('categories', 'premium_guide_config')) {
                $table->dropColumn('premium_guide_config');
            }
            if (Schema::hasColumn('categories', 'premium_header_config')) {
                $table->dropColumn('premium_header_config');
            }
        });
    }
};

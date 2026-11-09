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
        Schema::table('landing_page_pros', function (Blueprint $table): void {
            $table->text('custom_css')->nullable()->after('section_settings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('landing_page_pros', function (Blueprint $table): void {
            $table->dropColumn('custom_css');
        });
    }
};


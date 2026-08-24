<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('images', 'alt_text')) {
            Schema::table('images', function (Blueprint $table): void {
                $table->string('alt_text')->nullable()->after('filename');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('images', 'alt_text')) {
            Schema::table('images', function (Blueprint $table): void {
                $table->dropColumn('alt_text');
            });
        }
    }
};

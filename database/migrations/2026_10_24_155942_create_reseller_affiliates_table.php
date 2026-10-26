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
        Schema::create('reseller_affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('referrer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('referral_code', 32)->unique();
            $table->enum('direct_leg', ['left', 'right'])->nullable();

            $table->unsignedInteger('left_total')->default(0);
            $table->unsignedInteger('right_total')->default(0);
            $table->unsignedInteger('direct_left_count')->default(0);
            $table->unsignedInteger('direct_right_count')->default(0);

            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['referrer_id', 'direct_leg']);
            $table->index(['referrer_id', 'verified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseller_affiliates');
    }
};

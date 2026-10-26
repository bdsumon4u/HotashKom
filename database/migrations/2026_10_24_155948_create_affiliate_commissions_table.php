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
        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('milestone_x');
            $table->decimal('percentage', 5, 2)->unsigned();
            $table->decimal('base_amount', 12, 2);
            $table->decimal('amount', 12, 2);
            $table->string('trigger_leg', 10);
            $table->foreignId('triggered_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'milestone_x']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_commissions');
    }
};

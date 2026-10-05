<?php

declare(strict_types=1);

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
        Schema::create('investors', function (Blueprint $table): void {
            $table->id();
            $table->string('tenant_id')->nullable()->index();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number')->nullable()->index();
            $table->string('bkash_number')->nullable();
            $table->text('bank_details')->nullable();
            $table->text('address')->nullable();
            $table->string('password');
            $table->string('referral_code')->unique()->index();
            $table->foreignId('referred_by_id')->nullable()->constrained('investors')->nullOnDelete();
            $table->boolean('is_active')->default(true)->index();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investors');
    }
};

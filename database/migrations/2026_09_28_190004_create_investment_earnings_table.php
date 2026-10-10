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
        Schema::create('investment_earnings', function (Blueprint $table): void {
            $table->id();
            $table->string('tenant_id')->nullable()->index();
            $table->foreignId('investment_id')->nullable()->constrained('investments')->nullOnDelete();
            $table->foreignId('installment_id')->nullable()->constrained('investment_installments')->nullOnDelete();
            $table->foreignId('investor_id')->nullable()->constrained('investors')->nullOnDelete();
            $table->string('type', 50)->index(); // initial_deduction, monthly_deduction
            $table->decimal('amount', 15, 2);
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_earnings');
    }
};

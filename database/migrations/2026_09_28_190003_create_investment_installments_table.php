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
        Schema::create('investment_installments', function (Blueprint $table): void {
            $table->id();
            $table->string('tenant_id')->nullable()->index();
            $table->foreignId('investment_id')->constrained('investments')->cascadeOnDelete();
            $table->foreignId('investor_id')->constrained('investors')->cascadeOnDelete();
            $table->unsignedSmallInteger('installment_number');
            $table->date('due_date')->index();
            $table->decimal('gross_amount', 15, 2);
            $table->decimal('deduction_amount', 15, 2)->default(0.00);
            $table->decimal('referrer_bonus_amount', 15, 2)->default(0.00);
            $table->decimal('company_amount', 15, 2)->default(0.00);
            $table->decimal('net_investor_amount', 15, 2);
            $table->timestamp('paid_at')->nullable()->index();
            $table->string('status', 30)->default('pending')->index(); // pending, processed, failed
            $table->timestamps();

            $table->unique(['investment_id', 'installment_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investment_installments');
    }
};

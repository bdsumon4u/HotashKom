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
        Schema::create('investments', function (Blueprint $table): void {
            $table->id();
            $table->string('tenant_id')->nullable()->index();
            $table->foreignId('investor_id')->constrained('investors')->cascadeOnDelete();
            $table->decimal('invested_amount', 15, 2);
            $table->decimal('multiplier', 4, 2)->default(2.00);
            $table->decimal('total_return_amount', 15, 2);
            $table->unsignedSmallInteger('duration_months')->default(36);
            $table->decimal('monthly_installment_amount', 15, 2);
            $table->decimal('initial_deduction_rate', 5, 2)->default(10.00);
            $table->decimal('initial_referrer_bonus_rate', 5, 2)->default(5.00);
            $table->decimal('monthly_deduction_rate', 5, 2)->default(10.00);
            $table->decimal('monthly_referrer_bonus_rate', 5, 2)->default(5.00);
            $table->decimal('initial_deduction_amount', 15, 2)->default(0.00);
            $table->decimal('initial_referrer_bonus_amount', 15, 2)->default(0.00);
            $table->decimal('initial_company_amount', 15, 2)->default(0.00);
            $table->date('start_date')->index();
            $table->unsignedSmallInteger('installments_paid_count')->default(0);
            $table->decimal('total_paid_amount', 15, 2)->default(0.00);
            $table->string('status', 30)->default('active')->index(); // active, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};

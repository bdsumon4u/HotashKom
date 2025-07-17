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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained()->onDelete('cascade');
            $table->decimal('total_amount', 12, 2); // Total amount for this purchase
            $table->date('purchase_date');
            $table->string('supplier_name')->nullable();
            $table->string('supplier_phone')->nullable();
            $table->text('notes')->nullable();
            $table->string('invoice_number')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index('purchase_date');
            $table->index('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};

<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add verification fee setting
        Setting::create([
            'name' => 'verification_fee',
            'value' => json_encode([
                'amount' => 1000,
                'currency' => 'BDT',
                'description' => 'Reseller account verification fee',
            ]),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Setting::where('name', 'verification_fee')->delete();
    }
};

<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only seed when no delivery_areas row exists yet
        if (DB::table('settings')->where('name', 'delivery_areas')->exists()) {
            return;
        }

        // Use existing delivery_charge costs if available, otherwise use defaults
        $deliveryCharge = DB::table('settings')->where('name', 'delivery_charge')->value('value');
        $charge = $deliveryCharge ? json_decode($deliveryCharge) : null;

        $insideCost  = (int) ($charge->inside_dhaka  ?? 60);
        $outsideCost = (int) ($charge->outside_dhaka ?? 120);

        DB::table('settings')->insert([
            'name'       => 'delivery_areas',
            'value'      => json_encode([
                ['name' => 'Inside Dhaka',  'cost' => $insideCost,  'is_default' => true],
                ['name' => 'Outside Dhaka', 'cost' => $outsideCost, 'is_default' => false],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('name', 'delivery_areas')->delete();
    }
};

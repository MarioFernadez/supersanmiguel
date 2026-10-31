<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use App\Models\PaymentMethod;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('payment_methods')) {
            PaymentMethod::firstOrCreate(
                ['name' => 'Efectivo'],
                [
                    'type' => 'cash',
                    'is_active' => true,
                ]
            );

            // Asegurar que si ya existía pero con otro tipo o inactivo, quede activo y como cash
            PaymentMethod::where('name', 'Efectivo')
                ->orWhere('type', 'cash')
                ->update([
                    'type' => 'cash',
                    'is_active' => true,
                ]);

            Cache::forget('active_payment_methods');
            Cache::forget('pos_active_payment_methods');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revertir
    }
};

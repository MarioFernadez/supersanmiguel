<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing records safely
        PaymentMethod::query()->delete();

        PaymentMethod::create([
            'name' => 'Efectivo',
            'code' => 'efectivo',
            'type' => 'cash',
            'is_active' => true,
        ]);

        PaymentMethod::create([
            'name' => 'Tarjeta',
            'code' => 'tarjeta',
            'type' => 'card',
            'is_active' => true,
        ]);

        PaymentMethod::create([
            'name' => 'Transferencia',
            'code' => 'transferencia',
            'type' => 'transfer',
            'is_active' => true,
        ]);

        PaymentMethod::create([
            'name' => 'QR',
            'code' => 'qr',
            'type' => 'qr',
            'is_active' => true,
        ]);
    }
}

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
            'type' => 'cash',
            'is_active' => true,
        ]);

        PaymentMethod::create([
            'name' => 'Tarjeta',
            'type' => 'card',
            'is_active' => true,
        ]);

        PaymentMethod::create([
            'name' => 'Transferencia',
            'type' => 'transfer',
            'is_active' => true,
        ]);

        PaymentMethod::create([
            'name' => 'QR',
            'type' => 'qr',
            'is_active' => true,
        ]);
    }
}

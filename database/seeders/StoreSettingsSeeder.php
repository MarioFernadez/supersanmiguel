<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StoreSetting;

class StoreSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'store_name' => ['value' => 'ZonaPOS Store', 'description' => 'Nombre del comercio'],
            'store_ruc' => ['value' => '8000000-1', 'description' => 'RUC del comercio'],
            'store_address' => ['value' => 'Av. Eusebio Ayala km 5', 'description' => 'Dirección del comercio'],
            'store_phone' => ['value' => '0981 123 456', 'description' => 'Teléfono de contacto'],
            'ticket_footer' => ['value' => '¡Gracias por su compra!', 'description' => 'Mensaje al pie del ticket'],
            'shop_whatsapp' => ['value' => '981123456', 'description' => 'WhatsApp para pedidos e-commerce'],
            'license_expires_at' => ['value' => now()->addYear()->format('Y-m-d'), 'description' => 'Fecha de vencimiento de la licencia'],
        ];

        foreach ($settings as $key => $data) {
            StoreSetting::firstOrCreate(
                ['key' => $key],
                [
                    'value' => $data['value'],
                    'description' => $data['description']
                ]
            );
        }
    }
}

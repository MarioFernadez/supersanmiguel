<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use App\Models\CashRegister;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Sucursal base para asignación obligatoria de usuario
        $branch = Branch::firstOrCreate(
            ['name' => 'Sucursal Central'],
            [
                'address' => 'Casa Central',
                'phone' => '0981 000 000',
                'is_active' => true,
            ]
        );

        // 2. Caja inicial para poder operar el POS
        CashRegister::firstOrCreate(
            ['name' => 'Caja Principal'],
            [
                'branch_id' => $branch->id,
                'active' => true,
                'description' => 'Caja por defecto',
            ]
        );

        // 3. Métodos de cobro principales
        PaymentMethod::firstOrCreate(['name' => 'Efectivo'], ['type' => 'cash', 'is_active' => true]);
        PaymentMethod::firstOrCreate(['name' => 'Tarjeta'], ['type' => 'card', 'is_active' => true]);
        PaymentMethod::firstOrCreate(['name' => 'Transferencia'], ['type' => 'transfer', 'is_active' => true]);
        PaymentMethod::firstOrCreate(['name' => 'QR'], ['type' => 'qr', 'is_active' => true]);

        // 4. Crear únicamente el usuario Desarrollador
        User::withTrashed()->where('email', 'developer@gmail.com')->forceDelete();
        User::withTrashed()->where('role', 'developer')->forceDelete();

        User::create([
            'name' => 'Desarrollador Master',
            'email' => 'developer@gmail.com',
            'password' => Hash::make('Developer2026'),
            'role' => 'developer',
            'branch_id' => $branch->id,
        ]);

        $this->command?->info('Usuario Desarrollador creado exitosamente!');
        $this->command?->info('Developer - Email: developer@gmail.com | Password: Developer2026');
    }
}

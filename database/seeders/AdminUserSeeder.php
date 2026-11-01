<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::firstOrCreate(
            ['name' => 'Sucursal Central'],
            [
                'address' => 'Casa Central',
                'phone' => '0981 000 000',
                'is_active' => true,
            ]
        );

        // Limpiar registros previos para evitar duplicados en re-seeds
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

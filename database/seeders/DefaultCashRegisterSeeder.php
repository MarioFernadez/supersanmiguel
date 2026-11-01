<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\CashRegister;
use App\Models\Unit;

class DefaultCashRegisterSeeder extends Seeder
{
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

        CashRegister::updateOrCreate(
            ['name' => 'Caja Principal'],
            [
                'branch_id' => $branch->id,
                'active' => true,
                'description' => 'Caja por defecto del sistema',
            ]
        );

        Unit::firstOrCreate(['name' => 'kg'], ['abbreviation' => 'Kg', 'is_active' => true]);
        Unit::firstOrCreate(['name' => 'unit'], ['abbreviation' => 'Unid.', 'is_active' => true]);
    }
}

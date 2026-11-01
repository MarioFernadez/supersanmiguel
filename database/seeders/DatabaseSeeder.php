<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Únicamente crear usuario developer y estructura base (sin productos ni datos de prueba)
        $this->call([
            AdminUserSeeder::class,
        ]);
    }
}

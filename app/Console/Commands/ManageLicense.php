<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StoreSetting;

class ManageLicense extends Command
{
    protected $signature = 'licensing:set {--users=3 : Maximum number of users} {--registers=1 : Maximum number of cash registers} {--branches=1 : Maximum number of branches} {--tier=Básico : Name of the plan}';

    protected $description = 'Set system licensing limits and tier name';

    public function handle()
    {
        $users = $this->option('users');
        $registers = $this->option('registers');
        $branches = $this->option('branches');
        $tier = $this->option('tier');

        $this->updateSetting('license_max_users', $users, 'Límite máximo de usuarios permitidos');
        $this->updateSetting('license_max_cash_registers', $registers, 'Límite máximo de cajas registradoras permitidas');
        $this->updateSetting('license_max_branches', $branches, 'Límite máximo de sucursales permitidas');
        $this->updateSetting('license_tier_name', $tier, 'Nombre del plan/tier contratado');

        $this->info("Licencia actualizada correctamente:");
        $this->line("- Plan: <info>{$tier}</info>");
        $this->line("- Máx Usuarios: <info>{$users}</info>");
        $this->line("- Máx Cajas: <info>{$registers}</info>");
        $this->line("- Máx Sucursales: <info>{$branches}</info>");
        
        return Command::SUCCESS;
    }

    private function updateSetting($key, $value, $description)
    {
        StoreSetting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'description' => $description
            ]
        );
    }
}

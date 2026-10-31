<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\StoreSetting;
use App\Models\LicensePayment;
use App\Services\LicenseService;
use Illuminate\Support\Facades\Artisan;

class LicenseManager extends Component
{
    public $maxUsers;
    public $maxCashRegisters;
    public $maxBranches;
    public $tierName;
    public $sifenMasterEnabled;

    // Campos de Suscripción
    public $enabledAt;
    public $expiresAt;

    // Campos de Registro de Pago
    public $paymentAmount;
    public $paymentDate;
    public $paymentMethod = 'transferencia';
    public $paymentNotes;

    public function mount()
    {
        abort_unless(auth()->user()->isDeveloper(), 403);

        $this->maxUsers = LicenseService::getMaxUsers();
        $this->maxCashRegisters = LicenseService::getMaxCashRegisters();
        $this->maxBranches = LicenseService::getMaxBranches();
        $this->tierName = LicenseService::getTierName();
        $this->sifenMasterEnabled = LicenseService::isSifenEnabled();

        $this->enabledAt = LicenseService::getEnabledAt()->format('Y-m-d');
        $this->expiresAt = LicenseService::getExpiresAt()->format('Y-m-d');
        $this->paymentDate = now()->format('Y-m-d');
    }

    public function updateLicense()
    {
        abort_unless(auth()->user()->isDeveloper(), 403);

        $this->validate([
            'maxUsers' => 'required|integer|min:1',
            'maxCashRegisters' => 'required|integer|min:1',
            'maxBranches' => 'required|integer|min:1',
            'tierName' => 'required|string|min:2',
            'sifenMasterEnabled' => 'boolean',
            'enabledAt' => 'required|date',
            'expiresAt' => 'required|date',
        ]);

        Artisan::call('licensing:set', [
            '--users' => $this->maxUsers,
            '--registers' => $this->maxCashRegisters,
            '--branches' => $this->maxBranches,
            '--tier' => $this->tierName,
        ]);

        StoreSetting::updateOrCreate(
            ['key' => 'sifen_master_enabled'],
            ['value' => $this->sifenMasterEnabled ? '1' : '0', 'description' => 'Habilitar Módulo de Facturación Electrónica SIFEN por Master']
        );

        StoreSetting::updateOrCreate(
            ['key' => 'license_enabled_at'],
            ['value' => $this->enabledAt]
        );

        StoreSetting::updateOrCreate(
            ['key' => 'license_expires_at'],
            ['value' => $this->expiresAt]
        );

        session()->flash('message', 'Licencia, módulos y fechas de suscripción actualizadas correctamente.');
    }

    public function registerPayment()
    {
        abort_unless(auth()->user()->isDeveloper(), 403);

        $this->validate([
            'paymentAmount' => 'required|numeric|min:1',
            'paymentDate' => 'required|date',
            'paymentMethod' => 'required|in:transferencia,efectivo',
            'paymentNotes' => 'nullable|string',
        ]);

        LicensePayment::create([
            'amount' => $this->paymentAmount,
            'payment_date' => $this->paymentDate,
            'payment_method' => $this->paymentMethod,
            'notes' => $this->paymentNotes,
        ]);

        // Extender 30 días
        $currentExpires = LicenseService::getExpiresAt();
        $newExpires = $currentExpires->isPast() 
            ? \Carbon\Carbon::parse($this->paymentDate)->addMonth() 
            : $currentExpires->addMonth();

        $this->expiresAt = $newExpires->format('Y-m-d');
        StoreSetting::updateOrCreate(
            ['key' => 'license_expires_at'],
            ['value' => $this->expiresAt]
        );

        StoreSetting::updateOrCreate(
            ['key' => 'license_grace_period_used'],
            ['value' => '0']
        );

        $this->reset(['paymentAmount', 'paymentNotes']);
        $this->paymentDate = now()->format('Y-m-d');
        $this->paymentMethod = 'transferencia';

        session()->flash('payment_message', 'Pago registrado e historial actualizado correctamente.');
    }

    public function getPaymentsProperty()
    {
        return LicensePayment::orderBy('payment_date', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.admin.license-manager', [
            'payments' => $this->payments
        ])->layout('layouts.app');
    }
}

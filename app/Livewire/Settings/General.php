<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\StoreSetting;

class General extends Component
{
    public $store_name;
    public $store_address;
    public $store_phone;
    public $store_ruc;
    public $store_email;
    public $ticket_footer;

    // Discount feature toggles
    public $discount_product_enabled = false;
    public $discount_manual_enabled  = false;

    public $business_type;

    // Internal property to hold all settings
    private $settingsCollection;

    public function mount()
    {
        $this->loadSettings();
    }

    private function loadSettings()
    {
        $this->settingsCollection = StoreSetting::all()->pluck('value', 'key');

        $this->business_type = $this->getSetting('business_type', 'generic');
        
        $this->store_name = $this->getSetting('store_name', 'ZonaShop');
        $this->store_address = $this->getSetting('store_address', '');
        $this->store_phone = $this->getSetting('store_phone', '');
        $this->store_ruc = $this->getSetting('store_ruc', '');
        $this->store_email = $this->getSetting('store_email', '');
        $this->ticket_footer = $this->getSetting('ticket_footer', 'Gracias por su compra');

        $this->discount_product_enabled = $this->getSetting('discount_product_enabled', '0') === '1';
        $this->discount_manual_enabled = $this->getSetting('discount_manual_enabled', '0') === '1';
    }

    public function render()
    {
        return view('livewire.settings.general')->layout('layouts.app');
    }

    public function save()
    {
        $this->validate([
            'business_type' => 'nullable|string|in:generic,minimarket,boutique,perfumery,restaurant',
            'store_name' => 'required|string|max:255',
            'store_address' => 'nullable|string|max:255',
            'store_phone' => 'nullable|string|max:50',
            'store_ruc' => 'nullable|string|max:20',
            'store_email' => 'nullable|email|max:255',
            'ticket_footer' => 'nullable|string|max:255',
        ]);

        if (auth()->user()->role === 'developer' || auth()->user()->role === 'admin') {
            $this->updateSetting('business_type', $this->business_type, 'Tipo de Negocio del Sistema');
        }

        $this->updateSetting('store_name', $this->store_name, 'Nombre del comercio');
        $this->updateSetting('store_address', $this->store_address, 'Dirección física');
        $this->updateSetting('store_phone', $this->store_phone, 'Teléfono de contacto');
        $this->updateSetting('store_ruc', $this->store_ruc, 'RUC o Identificación Tributaria');
        $this->updateSetting('store_email', $this->store_email, 'Email de contacto');
        $this->updateSetting('ticket_footer', $this->ticket_footer, 'Mensaje al pie del ticket');
        $this->updateSetting('discount_product_enabled', $this->discount_product_enabled ? '1' : '0', 'Habilitar descuentos por producto');
        $this->updateSetting('discount_manual_enabled', $this->discount_manual_enabled ? '1' : '0', 'Habilitar descuento manual en POS');

        $this->loadSettings();

        session()->flash('message', 'Ajustes comerciales actualizados correctamente.');
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

    private function getSetting($key, $default = '')
    {
        return $this->settingsCollection->get($key, $default);
    }
}

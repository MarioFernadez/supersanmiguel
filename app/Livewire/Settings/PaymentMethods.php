<?php

namespace App\Livewire\Settings;

use App\Models\PaymentMethod;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentMethods extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $showModal = false;
    public $methodId;
    public $name = '';
    public $type = 'cash';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|min:3|unique:payment_methods,name',
        'type' => 'required|in:cash,transfer,card,qr',
    ];

    public function render()
    {
        $paymentMethods = PaymentMethod::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.settings.payment-methods', [
            'paymentMethods' => $paymentMethods,
            'methods' => $paymentMethods,
        ])->layout('layouts.app');
    }

    public function toggleStatus($id)
    {
        $method = PaymentMethod::findOrFail($id);
        $method->is_active = !$method->is_active;
        $method->save();

        \Illuminate\Support\Facades\Cache::forget('active_payment_methods');

        session()->flash('message', "Método '{$method->name}' " . ($method->is_active ? 'activado' : 'desactivado') . " correctamente.");
    }

    public function create()
    {
        $this->resetForm();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $method = PaymentMethod::findOrFail($id);
        $this->methodId = $id;
        $this->name = $method->name;
        $this->type = $method->type;
        $this->is_active = $method->is_active;
        $this->isOpen = true;
    }

    public function resetForm()
    {
        $this->methodId = null;
        $this->name = '';
        $this->type = 'cash';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function store()
    {
        $rules = $this->rules;
        if ($this->methodId) {
            $rules['name'] = 'required|min:3|unique:payment_methods,name,' . $this->methodId;
        }

        $this->validate($rules);

        PaymentMethod::updateOrCreate(['id' => $this->methodId], [
            'name' => $this->name,
            'type' => $this->type,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', $this->methodId ? 'Método actualizado.' : 'Método creado.');
        $this->isOpen = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        $method = PaymentMethod::findOrFail($id);
        $method->delete();
        session()->flash('message', 'Método eliminado.');
    }
}

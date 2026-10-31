<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use App\Models\CustomerTransaction;
use App\Models\CashSession;
use App\Models\CashMovement;
use Livewire\Component;
use App\Livewire\BaseCrudComponent;
use Livewire\Attributes\Rule;

class Index extends BaseCrudComponent
{
    public $customerId;

    #[Rule('required|min:3')]
    public $name;

    #[Rule('required')] 
    public $ruc; // Custom validation in store/update

    public $email;
    public $phone;
    public $address;
    public $credit_limit = null;
    
    public $totalReceivable = 0;
    
    public $is_active = true;

    // Payment Logic
    public $showPaymentModal = false;
    public $paymentAmount;
    public $paymentMethod = 'efectivo';
    public $paymentDescription;
    public $paymentCustomerId;
    public $paymentCustomerName;
    public $paymentCustomerBalance;

    // History Logic
    public $showHistoryModal = false;
    public $historyTransactions = [];
    public $historyCustomerName;

    public function render()
    {
        $this->calculateTotalReceivable();

        $customers = Customer::search($this->search, ['name', 'ruc'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.customers.index', [
            'customers' => $customers
        ])->layout('layouts.app');
    }

    public function calculateTotalReceivable()
    {
        $debits = CustomerTransaction::whereHas('customer')->where('type', 'debit')->sum('amount');
        $credits = CustomerTransaction::whereHas('customer')->where('type', 'credit')->sum('amount');
        $this->totalReceivable = $debits - $credits;
    }

    protected function resetInputFields()
    {
        $this->customerId = null;
        $this->name = '';
        $this->ruc = '';
        $this->email = '';
        $this->phone = '';
        $this->address = '';
        $this->credit_limit = null;
        $this->is_active = true;
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|min:3',
            'ruc' => [
                'required', 
                \Illuminate\Validation\Rule::unique('customers')->ignore($this->customerId)
            ],
            'email' => 'nullable|email',
            'phone' => 'nullable|min:6',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        Customer::updateOrCreate(['id' => $this->customerId], [
            'name' => $this->name,
            'ruc' => $this->ruc,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'credit_limit' => $this->credit_limit ? (float)$this->credit_limit : null,
            'is_active' => $this->is_active
        ]);

        session()->flash('message', $this->customerId ? 'Cliente actualizado correctamente.' : 'Cliente creado correctamente.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $this->customerId = $id;
        $this->name = $customer->name;
        $this->ruc = $customer->ruc;
        $this->email = $customer->email;
        $this->phone = $customer->phone;
        $this->address = $customer->address;
        $this->credit_limit = $customer->credit_limit;
        $this->is_active = $customer->is_active;

        $this->openModal();
    }

    public function delete($id)
    {
        if (!auth()->user()->can('admin')) {
            session()->flash('message', 'No tienes permiso para eliminar clientes.');
            return;
        }

        Customer::find($id)->delete();
        session()->flash('message', 'Cliente eliminado correctamente.');
    }

    public function deleteSelected()
    {
        if (!auth()->user()->can('admin')) {
            session()->flash('message', 'No tienes permiso para eliminar clientes.');
            return;
        }

        if (empty($this->selected)) return;
        
        Customer::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        session()->flash('message', 'Clientes seleccionados eliminados correctamente.');
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = Customer::where(function($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('ruc', 'like', '%' . $this->search . '%');
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    // History Logic
    public function openHistoryModal($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        $this->paymentCustomerId = $customer->id;
        $this->historyCustomerName = $customer->name;
        $this->historyTransactions = $customer->transactions()->orderBy('created_at', 'desc')->get();
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal()
    {
        $this->showHistoryModal = false;
        $this->reset(['historyTransactions', 'historyCustomerName']);
    }

    // Payment Methods
    public function openPaymentModal($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        $this->paymentCustomerId = $customer->id;
        $this->paymentCustomerName = $customer->name;
        $this->paymentCustomerBalance = $customer->balance;
        
        $this->paymentAmount = '';
        $this->paymentMethod = 'efectivo';
        $this->paymentDescription = 'Cobro a cuenta';
        $this->showPaymentModal = true;
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->reset(['paymentAmount', 'paymentMethod', 'paymentDescription', 'paymentCustomerId', 'paymentCustomerName', 'paymentCustomerBalance']);
    }

    public function registerPayment()
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:1',
            'paymentMethod' => 'required|in:efectivo,transferencia,qr,tarjeta',
            'paymentDescription' => 'nullable|string'
        ]);

        if ($this->paymentCustomerBalance <= 0) {
            $this->addError('paymentAmount', 'El cliente no tiene deuda pendiente.');
            return;
        }

        if ($this->paymentAmount > $this->paymentCustomerBalance) {
             $this->addError('paymentAmount', 'El monto a cobrar no puede superar el saldo pendiente (₲ ' . format_pyg($this->paymentCustomerBalance) . ').');
             return;
        }

        try {
            \DB::transaction(function () {
                 // 1. Create Credit Transaction
                 CustomerTransaction::create([
                     'customer_id' => $this->paymentCustomerId,
                     'type' => 'credit',
                     'amount' => $this->paymentAmount,
                     'reference_type' => 'payment',
                     'description' => $this->paymentDescription
                 ]);

                 // 2. Add to Cash Movement / Drawer
                 $session = CashSession::where('user_id', auth()->id())
                     ->where('status', 'open')
                     ->first();

                 if ($session) {
                     CashMovement::create([
                         'cash_session_id' => $session->id,
                         'type'            => 'in',
                         'amount'          => $this->paymentAmount,
                         'description'     => 'Cobro a Cliente (' . strtoupper($this->paymentMethod) . '): ' . $this->paymentCustomerName,
                         'reference'       => 'CUST-' . $this->paymentCustomerId
                     ]);
                 } elseif ($this->paymentMethod === 'efectivo') {
                     throw new \Exception('Debes tener una caja abierta para recibir pagos en efectivo.');
                 }
            });

            session()->flash('message', 'Pago registrado correctamente.');
            $this->closePaymentModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }
}

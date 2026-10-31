<?php

namespace App\Livewire\Cash;

use Livewire\Component;
use App\Models\CashSession;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SessionManager extends Component
{
    public $currentSession;
    public $openingAmount = ''; // Input for opening
    public $openingNotes = ''; // Input for opening notes
    public $closingAmount = ''; // Input for closing
    public $notes = '';
    
    public $selectedRegisterId = ''; // For selecting physical register
    
    // Manual movements
    public $movementAmount = '';
    public $movementDescription = '';
    public $movementType = 'in'; // 'in' or 'out'
    public $movementCategory = '';
    public $movementModal = false;


    // Computed totals
    public $totalSales = 0;
    public $totalCashSales = 0;
    public $totalIn = 0;
    public $totalOut = 0;
    public $salesCount = 0;
    public $salesByPaymentMethod = [];

    public function mount()
    {
        $this->loadCurrentSession();
    }

    public function getRegistersProperty()
    {
        // Get all active registers and check their availability
        return \App\Models\CashRegister::where('active', true)->get()->map(function($register) {
            $register->is_occupied = $register->currentSession()->exists();
            $register->current_user_name = $register->is_occupied 
                ? ($register->currentSession->user?->name ?? 'Usuario no encontrado')
                : null;
            return $register;
        });
    }

    public function loadCurrentSession()
    {
        // Find latest open session for this user
        $this->currentSession = CashSession::where('user_id', Auth::id())
            ->where('status', 'open')
            ->latest()
            ->first();

        if ($this->currentSession) {
            // Calculate totals since opening (excluding voided sales)
            $sales = Sale::with('payments')
                ->where('user_id', Auth::id())
                ->where('created_at', '>=', $this->currentSession->opened_at)
                ->where('status', '!=', 'voided')
                ->get();
            
            $this->totalSales = $sales->sum('total');
            
            // Calculate total cash sales from payments
            $this->totalCashSales = $sales->flatMap->payments
                ->filter(function($payment) {
                    return str_starts_with(strtolower($payment->method), 'efectivo');
                })
                ->sum('amount');
                
            // Calculate sales by payment method for breakdown
            $this->salesByPaymentMethod = $sales->flatMap->payments
                ->groupBy('method')
                ->map(function($payments) {
                    return $payments->sum('amount');
                })->toArray();

            $this->salesCount = $sales->count();

            // Calculate movements
            $this->totalIn = $this->currentSession->movements()->where('type', 'in')->sum('amount');
            $this->totalOut = $this->currentSession->movements()->where('type', 'out')->sum('amount');
        }
    }

    public function openSession()
    {
        // Clean amount
        $this->openingAmount = parse_pyg($this->openingAmount);

        // Authorization check - verify user can create a new session
        $this->authorize('create', CashSession::class);

        $this->validate([
            'openingAmount' => 'required|numeric|min:0',
            'selectedRegisterId' => ['required', 'exists:cash_registers,id', function ($attribute, $value, $fail) {
                 $isOccupied = CashSession::where('cash_register_id', $value)
                     ->where('status', 'open')
                     ->whereHas('user', function($q) {
                         $q->whereNull('deleted_at');
                     })
                     ->exists();
                 if ($isOccupied) {
                     $fail('Esta caja ya ha sido ocupada por otro usuario.');
                 }
            }],
        ], [
            'selectedRegisterId.required' => 'Debes seleccionar una caja física.',
        ]);

        CashSession::create([
            'user_id' => Auth::id(),
            'cash_register_id' => $this->selectedRegisterId,
            'opening_amount' => $this->openingAmount,
            'opened_at' => now(),
            'status' => 'open',
            'opening_notes' => $this->openingNotes
        ]);

        $this->reset(['openingAmount', 'openingNotes', 'selectedRegisterId']);
        
        return redirect()->route('pos.index');
    }

    public function closeSession()
    {
        abort_unless($this->currentSession, 403);

        $this->validate([
            'closingAmount' => 'required',
        ]);

        $closingAmount = parse_pyg($this->closingAmount);
        
        // Final calculations
        $expectedAmount = $this->currentSession->opening_amount + $this->totalCashSales + $this->totalIn - $this->totalOut;
        $difference = $closingAmount - $expectedAmount;

        $this->currentSession->update([
            'closing_amount' => $closingAmount,
            'calculated_amount' => $expectedAmount,
            'difference' => $difference,
            'closed_at' => now(),
            'status' => 'closed',
            'notes' => $this->notes
        ]);

        $this->reset(['closingAmount', 'notes']);
        $this->loadCurrentSession();

        session()->flash('message', 'Sesión cerrada correctamente. Balance: ' . format_pyg($difference));
    }


    public function openMovementModal()
    {
        $this->reset(['movementAmount', 'movementDescription', 'movementType']);
        $this->movementModal = true;
    }

    public function registerMovement()
    {
        abort_unless($this->currentSession, 403);
        
        $this->validate([
            'movementAmount' => 'required',
            'movementType' => 'required|in:in,out',
        ]);

        $amount = parse_pyg($this->movementAmount);
        if ($amount <= 0) {
            $this->addError('movementAmount', 'El monto debe ser mayor a 0.');
            return;
        }

        $this->currentSession->movements()->create([
            'type' => $this->movementType,
            'amount' => $amount,
            'category' => $this->movementCategory ?: null,
            'description' => $this->movementDescription,
            'user_id' => Auth::id()
        ]);

        $this->loadCurrentSession();
        $this->reset(['movementAmount', 'movementDescription', 'movementModal']);
        session()->flash('message', ($this->movementType === 'in' ? 'Entrada' : 'Salida') . ' registrada correctamente.');
    }



    public function getAllSessionsProperty()
    {
        // Only used by admin view — returns all open sessions with user + register
        return \App\Models\CashSession::with(['user', 'register'])
            ->where('status', 'open')
            ->orderBy('opened_at', 'asc')
            ->get()
            ->map(function ($s) {
                $s->is_stale = $s->opened_at->diffInHours(now()) >= 24;
                $s->hours_open = $s->opened_at->diffInHours(now());
                return $s;
            });
    }

    public function forceCloseSession($sessionId)
    {
        abort_unless(auth()->user()->can('admin'), 403);

        $session = \App\Models\CashSession::where('status', 'open')->findOrFail($sessionId);

        $session->update([
            'closing_amount'    => 0,
            'calculated_amount' => 0,
            'difference'        => 0,
            'closed_at'         => now(),
            'status'            => 'closed',
            'notes'             => '[Cierre forzado por administrador: ' . auth()->user()->name . ']',
        ]);

        session()->flash('message', 'Sesión de ' . $session->user?->name . ' cerrada forzosamente.');
    }

    public function render()
    {
        $movementCategories = \App\Models\CashMovementCategory::where('is_active', true)
            ->where(function($q) {
                $q->where('type', $this->movementType == 'in' ? 'income' : 'expense')
                  ->orWhere('type', 'both');
            })
            ->get();

        return view('livewire.cash.session-manager', [
            'allSessions' => auth()->user()->can('admin') ? $this->allSessions : collect(),
            'movementCategories' => $movementCategories,
        ])->layout('layouts.app');
    }
}

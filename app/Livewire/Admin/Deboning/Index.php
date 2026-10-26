<?php

namespace App\Livewire\Admin\Deboning;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DeboningSession;
use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedSession = null;
    public bool $showDetailsModal = false;

    // Cierre Mensual & Merma
    public bool $showClosureModal = false;
    public $selectedRawMaterialId = '';
    public $physicalRemaining = 0.00;
    public $closureNotes = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewDetails(string $sessionId): void
    {
        $this->selectedSession = DeboningSession::with(['rawMaterial', 'user', 'items.product'])->find($sessionId);
        $this->showDetailsModal = true;
    }

    public function closeDetails(): void
    {
        $this->showDetailsModal = false;
        $this->selectedSession = null;
    }

    public function openClosureModal(): void
    {
        $firstRaw = Product::where('is_raw_material', true)->where('is_active', true)->first();
        $this->selectedRawMaterialId = $firstRaw ? $firstRaw->id : '';
        $this->physicalRemaining = 0.00;
        $this->closureNotes = '';
        $this->showClosureModal = true;
    }

    public function closeClosureModal(): void
    {
        $this->showClosureModal = false;
    }

    public function getRawMaterialsProperty()
    {
        return Product::where('is_raw_material', true)->where('is_active', true)->orderBy('name')->get();
    }

    public function getSelectedRawMaterialProperty()
    {
        return $this->selectedRawMaterialId ? Product::find($this->selectedRawMaterialId) : null;
    }

    public function getCalculatedWasteProperty(): float
    {
        $currentStock = $this->selectedRawMaterial ? (float)$this->selectedRawMaterial->stock : 0.0;
        return max(0.0, $currentStock - (float)($this->physicalRemaining ?: 0));
    }

    public function processMonthlyClosure(): void
    {
        $this->validate([
            'selectedRawMaterialId' => 'required|exists:products,id',
            'physicalRemaining'     => 'required|numeric|min:0',
        ]);

        DB::transaction(function () {
            $rawMaterial = Product::lockForUpdate()->findOrFail($this->selectedRawMaterialId);
            $currentStock = (float)$rawMaterial->stock;
            $physical = (float)$this->physicalRemaining;
            $waste = max(0.0, $currentStock - $physical);

            // 1. Registrar sesión de cierre en historial
            DeboningSession::create([
                'raw_material_product_id' => $rawMaterial->id,
                'user_id'                 => auth()->id(),
                'total_weight_start'      => $currentStock,
                'total_weight_yield'      => $physical,
                'waste_weight'            => $waste,
                'status'                  => 'completed',
                'notes'                   => 'Cierre mensual / liquidación de carne global. Remanente real: ' . number_format($physical, 2) . ' Kg. Merma: ' . number_format($waste, 2) . ' Kg. ' . $this->closureNotes,
            ]);

            // 2. Registrar movimiento de inventario por la merma si hubo merma
            if ($waste > 0) {
                InventoryMovement::create([
                    'product_id' => $rawMaterial->id,
                    'user_id'    => auth()->id(),
                    'type'       => 'out',
                    'quantity'   => $waste,
                    'reason'     => 'Merma de Carnicería (Cierre de Mes)',
                    'notes'      => 'Ajuste mensual de merma de carne global en base a ventas de balanza y cortes derivados.',
                ]);
            }

            // 3. Ajustar stock del producto padre al remanente físico real
            $rawMaterial->stock = $physical;
            $rawMaterial->save();

            $branchId = auth()->user()->branch_id ?? \App\Models\Branch::first()?->id;
            if ($branchId) {
                $rawMaterial->branchStocks()->updateOrCreate(
                    ['branch_id' => $branchId],
                    ['stock' => $physical]
                );
            }
        });

        session()->flash('message', '¡Cierre de carnicería completado! Se liquidaron ' . number_format($this->calculatedWaste, 2) . ' Kg como merma del mes.');
        $this->showClosureModal = false;
        $this->resetPage();
    }

    public function render()
    {
        abort_unless(auth()->user()->can('admin') || auth()->user()->can('manager'), 403);
        abort_unless(hasModule('meat_deboning'), 404);

        $sessions = DeboningSession::with(['rawMaterial', 'user', 'items.product'])
            ->whereHas('rawMaterial', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.deboning.index', [
            'sessions' => $sessions
        ])->layout('layouts.app');
    }
}

<?php

namespace App\Livewire\Purchases;

use Livewire\Component;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;

class Create extends Component
{
    public $supplier_id;
    public $purchase_date;
    public $reference_number;
    public $notes;
    public $search = '';
    public $items = [];
    public $total = 0;

    public function mount()
    {
        $this->purchase_date = date('Y-m-d');
    }

    public function addItem($productId, $variationId = null)
    {
        $product = Product::find($productId);
        if (!$product) return;

        $variation = $variationId ? \App\Models\ProductVariation::find($variationId) : null;
        $key = $variationId ? "p{$productId}v{$variationId}" : "p{$productId}";

        if (isset($this->items[$key])) {
            $this->items[$key]['quantity'] = (float)$this->items[$key]['quantity'] + 1;
        } else {
            $this->items[$key] = [
                'id' => $product->id,
                'variation_id' => $variationId,
                'name' => $product->name . ($variation ? " [{$variation->name}]" : ""),
                'cost_price' => $variation ? ($variation->price_cost ?: $product->price_cost) : $product->price_cost,
                'quantity' => $product->unit_type === 'kg' ? 1.000 : 1,
            ];
        }
        $this->calculateTotal();
        $this->search = '';
    }

    public function removeItem($key)
    {
        unset($this->items[$key]);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total = array_reduce($this->items, function($carry, $item) {
            $qty = (float) str_replace(',', '.', (string)($item['quantity'] ?? 0));
            $cost = (float)($item['cost_price'] ?? 0);
            return $carry + round($cost * $qty);
        }, 0);
    }

    public function save()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.quantity' => 'required|numeric|gt:0',
            'items.*.cost_price' => 'required|numeric|min:0',
        ]);

        $branchId = auth()->user()->branch_id ?? \App\Models\Branch::first()?->id;

        DB::transaction(function() use ($branchId) {
            $purchase = Purchase::create([
                'supplier_id' => $this->supplier_id,
                'user_id' => auth()->id(),
                'purchase_date' => $this->purchase_date,
                'total' => $this->total,
                'reference_number' => $this->reference_number,
                'notes' => $this->notes,
            ]);

            foreach ($this->items as $item) {
                $qty = (float) str_replace(',', '.', (string)$item['quantity']);
                $cost = (float) $item['cost_price'];
                $subtotal = (int) round($cost * $qty);

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['id'],
                    'product_variation_id' => $item['variation_id'] ?? null,
                    'quantity' => $qty,
                    'cost_price' => $cost,
                    'subtotal' => $subtotal,
                ]);

                $product = Product::find($item['id']);
                if ($product && $product->manage_stock) {
                    if ($item['variation_id']) {
                        $variation = \App\Models\ProductVariation::find($item['variation_id']);
                        if ($variation) {
                            $variation->increment('stock', $qty);
                            $variation->update(['price_cost' => $cost]);
                            if ($branchId) {
                                $vBranchStock = $variation->branchStocks()->firstOrCreate(
                                    ['branch_id' => $branchId],
                                    ['stock' => 0]
                                );
                                $vBranchStock->increment('stock', $qty);
                            }
                        }
                    } else {
                        $product->increment('stock', $qty);
                        if ($branchId) {
                            $pBranchStock = $product->branchStocks()->firstOrCreate(
                                ['branch_id' => $branchId],
                                ['stock' => 0]
                            );
                            $pBranchStock->increment('stock', $qty);
                        }
                    }

                    \App\Models\InventoryMovement::create([
                        'product_id' => $product->id,
                        'user_id'    => auth()->id(),
                        'type'       => 'in',
                        'quantity'   => $qty,
                        'reason'     => 'Compra a Proveedor #' . ($this->reference_number ?: $purchase->id),
                        'notes'      => 'Ingreso de mercadería por factura de compra.',
                    ]);
                }
                
                // Actualizar precio de costo del producto base
                if (!$item['variation_id'] && $product) {
                    $product->update(['price_cost' => $cost]);
                }
            }
        });

        session()->flash('message', 'Compra registrada correctamente. El stock ha sido actualizado.');
        return redirect()->route('purchases.index');
    }

    public function render()
    {
        $suppliers = Supplier::where('active', true)->orderBy('name')->get();
        $productsList = [];
        if (strlen($this->search) > 1) {
            $productsList = Product::with('variations')
                ->where('is_active', true)
                ->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%')
                      ->orWhereHas('variations', function($vq) {
                          $vq->where('sku', 'like', '%' . $this->search . '%');
                      });
                })
                ->take(10)
                ->get();
        }

        return view('livewire.purchases.create', [
            'suppliers' => $suppliers,
            'productsList' => $productsList
        ])->layout('layouts.app');
    }
}

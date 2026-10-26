<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Validation\Rule;
use App\Models\CustomerTransaction;

class SalesInterface extends Component
{
    public $search = '';
    public $cart = [];
    public $total = 0;
    public $selectedCategory = null;
    public $lastSaleId;
    public $rates = [];
    
    // Variation Selection
    public $showVariationModal = false;
    public $selectingProduct = null;
    public $variationId = '';

    // Discount
    public $discountProductEnabled = false;
    public $discountManualEnabled  = false;
    public $manualDiscountPct = 0;  // 0-100
    public $discountAmount = 0;
    public $subtotalBeforeDiscount = 0;
    
    // Customer Management
    public $selectedCustomer = null;
    public $customerSearch = '';
    public $searchResults = [];
    public $showCustomerModal = false;
    
    // New Customer Form
    public $newCustomerName = '';
    public $newCustomerRuc = '';
    public $newCustomerEmail = '';
    public $newCustomerPhone = '';
    public $newCustomerAddress = '';
    public $newCustomerIsActive = true;

    // New Product Form
    public $showProductModal = false;
    public $newProductName = '';
    public $newProductSku = '';
    public $newProductCategoryId = '';
    public $newProductPriceCost = '';
    public $newProductPriceSale = '';
    public $newProductStock = 0;
    public $newProductMinStock = 5;
    public $newProductUnitType = 'unit';
    public $newProductManageStock = true;
    public $newProductIsActive = true;

    // Session Status Logic
    public $showStatusModal = false;
    public $sessionDetails = [
        'opened_at' => null,
        'opening_amount' => 0,
        'sales_count' => 0,
        'sales_total' => 0,
        'total_in' => 0,
        'total_out' => 0,
        'expected_total' => 0
    ];

    // Void Sale from POS
    public $showVoidModal = false;
    public $voidSearch = '';
    public $recentSales = [];

    public $currentRegisterName = null;
    public $sessionOpenedAt = null;

    // Web Orders Integration
    public $pendingWebOrders = [];
    public $showWebOrdersModal = false;
    public $importingWebOrderId = null;

    // Restaurant Module
    public $isRestaurantMode = false;
    public $showTableMap = true;         // true = show table map; false = active sale screen
    public $activeTableId = null;        // the RestaurantTable the mozo selected
    public $activeWaiterId = null;       // waiter assigned to this table
    public $activeSaleId = null;         // persisted pending sale UUID
    public $showWaiterModal = false;     // waiter selection modal
    public $showSendComandaModal = false; // confirm send-to-production modal

    public function mount()
    {
        // Check Session
        $session = \App\Models\CashSession::with('register')
            ->where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$session) {
            return redirect()->route('cash.index')->with('message', 'Debes abrir caja para poder vender.');
        }

        // Store session info for the header badge
        $this->currentRegisterName = $session->register?->name ?? 'Caja';
        $this->sessionOpenedAt    = $session->opened_at->format('H:i');

        // Load Rates
        $settings = \App\Models\StoreSetting::whereIn('key', [
            'currency_usd', 'currency_brl', 'currency_ars', 'currency_eur',
            'discount_product_enabled', 'discount_manual_enabled'
        ])->pluck('value', 'key');

        $this->rates = [
            'usd' => (float) $settings->get('currency_usd', 0),
            'brl' => (float) $settings->get('currency_brl', 0),
            'ars' => (float) $settings->get('currency_ars', 0),
            'eur' => (float) $settings->get('currency_eur', 0),
        ];

        $this->discountProductEnabled = $settings->get('discount_product_enabled', '0') === '1';
        $this->discountManualEnabled  = $settings->get('discount_manual_enabled', '0') === '1';

        // Restaurant mode
        $this->isRestaurantMode = hasModule('restaurant');

        $this->loadPaymentMethods();
    }

    public function loadPaymentMethods()
    {
        $this->paymentMethods = \Illuminate\Support\Facades\Cache::remember('active_payment_methods', 3600, function () {
            return \App\Models\PaymentMethod::where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }

    // ─── Restaurant: Table Map ─────────────────────────────────────────────────

    public function openTable($tableId)
    {
        $table = \App\Models\RestaurantTable::findOrFail($tableId);

        if ($table->status === 'occupied' && $table->current_sale_id) {
            // Load the existing pending sale
            $pendingSale = \App\Models\Sale::with('items.product')->find($table->current_sale_id);
            if ($pendingSale) {
                $this->activeSaleId  = $pendingSale->id;
                $this->activeTableId = $tableId;
                $this->activeWaiterId = $pendingSale->waiter_id;
                $this->cart = $pendingSale->items->map(fn($item) => [
                    'id'             => $item->product_id,
                    'variation_id'   => $item->product_variation_id ?? null,
                    'name'           => $item->product_name,
                    'quantity'       => $item->quantity,
                    'price'          => $item->price_unit,
                    'original_price' => $item->price_unit,
                    'subtotal'       => $item->subtotal,
                    'unit_type'      => $item->product?->unit_type ?? 'unit',
                    'sku'            => $item->product?->sku ?? '',
                    'discount_pct'   => 0,
                    'sale_item_id'   => $item->id,
                ])->keyBy(fn($i) => $i['variation_id'] ? 'v'.$i['variation_id'] : 'p'.$i['id'])->toArray();
                $this->calculateTotal();
                $this->showTableMap = false;
                return;
            }
        }

        // New table: pick a waiter first
        $this->activeTableId = $tableId;
        $this->showWaiterModal = true;
    }

    public function selectWaiter($waiterId)
    {
        $this->activeWaiterId = $waiterId;
        $this->showWaiterModal = false;
        $this->showTableMap = false;
        $this->cart = [];
        $this->calculateTotal();
    }

    public function backToTableMap()
    {
        // Save current cart as pending sale before going back
        if (!empty($this->cart) && $this->activeTableId) {
            $this->saveAsPendingSale();
        }
        $this->showTableMap = true;
        $this->activeTableId = null;
        $this->activeWaiterId = null;
        $this->activeSaleId = null;
        $this->cart = [];
        $this->total = 0;
    }

    public function saveAsPendingSale()
    {
        if (!$this->activeTableId) return;

        $saleData = [
            'user_id'             => auth()->id(),
            'restaurant_table_id' => $this->activeTableId,
            'waiter_id'           => $this->activeWaiterId,
            'total'               => $this->total,
            'status'              => 'pending',
        ];

        if ($this->activeSaleId) {
            $sale = \App\Models\Sale::findOrFail($this->activeSaleId);
            $sale->update($saleData);
            $sale->items()->delete();
        } else {
            $sale = \App\Models\Sale::create($saleData);
            $this->activeSaleId = $sale->id;

            // Mark table as occupied
            \App\Models\RestaurantTable::where('id', $this->activeTableId)->update([
                'status'          => 'occupied',
                'current_sale_id' => $sale->id,
            ]);
        }

        // Re-insert all items (calculate subtotal if missing)
        foreach ($this->cart as $item) {
            $sale->items()->create([
                'product_id'           => $item['id'] ?? null,
                'product_variation_id' => $item['variation_id'] ?? null,
                'product_name'         => $item['name'],
                'quantity'             => $item['quantity'],
                'price_unit'           => $item['price'],
                'subtotal'             => $item['subtotal'] ?? ($item['price'] * $item['quantity']),
            ]);
        }
    }

    public function sendComanda()
    {
        if (empty($this->cart)) {
            $this->dispatch('error-movement', 'El carrito está vacío.');
            return;
        }

        // Persist if not yet saved
        $this->saveAsPendingSale();

        // Get items not yet sent to production
        $sale = \App\Models\Sale::with(['items.product'])->find($this->activeSaleId);
        $unsentItems = $sale->items->filter(fn($i) => !$i->is_sent_to_production);

        if ($unsentItems->isEmpty()) {
            $this->dispatch('error-movement', 'No hay nuevos platos para enviar. Todos ya fueron marchados.');
            return;
        }

        // Mark items as sent
        $sale->items()->whereIn('id', $unsentItems->pluck('id'))->update(['is_sent_to_production' => true]);

        // Build payload and redirect to comanda print view
        $payload = base64_encode(json_encode([
            'table'  => \App\Models\RestaurantTable::find($this->activeTableId)?->name,
            'waiter' => \App\Models\Waiter::find($this->activeWaiterId)?->name,
            'items'  => $unsentItems->map(fn($i) => [
                'name'     => $i->product_name,
                'qty'      => $i->quantity,
                'zone'     => $i->product?->production_zone ?? 'none',
            ])->values()->toArray(),
        ]));

        $this->dispatch('openComanda', url: route('restaurant.comanda.print', ['payload' => $payload]));
    }

    #[Computed]
    public function products()
    {
        return Product::with(['branchStocks', 'variations.branchStocks'])
            ->where('is_active', true)
            ->where('is_raw_material', false)
            ->where(function($query) {
                // MySQL uses LIKE for case-insensitive search
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedCategory, function($query) {
                $query->where('category_id', $this->selectedCategory);
            })
            ->take(20)
            ->get();
    }

    public function render()
    {
        $currentSession = \App\Models\CashSession::with('register')->where('user_id', auth()->id())->where('status', 'open')->first();
        $branchId = $currentSession?->register?->branch_id;

        $categories = \Illuminate\Support\Facades\Cache::remember('active_categories', 3600, function () {
            return Category::where('is_active', true)->get();
        });

        $paymentMethods = \App\Models\PaymentMethod::where('is_active', true)->orderBy('name')->get();
        $this->paymentMethods = $paymentMethods;

        return view('livewire.pos.sales-interface', [
            'products'             => $this->products,
            'categories'           => $categories,
            'branchId'             => $branchId,
            'paymentMethods'       => $paymentMethods,
            'movementCategories'   => \App\Models\CashMovementCategory::where('is_active', true)
                ->where(function($q) {
                    $q->where('type', $this->movementType == 'in' ? 'income' : 'expense')
                      ->orWhere('type', 'both');
                })
                ->get(),
            'pendingWebOrdersCount' => \App\Models\WebOrder::whereIn('status', ['Pendiente WhatsApp', 'Atendido'])->count(),
            // Restaurant
            'restaurantTables'      => $this->isRestaurantMode ? \App\Models\RestaurantTable::with('currentSale')->get() : collect(),
            'waiters'               => $this->isRestaurantMode ? \App\Models\Waiter::where('active', true)->get() : collect(),
            'activeTable'           => $this->activeTableId ? \App\Models\RestaurantTable::find($this->activeTableId) : null,
        ])->layout('layouts.pos');
    }

    public function filterCategory($categoryId)
    {
        $this->selectedCategory = $categoryId === $this->selectedCategory ? null : $categoryId;
    }

    public function updatedSearch($value)
    {
        $term = trim((string)$value);
        if (empty($term)) return;

        // Auto-detección: si el texto leído en el buscador coincide con un código de barra / SKU,
        // se agrega de forma 100% automática al carrito y se limpia el buscador de inmediato:
        $this->tryAutoAddBarcode($term);
    }

    public function handleSearchSubmit($scannedTerm = null)
    {
        $term = trim($scannedTerm !== null ? (string)$scannedTerm : (string)$this->search);
        $this->search = '';

        if (empty($term)) {
            $this->dispatch('product-scanned');
            return;
        }

        $added = $this->tryAutoAddBarcode($term, true);
        if (!$added) {
            $this->dispatch('error-movement', "Producto con código '{$term}' no encontrado.");
            $this->dispatch('beep-error');
            $this->dispatch('product-scanned');
        }
    }

    protected function tryAutoAddBarcode($term, $isExplicitSubmit = false): bool
    {
        $term = trim((string)$term);
        if (empty($term)) return false;

        // 1. EAN-13 Scale Barcode Logic (prefixes 20 through 29)
        if (strlen($term) === 13 && preg_match('/^2[0-9]/', $term)) {
            $skuCode4 = substr($term, 2, 4);
            $weightString4 = substr($term, 6, 5);
            $weightInKg4 = (float)$weightString4 / 1000;

            $skuCode5 = substr($term, 2, 5);
            $weightString5 = substr($term, 7, 5);
            $weightInKg5 = (float)$weightString5 / 1000;

            $product = Product::where('is_active', true)->whereIn('sku', [
                $skuCode4, ltrim($skuCode4, '0'),
                $skuCode5, ltrim($skuCode5, '0')
            ])->first();

            $weightInKg = ($product && ($product->sku === $skuCode5 || $product->sku === ltrim($skuCode5, '0'))) 
                ? $weightInKg5 
                : $weightInKg4;

            if ($product) {
                if ($product->has_variations) {
                    $this->selectingProduct = $product;
                    $this->showVariationModal = true;
                } else {
                    $this->addToCartInternal($product, null, $weightInKg);
                }
                $this->search = '';
                $this->dispatch('product-scanned');
                $this->dispatch('beep-success');
                return true;
            }
        }

        // 2. Coincidencia directa por SKU (exacto, sin ceros o con TRIM)
        $cleanTermNoZeros = ltrim($term, '0');
        $skusToSearch = array_unique(array_filter([$term, $cleanTermNoZeros]));

        $productExact = Product::where('is_active', true)
            ->where(function($q) use ($skusToSearch, $term, $cleanTermNoZeros) {
                $q->whereIn('sku', $skusToSearch)
                  ->orWhereRaw('TRIM(sku) = ?', [$term])
                  ->orWhereRaw('TRIM(sku) = ?', [$cleanTermNoZeros]);
            })
            ->orderBy('is_raw_material', 'asc')
            ->first();

        if ($productExact) {
            if ($productExact->has_variations) {
                $this->selectingProduct = $productExact;
                $this->showVariationModal = true;
            } else {
                $this->addToCartInternal($productExact);
            }
            $this->search = '';
            $this->dispatch('product-scanned');
            $this->dispatch('beep-success');
            return true;
        }

        // 3. Match in ProductVariation by SKU
        $variationExact = \App\Models\ProductVariation::with('product')
            ->where('is_active', true)
            ->where(function($q) use ($skusToSearch, $term, $cleanTermNoZeros) {
                $q->whereIn('sku', $skusToSearch)
                  ->orWhereRaw('TRIM(sku) = ?', [$term])
                  ->orWhereRaw('TRIM(sku) = ?', [$cleanTermNoZeros]);
            })
            ->first();

        if ($variationExact && $variationExact->product && $variationExact->product->is_active) {
            $this->addToCartInternal($variationExact->product, $variationExact);
            $this->search = '';
            $this->dispatch('product-scanned');
            $this->dispatch('beep-success');
            return true;
        }

        // 4. Fallback search: if explicit submit (Enter/Tab) and single product match
        if ($isExplicitSubmit) {
            $matchingProducts = Product::where('is_active', true)
                ->where(function($query) use ($skusToSearch, $term) {
                    $query->whereIn('sku', $skusToSearch)
                          ->orWhere('name', $term);
                })
                ->get();

            if ($matchingProducts->count() === 1) {
                $singleProduct = $matchingProducts->first();
                if ($singleProduct->has_variations) {
                    $this->selectingProduct = $singleProduct;
                    $this->showVariationModal = true;
                } else {
                    $this->addToCartInternal($singleProduct);
                }
                $this->search = '';
                $this->dispatch('product-scanned');
                $this->dispatch('beep-success');
                return true;
            }
        }

        return false;
    }

    public function addToCart($productId)
    {
        $product = Product::with('variations')->where('is_raw_material', false)->find($productId);
        if (!$product) return;

        if ($product->has_variations) {
            $this->selectingProduct = $product;
            $this->showVariationModal = true;
            return;
        }

        $this->addToCartInternal($product);
    }

    public function selectVariation($variationId)
    {
        $variation = \App\Models\ProductVariation::find($variationId);
        if (!$variation) return;

        $this->addToCartInternal($this->selectingProduct, $variation);
        $this->showVariationModal = false;
        $this->selectingProduct = null;
    }

    protected function addToCartInternal($product, $variation = null, $quantity = 1)
    {
        $cartKey = $variation ? 'v' . $variation->id : 'p' . $product->id;
        
        $name = $product->name . ($variation ? ' (' . $variation->name . ')' : '');
        $price = $variation ? $variation->price_sale : $product->price_sale;
        $sku = $variation ? $variation->sku : $product->sku;
        $stock = $variation ? $variation->stock : $product->stock; // Fallback to total stock

        $currentSession = \App\Models\CashSession::with('register')->where('user_id', auth()->id())->where('status', 'open')->first();
        if ($currentSession && $currentSession->register && $currentSession->register->branch_id) {
            $branchId = $currentSession->register->branch_id;
            $stockable = $variation ? $variation : $product;
            $branchStock = $stockable->branchStocks()->where('branch_id', $branchId)->first();
            $stock = $branchStock ? $branchStock->stock : 0;
        }

        // Stock Validation
        if ($product->manage_stock) {
            $currentQty = isset($this->cart[$cartKey]) ? $this->cart[$cartKey]['quantity'] : 0;
            if ($stock < ($currentQty + $quantity)) {
                $this->dispatch('error-movement', 'Stock insuficiente. Disponible: ' . $stock);
                $this->dispatch('beep-error');
                return;
            }
        } elseif ($product->parent_id) {
            $parentProduct = Product::find($product->parent_id);
            if ($parentProduct && $parentProduct->manage_stock) {
                $parentBranchStock = isset($branchId) ? $parentProduct->branchStocks()->where('branch_id', $branchId)->first() : null;
                $parentStock = $parentBranchStock ? (float)$parentBranchStock->stock : (float)$parentProduct->stock;
                
                $currentQty = isset($this->cart[$cartKey]) ? $this->cart[$cartKey]['quantity'] : 0;
                $multiplier = (float)($product->fraction_multiplier ?: 1.0);
                $quantityToDeduct = ($currentQty + $quantity) * $multiplier;
                
                if ($parentStock < $quantityToDeduct) {
                    $unit = $parentProduct->unit_type ?: 'Kg';
                    $this->dispatch('error-movement', "Stock insuficiente en {$parentProduct->name}. Disponible: " . number_format($parentStock, 2) . " {$unit}");
                    $this->dispatch('beep-error');
                    return;
                }
            }
        }

        if(isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['quantity'] += $quantity;
        } else {
            // Apply product discount if setting is enabled
            $effectivePrice = ($this->discountProductEnabled && $product->discount_percentage > 0)
                ? (int) round($price * (1 - $product->discount_percentage / 100))
                : $price;

            $this->cart[$cartKey] = [
                'id'               => $product->id,
                'variation_id'     => $variation ? $variation->id : null,
                'name'             => $name,
                'price'            => $effectivePrice,
                'original_price'   => $price,
                'discount_pct'     => ($this->discountProductEnabled) ? $product->discount_percentage : 0,
                'quantity'         => (float)$quantity,
                'unit_type'        => $product->unit_type ?? 'unit',
                'sku'              => $sku
            ];
        }
        
        $this->calculateTotal();
        $this->dispatch('beep-success');
    }

    public function updateQuantity($cartKey, $change)
    {
        if(!isset($this->cart[$cartKey])) return;

        $item = $this->cart[$cartKey];
        $product = Product::find($item['id']);
        $variation = $item['variation_id'] ? \App\Models\ProductVariation::find($item['variation_id']) : null;
        
        $stock = $variation ? $variation->stock : ($product ? $product->stock : 0);
        
        $currentSession = \App\Models\CashSession::with('register')->where('user_id', auth()->id())->where('status', 'open')->first();
        if ($currentSession && $currentSession->register && $currentSession->register->branch_id) {
            $branchId = $currentSession->register->branch_id;
            $stockable = $variation ? $variation : $product;
            if ($stockable) {
                $branchStock = $stockable->branchStocks()->where('branch_id', $branchId)->first();
                $stock = $branchStock ? $branchStock->stock : 0;
            }
        }
        
        $newQty = $item['quantity'] + $change;

        // Stock Validation on Increase
        if ($change > 0 && $product && $product->manage_stock) {
            if ($stock < $newQty) {
                $this->dispatch('error-movement', 'Stock insuficiente. Disponible: ' . $stock);
                return;
            }
        }

        $this->cart[$cartKey]['quantity'] += $change;

        if($this->cart[$cartKey]['quantity'] <= 0) {
            unset($this->cart[$cartKey]);
        }

        $this->calculateTotal();
    }

    public function setQuantity($cartKey, $quantity)
    {
        if(!isset($this->cart[$cartKey])) return;
        
        $quantity = (float) $quantity;
        
        if($quantity <= 0) {
            unset($this->cart[$cartKey]);
        } else {
            $this->cart[$cartKey]['quantity'] = $quantity;
        }

        $this->calculateTotal();
    }

    public function removeFromCart($cartKey)
    {
        unset($this->cart[$cartKey]);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $subtotal = 0;
        foreach ($this->cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $this->subtotalBeforeDiscount = $subtotal;

        // Apply manual discount on top
        if ($this->discountManualEnabled && $this->manualDiscountPct > 0) {
            $this->discountAmount = (int) round($subtotal * $this->manualDiscountPct / 100);
        } else {
            $this->discountAmount = 0;
        }

        $this->total = $subtotal - $this->discountAmount;
    }

    public function updatedManualDiscountPct()
    {
        $this->manualDiscountPct = max(0, min(100, (int) $this->manualDiscountPct));
        $this->calculateTotal();
    }

    // Movement logic
    public $showMovementModal = false;
    public $showMobileCart = false; // New property for Mobile Cart
    public $movementType = 'out';
    public $movementAmount = '';
    public $movementDescription = '';
    public $movementCategory = '';

    public function toggleMobileCart()
    {
        $this->showMobileCart = !$this->showMobileCart;
    }

    public function openMovementModal($type)
    {
        $hasOpenSession = \App\Models\CashSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->exists();

        if (!$hasOpenSession) {
            return redirect()->route('cash.index')->with('message', 'Debes abrir caja para registrar movimientos.');
        }

        $this->movementType = $type;
        $this->movementAmount = '';
        $this->movementDescription = '';
        $this->movementCategory = '';
        $this->showMovementModal = true;
    }

    public function saveMovement()
    {
        $this->validate([
            'movementAmount' => 'required|numeric|min:0',
            'movementDescription' => 'required|string|max:255',
            'movementCategory' => 'nullable|string|max:50'
        ]);

        $currentSession = \App\Models\CashSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$currentSession) {
             session()->flash('message', 'No hay sesión de caja abierta.');
             return;
        }

        $currentSession->movements()->create([
            'type' => $this->movementType,
            'category' => $this->movementCategory ?: null,
            'amount' => $this->movementAmount,
            'description' => $this->movementDescription
        ]);

        $this->showMovementModal = false;
        
        // Notify success
        $this->dispatch('session-status-changed'); 
        session()->flash('success_movement', 'Movimiento registrado correctamente.');
    }

    public function openStatusModal()
    {
        $hasOpenSession = \App\Models\CashSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->exists();

        if (!$hasOpenSession) {
            return redirect()->route('cash.index')->with('message', 'Debes abrir caja para ver el estado.');
        }

        $this->loadSessionDetails();
        $this->showStatusModal = true;
    }

    public function loadSessionDetails()
    {
        $session = \App\Models\CashSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$session) return;

        $sales = \App\Models\Sale::where('user_id', auth()->id())
            ->where('created_at', '>=', $session->opened_at)
            ->get();
        
        $totalSales = $sales->sum('total');
        
        // Calculate amount that actually entered as CASH
        $cashSales = \App\Models\Payment::whereIn('sale_id', $sales->pluck('id'))
            ->where(function($query) {
                $query->where('method', 'like', 'efectivo%')
                      ->orWhere('method', 'like', 'Efectivo%');
            })
            ->sum('amount');
        
        $totalIn = $session->movements()->where('type', 'in')->sum('amount');
        $totalOut = $session->movements()->where('type', 'out')->sum('amount');

        $this->sessionDetails = [
            'opened_at' => $session->opened_at,
            'opening_amount' => $session->opening_amount,
            'sales_count' => $sales->count(),
            'sales_total' => $totalSales,
            'sales_cash' => $cashSales,
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'expected_total' => $session->opening_amount + $cashSales + $totalIn - $totalOut
        ];
    }

    public function updatedCustomerSearch()
    {
        if (strlen($this->customerSearch) < 2) {
            $this->searchResults = [];
            return;
        }

        $this->searchResults = \App\Models\Customer::where('is_active', true)
            ->where(function ($query) {
                // MySQL uses LIKE for case-insensitive search
                $query->where('name', 'like', '%' . $this->customerSearch . '%')
                      ->orWhere('ruc', 'like', '%' . $this->customerSearch . '%');
            })
            ->take(5)
            ->get()
            ->toArray();
    }

    public function selectCustomer($customerId)
    {
        $customer = \App\Models\Customer::find($customerId);
        if ($customer) {
            $this->selectedCustomer = $customer->toArray();
            $this->customerSearch = '';
            $this->searchResults = [];
        }
    }

    public function openCustomerModal()
    {
        $this->openCreateCustomerModal();
    }

    public function openCreateCustomerModal()
    {
        $this->reset(['newCustomerName', 'newCustomerRuc', 'newCustomerEmail', 'newCustomerPhone', 'newCustomerAddress', 'newCustomerIsActive']);
        $this->showCustomerModal = true;
        // Clear search to close the dropdown behind the modal
        $this->searchResults = [];
    }

    public function removeCustomer()
    {
        $this->selectedCustomer = null;
    }

    public function clearCart()
    {
        // In restaurant mode, going back to table map also saves if cart not empty
        if ($this->isRestaurantMode && $this->activeTableId) {
            $this->backToTableMap();
            return;
        }
        $this->cart = [];
        $this->importingWebOrderId = null;
        $this->calculateTotal();
    }

    // ---- Web Orders Integration ----
    public function openWebOrdersModal()
    {
        $this->pendingWebOrders = \App\Models\WebOrder::with('product')
            ->whereIn('status', ['Pendiente WhatsApp', 'Atendido'])
            ->orderByDesc('created_at')
            ->get()
            ->toArray();
        $this->showWebOrdersModal = true;
    }

    public function importWebOrder($orderId)
    {
        $order = \App\Models\WebOrder::with('product')->find($orderId);
        if (!$order) return;

        // Clear current cart or ask? For now, clear.
        $this->cart = [];
        $this->importingWebOrderId = $order->id;

        if ($order->items_json) {
            foreach ($order->items_json as $item) {
                // Try to find the actual product for accurate stock/price
                $product = Product::find($item['id']);
                if ($product) {
                    $this->addToCart($product->id);
                    // Update quantity if more than 1
                    if ($item['quantity'] > 1) {
                        $this->cart[$product->id]['quantity'] = $item['quantity'];
                    }
                } else {
                    // Fallback for custom/deleted items (not ideal for POS but better than nothing)
                    $this->cart[$item['id']] = [
                        'id' => $item['id'],
                        'name' => $item['name'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'unit_type' => 'unit'
                    ];
                }
            }
        } elseif ($order->product_id) {
            $this->addToCart($order->product_id);
        }

        // Try to match customer by phone
        $customer = \App\Models\Customer::where('phone', $order->customer_phone)->first();
        if ($customer) {
            $this->selectedCustomer = $customer->toArray();
        } else {
            // Pre-fill name for search or create?
            $this->customerSearch = $order->customer_name;
        }

        $this->calculateTotal();
        $this->showWebOrdersModal = false;
        $this->dispatch('success-movement', 'Pedido Web #' . substr($order->id, 0, 8) . ' importado.');
    }

    public function saveNewCustomer()
    {
        $this->validate([
            'newCustomerName' => 'required|string|max:255',
            'newCustomerRuc' => ['required', 'string', 'max:20'],
            'newCustomerEmail' => ['nullable', 'email', 'max:255'],
            'newCustomerPhone' => ['nullable', 'string', 'max:20'],
            'newCustomerAddress' => ['nullable', 'string', 'max:500'],
        ]);

        if (\App\Models\Customer::where('ruc', $this->newCustomerRuc)->exists()) {
            $this->addError('newCustomerRuc', 'Ya existe un cliente con este RUC.');
            return;
        }

        $customer = \App\Models\Customer::create([
            'name' => $this->newCustomerName,
            'ruc' => $this->newCustomerRuc,
            'email' => $this->newCustomerEmail,
            'phone' => $this->newCustomerPhone,
            'address' => $this->newCustomerAddress,
            'is_active' => $this->newCustomerIsActive
        ]);

        $this->selectedCustomer = $customer->toArray();
        $this->showCustomerModal = false;
        $this->reset(['newCustomerName', 'newCustomerRuc', 'newCustomerEmail', 'newCustomerPhone', 'newCustomerAddress', 'newCustomerIsActive']);
        
        $this->dispatch('success-movement', 'Cliente creado correctamente.');
        $this->searchResults = [];
    }

    public function openCreateProductModal()
    {
        $this->reset(['newProductName', 'newProductSku', 'newProductCategoryId', 'newProductPriceCost', 'newProductPriceSale', 'newProductStock', 'newProductMinStock', 'newProductUnitType', 'newProductManageStock', 'newProductIsActive']);
        // If user typed something in search, maybe use it as name? Optional UX improvement.
        if(!empty($this->search)) {
            $this->newProductName = $this->search; 
        }
        $this->showProductModal = true;
    }

    public function saveNewProduct()
    {
        $this->validate([
            'newProductName' => 'required|min:3',
            'newProductSku' => 'nullable|unique:products,sku',
            'newProductPriceCost' => 'required',
            'newProductPriceSale' => 'required',
            'newProductStock' => $this->newProductManageStock ? 'required|numeric|min:0' : 'nullable',
            'newProductMinStock' => $this->newProductManageStock ? 'required|numeric|min:0' : 'nullable',
            'newProductCategoryId' => 'nullable|exists:categories,id',
            'newProductUnitType' => 'required|in:unit,kg,liter,meter'
        ]);

        // Auto-generate SKU if empty
        if (empty($this->newProductSku)) {
            do {
                $this->newProductSku = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
            } while (\App\Models\Product::where('sku', $this->newProductSku)->exists());
        }

        $product = \App\Models\Product::create([
            'name' => $this->newProductName,
            'sku' => $this->newProductSku,
            'price_cost' => $this->newProductPriceCost,
            'price_sale' => $this->newProductPriceSale,
            'stock' => $this->newProductManageStock ? $this->newProductStock : 0,
            'min_stock' => $this->newProductManageStock ? $this->newProductMinStock : 0,
            'category_id' => $this->newProductCategoryId ?: null,
            'unit_type' => $this->newProductUnitType,
            'manage_stock' => $this->newProductManageStock,
            'is_active' => $this->newProductIsActive
        ]);

        $this->showProductModal = false;
        $this->reset(['newProductName', 'newProductSku', 'newProductCategoryId', 'newProductPriceCost', 'newProductPriceSale', 'newProductStock', 'newProductMinStock', 'newProductUnitType', 'newProductManageStock', 'newProductIsActive']);
        
        // Add to cart automatically
        $this->addToCart($product->id);
        
        $this->dispatch('success-movement', 'Producto creado y agregado al carrito.');
        
        // Clear search so the list refreshes or user can search again
        $this->search = ''; 
    }

    // Payment Logic
    public $showPaymentModal = false;
    public $paymentMethod = 'efectivo'; // efectivo, pos, tarjeta, transferencia, multiple
    public $receivedAmount = 0;
    public $change = 0;
    public $paymentCondition = 'contado'; // contado, credito
    public $multiplePayments = []; // Stores amounts for split payments
    public $activeCurrencies = []; // Dynamic visible currency inputs
    public $chequeDetails = [
        'bank_name' => '',
        'cheque_number' => '',
        'due_date' => '',
    ];

    public $paymentMethods = [];

    // Seller
    public $sellerId = null;
    public $sellerOptions = [];

    public function openPaymentModal()
    {
        if(empty($this->cart)) return;
        
        // Check session before opening modal to avoid frustration
        $hasOpenSession = \App\Models\CashSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->exists();

        if (!$hasOpenSession) {
            return redirect()->route('cash.index')->with('message', 'Debes abrir caja para poder vender.');
        }

        $this->loadPaymentMethods();

        $hasEfectivo = collect($this->paymentMethods)->contains(function ($pm) {
            return strtolower($pm->name) === 'efectivo' || $pm->type === 'cash';
        });
        $this->paymentMethod = $hasEfectivo ? 'efectivo' : (collect($this->paymentMethods)->first()?->name ? strtolower(collect($this->paymentMethods)->first()->name) : 'efectivo');
        $this->paymentCondition = 'contado';
        $this->receivedAmount = ''; // Start empty so user can type
        $this->change = -$this->total; // Initial change is effectively negative whole amount
        $this->showPaymentModal = true;
        $this->activeCurrencies = []; // Reset active currencies
        
        // Initialize multiple payments
        $this->multiplePayments = [
            'efectivo' => 0,
            'efectivo_usd' => 0,
            'efectivo_brl' => 0,
            'efectivo_ars' => 0,
        ];
        foreach($this->paymentMethods as $pm) {
            $this->multiplePayments[$pm->name] = 0;
        }
        $this->chequeDetails = [
            'bank_name' => '',
            'cheque_number' => '',
            'due_date' => '',
        ];

        // Load active sellers
        $this->sellerId = null;
        $this->sellerOptions = \App\Models\Seller::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function updatedReceivedAmount()
    {
        // Simple sanitation if needed, but type="number" usually suffices
        // If handling "1.000" format, we need to clean standardizing 
        $this->calculateChange();
    }

    public function updatedPaymentMethod()
    {
        if ($this->paymentMethod === 'multiple') {
            $this->receivedAmount = 0;
            $this->activeCurrencies = []; // Reset active currencies
            // Maybe initialize with total in cash?
            $this->multiplePayments = [
                'efectivo' => $this->total,
                'efectivo_usd' => 0,
                'efectivo_brl' => 0,
                'efectivo_ars' => 0,
            ]; 
            foreach($this->paymentMethods as $pm) {
                $this->multiplePayments[$pm->name] = 0;
            }
        } elseif ($this->paymentMethod === 'cheque') {
            // For cheque, we usually don't need 'receivedAmount' behavior like cash change
            // But we do need to set it to total so validation passes (received >= total)
            $this->receivedAmount = $this->total;
        } elseif ($this->paymentMethod !== 'efectivo') {
            $this->receivedAmount = $this->total;
        } else {
            // Restore received amount relative to logic? 
            // Or reset? Usually reset to force entry or keep if valid.
            // Let's reset to empty or 0 if returning to cash
             if($this->receivedAmount == $this->total) $this->receivedAmount = ''; 
        }
        $this->calculateChange();
    }

    public function toggleCurrency($currency)
    {
        if (in_array($currency, $this->activeCurrencies)) {
            $this->activeCurrencies = array_diff($this->activeCurrencies, [$currency]);
            $this->multiplePayments['efectivo_' . $currency] = 0;
        } else {
            $this->activeCurrencies[] = $currency;
        }
        $this->calculateChange();
    }

    public function updatedPaymentCondition()
    {
        if ($this->paymentCondition === 'credito') {
            // Logic for credit? Maybe clear payment method selection or just ignore it.
        } else {
            $this->paymentMethod = 'efectivo'; // Default back to cash
        }
    }

    public function calculateChange()
    {
        if ($this->paymentMethod === 'multiple') {
            $totalPaid = 0;
            foreach ($this->multiplePayments as $method => $amount) {
                $val = (float) $amount;
                if ($val <= 0) continue;

                if ($method === 'efectivo_usd') {
                    $totalPaid += $val * ($this->rates['usd'] ?: 1);
                } elseif ($method === 'efectivo_brl') {
                    $totalPaid += $val * ($this->rates['brl'] ?: 1);
                } elseif ($method === 'efectivo_ars') {
                    $totalPaid += $val * ($this->rates['ars'] ?: 1);
                } else {
                    $totalPaid += $val;
                }
            }
            $this->receivedAmount = (int) round($totalPaid);
            $this->change = $this->receivedAmount - $this->total;
        } elseif ($this->paymentMethod === 'efectivo') {
            // Cast to float to safely do math, then subtract
            $received = (float) $this->receivedAmount;
            $this->change = $received - $this->total;
        } else {
            $this->change = 0;
        }
    }

    // Listener for multiple payments updates
    public function updatedMultiplePayments()
    {
        $this->calculateChange();
    }

    public function processSale(\App\Services\SalesService $salesService)
    {
        if(empty($this->cart)) return;

        // Double check session
        $hasOpenSession = \App\Models\CashSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->exists();

        if (!$hasOpenSession) {
            return redirect()->route('cash.index')->with('message', 'La caja se ha cerrado. Abrela para continuar.');
        }

        $currentSession = \App\Models\CashSession::with('register')->where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        $branchId = $currentSession->register->branch_id ?? null;
        if (!$branchId) {
             $this->addError('paymentMethod', 'La caja actual no tiene sucursal asignada.');
             return;
        }

        // Validate Payment (Only if Contado)
        if ($this->paymentCondition === 'contado') {
            if ($this->paymentMethod === 'efectivo') {
                if ($this->receivedAmount <= 0) {
                     $this->addError('receivedAmount', 'El monto recibido debe ser mayor a 0.');
                     return;
                }
                if ($this->receivedAmount < $this->total) {
                    $this->addError('receivedAmount', 'El monto recibido es menor al total.');
                    return;
                }
            } elseif ($this->paymentMethod === 'multiple') {
                if ($this->receivedAmount < $this->total) {
                    $this->addError('multiplePayments', 'La suma de pagos (' . number_format($this->receivedAmount, 0, '', '.') . ') es menor al total.');
                    return;
                }
                
                // Validate that at least one amount is > 0
                 if ($this->receivedAmount <= 0) {
                     $this->addError('multiplePayments', 'Debes ingresar un monto.');
                     return;
                }
            } elseif ($this->paymentMethod === 'cheque') {
                $this->validate([
                    'chequeDetails.bank_name' => 'required|string|max:100',
                    'chequeDetails.cheque_number' => 'required|string|max:50',
                    'chequeDetails.due_date' => 'required|date',
                ]);
            }
        }

        // Validate Credit
        if ($this->paymentCondition === 'credito' && !$this->selectedCustomer) {
            $this->dispatch('error-movement', 'Para ventas a Crédito, debes seleccionar un Cliente.');
            return;
        }

        try {
            $sale = $salesService->processSale([
                'user_id'                  => auth()->id(),
                'branch_id'                => $branchId,
                'seller_id'                => $this->sellerId ?: null,
                'customer_id'              => $this->selectedCustomer ? $this->selectedCustomer['id'] : null,
                'subtotal_before_discount' => $this->subtotalBeforeDiscount,
                'discount_amount'          => $this->discountAmount,
                'total'                    => $this->total,
                'payment_condition'        => $this->paymentCondition,
                'payment_method'           => $this->paymentMethod,
                'multiple_payments'        => $this->multiplePayments,
                'cheque_details'           => $this->chequeDetails,
                'rates'                    => $this->rates,
                'cart'                     => $this->cart,
                'web_order_id'             => $this->importingWebOrderId,
                'active_sale_id'           => $this->activeSaleId,
                'restaurant_table_id'      => $this->activeTableId,
                'waiter_id'                => $this->activeWaiterId,
            ]);

            $this->lastSaleId = $sale->id;

            $this->cart = [];
            $this->total = 0;
            $this->selectedCustomer = null;
            $this->customerSearch = '';
            $this->importingWebOrderId = null;
            $this->showPaymentModal = false; // Close modal

            // Restaurant: reset table state and go back to map
            if ($this->isRestaurantMode) {
                $this->activeTableId   = null;
                $this->activeWaiterId  = null;
                $this->activeSaleId    = null;
                $this->showTableMap    = true;
            }
            session()->flash('success', 'Venta procesada correctamente');
            session()->flash('last_sale_id', $this->lastSaleId);

        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function printLastTicket()
    {
        if ($this->lastSaleId) {
            $this->dispatch('print-ticket', ['saleId' => $this->lastSaleId]);
        }
    }

    // ---- Void Sale from POS ----
    public function openVoidModal()
    {
        $this->recentSales   = \App\Models\Sale::with(['customer', 'seller'])
            ->where('status', '!=', 'voided')
            ->whereDate('created_at', today())
            ->orderByDesc('created_at')
            ->take(10)
            ->get()
            ->toArray();
        $this->voidSearch    = '';
        $this->showVoidModal = true;
    }

    public function updatedVoidSearch()
    {
        if (strlen($this->voidSearch) >= 2) {
            $this->recentSales = \App\Models\Sale::with(['customer', 'seller'])
                ->where('status', '!=', 'voided')
                ->where(function($q) {
                    $q->whereHas('customer', fn($c) => $c->where('name', 'like', '%'.$this->voidSearch.'%'))
                      ->orWhere('id', 'like', '%'.$this->voidSearch.'%');
                })
                ->orderByDesc('created_at')
                ->take(10)
                ->get()
                ->toArray();
        }
    }

    public function voidSale($saleId)
    {
        $sale = \App\Models\Sale::with('items.product')->findOrFail($saleId);

        if ($sale->status === 'voided') {
            session()->flash('error', 'Esta venta ya fue anulada.');
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($sale) {
            // We need the branch_id that the sale was made on.
            // Since sale has branch_id now (or at least user has branch_id)
            $branchId = $sale->branch_id ?? $sale->user->branch_id;

            // Restore stock
            foreach ($sale->items as $item) {
                if ($item->product && $item->product->manage_stock) {
                    $stockable = $item->product_variation_id ? $item->product->variations()->find($item->product_variation_id) : $item->product;
                    if ($stockable) {
                        $stockable->increment('stock', $item->quantity);
                        if ($branchId) {
                            $branchStock = $stockable->branchStocks()->firstOrCreate(
                                ['branch_id' => $branchId],
                                ['stock' => 0]
                            );
                            $branchStock->increment('stock', $item->quantity);
                        }
                    }
                }
            }
            // Mark as voided
            $sale->update(['status' => 'voided']);
        });

        $this->showVoidModal = false;
        session()->flash('success', 'Venta anulada y stock restaurado.');
    }
}

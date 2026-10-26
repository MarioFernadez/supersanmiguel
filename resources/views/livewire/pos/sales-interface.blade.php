<div class="flex h-screen w-full bg-gray-50 dark:bg-gray-900 transition-colors duration-300 font-sans">

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- RESTAURANT: TABLE MAP (shown instead of normal POS when mode is restaurant + no table selected) --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    @if($isRestaurantMode && $showTableMap)
    <div class="flex-1 flex flex-col h-full bg-gray-50 dark:bg-gray-900 overflow-hidden font-sans">
        {{-- Header --}}
        <div class="bg-white dark:bg-gray-800 p-4 shadow-sm flex items-center justify-between border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="text-emerald-600 font-bold text-xl flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    ZonaPOS
                </a>
                <span class="text-xl font-bold text-gray-900 dark:text-white tracking-tight ml-4">🍽️ Mapa de Mesas</span>
            </div>
            <div class="text-xs text-gray-500 dark:text-gray-400">
                {{ auth()->user()->name }} &nbsp;|&nbsp; {{ $currentRegisterName }} · desde {{ $sessionOpenedAt }}
            </div>
        </div>

        {{-- Table Grid --}}
        <div class="flex-1 overflow-y-auto p-6">
            @if($restaurantTables->isEmpty())
                <div class="flex flex-col items-center justify-center h-full gap-4 text-gray-400">
                    <span class="text-6xl">🍽️</span>
                    <p class="font-semibold text-lg">No hay mesas configuradas.</p>
                    <a href="{{ route('settings.restaurant') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition">Ir a Configuración de Restaurante</a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach($restaurantTables as $table)
                        <button wire:click="openTable({{ $table->id }})"
                            class="aspect-square rounded-2xl flex flex-col items-center justify-center gap-2 font-bold text-base shadow-sm border transition-all active:scale-95
                            {{ $table->status === 'occupied'
                                ? 'bg-rose-500 border-rose-600 text-white shadow-rose-200 dark:shadow-none'
                                : 'bg-white border-emerald-300 text-gray-900 shadow-sm hover:border-indigo-400 hover:shadow-md dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:shadow-none' }}">
                            <span class="text-2xl">{{ $table->status === 'occupied' ? '🔴' : '🟢' }}</span>
                            <span>{{ $table->name }}</span>
                            @if($table->status === 'occupied' && $table->currentSale)
                                <span class="text-xs font-semibold opacity-90 tabular-nums">₲ {{ number_format($table->currentSale->total, 0, ',', '.') }}</span>
                            @else
                                <span class="text-xs font-medium opacity-60">Libre</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- WAITER SELECTION MODAL --}}
    @if($showWaiterModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-8 shadow-2xl w-full max-w-md border border-gray-100 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight mb-6 text-center">¿Quién atiende esta mesa?</h2>
            <div class="grid grid-cols-2 gap-3">
                @foreach($waiters as $waiter)
                    <button wire:click="selectWaiter({{ $waiter->id }})"
                        class="bg-indigo-50/80 hover:bg-indigo-100 dark:bg-indigo-900/30 border border-indigo-200/80 dark:border-indigo-700 text-indigo-800 dark:text-indigo-300 font-semibold py-3.5 px-3 rounded-2xl text-sm transition active:scale-95">
                        👤 {{ $waiter->name }}
                    </button>
                @endforeach
                @if($waiters->isEmpty())
                    <div class="col-span-2 text-center text-gray-400 py-4 text-sm">No hay mozos activos. <a href="{{ route('settings.restaurant') }}" class="text-indigo-600 font-semibold">Agregar en Ajustes</a></div>
                @endif
            </div>
            <button wire:click="selectWaiter(null)" class="mt-6 w-full text-center text-gray-400 text-sm hover:text-gray-600">Continuar sin asignar mozo</button>
        </div>
    </div>
    @endif

    @else
    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- NORMAL POS (or restaurant with active table) --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}

    <!-- Left Section: Products -->
    <div class="flex-1 flex flex-col h-full overflow-hidden">
        <!-- Header / Search -->
        <div class="bg-white dark:bg-gray-800 p-4 shadow-sm z-10 transition-colors duration-300 border-b border-gray-100 dark:border-gray-700/80">
            <div class="flex justify-between items-center mb-4">
                <a href="{{ route('dashboard') }}" class="text-emerald-600 font-bold text-xl flex items-center tracking-tight">
                    <svg class="w-6 h-6 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    ZonaPOS
                </a>
                <div class="flex items-center gap-3">
                    <div class="text-xs font-normal text-gray-500 dark:text-gray-400 hidden md:block">
                        Usuario: <span class="text-gray-900 dark:text-white font-medium">{{ auth()->user()->name }}</span>
                    </div>
                    @if($currentRegisterName)
                    <div class="hidden md:flex items-center gap-1.5 bg-emerald-50 border border-emerald-200/60 text-emerald-700 px-3 py-1.5 rounded-xl text-xs font-medium dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/40">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        {{ $currentRegisterName }} · desde {{ $sessionOpenedAt }}
                    </div>
                    @endif

                    @if(\App\Services\SifenService::isEnabled())
                    <div class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider {{ \App\Services\SifenService::isTestMode() ? 'bg-amber-50 text-amber-700 border border-amber-200/80 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/40' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/80 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/40' }}" title="Facturación Electrónica SIFEN Activa">
                        <span>{{ \App\Services\SifenService::isTestMode() ? '🧪 SIFEN Test' : '🛡️ SIFEN' }}</span>
                    </div>
                    @endif

                    <!-- Web Orders Button -->
                    @if(\App\Services\LicenseService::isEcommerceEnabled())
                    <button type="button" wire:click="openWebOrdersModal" class="relative bg-indigo-50 border border-indigo-200/80 hover:bg-indigo-100 text-indigo-700 p-2 rounded-xl transition-all flex items-center justify-center shadow-sm active:scale-95 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800/50" title="Pedidos Web Pendientes">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        @if($pendingWebOrdersCount > 0)
                            <span class="absolute -top-1.5 -right-1.5 bg-rose-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white animate-bounce shadow-sm">
                                {{ $pendingWebOrdersCount > 9 ? '9+' : $pendingWebOrdersCount }}
                            </span>
                        @endif
                    </button>
                    @endif

                    <button type="button" wire:click="openMovementModal('in')" wire:loading.attr="disabled" class="bg-gray-50 dark:bg-gray-700/60 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 p-2 rounded-xl text-sm border border-gray-200/80 dark:border-gray-600 transition-colors flex items-center justify-center shadow-sm" title="Registrar Movimientos">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    </button>

                    <button wire:click="openStatusModal" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 p-2 rounded-xl text-sm border border-indigo-200/80 transition-colors flex items-center justify-center shadow-sm dark:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-800/50" title="Estado de Caja">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </button>

                    <button wire:click="openVoidModal" class="bg-orange-50 text-orange-600 hover:bg-orange-100 p-2 rounded-xl text-sm border border-orange-200/80 transition-colors flex items-center justify-center shadow-sm dark:bg-orange-950/30 dark:text-orange-400 dark:border-orange-800/50" title="Anular Venta">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    @if($lastSaleId)
                        <button wire:click="printLastTicket" class="bg-amber-50 text-amber-600 hover:bg-amber-100 p-2 rounded-xl text-sm border border-amber-200/80 transition-colors flex items-center justify-center shadow-sm dark:bg-amber-950/30 dark:border-amber-800/50" title="Imprimir último ticket">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        </button>
                    @endif

                    <a href="{{ route('cash.index') }}" class="bg-rose-50 text-rose-600 hover:bg-rose-100 p-2 rounded-xl text-sm border border-rose-200/80 transition-colors flex items-center justify-center shadow-sm dark:bg-rose-950/30 dark:border-rose-800/50" title="Cerrar Caja (Finalizar Sesión)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </a>
                </div>
            </div>
            
            <div class="flex gap-2">
                <div class="relative flex-1" x-data="{
                    scanTimer: null,
                    submitCode(val) {
                        val = (val || '').trim();
                        if (!val) return;
                        clearTimeout(this.scanTimer);
                        const input = document.getElementById('product-search-input');
                        if (input) input.value = '';
                        $wire.handleSearchSubmit(val);
                    }
                }">
                    <input id="product-search-input" 
                        wire:model.live.debounce.150ms="search" 
                        type="text" 
                        x-on:keydown.enter.prevent="submitCode($el.value)"
                        x-on:keydown.tab.prevent="submitCode($el.value)"
                        x-on:input="
                            clearTimeout(scanTimer);
                            let val = $el.value.trim();
                            if (/^\d{4,18}$/.test(val)) {
                                scanTimer = setTimeout(() => {
                                    submitCode(val);
                                }, 80);
                            }
                        "
                        class="w-full pl-10 pr-12 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 shadow-sm text-sm" 
                        placeholder="Buscar producto por nombre o código (SKU)..." 
                        autocomplete="off"
                        autofocus>
                    <div class="absolute left-3.5 top-3 text-gray-400 dark:text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <div class="absolute right-3 top-2.5">
                        <span class="text-[10px] font-semibold text-gray-400 border border-gray-200 dark:border-gray-600 rounded px-1.5 py-0.5 bg-gray-50 dark:bg-gray-800 dark:text-gray-400">F3</span>
                    </div>
                </div>
            </div>
            
            <!-- Category Filter Pills -->
            <div class="flex gap-1.5 mt-3 overflow-x-auto pb-1 scrollbar-hide">
                <button wire:click="filterCategory(null)" 
                    class="px-3.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap transition-colors {{ is_null($selectedCategory) ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                    Todos
                </button>
                @foreach($categories as $category)
                    <button wire:click="filterCategory('{{ $category->id }}')" 
                        class="px-3.5 py-1 rounded-full text-xs font-semibold whitespace-nowrap transition-colors {{ $selectedCategory === $category->id ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto p-4 content-start products-h">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3.5">
                @foreach($products as $product)
                    @php
                        $currentStock = $product->stock;
                        $otherStock = 0;
                        if (isset($branchId) && $branchId) {
                            $branchStock = $product->branchStocks->where('branch_id', $branchId)->first();
                            $currentStock = $branchStock ? $branchStock->stock : 0;
                            $otherStock = $product->branchStocks->where('branch_id', '!=', $branchId)->sum('stock');
                        }
                        $isOutOfStock = $product->manage_stock && $currentStock <= 0;
                        $isLowStock = $product->manage_stock && !$isOutOfStock && $currentStock <= $product->min_stock;
                    @endphp
                    <div 
                        wire:key="{{ $product->id }}"
                        wire:click="addToCart('{{ $product->id }}')" 
                        class="bg-white dark:bg-gray-800 rounded-2xl p-3.5 shadow-sm hover:shadow-md transition-all border flex flex-col justify-between cursor-pointer hover:-translate-y-0.5 group {{ $isLowStock ? 'border-amber-300/80 dark:border-amber-500/60' : ($isOutOfStock ? 'border-rose-300/80 dark:border-rose-500/60' : 'border-gray-100 hover:border-indigo-300 dark:border-gray-700/80') }}"
                    >
                        <div>
                            {{-- Top row: SKU left, BAJO STOCK right --}}
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <span class="text-[11px] font-mono text-gray-400 dark:text-gray-500">
                                    {{ $product->sku ?? '---' }}
                                </span>
                                @if($isOutOfStock)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200/60 dark:bg-rose-950/40 dark:text-rose-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        SIN STOCK
                                    </span>
                                @elseif($isLowStock)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/60 dark:bg-amber-950/40 dark:text-amber-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        BAJO
                                    </span>
                                @endif
                            </div>

                            {{-- Product Name --}}
                            <h3 class="font-medium text-gray-900 dark:text-white text-sm leading-snug line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                {{ $product->name }}
                            </h3>
                        </div>

                        {{-- Bottom row: Price left, Stock pill right --}}
                        <div class="mt-3 pt-2 border-t border-gray-50 dark:border-gray-700/60 flex items-center justify-between gap-2">
                            <div>
                                @if($discountProductEnabled && $product->discount_percentage > 0)
                                    <div class="flex items-center gap-1">
                                        <span class="line-through text-gray-400 text-xs tabular-nums">₲ {{ number_format($product->price_sale, 0, ',', '.') }}</span>
                                        <span class="bg-rose-500 text-white text-[9px] font-bold px-1 rounded">-{{ $product->discount_percentage }}%</span>
                                    </div>
                                    <div class="text-emerald-600 dark:text-emerald-400 font-bold text-base tabular-nums tracking-tight">
                                        <span class="text-xs font-normal text-gray-400 mr-0.5">₲</span>{{ number_format($product->effective_price, 0, ',', '.') }}
                                    </div>
                                @else
                                    <div class="text-emerald-600 dark:text-emerald-400 font-bold text-base tabular-nums tracking-tight">
                                        <span class="text-xs font-normal text-gray-400 mr-0.5">₲</span>{{ number_format($product->price_sale, 0, ',', '.') }}
                                    </div>
                                @endif
                            </div>

                            <div>
                                @if(!$product->manage_stock)
                                    <span class="inline-flex items-center justify-center w-7 h-6 rounded-lg bg-gray-50 dark:bg-gray-700/60 text-gray-500 dark:text-gray-300 text-xs font-semibold border border-gray-200/60 dark:border-gray-600">
                                        ∞
                                    </span>
                                @elseif($isLowStock || $isOutOfStock)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 text-[11px] font-medium border border-amber-200/60 dark:border-amber-800">
                                        {{ $currentStock }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-gray-50 dark:bg-gray-700/60 text-gray-600 dark:text-gray-300 text-[11px] font-medium border border-gray-200/60 dark:border-gray-600">
                                        {{ $currentStock }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            @if(count($products) === 0)
                <div class="flex flex-col items-center justify-center h-64 text-gray-400 dark:text-gray-500">
                    <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-base font-medium">No se encontraron productos</p>
                    <button wire:click="openCreateProductModal" class="mt-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-xl shadow-sm transition flex items-center text-xs">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Crear Nuevo Producto
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Section: Cart -->
    <div class="w-96 bg-white dark:bg-gray-800 shadow-xl z-20 flex flex-col h-full border-l border-gray-100 dark:border-gray-700/80 hidden md:flex transition-colors duration-300 font-sans">
        <!-- Cart Header -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/60 transition-colors">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-base font-bold text-gray-900 dark:text-white flex items-center tracking-tight">
                    <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Carrito
                </h2>
                @if($importingWebOrderId)
                    <div class="bg-indigo-50 border border-indigo-200/80 text-indigo-700 px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider animate-pulse dark:bg-indigo-950/40 dark:text-indigo-300">
                        Pedido Web
                    </div>
                @endif
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold bg-indigo-50 border border-indigo-200/60 text-indigo-700 px-2.5 py-0.5 rounded-full dark:bg-indigo-950/40 dark:text-indigo-300">{{ count($cart) }} {{ count($cart) === 1 ? 'ítem' : 'ítems' }}</span>
                    @if(count($cart) > 0)
                        <button wire:click="clearCart" wire:confirm="¿Vaciar carrito?" class="text-rose-500 hover:text-rose-700 p-1 hover:bg-rose-50 rounded-lg transition" title="Vaciar Carrito">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Customer Selector -->
            <div class="relative">
                @if($selectedCustomer)
                    <div class="flex justify-between items-center bg-white border border-emerald-200/80 rounded-xl p-2.5 shadow-sm dark:bg-gray-800 dark:border-emerald-800/40">
                        <div class="flex items-center">
                            <div class="bg-emerald-50 p-1.5 rounded-lg text-emerald-600 mr-2.5 dark:bg-emerald-950/40 dark:text-emerald-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <div class="leading-tight">
                                <div class="font-medium text-gray-900 text-xs truncate max-w-[150px] dark:text-white">{{ $selectedCustomer['name'] }}</div>
                                <div class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $selectedCustomer['ruc'] }}</div>
                            </div>
                        </div>
                        <button wire:click="removeCustomer" class="text-gray-400 hover:text-rose-500 p-1 transition" title="Cambiar cliente">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @else
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input id="customer-search-input" wire:model.live.debounce.300ms="customerSearch" type="text" 
                            class="w-full pl-9 pr-10 py-2 text-xs border border-gray-200 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 shadow-sm" 
                            placeholder="Buscar Cliente (Nombre / RUC)...">
                        <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none">
                             <span class="text-[10px] font-semibold text-gray-400 border border-gray-200 dark:border-gray-600 rounded px-1.5 py-0.5 bg-gray-50 dark:bg-gray-700 dark:text-gray-400">F4</span>
                        </div>
                    </div>

                    @if(!empty($customerSearch) && !$selectedCustomer)
                        <div class="absolute z-50 w-full mt-1.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                            @forelse($searchResults as $result)
                                <button wire:click="selectCustomer('{{ $result['id'] }}')" class="w-full text-left px-3.5 py-2 text-xs hover:bg-gray-50 dark:hover:bg-gray-700 flex justify-between items-center border-b border-gray-50 dark:border-gray-700 last:border-0 transition">
                                    <span class="font-medium text-gray-900 dark:text-gray-200">{{ $result['name'] }}</span>
                                    <span class="text-[11px] text-gray-400 font-mono">{{ $result['ruc'] }}</span>
                                </button>
                            @empty
                                <div class="px-4 py-3 text-xs text-gray-500 text-center dark:text-gray-400">
                                    No se encontró el cliente
                                    <button wire:click="openCreateCustomerModal" class="block w-full mt-1.5 text-indigo-600 font-semibold hover:underline dark:text-indigo-400">
                                        + Crear Nuevo Cliente
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-4 cart-h">
            @if(count($cart) > 0)
                <div class="space-y-3.5">
                    @foreach($cart as $cartKey => $item)
                        <div class="flex justify-between items-start pb-3.5 border-b border-gray-100 dark:border-gray-700/80 last:border-0" wire:key="cart-{{ $cartKey }}">
                            <div class="flex-1 pr-3">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100 text-xs leading-snug line-clamp-2">{{ $item['name'] }}</h4>
                                <div class="text-[10px] text-gray-400 font-mono mt-0.5">SKU: {{ $item['sku'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1 tabular-nums">
                                    <span class="text-[10px] font-normal text-gray-400 mr-0.5">₲</span>{{ format_pyg($item['price']) }}
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <div class="flex items-center bg-gray-50 dark:bg-gray-700/60 border border-gray-200/60 dark:border-gray-600 rounded-xl p-0.5">
                                    <button wire:click="updateQuantity('{{ $cartKey }}', -1)" class="w-6 h-6 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-white dark:hover:bg-gray-600 hover:shadow-xs rounded-lg transition text-xs font-bold">-</button>
                                    
                                    @if(isset($item['unit_type']) && $item['unit_type'] !== 'unit')
                                        <input 
                                            type="number" 
                                            step="0.001"
                                            value="{{ $item['quantity'] }}"
                                            wire:blur="setQuantity('{{ $cartKey }}', $event.target.value)"
                                            class="w-14 text-center text-xs font-semibold bg-transparent text-gray-900 dark:text-gray-100 border-0 focus:ring-0 p-0 appearance-none dark:bg-gray-900"
                                        >
                                    @else
                                        <span class="w-7 text-center text-xs font-semibold dark:text-white tabular-nums">{{ $item['quantity'] }}</span>
                                    @endif
                                    
                                    <button wire:click="updateQuantity('{{ $cartKey }}', 1)" class="w-6 h-6 flex items-center justify-center text-emerald-600 dark:text-emerald-400 hover:bg-white dark:hover:bg-gray-600 hover:shadow-xs rounded-lg transition text-xs font-bold">+</button>
                                </div>
                                @if(isset($item['unit_type']) && $item['unit_type'] !== 'unit')
                                    <div class="text-[10px] text-gray-400 mt-0.5 text-right pr-1">{{ $item['unit_type'] }}</div>
                                @endif
                                <div class="mt-1.5 font-semibold text-xs text-gray-900 dark:text-white tabular-nums">
                                    <span class="text-[10px] font-normal text-gray-400 mr-0.5">₲</span>{{ format_pyg($item['price'] * $item['quantity']) }}
                                </div>
                                <button wire:click="removeFromCart('{{ $cartKey }}')" class="text-[10px] text-rose-500 hover:underline mt-0.5 font-medium">Quitar</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-full text-gray-400 dark:text-gray-500">
                    <svg class="w-12 h-12 mb-2 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <p class="text-xs font-medium">El carrito está vacío</p>
                </div>
            @endif
        </div>

        <!-- Cart Footer / Totals -->
        <div class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 p-4 shadow-lg">

            @if($discountManualEnabled && count($cart) > 0)
            <div class="flex items-center gap-2 mb-3 bg-amber-50/80 border border-amber-200/60 rounded-xl px-3 py-1.5 dark:bg-amber-950/30 dark:border-amber-800/40">
                <span class="text-xs font-medium text-amber-800 dark:text-amber-300 whitespace-nowrap">🏷️ Descuento:</span>
                <div class="relative flex-1 max-w-[70px]">
                    <input type="number" wire:model.live.debounce.300ms="manualDiscountPct"
                        min="0" max="100" step="1"
                        class="w-full text-center border border-amber-300 rounded-lg py-0.5 text-xs font-bold text-amber-800 focus:outline-none focus:ring-1 focus:ring-amber-400 bg-white pr-4 dark:bg-gray-800 dark:text-gray-300">
                    <span class="absolute right-1.5 top-0.5 text-amber-500 text-xs font-bold">%</span>
                </div>
                @if($discountAmount > 0)
                <span class="text-xs text-amber-700 dark:text-amber-400 font-medium tabular-nums">- ₲ {{ number_format($discountAmount, 0, ',', '.') }}</span>
                @endif
            </div>
            @endif

            <div class="space-y-1.5 mb-3.5 font-sans">
                <div class="flex justify-between text-gray-500 dark:text-gray-400 text-xs">
                    <span>Subtotal</span>
                    <span class="tabular-nums"><span class="text-[10px] text-gray-400 font-normal mr-0.5">₲</span>{{ number_format($subtotalBeforeDiscount ?: $total, 0, ',', '.') }}</span>
                </div>
                @if($discountAmount > 0)
                <div class="flex justify-between text-rose-600 dark:text-rose-400 text-xs font-medium">
                    <span>Descuento @if($manualDiscountPct > 0)({{ $manualDiscountPct }}%)@endif</span>
                    <span class="tabular-nums">- ₲ {{ number_format($discountAmount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between text-gray-400 dark:text-gray-500 text-[11px]">
                    <span>IVA Incluido (10%)</span>
                    <span class="tabular-nums"><span class="text-[9px] font-normal mr-0.5">₲</span>{{ number_format($total / 11, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center pt-2.5 border-t border-gray-100 dark:border-gray-700">
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Total</span>
                    <span class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums tracking-tight"><span class="text-base font-normal text-emerald-600/70 mr-0.5">₲</span>{{ number_format($total, 0, ',', '.') }}</span>
                </div>

                <!-- Multi-Currency Display -->
                @if($total > 0 && (array_sum($rates) > 0))
                <div class="mt-2 pt-2 border-t border-gray-100 grid grid-cols-2 gap-2 text-[11px] text-gray-400 dark:border-gray-700 dark:text-gray-500">
                    @if($rates['usd'] > 0)
                        <div class="flex justify-between">
                            <span>USD ({{ number_format($rates['usd'], 0, ',', '.') }})</span>
                            <span class="font-medium text-gray-700 dark:text-gray-300 tabular-nums">U$ {{ number_format($total / $rates['usd'], 2, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($rates['brl'] > 0)
                        <div class="flex justify-between">
                            <span>BRL ({{ number_format($rates['brl'], 0, ',', '.') }})</span>
                            <span class="font-medium text-gray-700 dark:text-gray-300 tabular-nums">R$ {{ number_format($total / $rates['brl'], 2, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($rates['ars'] > 0)
                        <div class="flex justify-between">
                            <span>ARS ({{ number_format($rates['ars'], 2, ',', '.') }})</span>
                            <span class="font-medium text-gray-700 dark:text-gray-300 tabular-nums">$ {{ number_format($total / $rates['ars'], 2, ',', '.') }}</span>
                        </div>
                    @endif
                    @if($rates['eur'] > 0)
                        <div class="flex justify-between">
                            <span>EUR ({{ number_format($rates['eur'], 0, ',', '.') }})</span>
                            <span class="font-medium text-gray-700 dark:text-gray-300 tabular-nums">€ {{ number_format($total / $rates['eur'], 2, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
                @endif
            </div>
            
            @if (session()->has('success'))
                <div class="mt-3 bg-emerald-50 border border-emerald-200/80 rounded-2xl p-4 text-center dark:bg-emerald-950/30 dark:border-emerald-800/40">
                    <div class="mb-2 text-emerald-700 dark:text-emerald-300 font-bold text-base flex items-center justify-center">
                        <svg class="w-5 h-5 mr-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        ¡Venta Exitosa!
                    </div>
                    
                    @if(session('last_sale_id'))
                        <a href="{{ route('sales.ticket', session('last_sale_id')) }}" target="_blank" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-xl shadow-sm transition flex items-center justify-center text-xs">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            {{ \App\Services\SifenService::isEnabled() ? 'IMPRIMIR FACTURA ELECTRÓNICA (KuDE)' : 'IMPRIMIR TICKET' }}
                        </a>
                        <button wire:click="$set('lastSaleId', null)" class="mt-2 text-xs text-gray-500 hover:underline dark:text-gray-400">Nueva Venta</button>
                    @endif
                </div>
            @else
                <button 
                    id="btn-pay"
                    wire:click="openPaymentModal" 
                    @if(count($cart) === 0) disabled @endif
                    class="w-full font-semibold py-3.5 rounded-2xl shadow-md transform active:scale-98 transition-all text-base flex justify-center items-center gap-2 {{ count($cart) > 0 ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-600/20' : 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 cursor-not-allowed' }}">
                    <span>COBRAR <small class="opacity-80 font-normal ml-0.5">(F2)</small></span>
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </button>
            @endif

            @if (session()->has('error'))
                <div class="mt-3 bg-rose-50 border border-rose-200 text-rose-700 p-3 rounded-xl text-xs dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800" role="alert">
                    <p class="font-bold">Error</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Movement Modal (Raw HTML) -->
    @if($showMovementModal)
        <div class="fixed z-[100] inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4" id="modal-title">
                                    Registrar Movimiento
                                </h3>
                                
                                <!-- Type Switcher -->
                                <div class="flex rounded-md shadow-sm mb-4 dark:shadow-none" role="group">
                                    <button type="button" wire:click="$set('movementType', 'in')" class="flex-1 py-2 text-sm font-medium border rounded-l-lg focus:z-10 focus:ring-2 {{ $movementType === 'in' ? 'bg-green-600 text-white border-green-600 hover:bg-green-700' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                                        🟢 Ingreso Extra
                                    </button>
                                    <button type="button" wire:click="$set('movementType', 'out')" class="flex-1 py-2 text-sm font-medium border rounded-r-lg focus:z-10 focus:ring-2 {{ $movementType === 'out' ? 'bg-red-600 text-white border-red-600 hover:bg-red-700' : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                                        🔴 Gasto / Retiro
                                    </button>
                                </div>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Monto</label>
                                        <input wire:model="movementAmount" type="number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-gray-700 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline" placeholder="0">
                                        @error('movementAmount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Descripción / Motivo</label>
                                        <input wire:model="movementDescription" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-gray-700 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline" placeholder="Ej: Compra de insumos, Pago delivery, etc.">
                                        @error('movementDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <div class="flex justify-between items-center mb-2">
                                            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold">Categoría (Opcional)</label>
                                            <a href="{{ route('settings.cash-movement-categories') }}" target="_blank" class="text-[10px] text-indigo-600 dark:text-indigo-400 hover:underline font-bold uppercase tracking-widest">
                                                ⚙️ Gestionar
                                            </a>
                                        </div>
                                        <select wire:model="movementCategory" class="shadow border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-gray-700 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline">
                                            <option value="">Sin categoría</option>
                                            @foreach($movementCategories as $cat)
                                                <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('movementCategory') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto dark:shadow-none">
                            <button wire:click="saveMovement" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 text-base leading-6 font-medium text-white shadow-sm focus:outline-none focus:shadow-outline transition ease-in-out duration-150 sm:text-sm sm:leading-5 {{ $movementType === 'in' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} dark:shadow-none">
                                {{ $movementType === 'in' ? 'Guardar Ingreso' : 'Guardar Salida' }}
                            </button>
                        </span>
                        <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto dark:shadow-none">
                            <button wire:click="$set('showMovementModal', false)" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 bg-white dark:bg-gray-700 text-base leading-6 font-medium text-gray-700 dark:text-gray-300 shadow-sm hover:text-gray-500 dark:hover:text-gray-100 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5 dark:shadow-none">
                                Cancelar
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Cash Status Modal -->
    <!-- Cash Status Modal (Consistent Raw HTML) -->
    @if($showStatusModal)
        <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full dark:bg-gray-800">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-gray-800">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 dark:text-white" id="modal-title">
                                    Estado de Caja
                                </h3>
                                
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg dark:bg-gray-900">
                                        <span class="text-gray-600 dark:text-gray-400">Apertura:</span>
                                        <span class="font-mono font-bold">{{ \Carbon\Carbon::parse($sessionDetails['opened_at'])->format('d/m/Y H:i') }}</span>
                                    </div>

                                    <div class="space-y-2">
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">Monto Inicial:</span>
                                            <span class="font-bold text-gray-800 dark:text-white">₲ {{ format_pyg($sessionDetails['opening_amount']) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">Ventas Efectivo:</span>
                                            <span class="font-bold text-green-600">+ ₲ {{ format_pyg($sessionDetails['sales_cash'] ?? 0) }}</span>
                                        </div>
                                        @if(($sessionDetails['sales_total'] - ($sessionDetails['sales_cash'] ?? 0)) > 0)
                                            <div class="flex justify-between items-center text-xs text-gray-400 dark:text-gray-500">
                                                <span>Otros (Tarjeta/Crédito):</span>
                                                <span>₲ {{ format_pyg($sessionDetails['sales_total'] - ($sessionDetails['sales_cash'] ?? 0)) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">Ingresos Extra:</span>
                                            <span class="font-bold text-green-600">+ ₲ {{ format_pyg($sessionDetails['total_in']) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">Gastos / Retiros:</span>
                                            <span class="font-bold text-red-600">- ₲ {{ format_pyg($sessionDetails['total_out']) }}</span>
                                        </div>
                                        
                                        <div class="border-t pt-2 mt-2">
                                            <div class="flex justify-between items-center text-lg">
                                                <span class="font-bold text-gray-800 dark:text-white">Total en Caja:</span>
                                                <span class="font-bold text-indigo-600 dark:text-indigo-400">₲ {{ format_pyg($sessionDetails['expected_total']) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-gray-900">
                        <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto dark:shadow-none">
                            <button wire:click="$set('showStatusModal', false)" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:shadow-none">
                                Cerrar
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Success/Error Toast -->
    <div x-data="{ show: false, message: '', type: 'success' }"
         x-on:session-status-changed.window="message = 'Movimiento registrado'; type = 'success'; show = true; setTimeout(() => show = false, 3000)"
         x-on:success-movement.window="message = $event.detail; type = 'success'; show = true; setTimeout(() => show = false, 3000)"
         x-on:error-movement.window="message = $event.detail; type = 'error'; show = true; setTimeout(() => show = false, 3000)"
         class="fixed bottom-4 right-4 z-50">
        <div x-show="show" x-transition 
             :class="type === 'success' ? 'bg-gray-800 text-white' : 'bg-red-600 text-white'"
             class="px-6 py-3 rounded-lg shadow-lg flex items-center">
            
            <!-- Success Icon -->
            <svg x-show="type === 'success'" class="w-6 h-6 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            
            <!-- Error Icon -->
            <svg x-show="type === 'error'" class="w-6 h-6 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            
            <span x-text="message"></span>
        </div>
    </div>

    <!-- Customer Modal (Matches Customers Module) -->
    @if($showCustomerModal)
        <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                
                <!-- This element is to trick the browser into centering the modal contents. -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full dark:bg-gray-800">
                    <form wire:submit.prevent="saveNewCustomer">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-gray-800">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Registrar Nuevo Cliente
                            </h3>
                            
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                
                                <!-- RUC -->
                                <div class="col-span-1">
                                    <label for="newCustomerRuc" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">RUC / CI <span class="text-red-500">*</span></label>
                                    <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:text-gray-300 dark:bg-gray-900" id="newCustomerRuc" wire:model="newCustomerRuc">
                                    @error('newCustomerRuc') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>

                                <!-- Name -->
                                <div class="col-span-1">
                                    <label for="newCustomerName" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Razón Social / Nombre <span class="text-red-500">*</span></label>
                                    <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:text-gray-300 dark:bg-gray-900" id="newCustomerName" wire:model="newCustomerName">
                                    @error('newCustomerName') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>

                                <!-- Email -->
                                <div class="col-span-1">
                                    <label for="newCustomerEmail" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Email</label>
                                    <input type="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:text-gray-300 dark:bg-gray-900" id="newCustomerEmail" wire:model="newCustomerEmail">
                                    @error('newCustomerEmail') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>

                                <!-- Phone -->
                                <div class="col-span-1">
                                    <label for="newCustomerPhone" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Teléfono</label>
                                    <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:text-gray-300 dark:bg-gray-900" id="newCustomerPhone" wire:model="newCustomerPhone">
                                    @error('newCustomerPhone') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>

                                <!-- Address -->
                                <div class="col-span-1 md:col-span-2">
                                    <label for="newCustomerAddress" class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Dirección</label>
                                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:text-gray-300 dark:bg-gray-900" id="newCustomerAddress" wire:model="newCustomerAddress" rows="2"></textarea>
                                    @error('newCustomerAddress') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>

                                <!-- Activo -->
                                <div class="col-span-1 md:col-span-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" wire:model="newCustomerIsActive" class="form-checkbox h-5 w-5 text-indigo-600 dark:text-indigo-400 dark:bg-gray-900">
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">Cliente Activo</span>
                                    </label>
                                </div>
                            </div>

                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-gray-900">
                            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto dark:shadow-none">
                                <button type="submit" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-indigo-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo transition ease-in-out duration-150 sm:text-sm sm:leading-5 dark:shadow-none">
                                    Guardar Cliente
                                </button>
                            </span>
                            <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto dark:shadow-none">
                                <button wire:click="$set('showCustomerModal', false)" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:shadow-none">
                                    Cancelar
                                </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Product Modal (Consistent Style) -->
    @if($showProductModal)
        <div class="fixed z-50 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full dark:bg-gray-800">
                    <form wire:submit.prevent="saveNewProduct">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 dark:bg-gray-800">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Registrar Nuevo Producto
                            </h3>
                            
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- SKU -->
                                <div class="col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">SKU / Código (Opcional)</label>
                                    <input type="text" wire:model="newProductSku" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:text-gray-300 dark:bg-gray-900" placeholder="Vacío = Auto-generar">
                                    @error('newProductSku') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>

                                <!-- Category -->
                                <div class="col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Categoría</label>
                                    <select wire:model="newProductCategoryId" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:text-gray-300 dark:bg-gray-900">
                                        <option value="">Seleccionar Categoría</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('newProductCategoryId') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>

                                <!-- Unit Type -->
                                <div class="col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Unidad de Medida</label>
                                    <select wire:model="newProductUnitType" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:text-gray-300 dark:bg-gray-900">
                                        <option value="unit">Unidad (Default)</option>
                                        <option value="kg">Kilogramo (kg)</option>
                                        <option value="liter">Litro</option>
                                        <option value="meter">Metro</option>
                                    </select>
                                    @error('newProductUnitType') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>

                                <!-- Name -->
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Nombre del Producto <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="newProductName" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:text-gray-300 dark:bg-gray-900">
                                    @error('newProductName') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>

                                <!-- Cost Price -->
                                <div class="col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Precio Costo (₲) <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="newProductPriceCost" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:text-gray-300 dark:bg-gray-900" 
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    @error('newProductPriceCost') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>

                                <!-- Sale Price -->
                                <div class="col-span-1">
                                    <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Precio Venta (₲) <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="newProductPriceSale" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:text-gray-300 dark:bg-gray-900"
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    @error('newProductPriceSale') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>

                                <!-- Manage Stock Checkbox -->
                                <div class="col-span-1 md:col-span-2">
                                     <label class="inline-flex items-center">
                                        <input type="checkbox" wire:model.live="newProductManageStock" class="form-checkbox h-5 w-5 text-indigo-600 dark:text-indigo-400 dark:bg-gray-900">
                                        <span class="ml-2 text-gray-700 font-bold dark:text-gray-300">Controlar Stock</span>
                                    </label>
                                </div>

                                @if($newProductManageStock)
                                    <!-- Stock -->
                                    <div class="col-span-1">
                                        <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Stock Inicial <span class="text-red-500">*</span></label>
                                        <input type="number" step="0.001" wire:model="newProductStock" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:text-gray-300 dark:bg-gray-900">
                                        @error('newProductStock') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                    </div>

                                    <!-- Min Stock -->
                                    <div class="col-span-1">
                                        <label class="block text-gray-700 text-sm font-bold mb-2 dark:text-gray-300">Stock Mínimo</label>
                                        <input type="number" step="0.001" wire:model="newProductMinStock" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:text-gray-300 dark:bg-gray-900">
                                        @error('newProductMinStock') <span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                    </div>
                                @endif

                                <!-- Active -->
                                <div class="col-span-1 md:col-span-2">
                                     <label class="inline-flex items-center">
                                        <input type="checkbox" wire:model="newProductIsActive" class="form-checkbox h-5 w-5 text-indigo-600 dark:text-indigo-400 dark:bg-gray-900">
                                        <span class="ml-2 text-gray-700 dark:text-gray-300">Producto Activo</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse dark:bg-gray-900">
                            <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto dark:shadow-none">
                                <button type="submit" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-indigo-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo transition ease-in-out duration-150 sm:text-sm sm:leading-5 dark:shadow-none">
                                    Guardar Producto
                                </button>
                            </span>
                            <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto dark:shadow-none">
                                <button wire:click="$set('showProductModal', false)" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:shadow-none">
                                    Cancelar
                                </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Payment Processing Modal -->
    @if($showPaymentModal)
        <div class="fixed z-[60] inset-0 overflow-y-auto flex items-center justify-center p-4 pointer-events-none font-sans" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Fondo Oscuro Backdrop (Click afuera cierra modal) -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity pointer-events-auto" aria-hidden="true" wire:click="$set('showPaymentModal', false)"></div>
            
            <!-- Caja del Modal (relative z-50 para sobresalir y pointer-events-auto para interactuar) -->
            <div class="relative z-50 bg-white rounded-3xl text-left overflow-hidden shadow-2xl border border-gray-100 dark:border-gray-700/80 transform transition-all w-full max-w-md dark:bg-gray-800 flex flex-col max-h-[85vh] sm:max-h-[90vh] pointer-events-auto">
                    <!-- Cabecera Fija -->
                    <div class="bg-white px-5 pt-5 pb-3 border-b border-gray-100 dark:bg-gray-800 dark:border-gray-700 shrink-0">
                        <div class="text-center">
                            <h3 class="text-base font-bold text-gray-900 dark:text-white" id="modal-title">Confirmar Cobro</h3>
                            <div class="mt-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium uppercase tracking-wider">Total a Pagar</p>
                                <p class="text-3xl sm:text-4xl font-bold text-emerald-600 dark:text-emerald-400 tracking-tight tabular-nums mt-0.5"><span class="text-xl font-normal text-emerald-600/70 mr-0.5">₲</span>{{ format_pyg($total) }}</p>
                            </div>
                            
                            @if($total > 0)
                            <div class="mt-3 flex items-center justify-center gap-3 text-xs bg-gray-50 dark:bg-gray-700/50 p-2.5 rounded-xl border border-gray-100 dark:border-gray-700/50">
                                @if(isset($rates['usd']) && $rates['usd'] > 0)
                                    <div class="flex flex-col items-center">
                                        <span class="text-[10px] text-gray-400 dark:text-gray-400 font-medium">Equiv. USD</span>
                                        <span class="font-semibold text-gray-700 dark:text-gray-200 tabular-nums">U$ {{ number_format($total / $rates['usd'], 2, '.', ',') }}</span>
                                    </div>
                                @endif
                                @if(isset($rates['brl']) && $rates['brl'] > 0)
                                    <div class="border-l border-gray-200 dark:border-gray-600 h-6"></div>
                                    <div class="flex flex-col items-center">
                                        <span class="text-[10px] text-gray-400 dark:text-gray-400 font-medium">Equiv. BRL</span>
                                        <span class="font-semibold text-gray-700 dark:text-gray-200 tabular-nums">R$ {{ number_format($total / $rates['brl'], 2, '.', ',') }}</span>
                                    </div>
                                @endif
                                @if(isset($rates['ars']) && $rates['ars'] > 0)
                                    <div class="border-l border-gray-200 dark:border-gray-600 h-6"></div>
                                    <div class="flex flex-col items-center">
                                        <span class="text-[10px] text-gray-400 dark:text-gray-400 font-medium">Equiv. ARS</span>
                                        <span class="font-semibold text-gray-700 dark:text-gray-200 tabular-nums">A$ {{ number_format($total / $rates['ars'], 0, '', '.') }}</span>
                                    </div>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Cuerpo Desplazable del Modal -->
                    <div class="bg-white px-5 py-4 dark:bg-gray-800 overflow-y-auto flex-1 scrollbar-thin">
                        <!-- Condition Selection -->
                        <div class="mb-4">
                            <label class="block text-xs font-medium text-gray-600 mb-2 dark:text-gray-300">Condición de Venta</label>
                            <div class="flex items-center space-x-6 mt-1">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="paymentCondition" value="contado" wire:model.live="paymentCondition" class="form-radio h-4 w-4 text-emerald-600 transition duration-150 ease-in-out dark:text-emerald-400 dark:bg-gray-900">
                                    <span class="ml-2 text-xs font-semibold text-gray-700 dark:text-gray-300">Contado</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" name="paymentCondition" value="credito" wire:model.live="paymentCondition" class="form-radio h-4 w-4 text-amber-600 transition duration-150 ease-in-out dark:bg-gray-900 dark:text-gray-300">
                                    <span class="ml-2 text-xs font-semibold text-gray-700 dark:text-gray-300">Crédito</span>
                                </label>
                            </div>
                        </div>

                        @if($paymentCondition === 'contado')
                            <!-- Seller Selector -->
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Vendedor</label>
                                <select wire:model="sellerId" class="block w-full text-xs border-gray-200 rounded-xl py-2 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700">
                                    <option value="">— Sin vendedor asignado —</option>
                                    @foreach($sellerOptions as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Payment Method Selection -->
                            <div class="mb-4">
                                <label class="block text-xs font-medium text-gray-600 mb-2 dark:text-gray-300">Método de Pago</label>
                                <div class="grid grid-cols-3 gap-1.5">
                                    @foreach($paymentMethods as $pm)
                                        @php
                                            $isCash = ($pm->type === 'cash' || strtolower($pm->name) === 'efectivo');
                                            $methodVal = $isCash ? 'efectivo' : strtolower($pm->name);
                                            $isSelected = (strtolower($paymentMethod) === strtolower($methodVal) || ($isCash && strtolower($paymentMethod) === 'efectivo'));
                                        @endphp
                                        <button type="button" wire:click="$set('paymentMethod', '{{ $methodVal }}')" class="flex items-center justify-center gap-1.5 px-2 py-2.5 border rounded-xl text-xs font-semibold tracking-wide transition-all {{ $isSelected ? 'bg-indigo-600 border-indigo-600 text-white shadow-xs' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300' }}">
                                            @if($isCash)
                                                <svg class="w-4 h-4 shrink-0 {{ $isSelected ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <rect x="2" y="6" width="20" height="12" rx="2" stroke-width="1.8"/>
                                                    <circle cx="12" cy="12" r="2.5" stroke-width="1.8"/>
                                                    <path stroke-linecap="round" stroke-width="1.8" d="M6 10h.01M18 14h.01"/>
                                                </svg>
                                            @elseif($pm->type === 'card' || strtolower($pm->name) === 'tarjeta')
                                                <svg class="w-4 h-4 shrink-0 {{ $isSelected ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="1.8"/>
                                                    <line x1="2" y1="10" x2="22" y2="10" stroke-width="1.8"/>
                                                    <path stroke-linecap="round" stroke-width="1.8" d="M6 15h4"/>
                                                </svg>
                                            @elseif($pm->type === 'qr' || strtolower($pm->name) === 'qr')
                                                <svg class="w-4 h-4 shrink-0 {{ $isSelected ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                                </svg>
                                            @elseif($pm->type === 'transfer' || strtolower($pm->name) === 'transferencia')
                                                <svg class="w-4 h-4 shrink-0 {{ $isSelected ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M3 14h18M5 10v6m4-6v6m6-6v6m4-6v6M3 18h18M4 10l8-6 8 6"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 shrink-0 {{ $isSelected ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v8m-3-5h6"/>
                                                </svg>
                                            @endif
                                            <span>{{ $pm->name }}</span>
                                        </button>
                                    @endforeach

                                    <button type="button" wire:click="$set('paymentMethod', 'cheque')" class="flex items-center justify-center gap-1.5 px-2 py-2.5 border rounded-xl text-xs font-semibold tracking-wide transition-all {{ strtolower($paymentMethod) === 'cheque' ? 'bg-indigo-600 border-indigo-600 text-white shadow-xs' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300' }}">
                                        <svg class="w-4 h-4 shrink-0 {{ strtolower($paymentMethod) === 'cheque' ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <rect x="3" y="6" width="18" height="12" rx="2" stroke-width="1.8"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 12h5m-5 3h2m4-3l2 2 4-4"/>
                                        </svg>
                                        <span>Cheque</span>
                                    </button>
                                    <button type="button" wire:click="$set('paymentMethod', 'multiple')" class="flex items-center justify-center gap-1.5 px-2 py-2.5 border rounded-xl text-xs font-semibold tracking-wide transition-all {{ strtolower($paymentMethod) === 'multiple' ? 'bg-indigo-600 border-indigo-600 text-white shadow-xs' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300' }}">
                                        <svg class="w-4 h-4 shrink-0 {{ strtolower($paymentMethod) === 'multiple' ? 'text-white' : 'text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                        <span>Múltiple</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Cash Payment Logic -->
                            @if($paymentMethod === 'efectivo')
                                <div class="bg-gray-50/80 p-3.5 rounded-2xl border border-gray-100 dark:bg-gray-900/60 dark:border-gray-700">
                                    <div class="mb-3">
                                        <label class="block text-xs font-medium text-gray-600 mb-1 dark:text-gray-300">Monto Recibido</label>
                                        <div class="relative rounded-xl shadow-xs">
                                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                                <span class="text-gray-400 text-xs font-normal">₲</span>
                                            </div>
                                            <input type="number" wire:model.live.debounce.300ms="receivedAmount" class="focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full pl-8 pr-4 sm:text-base border-gray-200 rounded-xl py-2 text-right font-bold text-gray-900 dark:border-gray-600 dark:text-white dark:bg-gray-900" placeholder="0">
                                        </div>
                                        @error('receivedAmount') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="flex justify-between items-center pt-2.5 border-t border-gray-100 dark:border-gray-700">
                                        <span class="text-gray-500 font-medium text-xs dark:text-gray-400">Vuelto / Cambio:</span>
                                        <span class="text-xl font-bold tabular-nums {{ $change < 0 ? 'text-rose-600' : 'text-emerald-600 dark:text-emerald-400' }}"><span class="text-sm font-normal mr-0.5">₲</span>{{ format_pyg($change) }}</span>
                                    </div>
                                </div>
                            @elseif($paymentMethod === 'cheque')
                                <div class="bg-gray-50/80 p-3.5 rounded-2xl border border-gray-100 space-y-2.5 dark:bg-gray-900/60 dark:border-gray-700">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500 mb-1 dark:text-gray-400">Banco</label>
                                        <input type="text" wire:model="chequeDetails.bank_name" class="border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full text-xs rounded-xl py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300" placeholder="Nombre del Banco">
                                        @error('chequeDetails.bank_name') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1 dark:text-gray-400">Nro. Cheque</label>
                                            <input type="text" wire:model="chequeDetails.cheque_number" class="border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full text-xs rounded-xl py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300" placeholder="00000000">
                                            @error('chequeDetails.cheque_number') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1 dark:text-gray-400">Fecha de Cobro</label>
                                            <input type="date" wire:model="chequeDetails.due_date" class="border-gray-200 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full text-xs rounded-xl py-2 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300">
                                            @error('chequeDetails.due_date') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="bg-blue-50/80 border border-blue-200/60 p-2.5 rounded-xl text-center mt-2 dark:bg-blue-950/30">
                                        <p class="text-blue-800 dark:text-blue-300 text-xs font-semibold">Total a Pagar: ₲ {{ format_pyg($total) }}</p>
                                    </div>
                                </div>
                            @elseif($paymentMethod === 'multiple')
                                <div class="bg-gray-50/80 p-3.5 rounded-2xl border border-gray-100 space-y-3 dark:bg-gray-900/60 dark:border-gray-700">
                                    <!-- Badges Seleccionables de Monedas -->
                                    <div>
                                        <label class="block text-[10px] font-medium text-gray-400 uppercase tracking-wider mb-1.5">¿Recibir Moneda Extranjera?</label>
                                        <div class="flex flex-wrap gap-1.5">
                                            @if(isset($rates['usd']) && $rates['usd'] > 0)
                                                <button type="button" wire:click="toggleCurrency('usd')" 
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border transition-all active:scale-95 {{ in_array('usd', $activeCurrencies) ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300' }}">
                                                    USD ($)
                                                </button>
                                            @endif
                                            @if(isset($rates['brl']) && $rates['brl'] > 0)
                                                <button type="button" wire:click="toggleCurrency('brl')" 
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border transition-all active:scale-95 {{ in_array('brl', $activeCurrencies) ? 'bg-emerald-600 border-emerald-600 text-white shadow-sm' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300' }}">
                                                    BRL (R$)
                                                </button>
                                            @endif
                                            @if(isset($rates['ars']) && $rates['ars'] > 0)
                                                <button type="button" wire:click="toggleCurrency('ars')" 
                                                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border transition-all active:scale-95 {{ in_array('ars', $activeCurrencies) ? 'bg-rose-600 border-rose-600 text-white shadow-sm' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300' }}">
                                                    ARS (A$)
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2.5 pt-1">
                                        <div>
                                            <label class="block text-[11px] font-medium text-gray-500 mb-0.5 dark:text-gray-400">Efectivo ₲ (PYG)</label>
                                            <input type="number" wire:model.live.debounce.300ms="multiplePayments.efectivo" class="focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full text-xs border-gray-200 rounded-xl py-1.5 text-right dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 font-bold tabular-nums" placeholder="0">
                                        </div>
                                        
                                        @if(isset($rates['usd']) && $rates['usd'] > 0 && in_array('usd', $activeCurrencies))
                                        <div class="transition-all duration-300">
                                            <div class="flex justify-between items-center mb-0.5">
                                                <label class="block text-[11px] font-medium text-indigo-600 dark:text-indigo-400">Efectivo $ (USD)</label>
                                                <span class="text-[9px] text-gray-400">Cot: {{ number_format($rates['usd'], 0, '', '.') }}</span>
                                            </div>
                                            <input type="number" step="any" wire:model.live.debounce.300ms="multiplePayments.efectivo_usd" class="focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full text-xs border-indigo-200 rounded-xl py-1.5 text-right dark:border-gray-600 dark:bg-gray-900 font-bold text-indigo-600 tabular-nums" placeholder="0.00">
                                        </div>
                                        @endif

                                        @if(isset($rates['brl']) && $rates['brl'] > 0 && in_array('brl', $activeCurrencies))
                                        <div class="transition-all duration-300">
                                            <div class="flex justify-between items-center mb-0.5">
                                                <label class="block text-[11px] font-medium text-emerald-600 dark:text-emerald-400">Efectivo R$ (BRL)</label>
                                                <span class="text-[9px] text-gray-400">Cot: {{ number_format($rates['brl'], 0, '', '.') }}</span>
                                            </div>
                                            <input type="number" step="any" wire:model.live.debounce.300ms="multiplePayments.efectivo_brl" class="focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 block w-full text-xs border-emerald-200 rounded-xl py-1.5 text-right dark:border-gray-600 dark:bg-gray-900 font-bold text-emerald-600 tabular-nums" placeholder="0.00">
                                        </div>
                                        @endif

                                        @if(isset($rates['ars']) && $rates['ars'] > 0 && in_array('ars', $activeCurrencies))
                                        <div class="transition-all duration-300">
                                            <div class="flex justify-between items-center mb-0.5">
                                                <label class="block text-[11px] font-medium text-rose-500">Efectivo A$ (ARS)</label>
                                                <span class="text-[9px] text-gray-400">Cot: {{ number_format($rates['ars'], 0, '', '.') }}</span>
                                            </div>
                                            <input type="number" step="any" wire:model.live.debounce.300ms="multiplePayments.efectivo_ars" class="focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 block w-full text-xs border-rose-200 rounded-xl py-1.5 text-right dark:border-gray-600 dark:bg-gray-900 font-bold text-rose-500 tabular-nums" placeholder="0">
                                        </div>
                                        @endif

                                        @foreach($paymentMethods as $pm)
                                            @if($pm->type !== 'cash' && strtolower($pm->name) !== 'efectivo')
                                                <div>
                                                    <label class="block text-[11px] font-medium text-gray-500 mb-0.5 dark:text-gray-400">{{ $pm->name }}</label>
                                                    <input type="number" wire:model.live.debounce.300ms="multiplePayments.{{ $pm->name }}" class="focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 block w-full text-xs border-gray-200 rounded-xl py-1.5 text-right dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 font-bold tabular-nums" placeholder="0">
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    <div class="flex justify-between items-center pt-2.5 border-t border-gray-100 dark:border-gray-700">
                                        <span class="text-gray-500 font-medium text-xs dark:text-gray-400">Total Pagado:</span>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">₲ {{ format_pyg($receivedAmount) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500 font-medium text-xs dark:text-gray-400">Faltante / Vuelto:</span>
                                        <span class="text-sm font-bold tabular-nums {{ $change < 0 ? 'text-rose-600' : 'text-emerald-600' }}">₲ {{ format_pyg($change) }}</span>
                                    </div>
                                    @error('multiplePayments') <span class="text-rose-500 text-xs font-medium block text-center">{{ $message }}</span> @enderror
                                </div>
                            @else
                                <div class="bg-blue-50/80 border border-blue-200/60 p-3.5 rounded-2xl text-center dark:bg-blue-950/30">
                                    <p class="text-blue-800 dark:text-blue-300 text-xs">Se registrará el pago por el monto exacto.</p>
                                    <p class="text-base font-bold text-blue-900 dark:text-blue-200 mt-1 tabular-nums">₲ {{ format_pyg($total) }}</p>
                                </div>
                            @endif
                        @else
                            <!-- Credit Info -->
                             <div class="bg-amber-50/80 border border-amber-200/60 p-3.5 rounded-2xl text-center mb-4 dark:bg-amber-950/30 dark:border-amber-800/40">
                                <p class="text-amber-800 dark:text-amber-300 text-xs font-bold uppercase tracking-wider">VENTA A CRÉDITO</p>
                                <p class="text-xs text-gray-500 mt-1 dark:text-gray-400">La venta se registrará como cuenta por cobrar pendiente.</p>
                                <p class="text-base font-bold text-amber-900 dark:text-amber-200 mt-1 tabular-nums">₲ {{ format_pyg($total) }}</p>
                            </div>
                        @endif

                        @if (session()->has('error') || $errors->any())
                            <div class="mt-3 bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-3 text-xs rounded-r-xl dark:bg-rose-950/40 dark:text-rose-400" role="alert">
                                <p class="font-bold">Error al procesar</p>
                                @if(session()->has('error'))
                                    <p>{{ session('error') }}</p>
                                @endif
                                @if($errors->any())
                                    <ul class="list-disc list-inside mt-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endif

                    </div>

                    <!-- Botones Fijos Abajo -->
                    <div class="bg-gray-50/60 px-5 py-3.5 sm:flex sm:flex-row-reverse dark:bg-gray-900 border-t border-gray-100 dark:border-gray-800 shrink-0">
                        <span class="flex w-full sm:ml-3 sm:w-auto">
                            <button wire:click="processSale" type="button" 
                                class="inline-flex justify-center w-full rounded-xl px-5 py-2.5 text-xs font-semibold text-white shadow-sm transition {{ $paymentCondition === 'contado' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-amber-600 hover:bg-amber-700' }} {{ ($paymentCondition === 'contado' && (($paymentMethod === 'efectivo' && $change < 0) || ($paymentMethod === 'multiple' && $change < 0))) ? 'opacity-50 cursor-not-allowed' : '' }}"
                                @if($paymentCondition === 'contado' && (($paymentMethod === 'efectivo' && $change < 0) || ($paymentMethod === 'multiple' && $change < 0))) disabled @endif
                            >
                                {{ $paymentCondition === 'contado' ? 'CONFIRMAR COBRO' : 'CONFIRMAR CRÉDITO' }}
                            </button>
                        </span>
                        <span class="mt-2 flex w-full sm:mt-0 sm:w-auto">
                            <button wire:click="$set('showPaymentModal', false)" type="button" class="inline-flex justify-center w-full rounded-xl border border-gray-200 px-5 py-2.5 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 transition dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                Cancelar
                            </button>
                        </span>
                    </div>
                </div>
        </div>
    @endif
    <!-- Mobile Cart FAB -->
    <button 
        wire:click="toggleMobileCart" 
        class="md:hidden fixed bottom-6 right-6 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full p-4 shadow-2xl z-40 transition-transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
        <div class="relative">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            @if(count($cart) > 0)
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full border border-white min-w-[1.25rem] text-center">{{ count($cart) }}</span>
            @endif
        </div>
    </button>

    <!-- Mobile Cart Overlay -->
    @if($showMobileCart)
        <div class="fixed inset-0 z-50 bg-white flex flex-col md:hidden dark:bg-gray-800 font-sans">
            <!-- Mobile Cart Header -->
            <div class="p-4 border-b border-gray-100 bg-gray-50/80 flex justify-between items-center dark:border-gray-700 dark:bg-gray-900/60">
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-gray-900 flex items-center dark:text-white">
                        <svg class="w-5 h-5 mr-1.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Carrito
                    </h2>
                    <span class="text-xs font-semibold bg-indigo-50 border border-indigo-200/60 text-indigo-700 px-2.5 py-0.5 rounded-full dark:bg-indigo-950/40 dark:text-indigo-300">{{ count($cart) }} {{ count($cart) === 1 ? 'ítem' : 'ítems' }}</span>
                </div>
                <button wire:click="toggleMobileCart" class="text-gray-400 hover:text-rose-500 p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Mobile Cart Content -->
            <div class="flex-1 overflow-y-auto p-4 bg-gray-50/50 dark:bg-gray-900/50">
                <!-- Customer Mobile -->
                <div class="mb-4">
                     @if($selectedCustomer)
                        <div class="flex justify-between items-center bg-white border border-emerald-200/80 rounded-2xl p-3 shadow-xs dark:bg-gray-800 dark:border-emerald-800/40">
                            <div class="flex items-center">
                                <div class="bg-emerald-50 p-2 rounded-xl text-emerald-600 mr-2.5 dark:bg-emerald-950/40 dark:text-emerald-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                                <div class="leading-tight">
                                    <div class="font-semibold text-gray-900 text-xs truncate max-w-[200px] dark:text-white">{{ $selectedCustomer['name'] }}</div>
                                    <div class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $selectedCustomer['ruc'] }}</div>
                                </div>
                            </div>
                            <button wire:click="removeCustomer" class="text-gray-400 hover:text-rose-500 p-1 transition" title="Cambiar cliente">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @else
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input wire:model.live.debounce.300ms="customerSearch" type="text" 
                                class="w-full pl-9 pr-4 py-2.5 text-xs border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 shadow-xs" 
                                placeholder="Buscar Cliente (Nombre / RUC)...">
                        </div>
                        @if(!empty($customerSearch) && !$selectedCustomer)
                            <div class="mt-2 bg-white border border-gray-100 rounded-xl shadow-xl max-h-48 overflow-y-auto dark:bg-gray-800 dark:border-gray-700">
                                @forelse($searchResults as $result)
                                    <button wire:click="selectCustomer('{{ $result['id'] }}')" class="w-full text-left px-4 py-2.5 text-xs hover:bg-gray-50 flex justify-between items-center border-b border-gray-50 dark:border-gray-700 last:border-0 dark:hover:bg-gray-700">
                                        <span class="font-medium text-gray-900 dark:text-gray-200">{{ $result['name'] }}</span>
                                        <span class="text-[11px] text-gray-400 font-mono">{{ $result['ruc'] }}</span>
                                    </button>
                                @empty
                                    <div class="px-4 py-3 text-xs text-gray-500 text-center dark:text-gray-400">No encontrado</div>
                                @endforelse
                            </div>
                        @endif
                    @endif
                </div>

                @if(count($cart) > 0)
                    <div class="space-y-2.5">
                        @foreach($cart as $cartKey => $item)
                            <div class="bg-white p-3.5 rounded-2xl shadow-xs border border-gray-100 dark:bg-gray-800 dark:border-gray-700" wire:key="mobile-cart-{{ $cartKey }}">
                                <div class="flex justify-between items-start gap-2">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900 text-xs dark:text-white leading-snug">{{ $item['name'] }}</h4>
                                        <div class="text-[10px] text-gray-400 font-mono mt-0.5">SKU: {{ $item['sku'] }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1 tabular-nums">
                                            <span class="text-[10px] font-normal text-gray-400 mr-0.5">₲</span>{{ format_pyg($item['price']) }}
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-1">
                                        <div class="flex items-center bg-gray-50 dark:bg-gray-700/60 border border-gray-200/60 dark:border-gray-600 rounded-xl p-0.5">
                                            <button wire:click="updateQuantity('{{ $cartKey }}', -1)" class="w-6 h-6 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-white rounded-lg font-bold text-xs">-</button>
                                            <span class="w-7 text-center text-xs font-semibold dark:text-white tabular-nums">{{ $item['quantity'] }}</span>
                                            <button wire:click="updateQuantity('{{ $cartKey }}', 1)" class="w-6 h-6 flex items-center justify-center text-emerald-600 dark:text-emerald-400 hover:bg-white rounded-lg font-bold text-xs">+</button>
                                        </div>
                                        <button wire:click="removeFromCart('{{ $cartKey }}')" class="text-[10px] text-rose-500 hover:underline font-medium">Quitar</button>
                                    </div>
                                </div>
                                <div class="mt-2.5 pt-2 border-t border-gray-50 dark:border-gray-700 flex justify-between items-center">
                                    <span class="text-[11px] text-gray-400">Total ítem:</span>
                                    <span class="text-xs font-semibold text-gray-900 dark:text-white tabular-nums"><span class="text-[10px] font-normal text-gray-400 mr-0.5">₲</span>{{ format_pyg($item['price'] * $item['quantity']) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center h-64 text-gray-400 dark:text-gray-500">
                        <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <p class="text-xs font-medium">El carrito está vacío</p>
                    </div>
                @endif
            </div>

            <!-- Mobile Cart Footer -->
            <div class="bg-white border-t border-gray-100 p-4 shadow-lg z-20 dark:bg-gray-800 dark:border-gray-700">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Total a Pagar</span>
                    <span class="text-xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums"><span class="text-sm font-normal text-emerald-600/70 mr-0.5">₲</span>{{ format_pyg($total) }}</span>
                </div>
                
                @if(count($cart) > 0)
                    <button 
                        wire:click="openPaymentModal"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold py-3.5 rounded-2xl shadow-md text-sm flex justify-center items-center gap-2 transition active:scale-[0.98]"
                    >
                        <span>COBRAR <small class="opacity-80 font-normal">(₲ {{ format_pyg($total) }})</small></span>
                    </button>
                    <button wire:click="clearCart" wire:confirm="¿Vaciar todo el carrito?" class="w-full mt-2 text-rose-500 hover:text-rose-700 text-xs font-medium py-1.5 transition">
                        Vaciar Carrito
                    </button>
                @endif
            </div>
        </div>
    @endif

<script>
    document.addEventListener('keydown', function(event) {
        if(event.key === 'Escape') { 
            let active = document.activeElement;
            if(active.id === 'product-search-input') { 
                @this.set('search', ''); 
                active.blur(); 
            }
            else if(active.id === 'customer-search-input') { 
                @this.set('customerSearch', ''); 
                active.blur(); 
            }
            else { 
                active.blur(); 
            }
        }
    });

    document.addEventListener('print-ticket', function(event) {
        // Livewire 3: event.detail is the array/object passed in dispatch
        // dispatch('print-ticket', ['saleId' => 123]) -> detail: { saleId: 123 }
        let saleId = event.detail.saleId; 
        if(!saleId && event.detail[0]) saleId = event.detail[0].saleId; // Fallback

        if(saleId) {
            let url = "{{ route('sales.ticket', ':id') }}".replace(':id', saleId);
            window.open(url, 'Ticket', 'width=400,height=600,scrollbars=yes');
        } else {
            console.error('No Sale ID provided for printing');
        }
    });
</script>

@if(session()->has('last_sale_id'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let saleId = "{{ session('last_sale_id') }}";
            let url = "{{ route('sales.ticket', ':id') }}".replace(':id', saleId);
            // Open in a popup window (width 400px is enough for thermal receipt)
            window.open(url, 'Ticket', 'width=400,height=600,scrollbars=yes');
        });
    </script>
@endif

<!-- ============ VOID SALE MODAL ============ -->
@if($showVoidModal)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 dark:bg-gray-800" x-data>       
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">🚫 Anular Venta</h3>
            <button wire:click="$set('showVoidModal', false)" class="text-gray-400 hover:text-gray-600 dark:text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="px-6 py-4">
            <input wire:model.live.debounce.400ms="voidSearch" type="text"
                placeholder="Buscar por cliente o ID..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-4 focus:ring-orange-300 focus:border-orange-400 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300">

            <div class="space-y-2 max-h-64 overflow-y-auto">
                @forelse($recentSales as $sale)
                    <div class="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-2.5 border border-gray-200 dark:bg-gray-900 dark:border-gray-700">
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                {{ $sale['customer']['name'] ?? 'Sin cliente' }}
                                <span class="text-xs text-gray-400 ml-2 dark:text-gray-500">{{ substr($sale['id'], -8) }}</span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                ₲ {{ number_format($sale['total'] ?? 0, 0, ',', '.') }}
                                · {{ isset($sale['created_at']) ? \Carbon\Carbon::parse($sale['created_at'])->format('H:i') : '' }}
                            </p>
                        </div>
                        <button wire:click="voidSale('{{ $sale['id'] }}')"
                            onclick="return confirm('¿Anular esta venta?')"
                            class="ml-4 px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-lg hover:bg-red-200 transition dark:bg-red-900/40 dark:text-red-400">
                            Anular
                        </button>
                    </div>
                @empty
                    <p class="text-center text-sm text-gray-400 py-4 dark:text-gray-500">No hay ventas que mostrar.</p>
                @endforelse
            </div>
        </div>

        <div class="px-6 py-3 bg-gray-50 rounded-b-xl border-t dark:bg-gray-900">
            <p class="text-xs text-gray-400 dark:text-gray-500">Se muestran las ventas del día de hoy. El stock se restaura automáticamente.</p>
        </div>
    </div>
</div>
@endif

    <!-- Web Orders List Modal -->
    @if($showWebOrdersModal)
        <div class="fixed z-[100] inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-white/20 dark:bg-gray-800">
                    <div class="bg-gray-50/50 dark:bg-gray-900/50 px-6 py-4 border-b border-gray-100 flex justify-between items-center dark:border-gray-700">
                        <div class="flex items-center gap-3">
                            <div class="bg-indigo-600 p-2 rounded-xl text-white shadow-lg shadow-indigo-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3z"></path></svg>
                            </div>
                            <h3 class="text-xl font-black text-gray-900 uppercase tracking-tighter dark:text-white">Pedidos Web Pendientes</h3>
                        </div>
                        <button wire:click="$set('showWebOrdersModal', false)" class="text-gray-400 hover:text-gray-600 transition dark:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="p-6">
                        @if(empty($pendingWebOrders))
                            <div class="flex flex-col items-center justify-center py-12 text-gray-400 italic dark:text-gray-500">
                                <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-lg font-bold tracking-tight uppercase">No hay pedidos web pendientes</p>
                            </div>
                        @else
                            <div class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-700">
                                <table class="w-full text-left">
                                    <thead>
                                        <tr class="bg-gray-50 text-[10px] font-black text-gray-400 uppercase tracking-widest dark:bg-gray-900 dark:text-gray-500">
                                            <th class="px-4 py-3">Fecha</th>
                                            <th class="px-4 py-3">Cliente</th>
                                            <th class="px-4 py-3">Productos</th>
                                            <th class="px-4 py-3 text-right">Total</th>
                                            <th class="px-4 py-3 text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        @foreach($pendingWebOrders as $order)
                                            <tr class="hover:bg-indigo-50/30 transition-colors">
                                                <td class="px-4 py-4">
                                                    <div class="text-[10px] font-bold text-gray-900 uppercase dark:text-white">{{ \Carbon\Carbon::parse($order['created_at'])->diffForHumans() }}</div>
                                                    <div class="text-[9px] text-gray-400 tabular-nums dark:text-gray-500">{{ \Carbon\Carbon::parse($order['created_at'])->format('d/m/Y H:i') }}</div>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <div class="text-xs font-black text-gray-900 uppercase truncate max-w-[150px] dark:text-white">{{ $order['customer_name'] }}</div>
                                                    <div class="text-[10px] font-bold text-indigo-600 tabular-nums dark:text-indigo-400">{{ $order['customer_phone'] }}</div>
                                                </td>
                                                <td class="px-4 py-4">
                                                    <div class="text-[10px] text-gray-600 line-clamp-2 dark:text-gray-400">
                                                        @if(!empty($order['items_json']))
                                                            @foreach($order['items_json'] as $item)
                                                                <span class="font-bold text-indigo-500">{{ $item['quantity'] }}x</span> {{ $item['name'] }}{{ !$loop->last ? ',' : '' }}
                                                            @endforeach
                                                        @else
                                                            {{ $order['product']['name'] ?? 'Producto' }}
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 text-right">
                                                    <div class="text-sm font-black text-gray-900 tabular-nums dark:text-white">₲ {{ number_format($order['total'], 0, ',', '.') }}</div>
                                                    <div class="text-[9px] font-bold uppercase {{ $order['payment_method'] === 'transferencia' ? 'text-indigo-500' : 'text-emerald-500' }}">
                                                        {{ $order['payment_method'] }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <button wire:click="importWebOrder('{{ $order['id'] }}')" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition shadow-lg shadow-indigo-100 active:scale-95">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                        Importar
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex justify-end dark:bg-gray-900">
                        <button wire:click="$set('showWebOrdersModal', false)" class="px-6 py-2 bg-white text-gray-500 font-bold text-[10px] uppercase tracking-widest rounded-xl hover:bg-gray-100 transition border border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:border-gray-700">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!-- Variation Selection Modal -->
    @if($showVariationModal && $selectingProduct)
        <div class="fixed z-[100] inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="$set('showVariationModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full dark:bg-gray-800">
                    <div class="bg-white p-6 dark:bg-gray-800">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-xl font-black text-gray-900 uppercase tracking-tighter dark:text-white" id="modal-title">
                                    Seleccionar Variante
                                </h3>
                                <p class="text-sm text-gray-500 font-bold dark:text-gray-400">{{ $selectingProduct->name }}</p>
                            </div>
                            <button wire:click="$set('showVariationModal', false)" class="text-gray-400 hover:text-gray-500 dark:text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 gap-3 max-h-[60vh] overflow-y-auto pr-2">
                            @foreach($selectingProduct->variations as $var)
                                @php
                                    $varCurrentStock = $var->stock;
                                    $varOtherStock = 0;
                                    if (isset($branchId) && $branchId) {
                                        $varBranchStock = $var->branchStocks->where('branch_id', $branchId)->first();
                                        $varCurrentStock = $varBranchStock ? $varBranchStock->stock : 0;
                                        $varOtherStock = $var->branchStocks->where('branch_id', '!=', $branchId)->sum('stock');
                                    }
                                    $varOutOfStock = $selectingProduct->manage_stock && $varCurrentStock <= 0;
                                @endphp
                                <button 
                                    wire:click="selectVariation('{{ $var->id }}')"
                                    @if($varOutOfStock) disabled @endif
                                    class="flex justify-between items-center p-4 rounded-xl border-2 transition-all group {{ $varOutOfStock ? 'bg-gray-50 border-gray-100 opacity-60 cursor-not-allowed' : 'border-gray-100 hover:border-indigo-500 hover:bg-indigo-50' }} dark:border-gray-700"
                                >
                                    <div class="text-left">
                                        <div class="font-black text-gray-900 uppercase tracking-tight group-hover:text-indigo-700 dark:text-white">{{ $var->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-mono dark:text-gray-500">{{ $var->sku }}</div>
                                    </div>
                                    <div class="text-right flex flex-col items-end">
                                        <div class="font-black text-indigo-600 dark:text-indigo-400">₲ {{ format_pyg($var->price_sale) }}</div>
                                        <div class="text-[10px] {{ $varCurrentStock <= 0 ? 'text-red-500' : 'text-gray-500' }}">
                                            Stock: {{ $varCurrentStock }}
                                        </div>
                                        @if($selectingProduct->manage_stock && $varCurrentStock <= 0 && $varOtherStock > 0)
                                            <div class="text-[9px] text-blue-500 font-bold mt-0.5">Otras suc: {{ $varOtherStock }}</div>
                                        @endif
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex justify-end dark:bg-gray-900">
                <button wire:click="$set('showVariationModal', false)" type="button" class="px-6 py-2 bg-white text-gray-500 font-bold text-[10px] uppercase tracking-widest rounded-xl hover:bg-gray-100 transition border border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:border-gray-700">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- POS Bottom Hotkeys Bar --}}
    <div class="fixed bottom-0 left-0 right-0 lg:right-[380px] bg-slate-900/90 backdrop-blur-md border-t border-slate-850 px-4 py-1.5 flex items-center justify-between text-[11px] text-slate-300 z-30 shadow-lg select-none">
        <div class="flex items-center gap-4 overflow-x-auto py-0.5">
            <span class="flex items-center gap-1.5 cursor-pointer hover:text-white" onclick="document.getElementById('product-search-input')?.focus()">
                <kbd class="px-1.5 py-0.5 font-mono text-[10px] bg-slate-800 border border-slate-700 text-indigo-400 font-bold rounded">F3</kbd>
                <span>Buscar Producto</span>
            </span>
            <span class="flex items-center gap-1.5 cursor-pointer hover:text-white" wire:click="openCustomerModal">
                <kbd class="px-1.5 py-0.5 font-mono text-[10px] bg-slate-800 border border-slate-700 text-blue-400 font-bold rounded">F4</kbd>
                <span>Cliente</span>
            </span>
            @if(count($cart) > 0)
            <span class="flex items-center gap-1.5 cursor-pointer hover:text-emerald-300 font-bold text-emerald-400" wire:click="openPaymentModal">
                <kbd class="px-1.5 py-0.5 font-mono text-[10px] bg-emerald-950 border border-emerald-700 text-emerald-300 font-bold rounded">F2</kbd>
                <span>Cobrar Venta</span>
            </span>
            @endif
            @if($isRestaurantMode)
            <span class="flex items-center gap-1.5 cursor-pointer hover:text-white" wire:click="$set('showTableMap', true)">
                <kbd class="px-1.5 py-0.5 font-mono text-[10px] bg-slate-800 border border-slate-700 text-amber-400 font-bold rounded">F8</kbd>
                <span>Mesas</span>
            </span>
            @endif
        </div>
        <div class="hidden sm:flex items-center gap-2 text-slate-500 font-mono text-[10px]">
            <span>[ESC] Cerrar / Cancelar</span>
        </div>
    </div>

    @endif {{-- end @else (restaurant normal mode or non-restaurant mode) --}}

</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('openComanda', ({ url }) => {
            window.open(url, '_blank');
        });

        // 1. Sintetizador de sonido Beep nativo (como caja de supermercado)
        function playPosTone(type) {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);

                if (type === 'success') {
                    // Beep agudo corto y agradable (1400Hz, 70ms)
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(1400, ctx.currentTime);
                    gain.gain.setValueAtTime(0.12, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.07);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.07);
                } else {
                    // Tono grave de error (300Hz, 180ms)
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(300, ctx.currentTime);
                    gain.gain.setValueAtTime(0.18, ctx.currentTime);
                    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.18);
                    osc.start(ctx.currentTime);
                    osc.stop(ctx.currentTime + 0.18);
                }
            } catch(err) {}
        }

        Livewire.on('beep-success', () => playPosTone('success'));
        Livewire.on('beep-error', () => playPosTone('error'));

        // 2. Función para mantener el foco en el buscador de productos
        function keepSearchFocused() {
            // No robar foco si hay modales visibles abiertos
            const activeModal = document.querySelector('.fixed.z-50:not([style*="display: none"]):not([style*="display:none"])');
            if (activeModal) return;

            const searchInput = document.getElementById('product-search-input');
            if (searchInput && document.activeElement !== searchInput) {
                searchInput.focus();
            }
        }

        setTimeout(keepSearchFocused, 150);

        Livewire.on('product-scanned', () => {
            const searchInput = document.getElementById('product-search-input');
            if (searchInput) {
                searchInput.value = '';
            }
            setTimeout(keepSearchFocused, 40);
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('input, textarea, select, button, a, [role="button"], .no-refocus')) {
                setTimeout(keepSearchFocused, 30);
            }
        });

        // 3. Buffer y temporizador para pistola de código de barras global
        let barcodeBuffer = '';
        let lastKeyTime = Date.now();
        let globalScanTimer = null;

        window.addEventListener('keydown', (e) => {
            const activeEl = document.activeElement;
            const isSearchInput = activeEl && activeEl.id === 'product-search-input';
            const isOtherInput = activeEl && !isSearchInput && (
                activeEl.tagName === 'INPUT' || 
                activeEl.tagName === 'TEXTAREA' || 
                activeEl.isContentEditable
            );

            // Teclas de función POS (F2, F3, F4, F8)
            if (e.key === 'F2') {
                e.preventDefault();
                @this.call('openPaymentModal');
                return;
            } else if (e.key === 'F3') {
                e.preventDefault();
                const searchInput = document.getElementById('product-search-input');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
                return;
            } else if (e.key === 'F4') {
                e.preventDefault();
                @this.call('openCustomerModal');
                return;
            } else if (e.key === 'F8') {
                e.preventDefault();
                @this.set('showTableMap', true);
                return;
            }

            // Si el cajero está en otro input (ej: cliente, cobrar en efectivo), no interceptar
            if (isOtherInput) {
                return;
            }

            const now = Date.now();
            const timeDiff = now - lastKeyTime;
            lastKeyTime = now;

            // Si la pistola envía Enter o Tab
            if (e.key === 'Enter' || e.key === 'Tab') {
                if (barcodeBuffer.length >= 3 && !isSearchInput) {
                    e.preventDefault();
                    clearTimeout(globalScanTimer);
                    const code = barcodeBuffer.trim();
                    barcodeBuffer = '';
                    const searchInput = document.getElementById('product-search-input');
                    if (searchInput) searchInput.value = '';
                    @this.call('handleSearchSubmit', code);
                    return;
                }
                barcodeBuffer = '';
                return;
            }

            // Si pasaron más de 70ms entre teclas, es escritura manual humana, resetear buffer
            if (timeDiff > 70) {
                barcodeBuffer = '';
            }

            if (e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey) {
                barcodeBuffer += e.key;

                // Auto-procesamiento si la pistola NO envía Enter ni Tab
                clearTimeout(globalScanTimer);
                globalScanTimer = setTimeout(() => {
                    if (barcodeBuffer.length >= 4 && !isSearchInput) {
                        const code = barcodeBuffer.trim();
                        barcodeBuffer = '';
                        const searchInput = document.getElementById('product-search-input');
                        if (searchInput) searchInput.value = '';
                        @this.call('handleSearchSubmit', code);
                    }
                }, 75);
            }
        });
    });
</script>


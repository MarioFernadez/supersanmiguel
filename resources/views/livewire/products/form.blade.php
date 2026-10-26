<div class="py-8 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form wire:submit.prevent="store">
            <!-- Header -->
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('products.index') }}" class="p-2.5 bg-white rounded-xl shadow-sm border border-gray-200 text-gray-500 hover:text-indigo-600 hover:border-indigo-200 transition dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight dark:text-white">
                        {{ $productId ? 'Editar Producto' : 'Nuevo Producto' }}
                    </h1>
                </div>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <a href="{{ route('products.index') }}" class="flex-1 md:flex-none text-center bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 font-semibold py-2.5 px-5 rounded-xl transition text-xs shadow-xs dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                        Cancelar
                    </a>
                    <button type="submit" wire:loading.attr="disabled" class="flex-1 md:flex-none justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-5 rounded-xl transition text-xs flex items-center gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Guardar Producto
                    </button>
                </div>
            </div>

            <!-- Main Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Columna Principal -->
                <div class="lg:col-span-8 space-y-6">
                    
                    <!-- Información Básica -->
                    <div class="bg-white border border-gray-100 rounded-3xl p-6 md:p-8 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:shadow-none">
                        <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Información Básica
                        </h2>
                        
                        <div class="space-y-5">
                            <div>
                                <label for="name" class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-widest dark:text-gray-300">Nombre del Producto</label>
                                <input type="text" id="name" wire:model="name" class="w-full border-gray-200 rounded-xl py-3 px-4 text-gray-700 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300" placeholder="Ej: Zapatillas Nike Air Max">
                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label for="description" class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-widest dark:text-gray-300">Descripción (Opcional)</label>
                                <textarea id="description" wire:model="description" rows="4" class="w-full border-gray-200 rounded-xl py-3 px-4 text-gray-700 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300" placeholder="Escribe los detalles, beneficios y especificaciones técnicas..."></textarea>
                                @error('description') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Precios -->
                    <div class="bg-white border border-gray-100 rounded-3xl p-6 md:p-8 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:shadow-none">
                        <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Precios
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div x-data="{ 
                                value: @entangle('price_cost'),
                                format(val) {
                                    if (!val) return '';
                                    return val.toString().replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                },
                                update(event) {
                                    let raw = event.target.value.replace(/\./g, '');
                                    this.value = this.format(raw);
                                }
                            }">
                                <label for="price_cost" class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-widest dark:text-gray-300">Precio Costo (₲)</label>
                                <input type="text" x-model="value" @input="update" class="w-full border-gray-200 rounded-xl py-3 px-4 text-gray-700 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300" placeholder="0">
                                @error('price_cost') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                            </div>

                            <div x-data="{ 
                                value: @entangle('price_sale'),
                                format(val) {
                                    if (!val) return '';
                                    return val.toString().replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                },
                                update(event) {
                                    let raw = event.target.value.replace(/\./g, '');
                                    this.value = this.format(raw);
                                }
                            }">
                                <label for="price_sale" class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-widest dark:text-gray-300">Precio Venta (₲)</label>
                                <input type="text" x-model="value" @input="update" class="w-full border-gray-200 rounded-xl py-3 px-4 text-gray-700 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition text-lg font-black dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300" placeholder="0">
                                @error('price_sale') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        @if($discountProductEnabled)
                        <div class="mt-6 pt-6 border-t border-gray-100 border-dashed dark:border-gray-700" x-data="{ pct: $wire.entangle('discount_percentage') }">
                            <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-widest dark:text-gray-300">Descuento (%)</label>
                            <div class="flex items-center gap-4">
                                <div class="relative w-32">
                                    <input type="number" wire:model.live="discount_percentage" min="0" max="100" class="w-full border-gray-200 rounded-xl py-3 px-4 text-gray-700 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 pr-8">
                                    <span class="absolute right-3 top-3 text-gray-400 font-bold">%</span>
                                </div>
                                <div x-show="pct > 0" class="bg-emerald-50 text-emerald-700 px-4 py-2 rounded-xl text-sm font-bold border border-emerald-100 flex items-center gap-2 dark:bg-emerald-900/30 dark:border-emerald-800/50 dark:text-emerald-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                    Precio Final: ₲ {{ $discount_percentage > 0 ? number_format((int)round((int)(preg_replace('/[^0-9]/', '', $price_sale ?: '0')) * (1 - $discount_percentage / 100)), 0, ',', '.') : '–' }}
                                </div>
                            </div>
                            @error('discount_percentage') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                        </div>
                        @endif
                    </div>

                    <!-- Inventario y Variantes -->
                    <div class="bg-white border border-gray-100 rounded-3xl p-6 md:p-8 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:shadow-none">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest dark:text-white flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                Inventario y Variantes
                            </h2>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="manage_stock" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600 dark:bg-gray-700"></div>
                                <span class="ml-3 text-xs font-bold text-gray-700 uppercase tracking-widest dark:text-gray-300">Controlar Stock</span>
                            </label>
                        </div>

                        <!-- Opciones de Stock Simple -->
                        @if($manage_stock && !$has_variations)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-widest dark:text-gray-300">Stock Mínimo (Alerta)</label>
                                <input type="number" step="0.001" wire:model="min_stock" class="w-full border-gray-200 rounded-xl py-3 px-4 text-gray-700 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300" placeholder="0">
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 dark:bg-gray-900/50 dark:border-gray-800">
                            <h3 class="text-xs font-black text-gray-700 uppercase tracking-widest mb-4 dark:text-gray-400">Stock por Sucursal</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($branches as $branch)
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-500 mb-1.5 uppercase tracking-widest dark:text-gray-400">{{ $branch->name }}</label>
                                        <input type="number" step="0.001" wire:model="branch_stocks.{{ $branch->id }}" class="w-full border-gray-200 rounded-xl py-2 px-3 text-sm text-gray-700 bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300" placeholder="0">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        <!-- Decants / Materia Prima Global (Carnicería) -->
                        @if($is_raw_material)
                        <div class="mt-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 dark:bg-rose-950/30 dark:border-rose-800/50 flex items-center gap-3">
                            <span class="text-2xl">🥩</span>
                            <div>
                                <h4 class="text-xs font-black text-rose-900 uppercase tracking-wider dark:text-rose-300">Este producto es una Materia Prima Global</h4>
                                <p class="text-[11px] text-rose-700 dark:text-rose-400 font-medium">Su stock abastece a los cortes derivados (costilla, vacío, chorizo) y se utiliza para liquidar la merma mensual.</p>
                            </div>
                        </div>
                        @elseif(hasModule('decants') || hasModule('meat_deboning'))
                        <div class="mt-8 pt-8 border-t border-gray-100 border-dashed dark:border-gray-700">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
                                <div>
                                    <h4 class="text-sm font-black {{ hasModule('meat_deboning') ? 'text-rose-900 dark:text-rose-400' : 'text-fuchsia-900 dark:text-fuchsia-400' }} uppercase tracking-widest flex items-center gap-2">
                                        @if(hasModule('meat_deboning'))
                                            🥩 ¿Descontar de Producto Global? (Cortes / Chorizo)
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            Es un Decant (Fracción)
                                        @endif
                                    </h4>
                                    <p class="text-[10px] text-gray-500 font-bold uppercase mt-1 dark:text-gray-400">
                                        @if(hasModule('meat_deboning'))
                                            Activa si este corte (ej: Vacío, Costilla, Chorizo) descuenta stock automáticamente de una carne global (ej: Carne de Res).
                                        @else
                                            Activa si este producto descuenta stock de un frasco padre.
                                        @endif
                                    </p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model.live="is_decant" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 {{ hasModule('meat_deboning') ? 'peer-focus:ring-rose-300 dark:peer-focus:ring-rose-800 peer-checked:bg-rose-600' : 'peer-focus:ring-fuchsia-300 dark:peer-focus:ring-fuchsia-800 peer-checked:bg-fuchsia-600' }} rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:bg-gray-700"></div>
                                </label>
                            </div>

                            @if($is_decant)
                                <div class="{{ hasModule('meat_deboning') ? 'bg-rose-50/50 border-rose-100/50 dark:bg-rose-900/10 dark:border-rose-800/30' : 'bg-fuchsia-50/50 border-fuchsia-100/50 dark:bg-fuchsia-900/10 dark:border-fuchsia-800/30' }} rounded-2xl p-4 border">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-[10px] font-black {{ hasModule('meat_deboning') ? 'text-rose-800 dark:text-rose-400' : 'text-fuchsia-800 dark:text-fuchsia-400' }} mb-2 uppercase tracking-widest">
                                                {{ hasModule('meat_deboning') ? 'Materia Prima Origen (Carne Global)' : 'Seleccionar Producto Padre (Botella)' }}
                                            </label>
                                            <select wire:model="parent_id" class="w-full border-gray-200 rounded-xl py-3 px-4 text-sm text-gray-700 bg-white focus:ring-2 {{ hasModule('meat_deboning') ? 'focus:ring-rose-500' : 'focus:ring-fuchsia-500' }} focus:border-transparent transition dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                                <option value="">-- Seleccionar Carne Global Padre --</option>
                                                @forelse($potentialParents as $parent)
                                                    <option value="{{ $parent->id }}">{{ $parent->name }} (Stock: {{ number_format($parent->stock, 2) }} {{ $parent->unit_type }})</option>
                                                @empty
                                                    <option value="" disabled>⚠️ No hay ningún Producto Global registrado. Activa "¿Es Producto Global?" en la carne padre primero.</option>
                                                @endforelse
                                            </select>
                                            @error('parent_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black {{ hasModule('meat_deboning') ? 'text-rose-800 dark:text-rose-400' : 'text-fuchsia-800 dark:text-fuchsia-400' }} mb-2 uppercase tracking-widest">
                                                {{ hasModule('meat_deboning') ? 'Factor de Descuento (1 = 1 Kg vendido descuenta 1 Kg)' : 'Multiplicador de Consumo (ej: 0.1)' }}
                                            </label>
                                            <input type="number" step="0.001" wire:model="fraction_multiplier" class="w-full border-gray-200 rounded-xl py-3 px-4 text-sm text-gray-700 bg-white focus:ring-2 {{ hasModule('meat_deboning') ? 'focus:ring-rose-500' : 'focus:ring-fuchsia-500' }} focus:border-transparent transition dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300" placeholder="1.000">
                                            <p class="text-[9px] {{ hasModule('meat_deboning') ? 'text-rose-600/80 dark:text-rose-400/80' : 'text-fuchsia-600/80 dark:text-fuchsia-400/80' }} font-bold uppercase mt-2">
                                                {{ hasModule('meat_deboning') ? 'Por defecto es 1.0 (cada 1 Kg de este corte vendido descuenta 1 Kg del stock de carne global).' : 'Ej: Si el padre tiene 1 unidad (100ml) y vendes 10ml, el multiplicador es 0.1.' }}
                                            </p>
                                            @error('fraction_multiplier') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                    <div class="mt-4 text-xs font-bold {{ hasModule('meat_deboning') ? 'text-rose-700 bg-rose-100 dark:bg-rose-900/30 dark:text-rose-300' : 'text-fuchsia-700 bg-fuchsia-100 dark:bg-fuchsia-900/30 dark:text-fuchsia-300' }} p-3 rounded-lg">
                                        ℹ️ Al guardar, este corte no requiere carga previa de stock individual: se descontará automáticamente de la carne global seleccionada en cada venta o lectura de balanza.
                                    </div>
                                </div>
                            @endif
                        </div>
                        @endif
                        <!-- Variantes -->
                        @if(hasModule('variations'))
                        <div class="mt-8 pt-8 border-t border-gray-100 border-dashed dark:border-gray-700">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
                                <div>
                                    <h4 class="text-sm font-black text-indigo-900 uppercase tracking-widest flex items-center gap-2 dark:text-indigo-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                        Producto con Variantes
                                    </h4>
                                    <p class="text-[10px] text-gray-500 font-bold uppercase mt-1 dark:text-gray-400">Activa si el producto tiene talles, colores, etc.</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" wire:model.live="has_variations" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600 dark:bg-gray-700"></div>
                                </label>
                            </div>

                            @if($has_variations)
                                <div class="bg-indigo-50/50 rounded-2xl p-1 md:p-4 border border-indigo-100/50 dark:bg-indigo-900/10 dark:border-indigo-800/30">
                                    
                                    <!-- Generador Automático de Matriz -->
                                    <div class="mb-6 p-4 bg-white rounded-xl shadow-sm border border-indigo-100 dark:bg-gray-800 dark:border-gray-700">
                                        <h5 class="text-xs font-black text-indigo-900 uppercase tracking-widest mb-3 dark:text-indigo-300">Generador Automático de Matriz (Talles x Colores)</h5>
                                        
                                        <div class="mb-3">
                                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Seleccionar Talles:</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($availableSizes as $size)
                                                    <button type="button" wire:click="toggleSize('{{ $size }}')" class="px-3 py-1 text-xs font-bold rounded-lg border transition {{ in_array($size, $selectedSizes) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600' }}">
                                                        {{ $size }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Seleccionar Colores:</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($availableColors as $color)
                                                    <button type="button" wire:click="toggleColor('{{ $color }}')" class="px-3 py-1 text-xs font-bold rounded-lg border transition {{ in_array($color, $selectedColors) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600' }}">
                                                        {{ $color }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                        
                                        <button type="button" wire:click="generateVariations" class="w-full bg-indigo-900 hover:bg-indigo-800 text-white text-xs font-black py-2 rounded-lg uppercase tracking-widest shadow-sm transition">
                                            Cruzar y Generar Variantes
                                        </button>
                                        
                                        @if(session()->has('variation_message'))
                                            <p class="mt-2 text-xs font-bold text-green-600 text-center">{{ session('variation_message') }}</p>
                                        @endif
                                    </div>

                                    <div class="flex justify-between items-center mb-4 px-2">
                                        <h5 class="text-[10px] font-black text-indigo-600 uppercase tracking-widest dark:text-indigo-400">Lista de Variantes</h5>
                                        <button type="button" wire:click="addVariation" class="bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black px-4 py-2 rounded-xl uppercase tracking-widest shadow-sm flex items-center gap-1.5 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Añadir Manualmente
                                        </button>
                                    </div>

                                    <div class="overflow-x-auto rounded-xl">
                                        <table class="min-w-full divide-y divide-indigo-100 dark:divide-indigo-900/30">
                                            <thead class="bg-indigo-50/80 dark:bg-indigo-900/20">
                                                <tr class="text-[9px] font-black text-indigo-800 uppercase tracking-widest text-left dark:text-indigo-300">
                                                    <th class="px-3 py-3">Nombre</th>
                                                    <th class="px-3 py-3">SKU</th>
                                                    <th class="px-3 py-3">Venta (₲)</th>
                                                    <th class="px-3 py-3 text-center">Stock</th>
                                                    <th class="px-3 py-3"></th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-indigo-50 bg-white dark:bg-gray-800 dark:divide-indigo-900/20">
                                                @foreach($variations as $index => $var)
                                                    <tr wire:key="var-{{ $index }}" class="group">
                                                        <td class="px-3 py-3">
                                                            <input type="text" wire:model="variations.{{ $index }}.name" placeholder="Ej: Rojo L" class="w-full text-xs font-bold border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-transparent py-2 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                                        </td>
                                                        <td class="px-3 py-3">
                                                            <input type="text" wire:model="variations.{{ $index }}.sku" placeholder="SKU-VAR" class="w-24 text-[10px] font-mono border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-transparent py-2 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                                        </td>
                                                        <td class="px-3 py-3">
                                                            <input type="text" wire:model="variations.{{ $index }}.price_sale" placeholder="0" class="w-24 text-xs font-bold border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-transparent py-2 text-right dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                                        </td>
                                                        <td class="px-3 py-3 text-center">
                                                            <input type="number" wire:model="variations.{{ $index }}.stock" placeholder="0" class="w-16 text-xs font-bold border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-transparent py-2 text-center dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                                        </td>
                                                        <td class="px-3 py-3 text-right">
                                                            <button type="button" wire:click="removeVariation({{ $index }})" class="text-red-400 hover:text-red-600 transition p-1.5 hover:bg-red-50 rounded-lg dark:hover:bg-red-900/30">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @if(empty($variations))
                                                    <tr>
                                                        <td colspan="5" class="px-3 py-8 text-center">
                                                            <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest italic">Haz clic en Añadir Variante para comenzar</p>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @endif

                    </div>

                    <!-- Imágenes -->
                    <div class="bg-white border border-gray-100 rounded-3xl p-6 md:p-8 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:shadow-none">
                        <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Imágenes
                        </h2>

                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                            <div class="shrink-0 relative group">
                                @if ($product_image)
                                    <img src="{{ $product_image->temporaryUrl() }}" class="h-32 w-32 object-cover rounded-2xl border shadow-sm dark:border-gray-700 dark:shadow-none">
                                @elseif ($product_image_path)
                                    <img src="{{ Storage::url($product_image_path) }}" class="h-32 w-32 object-cover rounded-2xl border shadow-sm dark:border-gray-700 dark:shadow-none">
                                @else
                                    <div class="h-32 w-32 rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 flex items-center justify-center text-gray-400 dark:bg-gray-900 dark:border-gray-700">
                                        <svg class="w-10 h-10 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                                <div wire:loading wire:target="product_image" class="absolute inset-0 bg-white/80 dark:bg-gray-900/80 rounded-2xl flex items-center justify-center">
                                    <svg class="animate-spin h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <div class="flex-1 w-full">
                                <input type="file" wire:model="product_image" id="product_image" class="hidden">
                                <label for="product_image" class="cursor-pointer inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 bg-indigo-50 border border-indigo-100 text-indigo-700 font-black rounded-xl hover:bg-indigo-100 transition text-xs uppercase tracking-widest shadow-sm dark:bg-indigo-900/30 dark:border-indigo-800/50 dark:text-indigo-400 dark:shadow-none">
                                    Seleccionar Imagen
                                </label>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-3 dark:text-gray-500">Formato JPG o PNG. Tamaño máximo 1MB.<br>Se recomienda imagen cuadrada (ej: 800x800px).</p>
                                @error('product_image') <span class="text-red-500 text-xs mt-2 block font-bold">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Columna Lateral -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- Estado -->
                    <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:shadow-none">
                        <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-4 dark:text-white flex items-center gap-2">
                            ESTADO
                        </h2>
                        <div class="border border-emerald-100 bg-emerald-50 rounded-2xl p-4 dark:bg-emerald-900/20 dark:border-emerald-800/50">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <div class="flex items-center h-5 mt-0.5">
                                    <input type="checkbox" wire:model="is_active" class="w-5 h-5 text-emerald-600 bg-white border-emerald-300 rounded focus:ring-emerald-500 dark:focus:ring-emerald-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-emerald-900 dark:text-emerald-400">Producto Activo</span>
                                    <span class="text-[10px] font-bold text-emerald-600/70 uppercase tracking-widest dark:text-emerald-500">Visible en tienda y catálogo</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Carnicería / Producto Global -->
                    @if(hasModule('meat_deboning'))
                    <div class="bg-white border {{ $is_raw_material ? 'border-rose-400 bg-rose-50/30' : 'border-gray-100' }} rounded-3xl p-6 shadow-sm dark:bg-gray-800 dark:border-gray-700 transition">
                        <h2 class="text-sm font-black {{ $is_raw_material ? 'text-rose-900 dark:text-rose-400' : 'text-gray-900 dark:text-white' }} uppercase tracking-widest mb-4 flex items-center gap-2">
                            <span>🥩</span>
                            <span>PRODUCTO GLOBAL</span>
                        </h2>
                        
                        <div class="border {{ $is_raw_material ? 'border-rose-200 bg-rose-50 dark:bg-rose-950/40 dark:border-rose-800/50' : 'border-gray-200 bg-gray-50 dark:bg-gray-750 dark:border-gray-700' }} rounded-2xl p-4 transition">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <div class="flex items-center h-5 mt-0.5">
                                    <input type="checkbox" wire:model.live="is_raw_material" class="w-5 h-5 text-rose-600 bg-white border-gray-300 rounded focus:ring-rose-500">
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black {{ $is_raw_material ? 'text-rose-900 dark:text-rose-300' : 'text-gray-800 dark:text-gray-200' }}">¿Es Producto Global?</span>
                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mt-0.5">
                                        Carne en bruto / Media Res
                                    </span>
                                </div>
                            </label>
                        </div>

                        @if($is_raw_material)
                            <div class="mt-3 text-xs font-bold text-rose-700 bg-rose-100/90 p-3 rounded-xl dark:bg-rose-950/50 dark:text-rose-300 border border-rose-200">
                                ✓ Este producto es la <strong>Carne Global</strong> (Materia Prima). Aquí ingresas el stock total y de aquí se descontarán los cortes automáticamente.
                            </div>
                        @endif
                    </div>
                    @endif

                    <!-- Organización -->
                    <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:shadow-none">
                        <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6 dark:text-white flex items-center gap-2">
                            ORGANIZACIÓN
                        </h2>
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-[10px] font-black text-gray-500 mb-2 uppercase tracking-widest dark:text-gray-400">Categoría</label>
                                <select wire:model="category_id" class="w-full border-gray-200 rounded-xl py-3 px-4 text-sm text-gray-700 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                    <option value="">Seleccionar Categoría</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-500 mb-2 uppercase tracking-widest dark:text-gray-400">Proveedor</label>
                                <select wire:model="supplier_id" class="w-full border-gray-200 rounded-xl py-3 px-4 text-sm text-gray-700 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                    <option value="">Seleccionar Proveedor (Opcional)</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-gray-500 mb-2 uppercase tracking-widest dark:text-gray-400">Unidad de Medida</label>
                                <select wire:model="unit_type" class="w-full border-gray-200 rounded-xl py-3 px-4 text-sm text-gray-700 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                    <option value="unit">Unidad (Default)</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->name }}">{{ $unit->name }} ({{ $unit->abbreviation }})</option>
                                    @endforeach
                                </select>
                                @error('unit_type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                            </div>

                            @if(hasModule('restaurant'))
                            <div class="pt-5 border-t border-gray-100 border-dashed dark:border-gray-700">
                                <label class="block text-[10px] font-black text-indigo-500 mb-2 uppercase tracking-widest dark:text-indigo-400">Destino de Comanda (Restaurante)</label>
                                <select wire:model="production_zone" class="w-full border-indigo-200 rounded-xl py-3 px-4 text-sm text-indigo-900 bg-indigo-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition dark:bg-indigo-900/20 dark:border-indigo-800/50 dark:text-indigo-300">
                                    <option value="none">No imprimir comanda (Directo)</option>
                                    <option value="kitchen">🍳 Cocina (Platos, Entradas)</option>
                                    <option value="bar">🍹 Barra (Bebidas, Tragos)</option>
                                </select>
                                @error('production_zone') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                            </div>
                            @endif

                            <div class="pt-5 border-t border-gray-100 border-dashed dark:border-gray-700">
                                <label class="block text-[10px] font-black text-gray-500 mb-2 uppercase tracking-widest dark:text-gray-400">SKU / Código Interno</label>
                                <input type="text" wire:model="sku" class="w-full font-mono border-gray-200 rounded-xl py-3 px-4 text-sm text-gray-700 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300" placeholder="Vacío = Auto-generar">
                                @error('sku') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

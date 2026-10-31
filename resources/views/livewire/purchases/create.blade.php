<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 dark:bg-gray-800">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Nueva Compra (Entrada de Stock)</h2>
                <a href="{{ route('purchases.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">Volver al listado</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Supplier Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">Proveedor</label>
                    <select wire:model="supplier_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:shadow-none dark:bg-gray-900 dark:text-gray-300">
                        <option value="">Seleccionar proveedor...</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">Fecha de Compra</label>
                    <input type="date" wire:model="purchase_date" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:shadow-none dark:bg-gray-900 dark:text-gray-300">
                    @error('purchase_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Ref Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">Nro. de Factura / Referencia</label>
                    <input type="text" wire:model="reference_number" placeholder="Ej: 001-001-000123" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:shadow-none dark:bg-gray-900 dark:text-gray-300">
                </div>
            </div>

            <!-- Product Search -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">Buscar Productos</label>
                <div class="relative">
                    <input type="text" wire:model.live="search" placeholder="Escribe nombre o SKU del producto..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:shadow-none dark:bg-gray-900 dark:text-gray-300">
                    
                    @if(!empty($productsList))
                        <div class="absolute z-10 w-full bg-white border border-gray-200 rounded-md shadow-lg mt-1 dark:bg-gray-800 dark:border-gray-700">
                            @foreach($productsList as $product)
                                @if($product->has_variations)
                                    @foreach($product->variations as $var)
                                        <button wire:click="addItem('{{ $product->id }}', '{{ $var->id }}')" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex justify-between items-center border-b border-gray-100 last:border-0 dark:hover:bg-gray-700 dark:border-gray-700">
                                            <span>{{ $product->name }} <span class="text-indigo-600 font-bold dark:text-indigo-400">[{{ $var->name }}]</span> <span class="text-gray-400 text-xs dark:text-gray-500">({{ $var->sku }})</span></span>
                                            <span class="text-indigo-600 font-bold dark:text-indigo-400">₲ {{ number_format($var->price_cost ?: $product->price_cost, 0, ',', '.') }}</span>
                                        </button>
                                    @endforeach
                                @else
                                    <button wire:click="addItem('{{ $product->id }}')" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex justify-between items-center border-b border-gray-100 last:border-0 dark:hover:bg-gray-700 dark:border-gray-700">
                                        <span>{{ $product->name }} <span class="text-gray-400 text-xs dark:text-gray-500">({{ $product->sku }})</span></span>
                                        <span class="text-indigo-600 font-bold dark:text-indigo-400">₲ {{ number_format($product->price_cost, 0, ',', '.') }}</span>
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items Table -->
            <div class="mb-8 overflow-hidden border border-gray-200 rounded-lg dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Producto</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Cantidad</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Costo Unitario</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-400">Subtotal</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($items as $key => $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $item['name'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <input type="number" step="0.001" min="0.001" wire:model.live="items.{{ $key }}.quantity" wire:change="calculateTotal" class="w-24 text-center border-gray-300 rounded-md shadow-sm text-sm dark:border-gray-600 dark:shadow-none dark:bg-gray-900 dark:text-gray-300">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <input type="number" wire:model.live="items.{{ $key }}.cost_price" wire:change="calculateTotal" class="w-32 text-center border-gray-300 rounded-md shadow-sm text-sm dark:border-gray-600 dark:shadow-none dark:bg-gray-900 dark:text-gray-300">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900 dark:text-white">
                                    ₲ {{ number_format($item['cost_price'] * $item['quantity'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <button wire:click="removeItem('{{ $key }}')" class="text-red-400 hover:text-red-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic dark:text-gray-500">No hay productos añadidos a la compra.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-gray-700 uppercase dark:text-gray-300">Total Compra</td>
                            <td class="px-6 py-4 text-right text-lg font-black text-indigo-600 dark:text-indigo-400">₲ {{ number_format($total, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Notes -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">Notas adicionales</label>
                <textarea wire:model="notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:shadow-none dark:bg-gray-900 dark:text-gray-300"></textarea>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-3">
                <button wire:click="save" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-8 rounded-lg shadow transition-all">
                    Registrar Compra y Actualizar Stock
                </button>
            </div>
        </div>
    </div>
</div>

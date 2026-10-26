<div>
    <x-page-header title="Productos" section="Tienda › Catálogo" :badge="$products->total() . ' items'">
        <x-slot name="actions">
            @if(count($selected) > 0)
                <a href="{{ route('products.print-labels', ['ids' => implode(',', $selected)]) }}" 
                   class="bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold py-2 px-3.5 rounded-xl text-xs transition flex items-center gap-1.5 dark:bg-indigo-900/40 dark:text-indigo-300">
                    🖨️ Etiquetas ({{ count($selected) }})
                </a>
                <button wire:click="deleteSelected" 
                        onclick="confirm('¿Estás seguro de eliminar los productos seleccionados?') || event.stopImmediatePropagation()"
                        class="bg-red-50 text-red-600 hover:bg-red-100 font-bold py-2 px-3.5 rounded-xl text-xs transition flex items-center gap-1 dark:bg-red-900/30">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Borrar ({{ count($selected) }})
                </button>
            @endif
            <a href="{{ route('products.export') }}" class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-bold py-2 px-3.5 rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Exportar
            </a>
            <button x-data @click="$dispatch('open-import-modal')" class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-bold py-2 px-3.5 rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Importar
            </button>
            <a href="{{ route('products.create') }}" wire:navigate class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-xl text-xs transition shadow-sm flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Nuevo Producto
            </a>
            <a href="{{ route('settings.units') }}" wire:navigate class="p-2 bg-white border border-gray-200 text-gray-400 hover:text-indigo-600 hover:border-indigo-100 rounded-xl transition shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-gray-500" title="Gestionar Unidades de Medida">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2zm4-2v4m4-4v2m4-4v4"></path></svg>
            </a>
        </x-slot>
    </x-page-header>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-200/80 px-5 py-5 dark:bg-gray-800 dark:border-gray-700">
                
                @if (session()->has('message'))
                    <div class="bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded-lg text-sm mb-4 dark:bg-green-900/30 dark:text-green-300" role="alert">
                        {{ session('message') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-800 px-4 py-3 rounded-lg text-sm mb-4 dark:bg-red-900/30 dark:text-red-300" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
                    <div class="w-full md:w-1/2">
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition dark:text-gray-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </span>
                            <input wire:model.live.debounce.300ms="search" type="search" placeholder="Buscar por Nombre o SKU..." autocomplete="off" class="pl-10 shadow-sm border border-gray-200 rounded-xl w-full py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition dark:border-gray-700 dark:text-gray-300 dark:bg-gray-900">
                        </div>
                    </div>
                </div>

            <!-- Import Modal -->
            <div x-data="{ show: false }" x-show="show" @open-import-modal.window="show = true" @close-import-modal.window="show = false" style="display: none;">
                <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="show = false" aria-hidden="true"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full font-sans border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
                            <div class="bg-indigo-600 px-6 py-6 text-white">
                                <h3 class="text-xl font-black uppercase tracking-tight flex items-center gap-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    Importar Productos
                                </h3>
                                <p class="text-indigo-100 text-[10px] font-bold uppercase tracking-widest mt-1 opacity-80">Importación masiva mediante Excel/CSV</p>
                            </div>
                            <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="bg-white px-8 pt-8 pb-6 dark:bg-gray-800">
                                    <div class="bg-blue-50 border border-blue-100 p-4 rounded-2xl mb-6 dark:bg-blue-900/30">
                                        <p class="text-xs text-blue-800 leading-relaxed font-medium">
                                            Sube un archivo Excel (.xlsx) o CSV para importar productos masivamente.
                                            <a href="{{ route('products.template') }}" class="block mt-2 text-indigo-600 hover:text-indigo-900 font-black uppercase text-[10px] tracking-widest dark:text-indigo-400">
                                                ⬇️ Descargar Plantilla Oficial
                                            </a>
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 dark:text-gray-500">Seleccionar Archivo</label>
                                        <div class="relative group">
                                            <input type="file" name="file" required 
                                                class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition cursor-pointer bg-gray-50 border border-gray-200 rounded-2xl p-2 font-bold dark:text-gray-400 dark:bg-gray-900 dark:border-gray-700">
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-8 py-6 sm:flex sm:flex-row-reverse gap-3 rounded-b-3xl dark:bg-gray-900">
                                    <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-black py-2.5 px-8 rounded-xl transition text-[10px] uppercase tracking-widest shadow-lg shadow-indigo-100">
                                        Iniciar Importación
                                    </button>
                                    <button @click="show = false" type="button" class="mt-3 sm:mt-0 w-full sm:w-auto bg-white border border-gray-200 text-gray-500 font-black py-2.5 px-8 rounded-xl transition text-[10px] uppercase tracking-widest hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full">
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto -mx-4">
                    <div class="inline-block min-w-full align-middle font-sans">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50/80 uppercase text-[11px] tracking-wider font-semibold text-gray-500 dark:bg-gray-900/60 dark:text-gray-400">
                                <tr class="border-b border-gray-100 dark:border-gray-700">
                                    <th class="px-6 py-4 text-left w-10">
                                        <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 dark:border-gray-600 dark:text-indigo-400 dark:bg-gray-900"> 
                                    </th>
                                    <th class="px-6 py-4 text-left">Producto</th>
                                    <th class="px-6 py-4 text-left font-mono">SKU</th>
                                    <th class="px-6 py-4 text-left">Categoría</th>
                                    <th class="px-6 py-4 text-right">Costo</th>
                                    <th class="px-6 py-4 text-right">Precio Venta</th>
                                    <th class="px-6 py-4 text-center">Stock</th>
                                    <th class="px-6 py-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100 dark:bg-gray-800 dark:divide-gray-700">
                                @foreach($products as $product)
                                    <tr class="hover:bg-indigo-50/20 dark:hover:bg-gray-700/40 transition group">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" wire:model.live="selected" value="{{ $product->id }}" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 h-4 w-4 dark:border-gray-600 dark:text-indigo-400">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                @if($product->image_path)
                                                    <img src="{{ Storage::url($product->image_path) }}" class="h-10 w-10 rounded-lg object-cover border border-gray-100 shadow-sm dark:border-gray-700 dark:shadow-none">
                                                @else
                                                    <div class="h-10 w-10 rounded-lg bg-gray-50 flex items-center justify-center text-gray-300 border border-dashed dark:bg-gray-900">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $product->name }}</div>
                                                    <div class="flex items-center gap-1.5 mt-0.5">
                                                        @if($product->is_raw_material)
                                                            <span class="inline-flex items-center text-[9px] font-black bg-rose-100 text-rose-700 px-1.5 py-0.5 rounded-full uppercase tracking-wider">
                                                                🥩 GLOBAL
                                                            </span>
                                                        @endif
                                                        @if($product->discount_percentage > 0)
                                                            <span class="inline-flex items-center text-[9px] font-bold bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded-full uppercase tracking-tighter">
                                                                {{ $product->discount_percentage }}% OFF
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-xs font-mono text-gray-400 lowercase dark:text-gray-500">{{ $product->sku }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500 font-medium dark:text-gray-400">{{ $product->category ? $product->category->name : 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-400 text-right font-medium tabular-nums dark:text-gray-500"><span class="text-xs text-gray-400 font-normal mr-0.5">₲</span>{{ number_format($product->price_cost, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums"><span class="text-xs text-gray-400 font-normal mr-0.5 dark:text-gray-500">₲</span>{{ number_format($product->price_sale, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($product->manage_stock)
                                                <span class="px-2.5 py-0.5 inline-flex items-center gap-1.5 text-xs font-medium rounded-full {{ $product->stock <= 0 ? 'bg-red-50 text-red-700 border border-red-200/60 dark:bg-red-950/40 dark:text-red-300 dark:border-red-800/40' : ($product->stock <= $product->min_stock ? 'bg-amber-50 text-amber-700 border border-amber-200/60 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800/40' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/60 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/40') }}">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ $product->stock <= 0 ? 'bg-red-500' : ($product->stock <= $product->min_stock ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                                    {{ number_format($product->stock, ($product->unit_type === 'unit' ? 0 : 2), ',', '.') }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-xs italic font-normal">Infinito</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2 transition">
                                                <a href="{{ route('products.edit', $product->slug) }}" class="text-indigo-600 hover:text-indigo-900 p-1.5 rounded-lg hover:bg-indigo-50 dark:text-indigo-400 block" title="Editar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                                <button wire:click="delete('{{ $product->id }}')" class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50" title="Eliminar" onclick="confirm('¿Estás seguro?') || event.stopImmediatePropagation()">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden space-y-4 font-sans">
                    @foreach($products as $product)
                        <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm relative active:scale-[0.98] transition dark:bg-gray-800 dark:border-gray-700 dark:shadow-none">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1 pr-12">
                                    <h3 class="font-semibold text-gray-900 text-base leading-snug dark:text-white">{{ $product->name }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] font-mono text-gray-400 bg-gray-50 px-1.5 py-0.5 rounded dark:text-gray-500 dark:bg-gray-900">SKU: {{ $product->sku }}</span>
                                        @if($product->manage_stock)
                                            <span class="text-[10px] uppercase font-bold {{ $product->stock <= $product->min_stock ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600' }} px-1.5 py-0.5 rounded">
                                                Stock: {{ number_format($product->stock, ($product->unit_type === 'unit' ? 0 : 2), ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Info Grid -->
                            <div class="grid grid-cols-2 gap-4 py-3 border-t border-b border-dashed border-gray-100 dark:border-gray-700">
                                <div>
                                    <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1 dark:text-gray-500">Categoría</span>
                                    <span class="font-medium text-gray-700 text-sm dark:text-gray-300">{{ $product->category ? $product->category->name : 'N/A' }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="block text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-1 dark:text-gray-500">Precio</span>
                                    <div class="font-semibold text-indigo-700 text-base dark:text-indigo-400 tabular-nums"><span class="text-xs text-gray-400 font-normal mr-0.5">₲</span>{{ number_format($product->price_sale, 0, ',', '.') }}</div>
                                    @if($product->discount_percentage > 0)
                                        <span class="text-[9px] text-amber-600 font-bold uppercase">🏷️ {{ $product->discount_percentage }}% OFF</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex gap-2 mt-4 pt-4">
                                <a href="{{ route('products.edit', $product->slug) }}" class="flex-1 bg-indigo-50 text-indigo-700 font-bold py-2 rounded-xl text-xs flex items-center justify-center dark:bg-indigo-900/30 dark:text-indigo-400">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Editar
                                </a>
                                <button wire:click="delete('{{ $product->id }}')" onclick="confirm('¿Estás seguro?') || event.stopImmediatePropagation()" class="flex-1 bg-red-50 text-red-500 font-bold py-2 rounded-xl text-xs flex items-center justify-center dark:bg-red-900/30">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Borrar
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

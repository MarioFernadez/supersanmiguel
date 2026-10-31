<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4 dark:bg-gray-800">
            
            @if (session()->has('message'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 dark:bg-green-900/40 dark:text-green-400" role="alert">
                    <p class="font-bold">Info</p>
                    <p>{{ session('message') }}</p>
                </div>
            @endif

            <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight dark:text-white">Reporte de Ventas</h2>
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <a href="{{ route('reports.sales.print', ['from' => $dateFrom, 'to' => $dateTo]) }}" target="_blank" class="flex-1 md:flex-none bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-xl text-xs transition shadow-sm flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                        Exportar PDF
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-gray-50/50 p-4 rounded-2xl border border-gray-100 mb-8 dark:border-gray-700 dark:bg-gray-900/50">
                <div class="flex flex-col md:flex-row items-end gap-4">
                    <div class="w-full md:w-auto">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 shadow-sm dark:text-gray-400 dark:shadow-none">Desde</label>
                        <input wire:model.live="dateFrom" type="date" class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition text-sm py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>
                    <div class="w-full md:w-auto">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 dark:text-gray-400">Hasta</label>
                        <input wire:model.live="dateTo" type="date" class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition text-sm py-2 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>
                    
                    <div class="flex gap-2 w-full md:w-auto">
                        <button wire:click="filterToday" class="flex-1 md:flex-none px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold hover:bg-indigo-50 hover:border-indigo-200 hover:text-indigo-600 transition shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:shadow-none">
                            Hoy
                        </button>
                        <button wire:click="filterWeek" class="flex-1 md:flex-none px-4 py-2 bg-white border border-gray-200 rounded-xl text-xs font-bold hover:bg-indigo-50 hover:border-indigo-200 hover:text-indigo-600 transition shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:shadow-none">
                            Semana
                        </button>
                    </div>
                </div>
            </div>

            <!-- Financial Dashboard -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Sales -->
                <div class="bg-white overflow-hidden shadow-sm rounded-xl p-5 border-l-4 border-indigo-500 dark:bg-gray-800 dark:shadow-none">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Ventas Totales</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white tabular-nums"><span class="text-sm font-normal text-gray-400 mr-0.5">₲</span>{{ format_pyg($this->profitStats['sales']) }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Cost -->
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5 border-l-4 border-red-500 group dark:bg-gray-800 dark:shadow-none">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-red-50 text-red-600 dark:bg-red-900/30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Costo Estimado</p>
                            <p class="text-xl font-bold text-gray-900 dark:text-white tabular-nums"><span class="text-sm font-normal text-gray-400 mr-0.5">₲</span>{{ format_pyg($this->profitStats['cost']) }}</p>
                        </div>
                    </div>
                    <div class="h-1.5 w-full bg-red-50 mt-3 rounded-full overflow-hidden dark:bg-red-900/30">
                        <div class="h-full bg-red-400 rounded-full" style="width: 65%"></div>
                    </div>
                </div>

                <!-- Profit -->
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5 border-l-4 border-emerald-500 relative dark:bg-gray-800 dark:shadow-none">
                    <div class="absolute right-4 top-4">
                        <div class="bg-emerald-50 text-emerald-700 text-xs font-semibold px-2.5 py-1 rounded-full border border-emerald-200/60 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/40">
                            {{ number_format($this->profitStats['margin'], 1) }}%
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Ganancia Estimada</p>
                            <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400 tabular-nums"><span class="text-sm font-normal text-emerald-400 mr-0.5">₲</span>{{ format_pyg($this->profitStats['profit']) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Payments -->
                <div class="bg-white overflow-hidden shadow-sm rounded-2xl p-5 border-l-4 border-indigo-500 dark:bg-gray-800 dark:shadow-none">
                    <div class="flex items-center mb-3">
                        <div class="p-2 rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <p class="ml-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Medios de Pago</p>
                    </div>
                    <div class="space-y-1.5">
                        @foreach($this->paymentMethods as $payment)
                            <div class="flex justify-between items-center bg-gray-50/80 px-2.5 py-1 rounded-lg dark:bg-gray-900/50">
                                <span class="text-xs font-medium text-gray-600 uppercase dark:text-gray-400">{{ $payment->method }}</span>
                                <span class="text-xs font-semibold text-gray-900 tabular-nums dark:text-white"><span class="text-[10px] text-gray-400 font-normal mr-0.5">₲</span>{{ format_pyg($payment->total) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>    

            <!-- Charts Section -->
            <div x-data="salesCharts(@js($this->salesByBranch), @js($this->salesBySeller))" class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8" wire:ignore>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:shadow-none">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 dark:text-gray-300">Ventas por Sucursal</h3>
                    <div x-ref="chartBranches"></div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:shadow-none">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4 dark:text-gray-300">Ventas por Vendedor</h3>
                    <div x-ref="chartSellers"></div>
                </div>
            </div>

            <!-- Sales Table -->
            <div class="overflow-x-auto bg-white rounded-2xl border border-gray-100 shadow-sm mt-8 dark:bg-gray-800 dark:border-gray-700 dark:shadow-none">
                <table class="w-full text-left border-collapse font-sans">
                    <thead>
                        <tr class="bg-gray-50/80 text-gray-500 uppercase text-[11px] font-semibold tracking-wider border-b border-gray-100 dark:text-gray-400 dark:border-gray-700 dark:bg-gray-900/60">
                            <th class="px-6 py-4">ID / Fecha</th>
                            <th class="px-6 py-4">Cliente</th>
                            <th class="px-6 py-4">Vendedor</th>
                            <th class="px-6 py-4 text-right">Total</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($sales as $sale)
                            <tr class="group hover:bg-gray-50/60 transition duration-150 dark:hover:bg-gray-700/30">
                                <td class="px-6 py-4">
                                    <div class="text-xs font-mono text-gray-700 group-hover:text-indigo-600 transition truncate max-w-[100px] dark:text-gray-300">{{ $sale->id }}</div>
                                    <div class="text-xs font-mono text-gray-400 dark:text-gray-500">{{ $sale->created_at->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $sale->customer->name ?? 'Consumidor Final' }}</div>
                                    <div class="text-xs text-gray-400 font-mono dark:text-gray-500">{{ $sale->customer->ruc ?? '—' }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 font-normal dark:text-gray-400">
                                    {{ $sale->seller->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums"><span class="text-xs text-gray-400 font-normal mr-0.5">₲</span>{{ format_pyg($sale->total) }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($sale->status === 'voided')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-red-50 text-red-700 border border-red-200/60 text-xs font-medium dark:bg-red-950/40 dark:text-red-300 dark:border-red-800/40">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                            Anulada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-xs font-medium dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/40">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Completada
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-1.5 transition">
                                        <button wire:click="viewDetails('{{ $sale->id }}')" class="text-indigo-600 hover:text-indigo-800 p-1.5 rounded-lg hover:bg-indigo-50 dark:hover:bg-gray-700 transition" title="Ver Detalles">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                        @if($sale->status !== 'voided')
                                            <button wire:click="reprintReceipt('{{ $sale->id }}')" class="text-gray-500 hover:text-gray-700 p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="Reimprimir Ticket">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                            </button>
                                            @if(auth()->user()->can('admin') || auth()->user()->can('gerente'))
                                                <button wire:click="voidSale('{{ $sale->id }}')" 
                                                        wire:confirm="¿Estás seguro de que deseas anular esta venta? Esta acción no se puede deshacer y restaurará el stock."
                                                        class="text-red-500 hover:text-red-700 p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-gray-700 transition"
                                                        title="Anular Venta">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic dark:text-gray-500">No hay ventas registradas en este periodo.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Products & Suppliers Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8 pt-8 border-t border-gray-100 dark:border-gray-700">
                
                <!-- Products Table -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:shadow-none">
                    <h3 class="text-sm font-semibold text-gray-800 mb-4 dark:text-white uppercase tracking-wider">Productos Vendidos</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left whitespace-no-wrap text-sm font-sans">
                            <thead>
                                <tr class="bg-gray-50/80 uppercase text-[11px] tracking-wider font-semibold text-gray-500 dark:bg-gray-900/60 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                                    <th class="px-4 py-3">Producto</th>
                                    <th class="px-4 py-3 text-center">Cant.</th>
                                    <th class="px-4 py-3 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($this->soldProducts as $item)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                        <td class="px-4 py-2.5 text-sm font-medium text-gray-900 dark:text-white">{{ $item->product_name }}</td>
                                        <td class="px-4 py-2.5 text-xs text-gray-600 text-center tabular-nums dark:text-gray-300">{{ $item->total_qty }}</td>
                                        <td class="px-4 py-2.5 text-sm font-semibold text-gray-900 text-right tabular-nums dark:text-white"><span class="text-xs text-gray-400 font-normal mr-0.5">₲</span>{{ format_pyg($item->total_amount) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400 italic dark:text-gray-500">Sin datos.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Suppliers Table -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:shadow-none">
                    <h3 class="text-sm font-semibold text-gray-800 mb-4 dark:text-white uppercase tracking-wider">Ventas por Proveedor</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left whitespace-no-wrap text-sm font-sans">
                            <thead>
                                <tr class="bg-gray-50/80 uppercase text-[11px] tracking-wider font-semibold text-gray-500 dark:bg-gray-900/60 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700">
                                    <th class="px-4 py-3">Proveedor</th>
                                    <th class="px-4 py-3 text-center">Items</th>
                                    <th class="px-4 py-3 text-right">Total Ventas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($this->salesBySupplier as $supplier)
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                        <td class="px-4 py-2.5 text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ $supplier->supplier_name }}</td>
                                        <td class="px-4 py-2.5 text-xs text-gray-600 text-center tabular-nums dark:text-gray-300">{{ $supplier->items_count }}</td>
                                        <td class="px-4 py-2.5 text-sm font-semibold text-gray-900 text-right tabular-nums dark:text-white"><span class="text-xs text-gray-400 font-normal mr-0.5">₲</span>{{ format_pyg($supplier->total_sales) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400 italic dark:text-gray-500">Sin datos.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="mt-4">
                {{ $sales->links() }}
            </div>

            <!-- Sale Details Modal -->
            <x-dialog-modal wire:model="showingSaleDetails" maxWidth="4xl">
                <x-slot name="title">
                    <div class="flex items-center justify-between font-sans">
                        <span class="uppercase tracking-tight font-black">Detalles de Venta #{{ $selectedSale?->id }}</span>
                        @if($selectedSale?->status === 'voided')
                            <span class="px-3 py-1 bg-red-100 text-red-600 text-[10px] font-black uppercase rounded-full dark:bg-red-900/40">Anulada</span>
                        @endif
                    </div>
                </x-slot>

                <x-slot name="content">
                    @if($selectedSale)
                        <div class="font-sans space-y-6">
                            <!-- Header Info -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-50 p-6 rounded-3xl border border-gray-100 dark:bg-gray-900 dark:border-gray-700">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest dark:text-gray-400">Información General</p>
                                    <p class="text-sm font-bold text-gray-900 mt-1 dark:text-white">Fecha: {{ $selectedSale->created_at->format('d/m/Y H:i') }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Cajero: {{ $selectedSale->user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Vendedor: {{ $selectedSale->seller->name ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest dark:text-gray-400">Cliente</p>
                                    <p class="text-sm font-bold text-gray-900 mt-1 dark:text-white">{{ $selectedSale->customer->name ?? 'Consumidor Final' }}</p>
                                    <p class="text-xs text-gray-500 font-mono dark:text-gray-400">RUC: {{ $selectedSale->customer->ruc ?? '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest text-right dark:text-gray-400">Condición / Total</p>
                                    <p class="text-sm font-black text-indigo-600 mt-1 text-right uppercase dark:text-indigo-400">{{ $selectedSale->payment_condition }}</p>
                                    <p class="text-2xl font-black text-gray-900 text-right dark:text-white">₲ {{ format_pyg($selectedSale->total) }}</p>
                                </div>
                            </div>

                            <!-- Items Table -->
                            <div class="overflow-hidden border border-gray-100 rounded-2xl shadow-sm dark:border-gray-700 dark:shadow-none">
                                <table class="w-full text-left border-collapse">
                                    <thead class="bg-gray-50 text-gray-400 uppercase text-[9px] font-black tracking-widest border-b border-gray-100 dark:bg-gray-900 dark:text-gray-400 dark:border-gray-700">
                                        <tr>
                                            <th class="px-6 py-3">Producto</th>
                                            <th class="px-6 py-3 text-center">Cant.</th>
                                            <th class="px-6 py-3 text-right">Precio Unit.</th>
                                            <th class="px-6 py-3 text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @foreach($selectedSale->items as $item)
                                            <tr>
                                                <td class="px-6 py-3">
                                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->product_name }}</div>
                                                    <div class="text-[10px] font-mono text-gray-400 dark:text-gray-400">{{ $item->product?->sku ?? '—' }}</div>
                                                </td>
                                                <td class="px-6 py-3 text-center text-sm font-bold text-gray-900 dark:text-white">
                                                    {{ $item->quantity }}
                                                </td>
                                                <td class="px-6 py-3 text-right text-xs text-gray-500 font-medium dark:text-gray-400">
                                                    {{ format_pyg($item->price_unit) }}
                                                </td>
                                                <td class="px-6 py-3 text-right text-sm font-black text-gray-900 dark:text-white">
                                                    {{ format_pyg($item->subtotal) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Payments -->
                            @if($selectedSale->payments->isNotEmpty())
                                <div>
                                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 dark:text-gray-400">Historial de Pagos</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($selectedSale->payments as $payment)
                                            <div class="flex items-center justify-between p-3 bg-emerald-50 rounded-xl border border-emerald-100 dark:bg-emerald-900/30">
                                                <div class="flex items-center gap-3">
                                                    <div class="p-2 bg-white rounded-lg text-emerald-600 shadow-sm dark:bg-gray-800 dark:shadow-none">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                                    </div>
                                                    <span class="text-[10px] font-black text-emerald-700 uppercase dark:text-emerald-400">{{ $payment->method }}</span>
                                                </div>
                                                <span class="text-sm font-black text-emerald-900">₲ {{ format_pyg($payment->amount) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </x-slot>

                <x-slot name="footer">
                    <x-secondary-button wire:click="closeDetails" wire:loading.attr="disabled">
                        Cerrar
                    </x-secondary-button>

                    @if($selectedSale && $selectedSale->status !== 'voided')
                        <button wire:click="reprintReceipt('{{ $selectedSale->id }}')" class="ml-2 bg-indigo-600 hover:bg-indigo-700 text-white font-black py-2.5 px-6 rounded-xl transition text-xs uppercase tracking-widest shadow-lg shadow-indigo-100 flex items-center gap-2">
                             <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                             Reimprimir Ticket
                        </button>
                    @endif
                </x-slot>
            </x-dialog-modal>
</div>
<script>
    document.addEventListener('print-ticket', function(event) {
        let saleId = event.detail.saleId; 
        if(!saleId && event.detail[0]) saleId = event.detail[0].saleId;

        if(saleId) {
            let url = "{{ route('sales.ticket', ':id') }}".replace(':id', saleId);
            window.open(url, 'Ticket', 'width=400,height=600,scrollbars=yes');
        }
    });
</script>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('salesCharts', (initialBranch, initialSeller) => ({
        branchData: initialBranch,
        sellerData: initialSeller,
        chartB: null,
        chartS: null,
        
        init() {
            this.renderBranchChart();
            this.renderSellerChart();
            
            // Escuchar actualizaciones de Livewire (Filtros de fecha/canal)
            Livewire.on('update-charts', (data) => {
                this.branchData = data[0].branches;
                this.sellerData = data[0].sellers;
                
                this.chartB.updateSeries([{ data: this.branchData.map(i => i.total_sales) }]);
                this.chartB.updateOptions({ xaxis: { categories: this.branchData.map(i => i.branch_name) } });
                
                this.chartS.updateSeries([{ data: this.sellerData.map(i => i.total_sales) }]);
                this.chartS.updateOptions({ xaxis: { categories: this.sellerData.map(i => i.seller_name) } });
            });
        },
        
        renderBranchChart() {
            let options = {
                series: [{ name: 'Ventas ₲', data: this.branchData.map(i => i.total_sales) }],
                chart: { type: 'bar', height: 350, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                colors: ['#6366f1'], // Indigo 500
                plotOptions: { bar: { borderRadius: 4, horizontal: false } },
                dataLabels: { enabled: false },
                xaxis: { categories: this.branchData.map(i => i.branch_name) },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return "₲ " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
            };
            this.chartB = new ApexCharts(this.$refs.chartBranches, options);
            this.chartB.render();
        },
        
        renderSellerChart() {
            let options = {
                series: [{ name: 'Ventas ₲', data: this.sellerData.map(i => i.total_sales) }],
                chart: { type: 'bar', height: 350, toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
                colors: ['#10b981'], // Emerald 500
                plotOptions: { bar: { borderRadius: 4, horizontal: true } },
                dataLabels: { enabled: false },
                xaxis: { 
                    categories: this.sellerData.map(i => i.seller_name),
                    labels: {
                        formatter: function (value) {
                            return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return "₲ " + val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
            };
            this.chartS = new ApexCharts(this.$refs.chartSellers, options);
            this.chartS.render();
        }
    }));
});
</script>
@endpush


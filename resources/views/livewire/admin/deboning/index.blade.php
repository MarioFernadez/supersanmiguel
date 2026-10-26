<div>
    <x-page-header title="Taller de Desposte" section="Tienda › Carnicería" :badge="$sessions->total() . ' sesiones'">
        <x-slot name="actions">
            <div class="flex items-center gap-2">
                <button wire:click="openClosureModal" class="bg-rose-600 hover:bg-rose-700 text-white font-bold py-2 px-4 rounded-xl text-xs transition shadow-sm flex items-center gap-1.5">
                    <span>🥩</span>
                    <span>Cierre / Merma Mensual</span>
                </button>
                <a href="{{ route('admin.deboning.create') }}" wire:navigate class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-xl text-xs transition shadow-sm flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                    Registrar Desposte
                </a>
            </div>
        </x-slot>
    </x-page-header>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('message'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-xl text-sm font-bold text-green-700">
                    {{ session('message') }}
                </div>
            @endif

            <!-- Card content -->
            <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-gray-200/80 dark:bg-gray-800 dark:border-gray-700">
                <!-- Search -->
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 dark:bg-gray-900/50 dark:border-gray-700">
                    <div class="relative w-full md:w-96">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-shadow dark:bg-gray-800 dark:border-gray-600 dark:text-white" placeholder="Buscar por materia prima...">
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 font-sans">
                        <thead class="bg-gray-50/80 dark:bg-gray-900/60 text-gray-500 dark:text-gray-400 uppercase text-[11px] font-semibold tracking-wider border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left">Materia Prima</th>
                                <th scope="col" class="px-6 py-4 text-left">Carnicero</th>
                                <th scope="col" class="px-6 py-4 text-left">Rendimiento / Cortes</th>
                                <th scope="col" class="px-6 py-4 text-right">Merma</th>
                                <th scope="col" class="px-6 py-4 text-right">Fecha</th>
                                <th scope="col" class="px-6 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100 dark:bg-gray-800 dark:divide-gray-700">
                            @forelse($sessions as $session)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-750 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white flex items-center gap-2">
                                            <span>🥩</span>
                                            <span>{{ $session->rawMaterial->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="text-xs text-gray-400 dark:text-gray-500">Volumen: {{ number_format($session->total_weight_start, 2) }} Kg</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="h-6 w-6 rounded-full bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-semibold text-xs uppercase border border-indigo-200/50">
                                                {{ substr($session->user->name ?? 'U', 0, 1) }}
                                            </div>
                                            <span class="text-sm text-gray-700 dark:text-gray-300 font-normal">{{ $session->user->name ?? 'Usuario' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $yieldPct = $session->total_weight_start > 0 ? round(($session->total_weight_yield / $session->total_weight_start) * 100, 1) : 0;
                                        @endphp
                                        <div class="text-sm text-gray-900 dark:text-white mb-1">
                                            Útil: <span class="font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ number_format($session->total_weight_yield, 2) }} Kg</span>
                                            <span class="text-xs font-normal text-emerald-500">({{ $yieldPct }}%)</span>
                                        </div>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($session->items->take(4) as $item)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                                                    {{ $item->product->name ?? 'Corte' }}: {{ number_format($item->weight, 1) }}k
                                                </span>
                                            @endforeach
                                            @if($session->items->count() > 4)
                                                <span class="text-[11px] text-slate-400 font-normal self-center">+{{ $session->items->count() - 4 }} cortes más</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        @php
                                            $wastePct = $session->total_weight_start > 0 ? round(($session->waste_weight / $session->total_weight_start) * 100, 1) : 0;
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $session->waste_weight > 0 ? 'bg-red-50 text-red-700 border border-red-200/60 dark:bg-red-950/40 dark:text-red-300 dark:border-red-800/40' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/60 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/40' }}">
                                            {{ number_format($session->waste_weight, 2) }} Kg ({{ $wastePct }}%)
                                        </span>
                                        <div class="text-[10px] text-gray-400 mt-0.5 uppercase font-medium">Merma</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-mono text-gray-500 dark:text-gray-400">
                                        {{ $session->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="viewDetails('{{ $session->id }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950/50 dark:hover:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 text-xs font-bold rounded-lg transition border border-indigo-200 dark:border-indigo-800 shadow-2xs">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Ver Detalle</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                        <p class="text-sm font-medium">No se encontraron registros de desposte.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($sessions->hasPages())
                    <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                        {{ $sessions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL: VER DETALLE COMPLETO DEL DESPOSTE --}}
    @if($showDetailsModal && $selectedSession)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" wire:click="closeDetails"></div>

            {{-- Modal Dialog --}}
            <div class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full border border-slate-200 dark:border-slate-700 font-sans">
                
                {{-- Modal Header --}}
                <div class="bg-[#1e282c] px-6 py-5 text-white flex items-center justify-between border-b border-slate-700">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center text-xl">
                            🥩
                        </div>
                        <div>
                            <h3 class="text-lg font-black tracking-tight leading-tight">
                                Acta de Desposte #{{ substr($selectedSession->id, 0, 8) }}
                            </h3>
                            <p class="text-slate-400 text-xs mt-0.5">
                                {{ $selectedSession->created_at->format('d/m/Y H:i') }} • Registrado por {{ $selectedSession->user->name ?? 'Usuario' }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="closeDetails" class="text-slate-400 hover:text-white transition p-1.5 rounded-lg hover:bg-slate-700/60">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 space-y-6">

                    @php
                        $startWeight = floatval($selectedSession->total_weight_start);
                        $yieldWeight = floatval($selectedSession->total_weight_yield);
                        $wasteWeight = floatval($selectedSession->waste_weight);
                        $yieldPct = $startWeight > 0 ? round(($yieldWeight / $startWeight) * 100, 1) : 0;
                        $wastePct = $startWeight > 0 ? round(($wasteWeight / $startWeight) * 100, 1) : 0;

                        // Total Estimated Sale Value
                        $totalEstimatedSale = $selectedSession->items->sum(function($i) {
                            return floatval($i->weight) * floatval($i->product->price_sale ?? 0);
                        });
                        $rawCost = floatval($selectedSession->rawMaterial->price_cost ?? 0) * $startWeight;
                        $potentialMargin = $totalEstimatedSale - $rawCost;
                    @endphp

                    {{-- 4 Metric Cards --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-slate-50 dark:bg-slate-900/60 p-3.5 rounded-2xl border border-slate-200 dark:border-slate-700">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Materia Prima</p>
                            <p class="text-lg font-black text-slate-800 dark:text-slate-100 mt-1">{{ number_format($startWeight, 2) }} Kg</p>
                            <p class="text-[11px] text-slate-500 truncate">{{ $selectedSession->rawMaterial->name ?? 'Res' }}</p>
                        </div>

                        <div class="bg-emerald-50 dark:bg-emerald-950/30 p-3.5 rounded-2xl border border-emerald-200 dark:border-emerald-800/60">
                            <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Rendimiento Útil</p>
                            <p class="text-lg font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($yieldWeight, 2) }} Kg</p>
                            <p class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">{{ $yieldPct }}% del peso total</p>
                        </div>

                        <div class="bg-red-50 dark:bg-red-950/30 p-3.5 rounded-2xl border border-red-200 dark:border-red-800/60">
                            <p class="text-[10px] font-black text-red-600 dark:text-red-400 uppercase tracking-wider">Merma / Hueso</p>
                            <p class="text-lg font-black text-red-600 dark:text-red-400 mt-1">{{ number_format($wasteWeight, 2) }} Kg</p>
                            <p class="text-[11px] font-bold text-red-600 dark:text-red-400">{{ $wastePct }}% desperdicio</p>
                        </div>

                        <div class="bg-indigo-50 dark:bg-indigo-950/30 p-3.5 rounded-2xl border border-indigo-200 dark:border-indigo-800/60">
                            <p class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-wider">Valor Venta Est.</p>
                            <p class="text-base font-black text-indigo-600 dark:text-indigo-400 mt-1 tabular-nums">₲ {{ number_format($totalEstimatedSale, 0, ',', '.') }}</p>
                            <p class="text-[10px] font-bold text-indigo-500">En mostrador POS</p>
                        </div>
                    </div>

                    {{-- Table of Cuts --}}
                    <div>
                        <h4 class="text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2.5">
                            Cortes Minoristas Obtenidos ({{ $selectedSession->items->count() }} variedades)
                        </h4>
                        <div class="border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden shadow-2xs">
                            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 text-xs">
                                <thead class="bg-slate-50 dark:bg-slate-900/60 text-slate-400 font-black uppercase text-[10px] tracking-wider">
                                    <tr>
                                        <th class="px-4 py-2.5 text-left">Corte / Producto</th>
                                        <th class="px-4 py-2.5 text-left font-mono">SKU</th>
                                        <th class="px-4 py-2.5 text-right">Peso (Kg)</th>
                                        <th class="px-4 py-2.5 text-right">% Rend.</th>
                                        <th class="px-4 py-2.5 text-right">Precio PYG/Kg</th>
                                        <th class="px-4 py-2.5 text-right">Subtotal Est.</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white dark:bg-slate-800">
                                    @foreach($selectedSession->items as $item)
                                        @php
                                            $itemWeight = floatval($item->weight);
                                            $itemPct = $startWeight > 0 ? round(($itemWeight / $startWeight) * 100, 1) : 0;
                                            $priceSale = floatval($item->product->price_sale ?? 0);
                                            $subtotal = $itemWeight * $priceSale;
                                        @endphp
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-750">
                                            <td class="px-4 py-2.5 font-bold text-slate-800 dark:text-slate-100">
                                                {{ $item->product->name ?? 'Corte eliminado' }}
                                            </td>
                                            <td class="px-4 py-2.5 font-mono text-[10px] text-slate-400">
                                                {{ $item->product->sku ?? '---' }}
                                            </td>
                                            <td class="px-4 py-2.5 text-right font-black text-slate-900 dark:text-white tabular-nums">
                                                {{ number_format($itemWeight, 2) }} Kg
                                            </td>
                                            <td class="px-4 py-2.5 text-right text-slate-500 font-semibold">
                                                {{ $itemPct }}%
                                            </td>
                                            <td class="px-4 py-2.5 text-right text-slate-600 dark:text-slate-300 tabular-nums">
                                                ₲ {{ number_format($priceSale, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-2.5 text-right font-bold text-indigo-600 dark:text-indigo-400 tabular-nums">
                                                ₲ {{ number_format($subtotal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Notes Section --}}
                    @if($selectedSession->notes)
                    <div class="bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Observaciones del Carnicero</p>
                        <p class="text-xs text-slate-700 dark:text-slate-300">{{ $selectedSession->notes }}</p>
                    </div>
                    @endif

                </div>

                {{-- Modal Footer --}}
                <div class="bg-slate-50 dark:bg-slate-900/60 px-6 py-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition shadow-2xs">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Imprimir Acta</span>
                    </button>
                    <button wire:click="closeDetails" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl transition shadow-sm">
                        Cerrar
                    </button>
                </div>

            </div>
        </div>
    </div>
    @endif

    {{-- MODAL: CIERRE MENSUAL Y LIQUIDACIÓN DE MERMA --}}
    @if($showClosureModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-closure-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" wire:click="closeClosureModal"></div>

            <div class="relative inline-block align-bottom bg-white dark:bg-slate-800 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200 dark:border-slate-700 font-sans">
                <form wire:submit.prevent="processMonthlyClosure">
                    <div class="p-6">
                        <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-700 mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-rose-50 dark:bg-rose-950/50 flex items-center justify-center text-xl">
                                    🥩
                                </div>
                                <div>
                                    <h3 class="text-lg font-black text-gray-900 dark:text-white" id="modal-closure-title">Cierre Mensual de Carnicería</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Liquida el remanente no vendido como merma del mes.</p>
                                </div>
                            </div>
                            <button type="button" wire:click="closeClosureModal" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Materia Prima Global</label>
                                <select wire:model.live="selectedRawMaterialId" class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-rose-500 focus:border-rose-500">
                                    <option value="">-- Seleccionar Materia Prima --</option>
                                    @foreach($this->rawMaterials as $raw)
                                        <option value="{{ $raw->id }}">{{ $raw->name }} (Stock en sistema: {{ number_format($raw->stock, 2) }} {{ $raw->unit_type }})</option>
                                    @endforeach
                                </select>
                                @error('selectedRawMaterialId') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            @if($this->selectedRawMaterial)
                                <div class="bg-slate-50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-gray-500 dark:text-gray-400 font-bold uppercase">Stock Registrado en Sistema:</span>
                                        <span class="font-black text-gray-900 dark:text-white text-base tabular-nums">{{ number_format($this->selectedRawMaterial->stock, 2) }} {{ $this->selectedRawMaterial->unit_type }}</span>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">
                                            ¿Cuántos Kg quedan físicamente en cámara frigorífica?
                                        </label>
                                        <div class="relative rounded-xl shadow-xs">
                                            <input type="number" step="0.001" min="0" wire:model.live="physicalRemaining" class="w-full pr-12 text-base font-black border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-rose-500 focus:border-rose-500" placeholder="0.00">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 font-bold text-xs uppercase">{{ $this->selectedRawMaterial->unit_type ?: 'Kg' }}</span>
                                            </div>
                                        </div>
                                        <p class="text-[10px] text-gray-400 mt-1">Si ya no queda carne física, deja en 0.00 Kg para mandar todo el restante a merma.</p>
                                        @error('physicalRemaining') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="bg-rose-50 dark:bg-rose-950/40 p-3.5 rounded-xl border border-rose-200/80 dark:border-rose-800/50 flex items-center justify-between">
                                        <div>
                                            <p class="text-[10px] font-black uppercase tracking-wider text-rose-600 dark:text-rose-400">Merma Calculada a Liquidar</p>
                                            <p class="text-xs text-rose-500 dark:text-rose-400/80">Desperdicio, oreo, hueso y descarte</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xl font-black text-rose-600 dark:text-rose-400 tabular-nums">
                                                {{ number_format($this->calculatedWaste, 2) }} {{ $this->selectedRawMaterial->unit_type ?: 'Kg' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1">Observaciones / Notas (Opcional)</label>
                                    <textarea wire:model="closureNotes" rows="2" class="w-full text-xs border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-rose-500 focus:border-rose-500" placeholder="Ej: Cierre de mes Octubre 2026..."></textarea>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-900/60 px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-end gap-3">
                        <button type="button" wire:click="closeClosureModal" class="px-4 py-2 text-xs font-bold text-gray-600 hover:text-gray-800 dark:text-gray-400 rounded-xl">
                            Cancelar
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black uppercase tracking-wider rounded-xl transition shadow-sm disabled:opacity-50" wire:loading.attr="disabled" {{ !$this->selectedRawMaterial ? 'disabled' : '' }}>
                            <span wire:loading.remove wire:target="processMonthlyClosure">Confirmar y Liquidar Merma</span>
                            <span wire:loading wire:target="processMonthlyClosure">Procesando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

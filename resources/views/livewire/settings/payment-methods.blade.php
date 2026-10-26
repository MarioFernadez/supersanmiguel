<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 dark:bg-gray-800">
            
            <!-- Encabezado -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8 pb-6 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <a href="{{ route('settings.general') }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition dark:text-gray-400 dark:hover:bg-gray-700" title="Volver a Ajustes">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Métodos de Pago</h2>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">Activa o desactiva los canales de cobro disponibles en el Punto de Venta (POS).</p>
                    </div>
                </div>

                <!-- Buscador rápido -->
                <div class="w-full sm:w-72">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input wire:model.live="search" type="text" placeholder="Buscar método de pago..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-xs shadow-xs transition dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 p-4 mb-6 rounded-r-xl text-xs dark:bg-emerald-950/40 dark:border-emerald-600 dark:text-emerald-300 flex items-center justify-between" role="alert">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span class="font-medium">{{ session('message') }}</span>
                    </div>
                </div>
            @endif

            <!-- Tabla Profesional -->
            <div class="overflow-x-auto border border-gray-100 rounded-2xl shadow-xs dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50/80 dark:bg-gray-900/60">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400">Método de Pago</th>
                            <th scope="col" class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400">Tipo de Cobro</th>
                            <th scope="col" class="px-6 py-4 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400">Estado en Caja</th>
                            <th scope="col" class="px-6 py-4 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider dark:text-gray-400">Habilitar / Deshabilitar</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($paymentMethods as $pm)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-750 transition-colors">
                                <!-- Nombre y Detalle -->
                                <td class="px-6 py-4.5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-700/60 border border-gray-200/60 dark:border-gray-600 flex items-center justify-center text-gray-500 dark:text-gray-400 shrink-0">
                                            @if($pm->type === 'cash' || strtolower($pm->name) === 'efectivo')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <rect x="2" y="6" width="20" height="12" rx="2" stroke-width="1.8"/>
                                                    <circle cx="12" cy="12" r="2.5" stroke-width="1.8"/>
                                                    <path stroke-linecap="round" stroke-width="1.8" d="M6 10h.01M18 14h.01"/>
                                                </svg>
                                            @elseif($pm->type === 'card' || strtolower($pm->name) === 'tarjeta')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="1.8"/>
                                                    <line x1="2" y1="10" x2="22" y2="10" stroke-width="1.8"/>
                                                    <path stroke-linecap="round" stroke-width="1.8" d="M6 15h4"/>
                                                </svg>
                                            @elseif($pm->type === 'qr' || strtolower($pm->name) === 'qr')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                                </svg>
                                            @elseif($pm->type === 'transfer' || strtolower($pm->name) === 'transferencia')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h18M3 14h18M5 10v6m4-6v6m6-6v6m4-6v6M3 18h18M4 10l8-6 8 6"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="12" r="9" stroke-width="1.8"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v8m-3-5h6"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $pm->name }}</div>
                                            <div class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">
                                                @if($pm->type === 'cash' || strtolower($pm->name) === 'efectivo')
                                                    Cobro con papel moneda y billetes en caja
                                                @elseif($pm->type === 'qr' || strtolower($pm->name) === 'qr')
                                                    Billetera digital y lectura de código QR
                                                @elseif($pm->type === 'card' || strtolower($pm->name) === 'tarjeta')
                                                    Terminal bancaria POS (Débito y Crédito)
                                                @elseif($pm->type === 'transfer' || strtolower($pm->name) === 'transferencia')
                                                    Depósito o transferencia bancaria directa
                                                @else
                                                    Canal de cobro registrado
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Tipo -->
                                <td class="px-6 py-4.5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium text-gray-600 bg-gray-100 dark:bg-gray-700 dark:text-gray-300">
                                        @if($pm->type === 'cash') Efectivo
                                        @elseif($pm->type === 'card') Tarjeta POS
                                        @elseif($pm->type === 'transfer') Transferencia
                                        @elseif($pm->type === 'qr') Código QR
                                        @else {{ ucfirst($pm->type) }}
                                        @endif
                                    </span>
                                </td>

                                <!-- Estado Badge -->
                                <td class="px-6 py-4.5 whitespace-nowrap">
                                    @if($pm->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/80 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800/50">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 border border-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:border-gray-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                            Desactivado
                                        </span>
                                    @endif
                                </td>

                                <!-- Switch Toggle -->
                                <td class="px-6 py-4.5 whitespace-nowrap text-right">
                                    <button 
                                        type="button" 
                                        wire:click="toggleStatus('{{ $pm->id }}')" 
                                        wire:loading.attr="disabled"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 disabled:opacity-50 {{ $pm->is_active ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700' }}" 
                                        role="switch" 
                                        aria-checked="{{ $pm->is_active ? 'true' : 'false' }}" 
                                        title="{{ $pm->is_active ? 'Click para desactivar este método de la caja' : 'Click para activar este método en la caja' }}"
                                    >
                                        <span class="sr-only">Cambiar estado del método</span>
                                        <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $pm->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-400 text-xs italic dark:text-gray-500">
                                    No se encontraron métodos de pago.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $paymentMethods->links() }}
            </div>

        </div>
    </div>
</div>

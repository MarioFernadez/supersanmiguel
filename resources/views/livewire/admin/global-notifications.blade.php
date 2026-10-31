<div class="relative flex items-center ms-3" x-data="{ open: false }">
    <!-- Low Stock Alert Icon -->
    @if($lowStockCount > 0)
        <a href="{{ route('reports.inventory', ['stock' => 'low']) }}" class="relative p-2 text-orange-400 hover:text-orange-600 transition-colors focus:outline-none" title="Productos con stock bajo">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <span class="absolute top-1 right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-orange-600 rounded-full animate-bounce">
                {{ $lowStockCount }}
            </span>
        </a>
    @endif

    <!-- Notification Bell -->
    <button @click="open = !open; if(open) $wire.clearNotifications()" class="relative p-2 text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors focus:outline-none dark:text-gray-500">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
    </button>

    <!-- Dropdown Panel -->
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         class="absolute right-0 top-full mt-3 w-80 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden"
         x-cloak>
        
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
            <h3 class="text-sm font-bold text-gray-800 dark:text-gray-200 uppercase tracking-tight">
                Alertas del Sistema
            </h3>
        </div>

        <div class="max-h-96 overflow-y-auto">
            @if($lowStockCount > 0)
                <div class="p-4 bg-orange-50/50 dark:bg-orange-950/30 border-b border-orange-100 dark:border-orange-900/40">
                    <p class="text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest mb-2 flex items-center">
                         <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                         Alertas de Stock ({{ $lowStockCount }})
                    </p>
                    <a href="{{ route('reports.inventory', ['stock' => 'low']) }}" class="text-xs font-bold text-orange-700 dark:text-orange-300 hover:underline">Hay {{ $lowStockCount }} productos agotándose. Revisar inventario →</a>
                </div>
            @else
                <div class="p-8 text-center">
                    <svg class="w-10 h-10 text-emerald-100 dark:text-emerald-900/50 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-xs text-gray-400 dark:text-gray-500">El sistema no reporta alertas pendientes</p>
                </div>
            @endif
        </div>
    </div>
</div>

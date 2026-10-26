{{-- Sidebar — Estilo Auténtico PrestaShop 8 con Iconos Prominentes y Flyouts Flotantes --}}
<aside id="sidebar"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    x-data="{ 
        collapsed: localStorage.getItem('sidebar_collapsed') === '1',
        activeGroup: null 
    }"
    x-init="$watch('collapsed', v => localStorage.setItem('sidebar_collapsed', v ? '1' : '0'))"
    :style="collapsed ? 'width: 52px;' : 'width: 245px;'"
    class="fixed inset-y-0 left-0 z-50 flex flex-col bg-[#253238] border-r border-slate-700/80 shadow-xl transition-all duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:z-auto"
    style="width: 245px;">

    {{-- Logo + Toggle Header (Shopify/PrestaShop Modern Style) --}}
    <div class="flex items-center justify-between h-16 px-4 bg-[#1a2327] border-b border-slate-700/70 flex-shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0 group" x-show="!collapsed">
            <div class="w-8 h-8 rounded-xl bg-slate-800/90 border border-slate-700/70 p-1 flex items-center justify-center shadow-xs flex-shrink-0 group-hover:border-[#25b9d7]/60 transition">
                <x-application-mark class="w-full h-full object-contain" />
            </div>
            <span class="text-white font-bold text-base tracking-tight whitespace-nowrap">Zona<span class="text-[#25b9d7]">Shop</span></span>
        </a>
        <a href="{{ route('dashboard') }}" x-show="collapsed" class="flex items-center justify-center w-full" title="ZonaShop">
            <div class="w-8 h-8 rounded-xl bg-slate-800/90 border border-slate-700/70 p-1 flex items-center justify-center shadow-xs hover:border-[#25b9d7]/60 transition">
                <x-application-mark class="w-full h-full object-contain" />
            </div>
        </a>
        <button @click="collapsed = !collapsed"
            class="flex-shrink-0 p-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-700/60 transition"
            :title="collapsed ? 'Expandir menú lateral' : 'Colapsar menú lateral'">
            <svg x-show="!collapsed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            <svg x-show="collapsed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
            </svg>
        </button>
    </div>

    {{-- Navigation List --}}
    <nav class="flex-1 overflow-y-auto overflow-x-visible py-1.5 scrollbar-thin scrollbar-thumb-slate-600"
         style="font-family: 'Inter', 'Open Sans', system-ui, sans-serif; font-size: 13px;">

    @php
        // Top-level direct link
        $topLinkClass = fn($active) =>
            'flex items-center transition-colors duration-150 border-l-[3px] whitespace-nowrap ' .
            ($active
                ? 'bg-[#1e282c] text-white font-semibold border-[#25b9d7] shadow-inner'
                : 'text-slate-300 hover:bg-[#1e282c]/80 hover:text-white border-transparent');

        // Accordion parent button
        $groupBtnClass = fn($active) =>
            'w-full flex items-center transition-colors duration-150 border-l-[3px] whitespace-nowrap ' .
            ($active
                ? 'bg-[#1e282c] text-white font-semibold border-[#25b9d7]'
                : 'text-slate-300 hover:bg-[#1e282c]/80 hover:text-white border-transparent');

        // Sub-item link
        $subLinkClass = fn($active) =>
            'block px-2.5 py-1.5 rounded transition-colors duration-150 whitespace-nowrap text-[12px] ' .
            ($active
                ? 'text-white font-bold bg-[#25b9d7]/20 text-[#25b9d7]'
                : 'text-slate-300 hover:text-white hover:bg-slate-700/50');
    @endphp

    {{-- Dashboard / Inicio (Icono Casa / Home) --}}
    <div class="relative group/flyout mt-1">
        <a href="{{ route('dashboard') }}" wire:navigate
           :class="collapsed ? 'justify-center px-0 h-11' : 'px-3.5 py-2.5 gap-3'"
           class="{{ $topLinkClass(request()->routeIs('dashboard')) }}"
           title="Inicio / Panel Principal">
            <svg class="w-5 h-5 flex-shrink-0 text-[#25b9d7]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span x-show="!collapsed" class="truncate font-medium">Inicio</span>
        </a>
        <div x-show="collapsed" class="absolute left-full top-0 ml-1 px-3 py-2 bg-[#1e282c] border border-slate-700 text-white text-xs font-semibold rounded shadow-2xl whitespace-nowrap hidden group-hover/flyout:block z-[9999]">
            Inicio
        </div>
    </div>

    @if(App\Services\LicenseService::isEcommerceEnabled())
    <div class="relative group/flyout">
        <a href="{{ route('shop.index') }}" target="_blank"
           :class="collapsed ? 'justify-center px-0 h-11' : 'px-3.5 py-2.5 gap-3'"
           class="{{ $topLinkClass(false) }}"
           title="Ver Tienda Online">
            <svg class="w-5 h-5 flex-shrink-0 text-slate-300 group-hover/flyout:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            <span x-show="!collapsed" class="truncate font-medium">Ver Tienda</span>
            <span x-show="!collapsed" class="ml-auto text-[10px] bg-[#25b9d7]/20 text-[#25b9d7] px-1.5 py-0.5 rounded font-mono">web</span>
        </a>
        <div x-show="collapsed" class="absolute left-full top-0 ml-1 px-3 py-2 bg-[#1e282c] border border-slate-700 text-white text-xs font-semibold rounded shadow-2xl whitespace-nowrap hidden group-hover/flyout:block z-[9999]">
            Ver Tienda Online ↗
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════════════════════
         SECCIÓN: VENDER (SELL)
    ═════════════════════════════════════════════════ --}}
    <div x-show="!collapsed" class="px-3.5 pt-3 pb-1 text-[9.5px] font-black text-slate-400 uppercase tracking-wider">
        Vender
    </div>

    {{-- 1. PEDIDOS & POS (Bolsa de Compras PrestaShop) --}}
    @php
        $pedidosActive = request()->routeIs('pos.*')
            || request()->routeIs('admin.web-orders.*')
            || request()->routeIs('reports.cash')
            || request()->routeIs('reports.cash-movements');
    @endphp
    <div class="relative group/flyout" x-data="{ open: {{ $pedidosActive ? 'true' : 'false' }} }">
        <button @click="!collapsed && (open = !open)"
                :title="collapsed ? 'Pedidos & POS' : ''"
                :class="collapsed ? 'justify-center px-0 h-11' : 'justify-between px-3.5 py-2.5'"
                class="{{ $groupBtnClass($pedidosActive) }}">
            <div class="flex items-center gap-3 min-w-0" :class="collapsed ? 'justify-center w-full' : ''">
                <svg class="w-5 h-5 flex-shrink-0 text-slate-300 group-hover/flyout:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span x-show="!collapsed" class="truncate font-medium">Pedidos & POS</span>
            </div>
            <svg x-show="!collapsed"
                 :class="open ? 'rotate-180' : ''"
                 class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200 text-slate-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Accordion --}}
        <div x-show="open && !collapsed" x-collapse>
            <div class="pb-1.5 pt-0.5 space-y-0.5 pl-8 pr-2.5">
                <a href="{{ route('pos.index') }}" class="{{ $subLinkClass(request()->routeIs('pos.*')) }}">
                    Punto de Venta (POS)
                </a>
                @if(hasModule('restaurant'))
                <a href="{{ route('pos.index') }}" class="{{ $subLinkClass(false) }}">
                    Salón y Mesas
                </a>
                @endif
                @if(\App\Services\LicenseService::isEcommerceEnabled())
                <a href="{{ route('admin.web-orders.index') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('admin.web-orders.*')) }}">
                    Pedidos Online
                </a>
                @endif
                <a href="{{ route('reports.cash') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('reports.cash') || request()->routeIs('reports.cash-movements')) }}">
                    Arqueos y Cierres de Caja
                </a>
            </div>
        </div>

        {{-- Collapsed Flyout --}}
        <div x-show="collapsed" class="absolute left-full top-0 ml-1 w-56 bg-[#1e282c] border border-slate-700 shadow-2xl rounded py-2 hidden group-hover/flyout:block z-[9999]">
            <div class="px-3.5 py-1.5 text-[11px] font-black text-[#25b9d7] uppercase tracking-wider border-b border-slate-700/60">
                Pedidos & POS
            </div>
            <div class="p-1 space-y-0.5">
                <a href="{{ route('pos.index') }}" class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Punto de Venta (POS)</a>
                @if(hasModule('restaurant'))
                <a href="{{ route('pos.index') }}" class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Salón y Mesas</a>
                @endif
                @if(\App\Services\LicenseService::isEcommerceEnabled())
                <a href="{{ route('admin.web-orders.index') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Pedidos Online</a>
                @endif
                <a href="{{ route('reports.cash') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Arqueos y Cierres de Caja</a>
            </div>
        </div>
    </div>

    {{-- 2. CATÁLOGO (Caja / Tienda PrestaShop) --}}
    @can('gerente')
    @php
        $catalogoActive = request()->routeIs('products.*')
            || request()->routeIs('categories.*')
            || request()->routeIs('settings.units');
    @endphp
    <div class="relative group/flyout" x-data="{ open: {{ $catalogoActive ? 'true' : 'false' }} }">
        <button @click="!collapsed && (open = !open)"
                :title="collapsed ? 'Catálogo' : ''"
                :class="collapsed ? 'justify-center px-0 h-11' : 'justify-between px-3.5 py-2.5'"
                class="{{ $groupBtnClass($catalogoActive) }}">
            <div class="flex items-center gap-3 min-w-0" :class="collapsed ? 'justify-center w-full' : ''">
                <svg class="w-5 h-5 flex-shrink-0 text-slate-300 group-hover/flyout:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span x-show="!collapsed" class="truncate font-medium">Catálogo</span>
            </div>
            <svg x-show="!collapsed"
                 :class="open ? 'rotate-180' : ''"
                 class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200 text-slate-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Accordion --}}
        <div x-show="open && !collapsed" x-collapse>
            <div class="pb-1.5 pt-0.5 space-y-0.5 pl-8 pr-2.5">
                <a href="{{ route('products.index') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('products.*') && !request()->routeIs('products.print-labels')) }}">
                    Productos
                </a>
                <a href="{{ route('categories.index') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('categories.*')) }}">
                    Categorías
                </a>
                <a href="{{ route('products.print-labels') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('products.print-labels')) }}">
                    Etiquetas & Barcodes
                </a>
                <a href="{{ route('settings.units') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('settings.units')) }}">
                    Unidades de Medida
                </a>
            </div>
        </div>

        {{-- Collapsed Flyout --}}
        <div x-show="collapsed" class="absolute left-full top-0 ml-1 w-56 bg-[#1e282c] border border-slate-700 shadow-2xl rounded py-2 hidden group-hover/flyout:block z-[9999]">
            <div class="px-3.5 py-1.5 text-[11px] font-black text-[#25b9d7] uppercase tracking-wider border-b border-slate-700/60">
                Catálogo
            </div>
            <div class="p-1 space-y-0.5">
                <a href="{{ route('products.index') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Productos</a>
                <a href="{{ route('categories.index') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Categorías</a>
                <a href="{{ route('products.print-labels') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Etiquetas & Barcodes</a>
                <a href="{{ route('settings.units') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Unidades de Medida</a>
            </div>
        </div>
    </div>
    @endcan

    {{-- 3. CLIENTES (Icono Usuario Circular PrestaShop) --}}
    @php
        $clientesActive = request()->routeIs('customers.*')
            || request()->routeIs('admin.collections');
    @endphp
    <div class="relative group/flyout" x-data="{ open: {{ $clientesActive ? 'true' : 'false' }} }">
        <button @click="!collapsed && (open = !open)"
                :title="collapsed ? 'Clientes' : ''"
                :class="collapsed ? 'justify-center px-0 h-11' : 'justify-between px-3.5 py-2.5'"
                class="{{ $groupBtnClass($clientesActive) }}">
            <div class="flex items-center gap-3 min-w-0" :class="collapsed ? 'justify-center w-full' : ''">
                <svg class="w-5 h-5 flex-shrink-0 text-slate-300 group-hover/flyout:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-show="!collapsed" class="truncate font-medium">Clientes</span>
            </div>
            <svg x-show="!collapsed"
                 :class="open ? 'rotate-180' : ''"
                 class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200 text-slate-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Accordion --}}
        <div x-show="open && !collapsed" x-collapse>
            <div class="pb-1.5 pt-0.5 space-y-0.5 pl-8 pr-2.5">
                <a href="{{ route('customers.index') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('customers.*')) }}">
                    Directorio Clientes
                </a>
                @can('gerente')
                <a href="{{ route('admin.collections') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('admin.collections')) }}">
                    Cuentas por Cobrar (Créditos)
                </a>
                @endcan
            </div>
        </div>

        {{-- Collapsed Flyout --}}
        <div x-show="collapsed" class="absolute left-full top-0 ml-1 w-56 bg-[#1e282c] border border-slate-700 shadow-2xl rounded py-2 hidden group-hover/flyout:block z-[9999]">
            <div class="px-3.5 py-1.5 text-[11px] font-black text-[#25b9d7] uppercase tracking-wider border-b border-slate-700/60">
                Clientes
            </div>
            <div class="p-1 space-y-0.5">
                <a href="{{ route('customers.index') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Directorio Clientes</a>
                @can('gerente')
                <a href="{{ route('admin.collections') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Cuentas por Cobrar (Créditos)</a>
                @endcan
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════════
         SECCIÓN: GESTIÓN Y OPERACIONES
    ═════════════════════════════════════════════════ --}}
    <div x-show="!collapsed" class="px-3.5 pt-3 pb-1 text-[9.5px] font-black text-slate-400 uppercase tracking-wider">
        Gestión
    </div>

    {{-- CARNICERÍA / DESPOSTE --}}
    @can('gerente')
    <div class="relative group/flyout">
        <a href="{{ route('admin.deboning.index') }}" wire:navigate
           :class="collapsed ? 'justify-center px-0 h-11' : 'px-3.5 py-2.5 gap-2.5'"
           class="{{ $topLinkClass(request()->routeIs('admin.deboning.*')) }}"
           title="Desposte de Carne (Carnicería)">
            <svg class="w-5 h-5 flex-shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879a3 3 0 11-4.242-4.242L12 5m0 7l-7 7"/>
            </svg>
            <span x-show="!collapsed" class="font-medium text-[12.5px] whitespace-nowrap">Desposte de Carne</span>
            <span x-show="!collapsed" class="ml-auto text-[8.5px] bg-rose-500/20 text-rose-300 px-1.5 py-0.5 rounded font-black flex-shrink-0">CARNE</span>
        </a>
        <div x-show="collapsed" class="absolute left-full top-0 ml-1 px-3 py-2 bg-[#1e282c] border border-slate-700 text-white text-xs font-semibold rounded shadow-2xl whitespace-nowrap hidden group-hover/flyout:block z-[9999]">
            Desposte de Carne
        </div>
    </div>
    @endcan

    {{-- COMPRAS & PROVEEDORES (Camión / Envíos PrestaShop) --}}
    @can('gerente')
    @php
        $comprasActive = request()->routeIs('purchases.*')
            || request()->routeIs('suppliers.*');
    @endphp
    <div class="relative group/flyout" x-data="{ open: {{ $comprasActive ? 'true' : 'false' }} }">
        <button @click="!collapsed && (open = !open)"
                :title="collapsed ? 'Compras' : ''"
                :class="collapsed ? 'justify-center px-0 h-11' : 'justify-between px-3.5 py-2.5'"
                class="{{ $groupBtnClass($comprasActive) }}">
            <div class="flex items-center gap-3 min-w-0" :class="collapsed ? 'justify-center w-full' : ''">
                <svg class="w-5 h-5 flex-shrink-0 text-slate-300 group-hover/flyout:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                <span x-show="!collapsed" class="truncate font-medium">Compras</span>
            </div>
            <svg x-show="!collapsed"
                 :class="open ? 'rotate-180' : ''"
                 class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200 text-slate-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Accordion --}}
        <div x-show="open && !collapsed" x-collapse>
            <div class="pb-1.5 pt-0.5 space-y-0.5 pl-8 pr-2.5">
                <a href="{{ route('purchases.index') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('purchases.*')) }}">
                    Registro de Compras
                </a>
                <a href="{{ route('suppliers.index') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('suppliers.*')) }}">
                    Proveedores
                </a>
            </div>
        </div>

        {{-- Collapsed Flyout --}}
        <div x-show="collapsed" class="absolute left-full top-0 ml-1 w-56 bg-[#1e282c] border border-slate-700 shadow-2xl rounded py-2 hidden group-hover/flyout:block z-[9999]">
            <div class="px-3.5 py-1.5 text-[11px] font-black text-[#25b9d7] uppercase tracking-wider border-b border-slate-700/60">
                Compras
            </div>
            <div class="p-1 space-y-0.5">
                <a href="{{ route('purchases.index') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Registro de Compras</a>
                <a href="{{ route('suppliers.index') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Proveedores</a>
            </div>
        </div>
    </div>

    {{-- FINANZAS & GASTOS (Tarjeta / Pagos PrestaShop) --}}
    @php
        $finanzasActive = request()->routeIs('expenses.*')
            || request()->routeIs('expenses.categories.*');
    @endphp
    <div class="relative group/flyout" x-data="{ open: {{ $finanzasActive ? 'true' : 'false' }} }">
        <button @click="!collapsed && (open = !open)"
                :title="collapsed ? 'Finanzas' : ''"
                :class="collapsed ? 'justify-center px-0 h-11' : 'justify-between px-3.5 py-2.5'"
                class="{{ $groupBtnClass($finanzasActive) }}">
            <div class="flex items-center gap-3 min-w-0" :class="collapsed ? 'justify-center w-full' : ''">
                <svg class="w-5 h-5 flex-shrink-0 text-slate-300 group-hover/flyout:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span x-show="!collapsed" class="truncate font-medium">Finanzas</span>
            </div>
            <svg x-show="!collapsed"
                 :class="open ? 'rotate-180' : ''"
                 class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200 text-slate-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Accordion --}}
        <div x-show="open && !collapsed" x-collapse>
            <div class="pb-1.5 pt-0.5 space-y-0.5 pl-8 pr-2.5">
                <a href="{{ route('expenses.index') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('expenses.*') && !request()->routeIs('expenses.categories.*')) }}">
                    Gastos & Egresos
                </a>
                <a href="{{ route('expenses.categories.index') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('expenses.categories.*')) }}">
                    Categorías de Gastos
                </a>
            </div>
        </div>

        {{-- Collapsed Flyout --}}
        <div x-show="collapsed" class="absolute left-full top-0 ml-1 w-56 bg-[#1e282c] border border-slate-700 shadow-2xl rounded py-2 hidden group-hover/flyout:block z-[9999]">
            <div class="px-3.5 py-1.5 text-[11px] font-black text-[#25b9d7] uppercase tracking-wider border-b border-slate-700/60">
                Finanzas
            </div>
            <div class="p-1 space-y-0.5">
                <a href="{{ route('expenses.index') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Gastos & Egresos</a>
                <a href="{{ route('expenses.categories.index') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Categorías de Gastos</a>
            </div>
        </div>
    </div>
    @endcan

    {{-- ════════════════════════════════════════════════
         SECCIÓN: CONFIGURAR (CONFIGURE)
    ═════════════════════════════════════════════════ --}}
    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
    <div x-show="!collapsed" class="px-3.5 pt-3 pb-1 text-[9.5px] font-black text-slate-400 uppercase tracking-wider">
        Configurar
    </div>

    {{-- REPORTES & ESTADÍSTICAS (Barras Estadísticas PrestaShop) --}}
    @can('gerente')
    @php
        $reportesActive = request()->routeIs('reports.sales')
            || request()->routeIs('reports.salesperson')
            || request()->routeIs('reports.inventory')
            || request()->routeIs('reports.debtors');
    @endphp
    <div class="relative group/flyout" x-data="{ open: {{ $reportesActive ? 'true' : 'false' }} }">
        <button @click="!collapsed && (open = !open)"
                :title="collapsed ? 'Estadísticas' : ''"
                :class="collapsed ? 'justify-center px-0 h-11' : 'justify-between px-3.5 py-2.5'"
                class="{{ $groupBtnClass($reportesActive) }}">
            <div class="flex items-center gap-3 min-w-0" :class="collapsed ? 'justify-center w-full' : ''">
                <svg class="w-5 h-5 flex-shrink-0 text-slate-300 group-hover/flyout:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span x-show="!collapsed" class="truncate font-medium">Estadísticas</span>
            </div>
            <svg x-show="!collapsed"
                 :class="open ? 'rotate-180' : ''"
                 class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200 text-slate-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Accordion --}}
        <div x-show="open && !collapsed" x-collapse>
            <div class="pb-1.5 pt-0.5 space-y-0.5 pl-8 pr-2.5">
                <a href="{{ route('reports.sales') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('reports.sales')) }}">
                    Detalle de Ventas
                </a>
                <a href="{{ route('reports.salesperson') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('reports.salesperson')) }}">
                    Ranking Vendedores
                </a>
                <a href="{{ route('reports.inventory') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('reports.inventory')) }}">
                    Estado de Inventario
                </a>
                <a href="{{ route('reports.debtors') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('reports.debtors')) }}">
                    Saldos Deudores
                </a>
            </div>
        </div>

        {{-- Collapsed Flyout --}}
        <div x-show="collapsed" class="absolute left-full top-0 ml-1 w-56 bg-[#1e282c] border border-slate-700 shadow-2xl rounded py-2 hidden group-hover/flyout:block z-[9999]">
            <div class="px-3.5 py-1.5 text-[11px] font-black text-[#25b9d7] uppercase tracking-wider border-b border-slate-700/60">
                Estadísticas & Reportes
            </div>
            <div class="p-1 space-y-0.5">
                <a href="{{ route('reports.sales') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Detalle de Ventas</a>
                <a href="{{ route('reports.salesperson') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Ranking Vendedores</a>
                <a href="{{ route('reports.inventory') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Estado de Inventario</a>
                <a href="{{ route('reports.debtors') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Saldos Deudores</a>
            </div>
        </div>
    </div>
    @endcan

    {{-- PARÁMETROS DE LA TIENDA (Engranaje / Shop Parameters PrestaShop) --}}
    @php
        $tiendaParamActive = request()->routeIs('settings.general')
            || request()->routeIs('settings.billing')
            || request()->routeIs('settings.quotation')
            || request()->routeIs('settings.restaurant')
            || request()->routeIs('settings.customization')
            || request()->routeIs('pages.*')
            || request()->routeIs('admin.media');
    @endphp
    <div class="relative group/flyout" x-data="{ open: {{ $tiendaParamActive ? 'true' : 'false' }} }">
        <button @click="!collapsed && (open = !open)"
                :title="collapsed ? 'Parámetros Tienda' : ''"
                :class="collapsed ? 'justify-center px-0 h-11' : 'justify-between px-3.5 py-2.5'"
                class="{{ $groupBtnClass($tiendaParamActive) }}">
            <div class="flex items-center gap-3 min-w-0" :class="collapsed ? 'justify-center w-full' : ''">
                <svg class="w-5 h-5 flex-shrink-0 text-slate-300 group-hover/flyout:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span x-show="!collapsed" class="truncate font-medium">Parámetros Tienda</span>
            </div>
            <svg x-show="!collapsed"
                 :class="open ? 'rotate-180' : ''"
                 class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200 text-slate-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Accordion --}}
        <div x-show="open && !collapsed" x-collapse>
            <div class="pb-1.5 pt-0.5 space-y-0.5 pl-8 pr-2.5">
                @can('admin')
                <a href="{{ route('settings.general') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('settings.general')) }}">
                    Ajustes Generales
                </a>
                <a href="{{ route('settings.billing') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('settings.billing')) }}">
                    Plan & Suscripción
                </a>
                <a href="{{ route('settings.invoicing') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('settings.invoicing')) }}">
                    Facturación Electrónica
                </a>
                <a href="{{ route('settings.quotation') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('settings.quotation')) }}">
                    Cotizaciones Divisas
                </a>
                @if(hasModule('restaurant'))
                <a href="{{ route('settings.restaurant') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('settings.restaurant')) }}">
                    Mesas & Mozos
                </a>
                @endif
                @if(\App\Services\LicenseService::isEcommerceEnabled())
                <a href="{{ route('settings.customization') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('settings.customization') || request()->routeIs('pages.*') || request()->routeIs('admin.media')) }}">
                    Personalización Web
                </a>
                @endif
                @endcan
            </div>
        </div>

        {{-- Collapsed Flyout --}}
        <div x-show="collapsed" class="absolute left-full top-0 ml-1 w-56 bg-[#1e282c] border border-slate-700 shadow-2xl rounded py-2 hidden group-hover/flyout:block z-[9999]">
            <div class="px-3.5 py-1.5 text-[11px] font-black text-[#25b9d7] uppercase tracking-wider border-b border-slate-700/60">
                Parámetros Tienda
            </div>
            <div class="p-1 space-y-0.5">
                @can('admin')
                <a href="{{ route('settings.general') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Ajustes Generales</a>
                <a href="{{ route('settings.billing') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Plan & Suscripción</a>
                <a href="{{ route('settings.invoicing') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Facturación Electrónica</a>
                <a href="{{ route('settings.quotation') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Cotizaciones Divisas</a>
                @if(hasModule('restaurant'))
                <a href="{{ route('settings.restaurant') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Mesas & Mozos</a>
                @endif
                @if(\App\Services\LicenseService::isEcommerceEnabled())
                <a href="{{ route('settings.customization') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Personalización Web</a>
                @endif
                @endcan
            </div>
        </div>
    </div>

    {{-- PARÁMETROS AVANZADOS / ADMINISTRACIÓN (Sliders / Llave / Advanced Parameters PrestaShop) --}}
    @php
        $adminActive = request()->routeIs('branches.*')
            || request()->routeIs('cash_registers.*')
            || request()->routeIs('users.*')
            || request()->routeIs('sellers.*')
            || request()->routeIs('master.*');
    @endphp
    <div class="relative group/flyout" x-data="{ open: {{ $adminActive ? 'true' : 'false' }} }">
        <button @click="!collapsed && (open = !open)"
                :title="collapsed ? 'Parámetros Avanzados' : ''"
                :class="collapsed ? 'justify-center px-0 h-11' : 'justify-between px-3.5 py-2.5'"
                class="{{ $groupBtnClass($adminActive) }}">
            <div class="flex items-center gap-3 min-w-0" :class="collapsed ? 'justify-center w-full' : ''">
                <svg class="w-5 h-5 flex-shrink-0 text-slate-300 group-hover/flyout:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
                <span x-show="!collapsed" class="truncate font-medium">Administración</span>
            </div>
            <svg x-show="!collapsed"
                 :class="open ? 'rotate-180' : ''"
                 class="w-3.5 h-3.5 flex-shrink-0 transition-transform duration-200 text-slate-400"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Accordion --}}
        <div x-show="open && !collapsed" x-collapse>
            <div class="pb-1.5 pt-0.5 space-y-0.5 pl-8 pr-2.5">
                @can('admin')
                <a href="{{ route('branches.index') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('branches.*')) }}">
                    Sucursales
                </a>
                <a href="{{ route('cash_registers.index') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('cash_registers.*')) }}">
                    Cajas Registradoras
                </a>
                <a href="{{ route('users.index') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('users.*')) }}">
                    Usuarios & Roles
                </a>
                @endcan

                @can('gerente')
                <a href="{{ route('sellers.index') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('sellers.*')) }}">
                    Vendedores & Comisiones
                </a>
                @endcan

                @if(auth()->user()->isDeveloper())
                <a href="{{ route('master.licensing') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('master.licensing')) }}">
                    Master Licensing
                </a>
                <a href="{{ route('master.sifen') }}" wire:navigate class="{{ $subLinkClass(request()->routeIs('master.sifen*')) }}">
                    Facturación SIFEN
                </a>
                @endif
            </div>
        </div>

        {{-- Collapsed Flyout --}}
        <div x-show="collapsed" class="absolute left-full top-0 ml-1 w-52 bg-[#1e282c] border border-slate-700 shadow-2xl rounded py-2 hidden group-hover/flyout:block z-[9999]">
            <div class="px-3.5 py-1.5 text-[11px] font-black text-[#25b9d7] uppercase tracking-wider border-b border-slate-700/60">
                Administración
            </div>
            <div class="p-1 space-y-0.5">
                @can('admin')
                <a href="{{ route('branches.index') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Sucursales</a>
                <a href="{{ route('cash_registers.index') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Puntos de Venta</a>
                <a href="{{ route('users.index') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Usuarios</a>
                @endcan
                @can('gerente')
                <a href="{{ route('sellers.index') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Vendedores</a>
                @endcan
                @if(auth()->user()->isDeveloper())
                <a href="{{ route('master.licensing') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Master Licensing</a>
                <a href="{{ route('master.sifen') }}" wire:navigate class="block px-3 py-1.5 text-xs text-slate-200 hover:bg-slate-700 hover:text-white rounded">Facturación SIFEN</a>
                @endif
            </div>
        </div>
    </div>
    @endif

    </nav>

    {{-- User Profile Footer --}}
    <div class="flex-shrink-0 px-2.5 py-2 bg-[#1e282c] border-t border-slate-700/70">
        <div class="flex items-center justify-between gap-2 rounded" :class="collapsed ? 'justify-center' : 'px-1'">
            <div class="flex items-center gap-2 min-w-0" :class="collapsed ? 'justify-center w-full' : ''">
                <div class="w-7 h-7 rounded bg-[#25b9d7] flex items-center justify-center text-white font-black text-xs flex-shrink-0 shadow-sm" title="{{ auth()->user()->name }}">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div x-show="!collapsed" class="flex-1 min-w-0">
                    <p class="text-slate-200 font-semibold text-xs truncate leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-slate-400 text-[10px] truncate leading-none mt-0.5">{{ auth()->user()->role_label ?? 'Administrador' }}</p>
                </div>
            </div>
            <button type="button"
                    @click="window.dispatchEvent(new CustomEvent('open-about-modal'))"
                    x-show="!collapsed"
                    class="p-1 rounded text-slate-400 hover:text-[#25b9d7] hover:bg-slate-700/50 transition flex-shrink-0"
                    title="Acerca de ZonaShop / Soporte">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </button>
        </div>
    </div>

</aside>

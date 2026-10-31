<nav x-data="{ open: false, quickAccess: false }" class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-750 flex-shrink-0 shadow-xs z-30">
    <div class="px-4 sm:px-6">
        <div class="flex justify-between h-16 items-center gap-4">

            {{-- Left: Hamburger (mobile) + Breadcrumbs --}}
            <div class="flex items-center gap-3 min-w-0">
                {{-- Mobile hamburger --}}
                <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden inline-flex items-center justify-center p-1.5 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                {{-- Breadcrumb dinámico estilo PrestaShop --}}
                @php
                    $routeName = request()->route()?->getName() ?? '';

                    $sectionMap = [
                        'dashboard'              => ['Inicio', 'Dashboard'],
                        'pos.'                   => ['Vender', 'Caja POS'],
                        'admin.collections'      => ['Vender', 'Cobranzas & Créditos'],
                        'customers.'             => ['Vender', 'Clientes'],
                        'products.'              => ['Catálogo', 'Productos'],
                        'categories.'            => ['Catálogo', 'Categorías'],
                        'settings.units'         => ['Catálogo', 'Unidades de Medida'],
                        'purchases.'             => ['Gestión', 'Compras'],
                        'suppliers.'             => ['Gestión', 'Proveedores'],
                        'admin.deboning.'        => ['Gestión', 'Taller de Desposte'],
                        'expenses.'              => ['Finanzas', 'Gastos & Egresos'],
                        'reports.cash'           => ['Finanzas', 'Sesiones de Caja'],
                        'reports.cash-movements' => ['Finanzas', 'Movimientos de Caja'],
                        'reports.sales'          => ['Estadísticas', 'Detalle de Ventas'],
                        'reports.salesperson'    => ['Estadísticas', 'Ranking Vendedores'],
                        'reports.inventory'      => ['Estadísticas', 'Estado de Inventario'],
                        'reports.debtors'        => ['Estadísticas', 'Saldos Deudores'],
                        'settings.'              => ['Configurar', 'Ajustes'],
                        'branches.'              => ['Administración', 'Sucursales'],
                        'cash_registers.'        => ['Administración', 'Puntos de Venta'],
                        'users.'                 => ['Administración', 'Usuarios'],
                        'sellers.'               => ['Administración', 'Vendedores'],
                        'profile.'               => ['Usuario', 'Mi Perfil'],
                        'cash.'                  => ['Caja', 'Mi Caja'],
                        'master.'                => ['Master', 'Control Licensing'],
                    ];

                    $section = 'Inicio';
                    $page = 'ZonaShop';

                    foreach ($sectionMap as $prefix => [$sec, $pag]) {
                        if (str_starts_with($routeName, $prefix) || $routeName === $prefix) {
                            $section = $sec;
                            $page = $pag;
                            break;
                        }
                    }
                @endphp

                <div class="hidden lg:flex items-center gap-2 text-xs" style="font-family: 'Inter', system-ui, sans-serif;">
                    <span class="text-slate-400 dark:text-slate-500 font-semibold uppercase tracking-wider text-[11px]">{{ $section }}</span>
                    <svg class="w-3 h-3 text-slate-300 dark:text-slate-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-slate-800 dark:text-slate-200 font-bold text-sm">{{ $page }}</span>
                </div>
            </div>

            {{-- Center / Quick Search (PrestaShop Omni-search Style + Spotlight Trigger) --}}
            <div class="hidden md:flex flex-1 max-w-md mx-4">
                <button type="button"
                        onclick="window.dispatchEvent(new CustomEvent('open-command-palette'))"
                        class="w-full flex items-center justify-between pl-3.5 pr-2.5 py-1.5 text-xs bg-slate-100 dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-200/70 dark:hover:bg-slate-700 transition cursor-pointer text-left shadow-2xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span class="text-xs text-slate-400 font-normal">Buscar producto, cliente o acción...</span>
                    </div>
                    <kbd class="hidden sm:inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-mono font-bold text-slate-400 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded shadow-2xs">
                        <span class="text-[9px]">Ctrl</span> K
                    </kbd>
                </button>
            </div>

            {{-- Right: Quick Tools & Profile --}}
            <div class="flex items-center gap-2">

                {{-- Acceso Rápido (Quick Access Dropdown PrestaShop Style) --}}
                <div class="relative" x-data="{ quickOpen: false }">
                    <button @click="quickOpen = !quickOpen"
                            @click.away="quickOpen = false"
                            class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition border border-slate-200 dark:border-slate-600 shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span>Acceso Rápido</span>
                        <svg class="w-3 h-3 text-slate-400 transition-transform" :class="quickOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Quick Menu Dropdown --}}
                    <div x-show="quickOpen"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 py-1.5 z-50 divide-y divide-slate-100 dark:divide-slate-700 text-xs"
                         style="display: none;">
                        <div class="p-1 space-y-0.5">
                            <a href="{{ route('pos.index') }}" class="flex items-center gap-2.5 px-3 py-2 text-slate-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg transition font-medium">
                                <span class="p-1 rounded bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400">⚡</span>
                                <span>Abrir Caja POS (Vender)</span>
                            </a>
                            <a href="{{ route('products.index') }}" wire:navigate class="flex items-center gap-2.5 px-3 py-2 text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-lg transition font-medium">
                                <span class="p-1 rounded bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400">📦</span>
                                <span>Nuevo Producto</span>
                            </a>
                            <a href="{{ route('customers.index') }}" wire:navigate class="flex items-center gap-2.5 px-3 py-2 text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg transition font-medium">
                                <span class="p-1 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400">👥</span>
                                <span>Nuevo Cliente</span>
                            </a>
                            <a href="{{ route('purchases.index') }}" wire:navigate class="flex items-center gap-2.5 px-3 py-2 text-slate-700 dark:text-slate-200 hover:bg-amber-50 dark:hover:bg-amber-900/30 hover:text-amber-600 dark:hover:text-amber-400 rounded-lg transition font-medium">
                                <span class="p-1 rounded bg-amber-100 dark:bg-amber-900/50 text-amber-600 dark:text-amber-400">📥</span>
                                <span>Registrar Compra</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Dark Mode Toggle --}}
                <button
                    id="darkModeToggle"
                    onclick="toggleDarkMode()"
                    class="dark-mode-toggle p-2 rounded-lg text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition"
                    title="Alternar modo oscuro"
                    aria-label="Modo oscuro">
                    <svg id="iconSun" class="hidden w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <svg id="iconMoon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </button>

                {{-- Notifications Bell --}}
                @livewire('admin.global-notifications')

                {{-- User Profile Dropdown --}}
                <div class="ms-1 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <button class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-slate-300 transition">
                                    <img class="h-7 w-7 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                                </button>
                            @else
                                <button type="button"
                                    class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                    <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-indigo-600 to-indigo-400 flex items-center justify-center text-white font-black text-[10px] shadow-xs">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                    <span class="hidden sm:block max-w-[110px] truncate">{{ Auth::user()->name }}</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            @endif
                        </x-slot>

                        <x-slot name="content">
                            <div class="block px-4 py-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                {{ __('Mi Cuenta') }}
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                👤 {{ __('Perfil de Usuario') }}
                            </x-dropdown-link>

                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                    🔑 {{ __('API Tokens') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-slate-100 dark:border-slate-700"></div>

                            @if(auth()->user()->isDeveloper())
                                <x-dropdown-link href="{{ route('master.licensing') }}" class="bg-yellow-50 dark:bg-yellow-950/40 hover:bg-yellow-100 text-yellow-800 dark:text-yellow-400 font-bold">
                                    🗝️ {{ __('Master Licensing') }}
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('master.sifen') }}" class="bg-indigo-50 dark:bg-indigo-950/40 hover:bg-indigo-100 text-indigo-800 dark:text-indigo-400 font-bold">
                                    📑 {{ __('Facturación SIFEN') }}
                                </x-dropdown-link>
                                <div class="border-t border-slate-100 dark:border-slate-700"></div>
                            @endif

                            @if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isCashier())
                                <x-dropdown-link href="{{ route('cash.index') }}">
                                    💵 {{ __('Mi Sesión de Caja') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-slate-100 dark:border-slate-700"></div>

                            <button type="button"
                                    @click="window.dispatchEvent(new CustomEvent('open-about-modal'))"
                                    class="w-full text-left block px-4 py-2 text-xs leading-5 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                ℹ️ {{ __('Acerca del Sistema') }}
                            </button>

                            <div class="border-t border-slate-100 dark:border-slate-700"></div>

                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();" class="text-red-600 dark:text-red-400 font-medium">
                                    🚪 {{ __('Cerrar Sesión') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    function toggleDarkMode() {
        const html = document.documentElement;
        const isDark = html.classList.toggle('dark');
        localStorage.setItem('zonashop_theme', isDark ? 'dark' : 'light');
        updateDarkIcons(isDark);
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { isDark } }));
    }

    function updateDarkIcons(isDark) {
        const sun  = document.getElementById('iconSun');
        const moon = document.getElementById('iconMoon');
        if (sun)  sun.classList.toggle('hidden', isDark);
        if (moon) moon.classList.toggle('hidden', !isDark);
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateDarkIcons(document.documentElement.classList.contains('dark'));

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
            if (!localStorage.getItem('zonashop_theme')) {
                document.documentElement.classList.toggle('dark', e.matches);
                updateDarkIcons(e.matches);
                window.dispatchEvent(new CustomEvent('theme-changed', { detail: { isDark: e.matches } }));
            }
        });
    });
</script>

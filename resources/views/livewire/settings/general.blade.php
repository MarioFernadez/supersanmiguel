<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Section: Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
                ⚙️ Ajustes Generales del Sistema
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configura la información comercial, opciones de facturación, descuentos y copias de seguridad de la tienda.</p>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 bg-green-500/10 border-l-4 border-green-500 text-green-700 dark:text-green-300 p-4 rounded-xl shadow-sm flex items-center gap-3" role="alert">
                <svg class="h-6 w-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm font-semibold">
                    {{ session('message') }}
                </div>
            </div>
        @endif

        <!-- Section: Datos del Comercio -->
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">🏢 Datos del Comercio</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Esta información se utiliza de forma global en tus tickets de facturación, reportes y comprobantes del sistema.
            </p>
        </div>

        <div class="mt-2 mb-10">
            <form wire:submit.prevent="save">
                <div class="shadow-md rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-6 gap-6">
                            @if(auth()->user()->role === 'developer' || auth()->user()->role === 'admin')
                            <!-- Master/Admin Solo: Selector de Rubro -->
                            <div class="col-span-6 border border-purple-200 bg-purple-50 dark:bg-purple-900/20 dark:border-purple-800 p-4 rounded-xl shadow-sm mb-4">
                                <label for="business_type" class="block text-sm font-black text-purple-900 dark:text-purple-300 flex items-center gap-2">
                                    👑 Tipo de Negocio (Multirrubro)
                                    <span class="inline-flex px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest bg-purple-200 text-purple-700 dark:bg-purple-800 dark:text-purple-100">Master</span>
                                </label>
                                <p class="text-xs text-purple-600 dark:text-purple-400 mb-3 mt-1 font-medium">Define el modelo de negocio para activar/desactivar módulos específicos (ej: Tallas, Desposte, Decants).</p>
                                <select wire:model="business_type" id="business_type" class="mt-1 focus:ring-purple-500 focus:border-purple-500 block w-full shadow-sm sm:text-sm border-purple-300 rounded-lg dark:border-purple-600 dark:bg-gray-900 dark:text-gray-300">
                                    <option value="generic">📦 Genérico (Compra/Venta Unitario Estándar)</option>
                                    <option value="minimarket">🛒 Minimercado / Supermercado / Carnicería (Balanza, Desposte)</option>
                                    <option value="boutique">👗 Boutique / Ropa / Zapatos (Tallas y Colores)</option>
                                    <option value="perfumery">🧪 Perfumería (Fraccionamiento / Decants)</option>
                                    <option value="restaurant">🍽️ Restaurante / Gastronomía (Mesas y Comandas)</option>
                                </select>
                                @error('business_type') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>
                            @endif

                            <div class="col-span-6 sm:col-span-3">
                                <label for="store_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nombre del Comercio</label>
                                <input type="text" wire:model="store_name" id="store_name" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300">
                                @error('store_name') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="store_ruc" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">RUC / Identificación Tributaria</label>
                                <input type="text" wire:model="store_ruc" id="store_ruc" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300">
                                @error('store_ruc') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="store_address" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Dirección Física</label>
                                <input type="text" wire:model="store_address" id="store_address" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300">
                                @error('store_address') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="store_phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Teléfono Comercial</label>
                                <input type="text" wire:model="store_phone" id="store_phone" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300">
                                @error('store_phone') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="store_email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Email del Comercio</label>
                                <input type="email" wire:model="store_email" id="store_email" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300">
                                @error('store_email') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="ticket_footer" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Mensaje al Pie del Ticket</label>
                                <input type="text" wire:model="ticket_footer" id="ticket_footer" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-lg dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300" placeholder="Gracias por su compra">
                                @error('ticket_footer') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Descuentos -->
                            <div class="col-span-6 border-t border-gray-100 dark:border-gray-700 pt-5 mt-2">
                                <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">🏷️ Funcionalidades de Descuento</h4>
                                <div class="space-y-4 bg-slate-50 dark:bg-slate-900/40 p-4 rounded-xl border border-slate-100 dark:border-slate-800">
                                    <label class="flex items-start gap-3 cursor-pointer select-none">
                                        <input type="checkbox" wire:model="discount_product_enabled" id="discount_product_enabled"
                                            class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-4.5 w-4.5 dark:border-gray-600 dark:bg-gray-900">
                                        <div>
                                            <span class="block text-sm font-bold text-gray-700 dark:text-gray-300">Descuento por Producto</span>
                                            <span class="block text-xs text-gray-400 dark:text-gray-500">Permite establecer un porcentaje de descuento predefinido y permanente para cada producto desde su ficha en el catálogo.</span>
                                        </div>
                                    </label>
                                    <label class="flex items-start gap-3 cursor-pointer select-none border-t border-gray-100 dark:border-gray-700/50 pt-3">
                                        <input type="checkbox" wire:model="discount_manual_enabled" id="discount_manual_enabled"
                                            class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-4.5 w-4.5 dark:border-gray-600 dark:bg-gray-900">
                                        <div>
                                            <span class="block text-sm font-bold text-gray-700 dark:text-gray-300">Descuento Manual en POS</span>
                                            <span class="block text-xs text-gray-400 dark:text-gray-500">Habilita al cajero a aplicar un porcentaje de descuento personalizado directamente al total de la compra antes de cobrar.</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-900/50 text-right">
                        <button type="submit" class="inline-flex justify-center py-2.5 px-6 border border-transparent shadow-md text-sm font-black rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 transition">
                            Guardar Ajustes Comerciales
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 mb-10"></div>

        <!-- Section: Parámetros del Sistema -->
        <div class="mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">🛠️ Parámetros Avanzados</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Ajusta las configuraciones secundarias de facturación, pasarelas de pago, unidades de medida y flujo de caja.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            
            <!-- Card: Métodos de Pago -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col justify-between hover:shadow-lg transition duration-300">
                <div>
                    <div class="h-10 w-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white mb-2">💳 Métodos de Pago</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        Administra las formas de pago aceptadas (Efectivo, Tarjetas, Bancos) en tu caja POS y compras.
                    </p>
                </div>
                <div class="mt-5">
                    <a href="{{ route('settings.payment-methods') }}" class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                        Configurar
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>

            <!-- Card: Unidades de Medida -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col justify-between hover:shadow-lg transition duration-300">
                <div>
                    <div class="h-10 w-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white mb-2">📏 Unidades de Medida</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        Crea y edita unidades (Unidad, Caja, Kg, Litros) asociadas a tus productos para control de inventario exacto.
                    </p>
                </div>
                <div class="mt-5">
                    <a href="{{ route('settings.units') }}" class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                        Configurar
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>

            <!-- Card: Conceptos de Caja -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col justify-between hover:shadow-lg transition duration-300">
                <div>
                    <div class="h-10 w-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white mb-2">💸 Conceptos de Caja</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        Gestiona los motivos para movimientos manuales de caja (ej: Retiro para depósito, Sencillo, etc.).
                    </p>
                </div>
                <div class="mt-5">
                    <a href="{{ route('settings.cash-movement-categories') }}" class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                        Configurar
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>

            <!-- Card: Cotizaciones de Moneda -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col justify-between hover:shadow-lg transition duration-300">
                <div>
                    <div class="h-10 w-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white mb-2">📈 Cotizaciones de Moneda</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        Establece el tipo de cambio del Guaraní frente al Dólar, Real, Peso para cobros multidivisa en POS.
                    </p>
                </div>
                <div class="mt-5">
                    <a href="{{ route('settings.quotation') }}" class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                        Configurar
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>

            <!-- Card: Licencia & Soporte -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col justify-between hover:shadow-lg transition duration-300">
                <div>
                    <div class="h-10 w-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white mb-2">🛡️ Licencia & Facturación</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        Verifica el estado actual de tu licencia comercial del software, planes contratados y fecha de vencimiento.
                    </p>
                </div>
                <div class="mt-5">
                    <a href="{{ route('settings.billing') }}" class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                        Ver Estado
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>

            <!-- Card: Copia de Respaldo -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col justify-between hover:shadow-lg transition duration-300">
                <div>
                    <div class="h-10 w-10 bg-green-50 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-green-600 dark:text-green-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    </div>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white mb-2">💾 Respaldo de Base de Datos</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                        Genera y descarga al instante un archivo de respaldo completo SQL de tu negocio para mayor seguridad.
                    </p>
                </div>
                <div class="mt-5">
                    <form action="{{ route('settings.backup.run') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-widest text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 transition">
                            Descargar SQL
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
